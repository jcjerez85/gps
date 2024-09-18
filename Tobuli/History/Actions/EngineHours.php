<?php

namespace Tobuli\History\Actions;


use Tobuli\Entities\TraccarPosition;
use Tobuli\History\Stats\Duration AS DurationStat;

class EngineHours extends ActionStat
{
    protected $callback;

    protected $sensor;

    static public function required()
    {
        return [
            AppendEngineStatus::class,
            AppendMoveState::class,
        ];
    }

    public function boot()
    {
        $device = $this->getDevice();

        $this->sensor = $device->getEngineHoursSensor();

        $formatTags = [
            TraccarPosition::ENGINE_HOURS_KEY,
            TraccarPosition::VIRTUAL_ENGINE_HOURS_KEY
        ];

        if ($this->sensor && !in_array($this->sensor->tag_name, $formatTags))
            $this->callback = [$this, 'byEngineHoursSensor'];
        else if ($this->sensor && $this->sensor->shown_value_by !== 'virtual')
            $this->callback = [$this, 'byEngineHoursFormatSensor'];
        else
            $this->callback = [$this, 'byEngine'];

        $this->registerStat('engine_hours', new DurationStat());
        $this->registerStat('engine_idle', new DurationStat());
        $this->registerStat('engine_work', new DurationStat());
    }

    public function proccess($position)
    {
        call_user_func($this->callback, $position);
    }

    protected function byEngineHoursSensor($position)
    {
        //first to set previous position sensor
        $value = $this->getSensorValue($this->sensor, $position);

        $previous = $this->history->getPrevPosition();

        if ( ! $previous)
            return;

        $previousValue = $this->getSensorValue($this->sensor, $previous);

        $this->setStats(floatval($value) - floatval($previousValue), $position);
    }

    protected function byEngineHoursFormatSensor($position)
    {
        //first to set previous position sensor
        $value = $this->getSensorValue($this->sensor, $position);

        $previous = $this->history->getPrevPosition();

        if ( ! $previous)
            return;

        $previousValue = $this->getSensorValue($this->sensor, $previous);

        $duration = (floatval($value) - floatval($previousValue)) * 3600;

        if ($duration > ($position->duration + 60))
            $duration = $position->duration;

        $duration = max(0, $duration);

        $this->setStats($duration, $position);
    }

    protected function byEngine($position)
    {
        if (is_null($position->engine))
            return;

        if ( ! $position->engine)
            return;

        $duration = $this->isStateChanged($position, 'engine') ? 0 : $position->duration;

        $this->setStats($duration, $position);
    }

    protected function setStats($value, $position)
    {
        $isMoving = $this->isStateCalcable($position, 'moving');

        $this->history->applyStat('engine_hours', $value);

        if ($isMoving)
            $this->history->applyStat('engine_work', $value);
        else
            $this->history->applyStat('engine_idle', $value);
    }
}