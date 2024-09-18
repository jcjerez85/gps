<?php

namespace Tobuli\Helpers\GeoLocation\GeoServices;

use Tobuli\Helpers\GeoLocation\GeoSettings;

abstract class AbstractGeoService implements GeoServiceInterface
{
    protected $settings;

    public function __construct(GeoSettings $settings)
    {
        $this->settings = $settings;
    }
}