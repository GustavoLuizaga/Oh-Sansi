<?php


namespace App\Listeners;

use App\Events\InscripcionAprobadaEstudiante;
use App\Models\Notificacion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificarInscripcionAprobada
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(InscripcionAprobadaEstudiante $event)
    {
        // Crear la notificación en la base de datos
        Notificacion::create([
            'user_id' => $event->userId,
            'mensaje' => $event->mensaje . ' Área: ' . $event->area,
            'tipo' => $event->tipo
        ]);
    }
}