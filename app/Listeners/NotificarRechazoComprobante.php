<?php

namespace App\Listeners;

use App\Events\InscripcionRechazarComprobante;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Notificacion;

class NotificarRechazoComprobante
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
     * @param  \App\Events\InscripcionRechazarComprobante  $event
     * @return void
     */
    public function handle(InscripcionRechazarComprobante $event)
    {
             Notificacion::create([
            'user_id' => $event->userId,
            'mensaje' => $event->mensaje . ' Comprobante : ' . $event->comprobante,
            'tipo' => $event->tipo
        ]);
    }
}
