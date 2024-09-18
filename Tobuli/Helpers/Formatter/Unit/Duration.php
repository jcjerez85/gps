<?php

namespace Tobuli\Helpers\Formatter\Unit;

use CustomFacades\Language;

class Duration extends Unit
{
    /**
     * @var string
     */
    protected $format;

    public function setFormat(string $format)
    {
        $this->format = $format;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function byMeasure($unit) {}

    public function human($seconds, $format = null)
    {
        if (is_null($format))
            $format = $this->format;

        $seconds = max(0, intval($seconds));

        if ($format != 'number') {
            // extract days
            $days = floor($seconds / (24 * 60 * 60));
    
            // extract hours
            $seconds %= (24 * 60 * 60);
            $hours = floor($seconds / (60 * 60));
    
            // extract minutes
            $divisor_for_minutes = $seconds % (60 * 60);
            $minutes = floor($divisor_for_minutes / 60);
    
            // extract the remaining seconds
            $divisor_for_seconds = $divisor_for_minutes % 60;
            $seconds = ceil($divisor_for_seconds);
        }

        switch ($format) {
            case 'number':
                return round($seconds / 3600, 2) . ' ' . trans('front.hour_short');
            case 'dd:hh:mm:ss':
                return $this->timeWithDaysFormat($seconds, $minutes, $hours, $days);
            case 'hh:mm:ss':
                return $this->timeFormat($seconds, $minutes, $hours);
            default:
                return $this->standartFormat($seconds, $minutes, $hours, $days);

        }
    }

    // private function standartFormat($seconds, $minutes, $hours)
    // {
    //     $result = [];

    //     if ($hours)
    //         $result[] = $hours . trans('front.hour_short');

    //     if ($minutes)
    //         $result[] = $minutes . trans('front.minute_short');

    //     $result[] = $seconds . trans('front.second_short');

    //     return implode(" ", $result);
    // }

    private function standartFormat($seconds, $minutes, $hours, $days)
    {
        $result = [];

        if ($days)
            $result[] = $days . trans('front.day_short');

        if ($hours)
            $result[] = $hours . trans('front.hour_short');

        if ($minutes)
            $result[] = $minutes . trans('front.minute_short');

        $result[] = $seconds . trans('front.second_short');

        return implode(" ", $result);
    }

    private function timeFormat($seconds, $minutes, $hours)
    {
        $seconds = str_pad($seconds, 2, "0", STR_PAD_LEFT);
        $minutes = str_pad($minutes, 2, "0", STR_PAD_LEFT);
        $hours = str_pad($hours, 2, "0", STR_PAD_LEFT);

        return "$hours:$minutes:$seconds";
    }

    private function timeWithDaysFormat($seconds, $minutes, $hours, $days)
    {
        $seconds = str_pad($seconds, 2, "0", STR_PAD_LEFT);
        $minutes = str_pad($minutes, 2, "0", STR_PAD_LEFT);
        $hours = str_pad($hours, 2, "0", STR_PAD_LEFT);
        $days = str_pad($days, 2, "0", STR_PAD_LEFT);

        return "$days:$hours:$minutes:$seconds";
    }
}