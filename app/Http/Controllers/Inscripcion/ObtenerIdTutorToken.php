<?php

namespace App\Http\Controllers\Inscripcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TutorAreaDelegacion; 

class ObtenerIdTutorToken extends Controller
{
    public function obtenerIdTutorDesdeToken($tokenTutor)
{
    $idTutor = TutorAreaDelegacion::where('tokenTutor', $tokenTutor)->value('id');

    if (!$idTutor) {
        return response()->json(['error' => 'No existe el tutor con este codigo'], 404);
    }

    return $idTutor;
}

}
