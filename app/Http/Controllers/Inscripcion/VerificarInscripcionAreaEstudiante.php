<?php

namespace App\Http\Controllers\Inscripcion;

use App\Http\Controllers\Controller;

use App\Models\TutorEstudianteInscripcion;

class VerificarInscripcionAreaEstudiante extends Controller
{
    public function VerificarInscripcionAreaEstudiante($idEstudiante)
    {
        $areas = TutorEstudianteInscripcion::where('idEstudiante', $idEstudiante)
        ->with('inscripcion.area') // Cargar la inscripción y el área
        ->get()
        ->pluck('inscripcion.area.nombre') // Solo sacar el nombre del área
        ->filter() // Por si hay registros nulos
        ->unique() // Por si no quieres nombres repetidos
        ->values(); // Reindexar el array

    return $areas;
    }
}
