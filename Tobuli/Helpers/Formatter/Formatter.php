<?php

namespace Tobuli\Helpers\Formatter;

use CustomFacades\Appearance;
use Tobuli\Entities\Timezone;
use Tobuli\Entities\User;
use Language;

class Formatter
{
    /**
     * @var DST
     */
    protected $DST;

    /**
     * @var Unit\Speed
     */
    protected $speed;

    /**
     * @var Unit\Distance
     */
    protected $distance;

    /**
     * @var Unit\Altitude
     */
    protected $altitude;

    /**
     * @var Unit\Capacity
     */
    protected $capacity;

    /**
     * @var Unit\Duration
     */
    protected $duration;

    /**
     * @var Unit\Course
     */
    protected $course;

    /**
     * @var Unit\FuelAvg
     */
    protected $fuelAvg;

    /**
     * @var Unit\Weight
     */
    protected $weight;

    /**
     * @var Unit\Currency
     */
    protected $currency;

    /**
     * @var Unit\DateTime
     */
    protected $time;
    protected $date;
    protected $datetime;

    public function __construct()
    {
        $this->DST = new DST();

        $this->speed    = new Unit\Speed();
        $this->distance = new Unit\Distance();
        $this->altitude = new Unit\Altitude();
        $this->capacity = new Unit\Capacity();
        $this->duration = new Unit\Duration();
        $this->course   = new Unit\Course();
        $this->fuelAvg  = new Unit\FuelAvg();
        $this->weight   = new Unit\Weight();
        $this->currency = new Unit\Currency();

        $this->time     = new Unit\DateTime($this->DST, 'H:i:s');
        $this->date     = new Unit\DateTime($this->DST, 'Y-m-d');
        $this->datetime = new Unit\DateTime($this->DST, 'Y-m-d H:i:s');

        $this->byDefault();
    }

    public function byDefault()
    {
        $defaults = settings('main_settings');

        $this->speed->setMeasure( $defaults['default_unit_of_distance'] ?? 'km' );
        $this->distance->setMeasure( $defaults['default_unit_of_distance'] ?? 'km' );
        $this->altitude->setMeasure( $defaults['default_unit_of_altitude'] ?? 'mt' );
        $this->capacity->setMeasure( $defaults['default_unit_of_capacity'] ?? 'lt' );
        $this->fuelAvg->setPer($defaults['default_fuel_avg_per'] ?? 'distance');

        $this->duration->setFormat($defaults['default_duration_format'] ?? 'standart');

        if ($timezone = Timezone::findOrInit($defaults['default_timezone'] ?? null)) {
            $this->DST->byTimezone($timezone);
        }

        $this->datetime->setFormat(
            Appearance::getSetting('default_date_format').' '.Appearance::getSetting('default_time_format')
        );
        $this->date->setFormat(Appearance::getSetting('default_date_format'));
        $this->time->setFormat(Appearance::getSetting('default_time_format'));
    }

    /**
     * @param User $user
     */
    public function byUser(User $user)
    {
        $lang = Language::getLangKey() ?: $user->lang;
        Language::set($lang);

        $this->speed->setMeasure($user->unit_of_distance);
        $this->distance->setMeasure($user->unit_of_distance);
        $this->altitude->setMeasure($user->unit_of_altitude);
        $this->capacity->setMeasure($user->unit_of_capacity);

        $this->duration->setFormat($user->duration_format ?? 'standart');

        $timezone = Timezone::findOrInit($user->timezone_id);
        $this->DST->byTimezone($timezone, $this->DST->getUserDSTRange($user));
    }

    /**
     * @return DST
     */
    public function DST()
    {
        return $this->DST;
    }

    /**
     * @return Unit\Speed
     */
    public function speed()
    {
        return $this->speed;
    }

    /**
     * @return Unit\Distance
     */
    public function distance()
    {
        return $this->distance;
    }

    /**
     * @return Unit\Altitude
     */
    public function altitude()
    {
        return $this->altitude;
    }

    /**
     * @return Unit\Capacity
     */
    public function capacity()
    {
        return $this->capacity;
    }

    /**
     * @return Unit\Duration
     */
    public function duration()
    {
        return $this->duration;
    }

    /**
     * @return Unit\DateTime
     */
    public function time()
    {
        return $this->datetime;
    }

    /**
     * @return Unit\DateTime
     */
    public function date()
    {
        return $this->date;
    }

    /**
     * @return Unit\DateTime
     */
    public function dtime()
    {
        return $this->time;
    }

    /**
     * @return Unit\Course
     */
    public function course()
    {
        return $this->course;
    }

    /**
     * @return Unit\FuelAvg
     */
    public function fuelAvg()
    {
        return $this->fuelAvg;
    }

    /**
     * @return Unit\Weight
     */
    public function weight()
    {
        return $this->weight;
    }

    /**
     * @return Unit\Currency
     */
    public function currency()
    {
        return $this->currency;
    }
}
