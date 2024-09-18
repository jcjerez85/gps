<?php

namespace App\Listeners;

use App\Events\TaskStatusChange;
use Carbon\Carbon;
use Tobuli\Entities\Event;
use Tobuli\Entities\SendQueue;
use Tobuli\Entities\TaskStatus;
use Tobuli\Helpers\Alerts\BaseAlertCheck;
use Tobuli\Helpers\Alerts\Checker;
use Tobuli\Services\EventWriteService;

class TaskStatusChangeListener
{
    protected $mapTaskEvent = [
        TaskStatus::STATUS_NEW => Event::TYPE_TASK_NEW,
        TaskStatus::STATUS_COMPLETED => Event::TYPE_TASK_COMPLETE,
        TaskStatus::STATUS_FAILED => Event::TYPE_TASK_FAILED,
        TaskStatus::STATUS_IN_PROGRESS => Event::TYPE_TASK_IN_PROGRESS,
    ];

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
     * @param  TaskStatusChange  $event
     * @return void
     */
    public function handle(TaskStatusChange $event)
    {
        $task = $event->task;
        $type = $this->mapTaskEvent[$task->status] ?? null;

        if (empty($type))
            return;

        if ( ! $task->device)
            return;

        $events = $this->alertEvents($task);

        $events[] = $this->autoEvents($task);

        (new EventWriteService())->write($events);
    }

    protected function alertEvents($task) {
        $alerts = $task->device
            ->alerts()
            ->with('user', 'zones')
            ->where('type', 'task_status')
            ->active()
            ->get()
            ->filter(function($alert) use ($task) {
                return in_array($task->status, $alert->statuses);
            });

        $checker = new Checker($task->device, $alerts);

        $position = $task->device->positionTraccar();
        if ($position)
            $position->time = date('Y-m-d H:i:s');

        return array_map(function($event) use ($task) {
            $event->type = $this->mapTaskEvent[$task->status];
            $event->setAdditional('task', $task->title);

            return $event;
        }, $checker->check($position));
    }

    protected function autoEvents($task) {
        $position = $task->device->positionTraccar();

        $event = new Event([
            'type'         => $this->mapTaskEvent[$task->status],
            'user_id'      => $task->user_id,
            'device_id'    => $task->device_id,
            'alert_id'     => null,
            'geofence_id'  => null,
            'poi_id'       => null,
            'message'      => '',
            'altitude'     => $position ? $position->altitude : null,
            'course'       => $position ? $position->course : null,
            'latitude'     => $position ? $position->latitude : null,
            'longitude'    => $position ? $position->longitude : null,
            'speed'        => $position ? $task->device->getSpeed($position) : null,
            'time'         => Carbon::now(),
            'additional'   => [
                'task' =>  $task->title,
            ],
        ]);

        $event->setCreatedAt( Carbon::now() );
        $event->setUpdatedAt( Carbon::now() );
        $event->silent = null;
        $event->channels = [
            'push' => true
        ];

        return $event;
    }
}
