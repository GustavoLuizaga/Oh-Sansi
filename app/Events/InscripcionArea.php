<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InscripcionArea
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $mensaje;
    public $tipo;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userId, $mensaje, $tipo = 'mensaje')//sistema
    {
        $this->userId = $userId;
        $this->mensaje = $mensaje;
        $this->tipo = $tipo;
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
