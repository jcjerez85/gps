<?php

namespace Tobuli\Reports;

trait ColorsTrait
{
    private $predefinedColors = [
        'black',
        'red',
        'green',
        'purple',
        'olive',
        'blue',
        'fuchsia',
        'maroon',
        'lime',
        'yellow',
        'navy',
        'teal',
        'aqua',
        'gray',
    ];

    private function generateColor(): string
    {
        static $index = null;

        $index = is_null($index) ? 0 : $index + 1;

        return $this->predefinedColors[$index] ?? sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }
}