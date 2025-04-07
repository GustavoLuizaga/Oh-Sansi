<?php

namespace App\Http\Controllers\Inscripcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Convocatoria;
use Carbon\Carbon; 

class VerificarExistenciaConvocatoria extends Controller
{
    public function verificarConvocatoriaActiva()
    {
        $fechaActual = Carbon::now()->toDateString();

        $convocatoria = Convocatoria::where('fechaInicio', '<=', $fechaActual)
                                 ->where('fechaFin', '>=', $fechaActual)
                                 ->where('estado', 'Publicada')
                                 ->first();

        if ($convocatoria) {
            return $convocatoria->idConvocatoria;
        } else {
            return response()->json([
                'error' => true,
                'mensaje' => 'No hay convocatoria publicada en este momento'
            ]);
        }
    }    
}
