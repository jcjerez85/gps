<?php

namespace Tobuli\History\Actions;


class AppendHarshAcceleration extends ActionAppend
{
    protected $sensor;

    static public function required()
    {
        return [];
    }

    public function boot()
    {
        $this->sensor = $this->getSensor('harsh_acceleration');
    }

    public function proccess(&$position)
    {
        $position->harsh_acceleration = null;

        if ($this->sensor)
            $position->harsh_acceleration = $this->sensor->getValue($position->other, false, false);
    }
}