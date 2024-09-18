<?php

namespace Tobuli\History\Actions;


class AppendOverspeeding extends ActionAppend
{
    protected $tolerance;

    static public function required()
    {
        return [
            AppendSpeed::class,
        ];
    }

    static public function after()
    {
        return [
            AppendSpeedLimitStatic::class,
            AppendSpeedLimitRoads::class,
            AppendSpeedLimitGeofence::class,
        ];
    }

    public function boot()
    {
        if ($this->history->hasConfig('speed_limit_tolerance')) {
            $this->tolerance = intval($this->history->config('speed_limit_tolerance'));
        } else {
            $this->tolerance = 0;
        }
    }

    public function proccess(&$position)
    {
        $position->overspeeding = 0;

        if ( ! $this->isOverspeed($position))
            return;

        $position->overspeeding++;

        $previous = $this->getPrevPosition();

        if ($previous && !empty($previous->overspeeding) && $previous->speed_limit == $position->speed_limit)
            $position->overspeeding += $previous->overspeeding;
    }

    protected function isOverspeed($position)
    {
        if (is_null($position->speed_limit))
            return false;

        return ($position->speed_limit + $this->tolerance) < $position->speed;
    }
}