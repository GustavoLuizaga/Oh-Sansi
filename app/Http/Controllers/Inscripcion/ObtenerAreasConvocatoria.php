<?php

namespace App\Http\Controllers\Inscripcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConvocatoriaAreaCategoria;

class ObtenerAreasConvocatoria extends Controller
{
    /**
 * Obtiene las áreas asociadas a una convocatoria específica
 *
 * @param int $idConvocatoria ID de la convocatoria
 * @return \Illuminate\Http\JsonResponse
 */
public function obtenerAreasPorConvocatoria($idConvocatoria)
{
    try {
        $areas = ConvocatoriaAreaCategoria::with('area')
                    ->where('idConvocatoria', $idConvocatoria)
                    ->get()
                    ->pluck('area')    // Pluck the Area models (or nulls if relation fails for an entry)
                    ->filter()         // Remove any null items from the collection
                    ->unique('idArea')   // Ensure uniqueness among valid Area models
                    ->values();          // Reindex the array

        // Verificar si no hay áreas disponibles (this check is fine, or can be removed as an empty array is valid JSON)
        if ($areas->isEmpty()) {
            return response()->json([]);
        }

        return response()->json($areas);
    } catch (\Exception $e) {
        // Consider logging the error for server-side debugging: \Log::error("Error fetching areas for convocatoria {$idConvocatoria}: " . $e->getMessage());
        return response()->json(['error' => 'Error al obtener las áreas: ' . $e->getMessage()], 500);
    }
}
}
