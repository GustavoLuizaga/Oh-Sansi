<?php

namespace App\Listeners;

use App\Events\CreacionCuenta;
use App\Models\Notificacion;

class CrearNotificacion
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\CreacionCuenta  $event
     * @return void
     */
    public function handle(CreacionCuenta $event)
    {
        Notificacion::create([
            'user_id' => $event->userId,
            'mensaje' => $event->mensaje,
            'tipo'    => $event->tipo,
        ]);
    }
}