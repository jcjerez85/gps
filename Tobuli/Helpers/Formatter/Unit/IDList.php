<?php


namespace Tobuli\Helpers\Formatter\Unit;


class IDList extends Unit
{
    public function human($value)
    {
        return implode(", ", $value);
    }
}