<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Tobuli\Entities\Device;
use Tobuli\Entities\Event;
use Formatter;
use Tobuli\Entities\SendQueue;
use Tobuli\Entities\User;
use Tobuli\Services\EventWriteService;

class ConfirmUnpluggedAlert extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $event_data;
    private $duration;
    private $device;
    private $user;
    private $sensor;

    public function __construct($event_data, $duration)
    {
        $this->duration = $duration;
        $this->event_data = $event_data;
        $this->device = Device::find($this->event_data['device_id']);
        $this->user = User::find($this->event_data['user_id']);
        $this->sensor = $this->device->getSensorByType('plugged');

        Formatter::byUser($this->user);
    }

    public function handle()
    {
        if (!$this->sensor)
            return;

        if($this->isPluggedIn())
            return;

        $event = Event::create($this->event_data);

        SendQueue::create([
            'user_id'   => $event->user_id,
            'type'      => $event->type,
            'data'      => $event,
            'channels'  => $this->event_data['channels']
        ]);
    }

    protected function isPluggedIn()
    {
        $positions = $this->device->positions()
            ->whereBetween('time', [
                Carbon::parse($this->event_data['time']),
                Carbon::parse($this->event_data['time'])->addSeconds($this->duration)
            ])
            ->orderBy('time', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        foreach($positions as $position) {
            if ($this->sensor->getValue($position->other, false))
                return true;
        }

        return false;
    }
}