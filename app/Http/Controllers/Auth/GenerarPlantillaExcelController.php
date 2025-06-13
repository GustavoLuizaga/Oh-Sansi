<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\Area;
use App\Models\Categoria;
use App\Models\Grado;
use App\Models\Delegacion;
use App\Http\Controllers\Inscripcion\VerificarExistenciaConvocatoria;
use App\Http\Controllers\Inscripcion\ObtenerAreasConvocatoria;

class GenerarPlantillaExcelController extends Controller
{
    public function generarPlantilla()
    {
        // Crear nuevo libro de Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inscripciones');
        
        // Definir encabezados
        $headers = [
            'Nombre', 'Apellido Paterno', 'Apellido Materno', 'CI', 'Email', 
            'Fecha Nacimiento', 'Género', 'Área', 'Categoría', 'Grado', 
            'Número Contacto', 'Delegación', 'Nombre Tutor', 'Email Tutor',
            'Modalidad', 'Código Invitación'
        ];
        
        // Establecer encabezados en columnas (primera fila)
        foreach ($headers as $key => $header) {
            $column = chr(65 + $key); // A, B, C, etc.
            $sheet->setCellValue($column . '1', $header);
        }
        
        // Estilo para encabezados
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        
        $sheet->getStyle('A1:P1')->applyFromArray($headerStyle);
        
        // Obtener datos para listas desplegables
        $idConvocatoriaResult = (new VerificarExistenciaConvocatoria())->verificarConvocatoriaActiva();
        $areas = [];
        
        if ($idConvocatoriaResult) {
            $areasHabilitadas = (new ObtenerAreasConvocatoria())->obtenerAreasPorConvocatoria($idConvocatoriaResult);
            $areas = $areasHabilitadas->pluck('nombre')->toArray();
        } else {
            $areas = Area::pluck('nombre')->toArray();
        }
        
        $delegaciones = Delegacion::pluck('nombre')->toArray();
        
        // Crear listas desplegables
        $this->crearListaDesplegable($sheet, 'G', ['M', 'F'], 2, 100); // Género
        $this->crearListaDesplegable($sheet, 'H', $areas, 2, 100); // Áreas
        $this->crearListaDesplegable($sheet, 'L', $delegaciones, 2, 100); // Delegaciones
        $this->crearListaDesplegable($sheet, 'O', ['Individual', 'Duo', 'Equipo'], 2, 100); // Modalidad
        
        // Ajustar ancho de columnas
        foreach (range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Agregar instrucciones en una hoja separada
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Instrucciones');
        
        $instructionSheet->setCellValue('A1', 'INSTRUCCIONES PARA COMPLETAR LA PLANTILLA');
        $instructionSheet->setCellValue('A3', '1. Complete todos los campos obligatorios marcados con (*)');
        $instructionSheet->setCellValue('A4', '2. Nombre, Apellidos, CI, Email y Género son obligatorios.');
        $instructionSheet->setCellValue('A5', '3. La fecha de nacimiento debe estar en formato DD/MM/AAAA.');
        $instructionSheet->setCellValue('A6', '4. El área, categoría y grado deben existir en la convocatoria actual.');
        $instructionSheet->setCellValue('A7', '5. El número de contacto debe tener 8 dígitos.');
        $instructionSheet->setCellValue('A8', '6. Si selecciona modalidad "Duo" o "Equipo", debe proporcionar un código de invitación.');
        $instructionSheet->setCellValue('A9', '7. El código de invitación es necesario para agrupar estudiantes en la misma modalidad.');
        $instructionSheet->setCellValue('A11', 'CAMPOS OBLIGATORIOS:');
        $instructionSheet->setCellValue('A12', '- Nombre (*)');
        $instructionSheet->setCellValue('A13', '- Apellido Paterno (*)');
        $instructionSheet->setCellValue('A14', '- Apellido Materno (*)');
        $instructionSheet->setCellValue('A15', '- CI (*)');
        $instructionSheet->setCellValue('A16', '- Email (*)');
        $instructionSheet->setCellValue('A17', '- Fecha Nacimiento (*)');
        $instructionSheet->setCellValue('A18', '- Género (*)');
        $instructionSheet->setCellValue('A19', '- Área (*)');
        $instructionSheet->setCellValue('A20', '- Categoría (*)');
        $instructionSheet->setCellValue('A21', '- Grado (*)');
        $instructionSheet->setCellValue('A22', '- Número Contacto (*)');
        $instructionSheet->setCellValue('A23', '- Delegación (*)');
        
        // Estilo para instrucciones
        $titleStyle = [
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '4F46E5'],
            ],
        ];
        
        $instructionSheet->getStyle('A1')->applyFromArray($titleStyle);
        $instructionSheet->getColumnDimension('A')->setWidth(60);
        
        // Guardar archivo
        $writer = new Xlsx($spreadsheet);
        $filePath = public_path('plantillasExel/plantilla_inscripcion.xlsx');
        $writer->save($filePath);
        
        return $filePath;
    }
    
    private function crearListaDesplegable($sheet, $column, $options, $startRow, $endRow)
    {
        // Crear una hoja oculta para las opciones
        $spreadsheet = $sheet->getParent();
        $optionsSheetName = 'Options_' . $column;
        
        if (!$spreadsheet->sheetNameExists($optionsSheetName)) {
            $optionsSheet = $spreadsheet->createSheet();
            $optionsSheet->setTitle($optionsSheetName);
            
            // Agregar opciones a la hoja
            foreach ($options as $index => $option) {
                $optionsSheet->setCellValue('A' . ($index + 1), $option);
            }
            
            // Ocultar la hoja de opciones
            $optionsSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);
        } else {
            $optionsSheet = $spreadsheet->getSheetByName($optionsSheetName);
        }
        
        // Crear nombre para el rango de opciones
        $rangeName = 'Options_' . $column;
        $optionsRange = $optionsSheetName . '!$A$1:$A$' . count($options);
        
        // Definir el rango nombrado
        // Verificar si el rango nombrado ya existe
        $namedRangeExists = false;
        foreach ($spreadsheet->getNamedRanges() as $namedRange) {
            if ($namedRange->getName() == $rangeName) {
                $namedRangeExists = true;
                break;
            }
        }
        
        // Si no existe, agregarlo
        if (!$namedRangeExists) {
            $spreadsheet->addNamedRange(
                new \PhpOffice\PhpSpreadsheet\NamedRange(
                    $rangeName,
                    $optionsSheet,
                    $optionsRange
                )
            );
        }
        
        // Aplicar validación de datos
        $validation = $sheet->getCell($column . $startRow)->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(true);
        $validation->setFormula1('=' . $rangeName);
        $validation->setPromptTitle('Seleccione una opción');
        $validation->setPrompt('Elija un valor de la lista');
        $validation->setErrorTitle('Error');
        $validation->setError('El valor no está en la lista');
        
        // Copiar la validación a todas las celdas del rango
        for ($i = $startRow; $i <= $endRow; $i++) {
            $sheet->getCell($column . $i)->setDataValidation(clone $validation);
        }
    }
}