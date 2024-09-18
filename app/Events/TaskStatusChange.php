<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Tobuli\Entities\Task;

class TaskStatusChange extends Event
{
    use SerializesModels;

    public $task;

    public function __construct(Task $task) {
        $this->task = $task;
    }
}
