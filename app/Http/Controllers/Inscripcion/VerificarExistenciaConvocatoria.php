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
        // Obtener la fecha actual
        $fechaActual = Carbon::now()->toDateString(); // Formato: 'YYYY-MM-DD'
    
        // Buscar la convocatoria activa
        $convocatoria = Convocatoria::where('fechaInicio', '<=', $fechaActual)
                                     ->where('fechaFin', '>=', $fechaActual)
                                     ->first(); 
    
        if ($convocatoria) {
            return $convocatoria->idConvocatoria;
        } else {
        return response()->json([
            'mensaje' => 'No hay convocatoria activa en este momento',
        ]);
        }
    }    
}
