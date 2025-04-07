<?php

namespace App\Http\Controllers\AreasYCategorias;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Categoria;
use App\Models\Convocatoria;
use App\Models\Grado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}