<?php

namespace Tobuli\History\Actions;

use Formatter;

class AppendQuarterHour extends ActionAppend
{
    protected $current;

    static public function required()
    {
        return [];
    }

    public function boot(){}

    public function proccess(&$position)
    {
        $position->quarter = $this->getQuarter($position);
    }

    protected function getQuarter($position)
    {
        $converted = Formatter::time()->timestamp($position->time);
        $rounded = floor($converted / (15 * 60)) * (15 * 60);

        return date('Y-m-d H:i:s', $rounded);
    }
}