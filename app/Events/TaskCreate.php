<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Tobuli\Entities\Task;

class TaskCreate extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $task;

    public function __construct(Task $task) {
        $this->task = $task;
    }

    public function broadcastOn() {
        return [md5('task_for_'. 1)];
    }

    public function broadcastAs()
    {
        return 'task';
    }
}
