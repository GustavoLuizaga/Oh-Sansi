<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InscripcionRechazarComprobante
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $mensaje;
    public $tipo;
    public $comprobante;

    public function __construct($userId, $mensaje, $tipo = 'denegacion',$comprobante)
    {
        $this->userId = $userId;
        $this->mensaje = $mensaje;
        $this->tipo = $tipo;
        $this->comprobante = $comprobante;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
