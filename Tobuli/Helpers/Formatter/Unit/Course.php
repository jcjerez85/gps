<?php namespace Tobuli\Helpers\Formatter\Unit;


class Course
{
    private static $directions = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N'];

    public function human($value)
    {
        return self::$directions[($value / 45) % 8];
    }
}