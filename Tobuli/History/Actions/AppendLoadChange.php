<?php

namespace Tobuli\History\Actions;

class AppendLoadChange extends ActionAppend
{
    private $sensor;
    private $startedAt = null;
    private $startLoad = null;

    static public function required()
    {
        return [
            AppendSpeed::class,
        ];
    }

    public function boot()
    {
        $this->sensor = $this->getDevice()->getLoadSensor();
    }

    public function proccess(&$position)
    {
        if (!$this->sensor) {
            return;
        }

        $prevPosition = $this->getPrevPosition();

        if (!$prevPosition) {
            return;
        }

        $value = $this->sensor->getValue($position->other, false);

        $prevPosition = $this->getPrevPosition();
        $prevValue = $this->sensor->getValue($prevPosition->other, false);

        if (empty($value) || empty($prevValue)) {
            return;
        }

        if ($this->startedAt === null) {
            $this->startedAt = $position->time;
            $this->startLoad = $value;

            return;
        }

        // wait for x seconds
        if ((strtotime($position->time) - strtotime($this->startedAt)) < 400) {
            return;
        }

        $loading = $value > $this->startLoad ;
        $difference = abs($value - $this->startLoad);
        $changePercentage = ($difference / ($loading ? $value : $this->startLoad)) * 100;

        // drop the noise if any after x second
        if ($changePercentage < 10) {
            $this->startedAt = null;
            $this->startLoad  = null;

            return;
        }

        // continue loading proccess if device is not moving
        if ($position->speed <= $prevPosition->speed) {
            return;
        }

        // drop the noise if no significant load
        if ($changePercentage < 25) {
            $this->startedAt = null;
            $this->startLoad  = null;

            return;
        }

        $position->loadChange = [
            'state'             => (int)$loading,
            'time'              => $this->startedAt,
            'previous_load'     => $this->startLoad,
            'current_load'      => $value,
            'difference'        => $difference,
        ];
        $this->startedAt = null;
        $this->startLoad  = null;
    }
}