<?php

namespace Tobuli\History\Actions;


class AppendHarshBreaking extends ActionAppend
{
    protected $sensor;

    static public function required()
    {
        return [];
    }

    public function boot()
    {
        $this->sensor = $this->getSensor('harsh_breaking');
    }

    public function proccess(&$position)
    {
        $position->harsh_breaking = null;

        if ($this->sensor)
            $position->harsh_breaking = $this->sensor->getValue($position->other, false, false);
    }
}