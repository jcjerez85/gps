<?php

namespace Tobuli\Helpers\GeoLocation;

use Language;
use Illuminate\Support\Facades\Cache;

class GeoCache
{
    /**
     * @var Cache
     */
    protected $drive;

    /**
     * @var int
     */
    protected $expiration;

    public function __construct()
    {
        $this->expiration = (int)settings('main_settings.geocoder_cache_days') * 24 * 60 * 60;
        $this->language = Language::iso();

        $this->drive();
    }

    private function drive()
    {
        $this->drive = null;

        try {
            $this->drive = Cache::store(config('tobuli.geocoder_cache_driver'));
        } catch (\Exception $e) {
            $this->drive = null;
        }
    }

    private function key($method, $parameters)
    {
        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }

        $parameters[] = $method;
        $parameters[] = $this->language;

        return implode(',', $parameters);
    }

    public function get($method, $parameters, $callback)
    {
        return $this->drive->remember(
            $this->key($method, $parameters),
            $this->expiration,
            $callback
        );
    }

    public function flush()
    {
        $this->drive->flush();
    }
}