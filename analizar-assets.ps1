# Analizador de Assets Avanzado para Laravel - Version Windows
# Busca problemas en rutas de CSS, JS, imagenes y referencias a "assets"

param(
    [string]$ProjectPath = ".",
    [switch]$ShowDetails = $false,
    [switch]$SearchAssetsWord = $false,
    [string]$SpecificFileType = "",
    [string]$CustomPattern = ""
)

Write-Host "Analizando proyecto Laravel..." -ForegroundColor Green
Write-Host "Ruta: $(Get-Location)" -ForegroundColor Yellow
Write-Host ""

# Contadores
$totalFiles = 0
$problemFiles = 0
$totalProblems = 0
$assetsReferences = 0

# Tipos de archivos a analizar
$fileExtensions = @("*.blade.php", "*.php", "*.html", "*.js", "*.vue", "*.css", "*.scss", "*.json")

# Patrones problematicos originales
$patterns = @{
    "CSS sin barra inicial" = "href\s*=\s*[`"'](?!/)css/"
    "JS sin barra inicial" = "src\s*=\s*[`"'](?!/)js/"
    "IMG sin barra inicial" = "src\s*=\s*[`"'](?!/)img/"
    "Asset helper CSS" = "\{\{\s*asset\s*\(\s*[`"']css/"
    "Asset helper JS" = "\{\{\s*asset\s*\(\s*[`"']js/"
    "Asset helper IMG" = "\{\{\s*asset\s*\(\s*[`"']img/"
}

# Patrones adicionales para busqueda avanzada
$advancedPatterns = @{
    "Palabra assets" = "assets"
    "Asset helper general" = "\{\{\s*asset\s*\("
    "Mix helper" = "mix\s*\("
    "URL helper con assets" = "url\s*\(\s*[`"'][^`"']*assets"
    "Vite assets" = "@vite\s*\("
    "Asset paths" = "[`"'][^`"']*assets[^`"']*[`"']"
    "Public path helper" = "public_path\s*\("
    "Storage path" = "storage_path\s*\("
}

# Agregar patrones avanzados si se solicita
if ($SearchAssetsWord -or $CustomPattern) {
    foreach ($key in $advancedPatterns.Keys) {
        $patterns[$key] = $advancedPatterns[$key]
    }
}

# Agregar patron personalizado si se proporciona
if ($CustomPattern) {
    $patterns["Patron personalizado"] = $CustomPattern
}

# Filtrar tipos de archivo si se especifica
if ($SpecificFileType) {
    $fileExtensions = @("*.$SpecificFileType")
    Write-Host "Buscando solo en archivos: $SpecificFileType" -ForegroundColor Cyan
}

