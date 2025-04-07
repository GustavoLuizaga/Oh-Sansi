<?php

namespace App\Http\Controllers\Inscripcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConvocatoriaAreaCategoria;

class ObtenerAreasConvocatoria extends Controller
{
    public function obtenerAreasPorConvocatoria($idConvocatoria)
{
    $areas = ConvocatoriaAreaCategoria::with('area')
                ->where('idConvocatoria', $idConvocatoria)
                ->get()
                ->pluck('area')
                ->unique('idArea')   // Opcional: para evitar duplicados
                ->values();          // Reindexar el array

    return $areas;
}
}
