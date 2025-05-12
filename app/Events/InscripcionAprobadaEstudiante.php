<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InscripcionAprobadaEstudiante
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

 
    public $userId;
    public $mensaje;
    public $tipo;
    public $area;

    public function __construct($userId, $mensaje, $tipo = 'aprobacion',$area)
    {
        $this->userId = $userId;
        $this->mensaje = $mensaje;
        $this->tipo = $tipo;
        $this->area = $area;
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
