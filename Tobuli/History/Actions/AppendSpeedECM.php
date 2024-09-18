<?php

namespace Tobuli\History\Actions;


class AppendSpeedECM extends ActionAppend
{
    const RADIO = 100000;

    protected $sensor;

    static public function required()
    {
        return [
            AppendSpeed::class,
        ];
    }

    public function boot()
    {
        $this->sensor = $this->getSensor('speed_ecm');
    }

    public function proccess(&$position)
    {
        $position->speed = 0;

        if ( ! $this->sensor)
            return;

        $position->speed = $this->sensor->getValue($position->other, false, 0);
    }
}