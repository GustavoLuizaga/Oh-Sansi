<?php

namespace App\Listeners;

use App\Events\InscripcionArea;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Notificacion;

class CrearNotificacionInscripcionArea
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\InscripcionArea  $event
     * @return void
     */
    public function handle(InscripcionArea $event)
    {
        Notificacion::create([
            'user_id' => $event->userId,
            'mensaje' => $event->mensaje,
            'tipo'    => $event->tipo,
        ]);
    }
}
