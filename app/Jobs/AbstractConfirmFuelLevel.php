<?php

namespace App\Jobs;

use App\Console\ProcessManager;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Tobuli\Entities\Device;
use Tobuli\Entities\DeviceSensor;
use Tobuli\Entities\Event;
use Tobuli\Entities\SendQueue;

abstract class AbstractConfirmFuelLevel extends Job implements ShouldQueue
{
    use InteractsWithQueue;
    use SerializesModels;

    const SECONDS_GAP = 60;
    const FINALIZATION_GAP = 2 * 60;

    protected $eventData;
    protected $initTime;
    protected $mainFuel;
    protected $iteration;
    protected $device;
    protected $deviceSensor;
    protected $processManager;
    protected $processKey;

    public function __construct(
        $processKey,
        $time,
        array $eventData,
        array $mainFuel,
        int $iteration = 1
    ) {
        $this->processKey = $processKey;
        $this->device = Device::find($eventData['device_id']);
        $this->deviceSensor = DeviceSensor::find($eventData['additional']['sensor_id']);
        $this->initTime = $time;
        $this->eventData = $eventData;
        $this->mainFuel = $mainFuel;
        $this->iteration = $iteration;

        $this->processManager = self::generateProcessManager();
    }

    public static function generateProcessManager(): ProcessManager
    {
        $manager = new ProcessManager('ConfirmFuelLevelChange', AbstractConfirmFuelLevel::SECONDS_GAP * 2);
        $manager->disableUnlocking();

        return $manager;
    }

    protected function sendEvent(): bool
    {
        $event = Event::create($this->eventData);

        SendQueue::create([
            'user_id'   => $event->user_id,
            'type'      => $event->type,
            'data'      => $event,
            'channels'  => $this->eventData['channels']
        ]);

        return true;
    }

    protected function extractPositionsValues($positions): array
    {
        $values = [];

        foreach ($positions as $position) {
            $value = $this->deviceSensor->getValuePosition($position);

            if ($value === null) {
                continue;
            }

            $values[] = $value;
        }

        return $values;
    }

    protected function getSizeDiff($number1, $number2, $divZeroResult = 1): float
    {
        try {
            return abs(1 - ($number1 / $number2));
        } catch (\Throwable $e) {
            return $divZeroResult;
        }
    }
}