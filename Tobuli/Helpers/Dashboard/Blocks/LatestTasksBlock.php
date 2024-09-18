<?php namespace Tobuli\Helpers\Dashboard\Blocks;

use Tobuli\Entities\TaskStatus;
use Tobuli\Helpers\Dashboard\Traits\HasPeriodOption;

class LatestTasksBlock extends Block
{
    use HasPeriodOption;

    protected function getName()
    {
        return 'latest_tasks';
    }

    protected function getContent()
    {
        $tasks = $this->user->tasks()->with('lastStatus')
            ->where('created_at', '>=', $this->getPeriod())
            ->get();

        $status_counts = array_combine(
            array_keys(TaskStatus::$statuses),
            array_fill(0, count(TaskStatus::$statuses), 0)
        );

        foreach ($tasks as $task) {
            $status_counts[$task->status]++;
        }

        return [
            'task_count'      => $tasks->count(),
            'latests'         => $tasks->take(5),
            'statuses'        => TaskStatus::$statuses,
            'status_counts'   => $status_counts
        ];
    }
}