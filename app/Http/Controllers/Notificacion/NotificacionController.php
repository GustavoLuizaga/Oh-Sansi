<?php

namespace App\Http\Controllers\Notificacion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Events\InscripcionAprobada;

class NotificacionController extends Controller
{
    public function aprobarInscripcion($userId)
    {
        $user = User::findOrFail($userId);

        // Aquí puedes poner la lógica para aprobar la inscripción
        // Por ejemplo: $user->inscripcion_aprobada = true; $user->save();

        // Emitir el evento de notificación
        event(new InscripcionAprobada('Tu inscripción ha sido aprobada 🎉', $user->id));

        return response()->json(['mensaje' => 'Notificación enviada']);
    }
}
