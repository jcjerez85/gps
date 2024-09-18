<?php

namespace Tobuli\History\Actions;


class AppendEngineStatus extends ActionAppend
{
    protected $callback;

    protected $sensor;

    protected $current = null;
    protected $previuos = null;

    protected $engine_off_offset = null;
    protected $engine_off_at = null;

    static public function required()
    {
        return [
            AppendDistanceGPS::class,
            AppendDuration::class,
            AppendSpeed::class,
        ];
    }

    public function boot()
    {
        if ($this->history->hasConfig('ignition_off'))
            $this->engine_off_offset = $this->history->config('ignition_off') * 60;

        $this->sensor = $this->getDevice()->getEngineSensor();

        if ( ! $this->sensor)
            $this->callback = [$this, 'byGps'];
        else
            $this->callback = [$this, 'bySensor'];
    }

    public function proccess(&$position)
    {
        if (empty($this->engine_off_offset)) {
            $position->engine = call_user_func($this->callback, $position);

            return;
        }

        $this->setState($position);

        if (is_null($position->engine))
        {
            $this->addList($position);
            $this->proceed();

            return;
        }

        $previous = $this->getPrevPosition();

        if ($previous && is_null($previous->engine))
        {
            $this->history->processList(function($listPosition) use ($position) {

                if (!isset($listPosition->engine)) {
                    $listPosition->engine = $position->engine;
                }

                if (is_null($listPosition->engine)) {
                    $listPosition->engine = $position->engine;
                }

                return $listPosition;
            });

            $this->doitList();
        }
    }

    protected function bySensor($position)
    {
        return $this->getSensorValue($this->sensor, $position);
    }

    protected function byGps($position)
    {
        if ($position->duration < 300)
            return $position->speed > 0;

        return ($position->distance_gps / $position->duration * 3600) > 1;
    }

    protected function setState(& $position)
    {
        if (isset($position->engine))
            return;

        $engine = call_user_func($this->callback, $position);

        if ($engine)
        {
            $position->engine = $this->engine_off_at ? $position->timestamp - $this->engine_off_at < $this->engine_off_offset : $engine;
            $this->engine_off_at = null;
            return;
        }

        if ( ! isset($this->engine_off_at)) {
            $this->engine_off_at = $position->timestamp;
        }

        if ($position->timestamp - $this->engine_off_at > $this->engine_off_offset) {
            $position->engine = $engine;
            return;
        }

        $position->engine = null;
    }
}