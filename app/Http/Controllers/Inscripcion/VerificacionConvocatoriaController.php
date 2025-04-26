<?php

namespace App\Http\Controllers\Inscripcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Convocatoria;
use App\Models\ConvocatoriaAreaCategoria;
use App\Models\GradoCategoria;
use Carbon\Carbon;

class VerificacionConvocatoriaController extends Controller
{
    public function mostrarAreasCategoriasGrados()
    {
        // Verificar si hay una convocatoria activa
        $convocatoriaId = $this->verificarConvocatoriaActiva();
        
        if (!$convocatoriaId) {
            return response()->json(['error' => 'No hay convocatoria activa']);
        }

        // Obtener las áreas por convocatoria
        $areas = $this->obtenerAreasPorConvocatoria($convocatoriaId);
        
        $resultado = [];

        foreach ($areas as $area) {
            // Obtener categorías asociadas a esta área en esta convocatoria
            $categorias = $this->obtenerCategoriasPorArea($convocatoriaId, $area->idArea);

            $categoriasConGrados = [];

            foreach ($categorias as $categoria) {
                // Obtener grados asociados a esta categoría
                $grados = $this->obtenerGradosPorCategoria($categoria->idCategoria);

                $categoriasConGrados[] = [
                    'categoria' => $categoria,
                    'grados' => $grados,
                ];
            }

            $resultado[] = [
                'area' => $area,
                'categorias' => $categoriasConGrados,
            ];
        }

        // Pasar los resultados a la vista
        return view('inscripciones.mostarLosdatosInscripciones', ['resultado' => $resultado]);
    }

    private function verificarConvocatoriaActiva()
    {
        $fechaActual = Carbon::now()->toDateString();

        $convocatoria = Convocatoria::where('fechaInicio', '<=', $fechaActual)
            ->where('fechaFin', '>=', $fechaActual)
            ->where('estado', 'Publicada')
            ->first();

        return $convocatoria ? $convocatoria->idConvocatoria : null;
    }

    private function obtenerAreasPorConvocatoria($idConvocatoria)
    {
        return ConvocatoriaAreaCategoria::with('area')
            ->where('idConvocatoria', $idConvocatoria)
            ->get()
            ->pluck('area')
            ->unique('idArea')
            ->values();
    }

    private function obtenerCategoriasPorArea($idConvocatoria, $idArea)
    {
        return ConvocatoriaAreaCategoria::with('categoria')
            ->where('idConvocatoria', $idConvocatoria)
            ->where('idArea', $idArea)
            ->get()
            ->pluck('categoria')
            ->unique('idCategoria')
            ->values();
    }

    private function obtenerGradosPorCategoria($idCategoria)
    {
        return GradoCategoria::with('grado')
            ->where('idCategoria', $idCategoria)
            ->get()
            ->pluck('grado')
            ->unique('idGrado')
            ->values();
    }
}
