<?php namespace Tobuli\Entities;

use Illuminate\Support\Facades\Cache;

class Timezone extends AbstractEntity {

    protected $table = 'timezones';

    protected $fillable = ['title', 'zone', 'order', 'prefix', 'time'];

    protected $zoneReversed;
    protected $zoneDST;
    protected $zoneReversedDST;

    public $timestamps = false;

    public function getZoneAttribute($value)
    {
        return $value ?: '+0hours';
    }

    public function getReversedZoneAttribute()
    {
        if (! isset($this->zoneReversed)) {
            $this->zoneReversed = timezoneReverse($this->zone);
        }

        return $this->zoneReversed;
    }

    public function getDSTZoneAttribute()
    {
        if (! isset($this->zoneDST)) {
            $this->zoneDST = $this->zone.' +1hours';
        }

        return $this->zoneDST;
    }

    public function getReversedDSTZoneAttribute()
    {
        if (! isset($this->zoneReversedDST)) {
            $this->zoneReversedDST = $this->reversedZone.' -1hours';
        }

        return $this->zoneReversedDST;
    }

    public function getHiFormatAttribute(): string
    {
        $time = explode(' ', $this->time);

        if (strlen($time[1]) === 1) {
            $time[1] = '0' . $time[1];
        }

        return ($this->prefix === 'minus' ? '-' : '+') . $time[0] . ':' . $time[1];
    }

    static public function findOrInit($timezone_id)
    {
        if (empty($timezone_id))
            return new Timezone();

        return Cache::store('array')
            ->rememberForever("timezone.$timezone_id", function () use ($timezone_id) {
                $timezone = Timezone::find($timezone_id);

                return $timezone ?: new Timezone();
            });
    }
}
