<?php

namespace Tobuli\History\Actions;


class AppendHarshTurning extends ActionAppend
{
    protected $sensors;

    static public function required()
    {
        return [];
    }

    public function boot()
    {
        $this->sensors = $this->getDevice()->sensors
            ->filter(function($sensor) {
                return in_array($sensor->type, ['harsh_turning']);
            });

    }

    public function proccess(&$position)
    {
        $position->harsh_turning = null;

        if (!$this->sensors)
            return;

        foreach ($this->sensors as $sensor) {
            $position->harsh_turning = $sensor->getValue($position->other, false, false);

            if ($position->harsh_turning)
                break;
        }

    }
}