# Funcion para analizar archivo
function Analyze-File {
    param($filePath)
    
    try {
        $content = Get-Content $filePath -Raw -ErrorAction SilentlyContinue -Encoding UTF8
        if (-not $content) { 
            return $null 
        }
        
        # Ignorar archivos de configuracion para storage_path y public_path
        $isConfigFile = $filePath -match "config[\\/]"
        
        $fileProblems = @()
        $lines = Get-Content $filePath -Encoding UTF8
        
        foreach ($patternName in $patterns.Keys) {
            $pattern = $patterns[$patternName]
            
            # Saltar storage_path y public_path en archivos de configuracion
            if ($isConfigFile -and ($patternName -eq "Storage path" -or $patternName -eq "Public path helper")) {
                continue
            }
            
            try {
                $matches = [regex]::Matches($content, $pattern, [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
                
                foreach ($match in $matches) {
                    # Encontrar numero de linea
                    $lineNumber = 1
                    $position = 0
                    foreach ($line in $lines) {
                        $position += $line.Length + 1
                        if ($position -gt $match.Index) {
                            break
                        }
                        $lineNumber++
                    }
                    
                    # Verificar si la linea esta comentada
                    $currentLine = if ($lineNumber -le $lines.Count) { $lines[$lineNumber - 1] } else { "" }
                    $isCommented = $currentLine -match "^\s*(//|/\*|\{\{--)" -or $currentLine -match "--\}\}"
                    
                    # Solo agregar si no esta comentado
                    if (-not $isCommented) {
                        $fileProblems += [PSCustomObject]@{
                            Tipo = $patternName
                            Linea = $lineNumber
                            Contenido = $match.Value
                            LineaCompleta = $currentLine.Trim()
                            Contexto = Get-LineContext -lines $lines -lineNumber $lineNumber
                        }
                        
                        if ($patternName -eq "Palabra assets") {
                            $script:assetsReferences++
                        }
                    }
                }
            }
            catch {
                Write-Warning "Error procesando patron '$patternName' en archivo '$filePath': $($_.Exception.Message)"
            }
        }
        
        return $fileProblems
    }
    catch {
        Write-Warning "Error leyendo archivo '$filePath': $($_.Exception.Message)"
        return $null
    }
}

# Funcion para obtener contexto de lineas
function Get-LineContext {
    param($lines, $lineNumber, $contextLines = 2)
    
    if (-not $lines -or $lineNumber -le 0) {
        return ""
    }
    
    $start = [Math]::Max(0, $lineNumber - $contextLines - 1)
    $end = [Math]::Min($lines.Count - 1, $lineNumber + $contextLines - 1)
    
    $context = @()
    for ($i = $start; $i -le $end; $i++) {
        if ($i -lt $lines.Count) {
            $prefix = if ($i -eq ($lineNumber - 1)) { ">>> " } else { "    " }
            $context += "$prefix$($i + 1): $($lines[$i])"
        }
    }
    
    return $context -join "`n"
}

# Buscar archivos
Write-Host "Buscando archivos..." -ForegroundColor Cyan

# Directorios a excluir especificos para tu proyecto
$excludePatterns = @(
    'node_modules',
    'vendor', 
    '\.git',
    'storage[\\/]logs',
    'storage[\\/]framework',
    'storage[\\/]app[\\/]public',
    'bootstrap[\\/]cache',
    'docker'
)

$excludeRegex = $excludePatterns -join '|'

$allFiles = @()
foreach ($ext in $fileExtensions) {
    try {
        $files = Get-ChildItem -Path $ProjectPath -Filter $ext -Recurse -File -ErrorAction SilentlyContinue | 
                 Where-Object { $_.FullName -notmatch $excludeRegex }
        $allFiles += $files
    }
    catch {
        Write-Warning "Error buscando archivos con extension $ext : $($_.Exception.Message)"
    }
}

$totalFiles = $allFiles.Count
Write-Host "Archivos encontrados: $totalFiles" -ForegroundColor Yellow

if ($SearchAssetsWord) {
    Write-Host "Busqueda avanzada de 'assets' activada" -ForegroundColor Magenta
}

Write-Host ""

# Analizar cada archivo
$results = @()
$fileCount = 0

foreach ($file in $allFiles) {
    $fileCount++
    $percent = [Math]::Round(($fileCount / $totalFiles) * 100, 1)
    Write-Progress -Activity "Analizando archivos" -Status "$($file.Name) ($fileCount de $totalFiles)" -PercentComplete $percent
    
    $problems = Analyze-File -filePath $file.FullName
    
    if ($problems -and $problems.Count -gt 0) {
        $problemFiles++
        $totalProblems += $problems.Count
        
        $relativePath = $file.FullName.Replace((Get-Location).Path, "").TrimStart('\', '/')
        
        $results += [PSCustomObject]@{
            Archivo = $relativePath
            Problemas = $problems
            CantidadProblemas = $problems.Count
            Extension = $file.Extension
        }
    }
}

Write-Progress -Completed -Activity "Analisis completado"

# Mostrar resultados
Write-Host "RESUMEN DEL ANALISIS" -ForegroundColor Green
Write-Host "===========================================" -ForegroundColor Green
Write-Host "Archivos analizados: $totalFiles" -ForegroundColor White
Write-Host "Archivos con problemas: $problemFiles" -ForegroundColor Red
Write-Host "Total de problemas: $totalProblems" -ForegroundColor Red

if ($SearchAssetsWord) {
    Write-Host "Referencias a 'assets': $assetsReferences" -ForegroundColor Magenta
}

Write-Host ""

if ($results.Count -eq 0) {
    Write-Host "Perfecto! No se encontraron problemas." -ForegroundColor Green
    return
}

# Agrupar por tipo de archivo
if ($results.Count -gt 0) {
    $groupedResults = $results | Group-Object Extension | Sort-Object Count -Descending

    Write-Host "PROBLEMAS POR TIPO DE ARCHIVO:" -ForegroundColor Cyan
    foreach ($group in $groupedResults) {
        $totalProblemsInType = ($group.Group | Measure-Object CantidadProblemas -Sum).Sum
        Write-Host "$($group.Name): $($group.Count) archivos, $totalProblemsInType problemas" -ForegroundColor Yellow
    }
    Write-Host ""
}

# Mostrar archivos problematicos
Write-Host "ARCHIVOS CON PROBLEMAS:" -ForegroundColor Red
Write-Host "===========================================" -ForegroundColor Red

foreach ($result in $results | Sort-Object CantidadProblemas -Descending) {
    Write-Host ""
    Write-Host "Archivo: $($result.Archivo)" -ForegroundColor Yellow
    Write-Host "Problemas encontrados: $($result.CantidadProblemas)" -ForegroundColor Red
    
    if ($ShowDetails) {
        foreach ($problem in $result.Problemas) {
            Write-Host ""
            Write-Host "   - Linea $($problem.Linea): $($problem.Tipo)" -ForegroundColor Cyan
            Write-Host "   Encontrado: $($problem.Contenido)" -ForegroundColor Gray
            
            if ($SearchAssetsWord -and $problem.Tipo -eq "Palabra assets" -and $problem.Contexto) {
                Write-Host "   Contexto:" -ForegroundColor DarkGray
                Write-Host $problem.Contexto -ForegroundColor DarkGray
            } else {
                Write-Host "   Linea completa: $($problem.LineaCompleta)" -ForegroundColor DarkGray
            }
        }
    }
}

# Recomendaciones
Write-Host ""
Write-Host "RECOMENDACIONES:" -ForegroundColor Green
Write-Host "===========================================" -ForegroundColor Green
Write-Host "1. Cambiar 'css/' por '/css/'" -ForegroundColor White
Write-Host "2. Cambiar 'js/' por '/js/'" -ForegroundColor White  
Write-Host "3. Cambiar 'img/' por '/img/'" -ForegroundColor White
Write-Host "4. Considerar usar Vite en lugar de asset() helpers" -ForegroundColor White
Write-Host "5. Revisar referencias a 'assets' para consistencia" -ForegroundColor White
Write-Host ""

# Ejemplos especificos para tu proyecto
Write-Host "EJEMPLOS PARA TU PROYECTO:" -ForegroundColor Green
Write-Host "===========================================" -ForegroundColor Green
Write-Host "# Analizar solo vistas Blade:" -ForegroundColor Cyan
Write-Host ".\analizar-assets.ps1 -ProjectPath '.\resources\views' -ShowDetails -SearchAssetsWord" -ForegroundColor Yellow
Write-Host ""
Write-Host "# Analizar configuraciones de build:" -ForegroundColor Cyan  
Write-Host ".\analizar-assets.ps1 -SpecificFileType js -SearchAssetsWord" -ForegroundColor Yellow
Write-Host ""
Write-Host "# Verificar archivos publicos:" -ForegroundColor Cyan
Write-Host ".\analizar-assets.ps1 -ProjectPath '.\public' -ShowDetails" -ForegroundColor Yellow
Write-Host ""
Write-Host "# Buscar helpers de Laravel:" -ForegroundColor Cyan
Write-Host ".\analizar-assets.ps1 -CustomPattern 'public_path|storage_path|asset\(' -ShowDetails" -ForegroundColor Yellow

# Generar archivo de reporte
try {
    $reportFile = "assets-report-$(Get-Date -Format 'yyyyMMdd-HHmmss').json"
    $reportData = @{
        Timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        Summary = @{
            TotalFiles = $totalFiles
            ProblemFiles = $problemFiles
            TotalProblems = $totalProblems
            AssetsReferences = $assetsReferences
        }
        Problems = $results | ForEach-Object {
            @{
                File = $_.Archivo
                Extension = $_.Extension
                ProblemCount = $_.CantidadProblemas
                Problems = $_.Problemas | ForEach-Object {
                    @{
                        Type = $_.Tipo
                        Line = $_.Linea
                        Content = $_.Contenido
                        FullLine = $_.LineaCompleta
                    }
                }
            }
        }
    }

    $reportData | ConvertTo-Json -Depth 10 | Out-File $reportFile -Encoding UTF8
    Write-Host ""
    Write-Host "Reporte JSON guardado en: $reportFile" -ForegroundColor Green
}
catch {
    Write-Warning "No se pudo generar el reporte JSON: $($_.Exception.Message)"
}