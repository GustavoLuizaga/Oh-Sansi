<?php

namespace App\Http\Controllers\AreasYCategorias;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Categoria;
use App\Models\Convocatoria;
use App\Models\Grado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AreaCategoriaGradoController extends Controller
{
    public function index()
    {
        // Obtener la convocatoria con estado 'Publicada'
        $convocatoriaActiva = Convocatoria::where('estado', 'Publicada')->first();
        
        if (!$convocatoriaActiva) {
            return view('areas y categorias.areasCategorias')->with('message', 'No hay convocatoria publicada actualmente');
        }
        
        // Consulta personalizada para obtener las áreas, categorías y grados relacionados
        $areaData = DB::table('convocatoriaareacategoria')
            ->join('area', 'convocatoriaareacategoria.idArea', '=', 'area.idArea')
            ->join('categoria', 'convocatoriaareacategoria.idCategoria', '=', 'categoria.idCategoria')
            ->where('convocatoriaareacategoria.idConvocatoria', $convocatoriaActiva->idConvocatoria)
            ->select('area.idArea', 'area.nombre as areaNombre', 'categoria.idCategoria', 'categoria.nombre as categoriaNombre')
            ->get();
        
        // Estructura para organizar la información
        $areasWithCategorias = [];
        
        foreach ($areaData as $data) {
            if (!isset($areasWithCategorias[$data->idArea])) {
                $areasWithCategorias[$data->idArea] = [
                    'nombre' => $data->areaNombre,
                    'categorias' => []
                ];
            }
            
            // Obtener los grados relacionados con la categoría
            $grados = DB::table('gradocategoria')
                ->join('grado', 'gradocategoria.idGrado', '=', 'grado.idGrado')
                ->where('gradocategoria.idCategoria', $data->idCategoria)
                ->select('grado.idGrado', 'grado.grado')
                ->get();
            
            $areasWithCategorias[$data->idArea]['categorias'][$data->idCategoria] = [
                'nombre' => $data->categoriaNombre,
                'grados' => $grados
            ];
        }
        
        // Convertir el array asociativo a un objeto similar a una colección para mantener compatibilidad con la vista
        $areas = collect($areasWithCategorias)->map(function($area) {
            $areaObj = new \stdClass();
            $areaObj->nombre = $area['nombre'];
            $areaObj->categorias = collect($area['categorias'])->map(function($categoria) {
                $categoriaObj = new \stdClass();
                $categoriaObj->nombre = $categoria['nombre'];
                $categoriaObj->grados = $categoria['grados'];
                return $categoriaObj;
            });
            return $areaObj;
        });
        
        return view('areas y categorias.areasCategorias', compact('areas', 'convocatoriaActiva'));
    }

    public function exportPdf()
    {
        // Obtener la convocatoria activa
        $convocatoriaActiva = Convocatoria::where('estado', 'Publicada')->first();
        
        if (!$convocatoriaActiva) {
            return redirect()->back()->with('error', 'No hay convocatoria publicada actualmente');
        }
        
        // Consulta para obtener áreas, categorías y grados (igual que en index)
        $areaData = DB::table('convocatoriaareacategoria')
            ->join('area', 'convocatoriaareacategoria.idArea', '=', 'area.idArea')
            ->join('categoria', 'convocatoriaareacategoria.idCategoria', '=', 'categoria.idCategoria')
            ->where('convocatoriaareacategoria.idConvocatoria', $convocatoriaActiva->idConvocatoria)
            ->select('area.idArea', 'area.nombre as areaNombre', 'categoria.idCategoria', 'categoria.nombre as categoriaNombre')
            ->get();
        
        // Estructurar los datos (igual que en index)
        $areasWithCategorias = [];
        
        foreach ($areaData as $data) {
            if (!isset($areasWithCategorias[$data->idArea])) {
                $areasWithCategorias[$data->idArea] = [
                    'nombre' => $data->areaNombre,
                    'categorias' => []
                ];
            }
            
            $grados = DB::table('gradocategoria')
                ->join('grado', 'gradocategoria.idGrado', '=', 'grado.idGrado')
                ->where('gradocategoria.idCategoria', $data->idCategoria)
                ->select('grado.idGrado', 'grado.grado')
                ->get();
            
            $areasWithCategorias[$data->idArea]['categorias'][$data->idCategoria] = [
                'nombre' => $data->categoriaNombre,
                'grados' => $grados
            ];
        }
        
        // Convertir a objetos stdClass (igual que en index)
        $areas = collect($areasWithCategorias)->map(function($area) {
            $areaObj = new \stdClass();
            $areaObj->nombre = $area['nombre'];
            $areaObj->categorias = collect($area['categorias'])->map(function($categoria) {
                $categoriaObj = new \stdClass();
                $categoriaObj->nombre = $categoria['nombre'];
                $categoriaObj->grados = $categoria['grados'];
                return $categoriaObj;
            });
            return $areaObj;
        });
        
        // Generar el nombre del archivo
        $nombreArchivo = 'Areas_Categorias_Grados_Convocatoria_'.str_replace(' ', '_', $convocatoriaActiva->nombre).'.pdf';
        
        // Pasar el título personalizado a la vista
        $tituloPDF = 'Areas Categorias Grados Convocatoria: '.$convocatoriaActiva->nombre;
        
        $pdf = PDF::loadView('areas y categorias.pdf', compact('areas', 'convocatoriaActiva', 'tituloPDF'));
        
        return $pdf->download($nombreArchivo);
    }

    public function exportExcel()
{
    // Obtener la convocatoria activa
    $convocatoriaActiva = Convocatoria::where('estado', 'Publicada')->first();
    
    if (!$convocatoriaActiva) {
        return redirect()->back()->with('error', 'No hay convocatoria publicada actualmente');
    }
    
    // Obtener datos estructurados
    $areas = DB::table('convocatoriaareacategoria')
        ->join('area', 'convocatoriaareacategoria.idArea', '=', 'area.idArea')
        ->join('categoria', 'convocatoriaareacategoria.idCategoria', '=', 'categoria.idCategoria')
        ->leftJoin('gradocategoria', 'categoria.idCategoria', '=', 'gradocategoria.idCategoria')
        ->leftJoin('grado', 'gradocategoria.idGrado', '=', 'grado.idGrado')
        ->where('convocatoriaareacategoria.idConvocatoria', $convocatoriaActiva->idConvocatoria)
        ->select(
            'area.nombre as area_nombre',
            'categoria.nombre as categoria_nombre',
            'grado.grado as grado_nombre'
        )
        ->orderBy('area.nombre')
        ->orderBy('categoria.nombre')
        ->orderBy('grado.grado')
        ->get()
        ->groupBy(['area_nombre', 'categoria_nombre']);
    
    // Crear el archivo Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Añadir solo los nombres de las columnas sin formato especial
    $sheet->setCellValue('A1', 'Área');
    $sheet->setCellValue('B1', 'Categoría');
    $sheet->setCellValue('C1', 'Grados');
    
    // Llenar datos
    $row = 2; // Comenzamos en la fila 2 porque la fila 1 tiene los nombres de columnas
    foreach ($areas as $areaNombre => $categorias) {
        foreach ($categorias as $categoriaNombre => $grados) {
            $sheet->setCellValue('A'.$row, $areaNombre);
            $sheet->setCellValue('B'.$row, $categoriaNombre);
            
            // Lista de grados separados por coma
            $gradosList = $grados->pluck('grado_nombre')->filter()->unique()->implode(', ');
            $sheet->setCellValue('C'.$row, $gradosList);
            
            $row++;
        }
    }
    
    // Autoajustar columnas
    foreach (range('A', 'C') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }
    
    // Bordes para toda la tabla
    $lastRow = $row - 1;
    $borderStyle = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000']
            ]
        ]
    ];
    $sheet->getStyle('A1:C'.$lastRow)->applyFromArray($borderStyle);
    
    // Generar nombre del archivo
    $filename = 'Areas_Categorias_Grados_Convocatoria_'.preg_replace('/[^a-zA-Z0-9_]/', '_', $convocatoriaActiva->nombre).'.xlsx';
    
    // Descargar el archivo
    $writer = new Xlsx($spreadsheet);
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$filename.'"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    exit;
}   
}