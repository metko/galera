<?php

namespace Metko\Galera\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class MessageWasSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $message;
    public $to_user;

    /**
     * Create a new event instance.
     */
    public function __construct($to_user, $message)
    {
        $this->to_user = $to_user;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('conversation.'.$this->message->conversation_id);
    }
}
