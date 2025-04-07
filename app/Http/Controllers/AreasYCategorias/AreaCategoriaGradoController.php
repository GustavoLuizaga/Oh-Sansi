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

    /*public function exportExcel()
    {
        $delegaciones = DB::table('delegacion')->get();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Headers
        $sheet->setCellValue('A1', 'Código SIE');
        $sheet->setCellValue('B1', 'Nombre');
        $sheet->setCellValue('C1', 'Departamento');
        $sheet->setCellValue('D1', 'Provincia');
        $sheet->setCellValue('E1', 'Municipio');
        $sheet->setCellValue('F1', 'Dependencia');
        
        // Data
        $row = 2;
        foreach ($delegaciones as $delegacion) {
            $sheet->setCellValue('A' . $row, $delegacion->codigo_sie);
            $sheet->setCellValue('B' . $row, $delegacion->nombre);
            $sheet->setCellValue('C' . $row, $delegacion->departamento);
            $sheet->setCellValue('D' . $row, $delegacion->provincia);
            $sheet->setCellValue('E' . $row, $delegacion->municipio);
            $sheet->setCellValue('F' . $row, $delegacion->dependencia);
            $row++;
        }
        
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="delegaciones.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }*/
}