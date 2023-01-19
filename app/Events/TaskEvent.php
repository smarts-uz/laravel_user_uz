<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var User */
    private $task;

    public function __construct(User $task)
    {
        $this->task = $task;
    }

    public function getTask(): User
    {
        return $this->task;
    }
}
