<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InscripcionAprobada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $mensaje;
    public $userId;

    // El constructor recibe el mensaje y el ID del usuario
    public function __construct($mensaje, $userId)
    {
        $this->mensaje = $mensaje;
        $this->userId = $userId;
    }

    // Definimos el canal privado en el que se enviará el evento
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->userId);
    }

    // El evento será enviado con los siguientes datos
    public function broadcastWith()
    {
        return ['mensaje' => $this->mensaje];
    }
}
