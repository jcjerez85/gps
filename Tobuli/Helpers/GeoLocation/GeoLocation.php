<?php

namespace Tobuli\Helpers\GeoLocation;

use Illuminate\Support\Facades\Cache;
use Tobuli\Entities\Geofence;
use Tobuli\Entities\User;
use Tobuli\Helpers\GeoLocation\GeoServices\AbstractGeoService;

class GeoLocation
{
    /**
     * @var AbstractGeoService
     */
    protected $service = null;

    /**
     * @var GeoCache
     */
    protected $cache;

    protected $cacheMethods = ['byCoordinates'];

    protected $methodServiceMap = [
        'byCoordinates' => 'primary',
        'listByAddress' => 'address',
        'byAddress'     => 'address',
    ];

    /**
     * @var bool
     */
    protected $geofenceOverAddress = false;

    public function __construct()
    {
        if (settings('main_settings.geocoder_cache_enabled')) {
            $this->cache = new GeoCache();
        }

        if (settings('plugins.geofence_over_address.status')) {
            $this->geofenceOverAddress = true;
        }
    }

    public function __call($method, $parameters)
    {
        $parameters = call_user_func_array([$this, $method . 'Normalize'], $parameters);

        if (!($this->cache && in_array($method, $this->cacheMethods)))
            return call_user_func_array([$this->getService($method), $method], $parameters);

        return $this->cache->get($method, $parameters, function () use ($method, $parameters) {
            return call_user_func_array([$this->getService($method), $method], $parameters);
        });
    }

    public function resolveLocation(User $user, $lat, $lon)
    {
        if ($this->geofenceOverAddress)
            return $this->locateGeofence($user, $lat, $lon);

        return $this->resolveAddress($lat, $lon);
    }
    
    public function locateGeofence(User $user, $lat, $lon)
    {
        $geofence = Geofence::userOwned($user)
            ->containPoint($lat, $lon)
            ->first(['name']);

        if ($geofence) {
            return $geofence->name;
        }

        return $this->resolveAddress($lat, $lon);
    }

    public function resolveAddress($lat, $lon)
    {
        try {
            $location = $this->byCoordinates($lat, $lon);

            if ($location)
                return $location->address;

            return trans('front.nothing_found_request');
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    protected function setService(string $geocoder)
    {
        if (empty($geocoder))
            throw new \Exception('Geocoder required');

        $settings = settings('main_settings.geocoders.' . $geocoder);

        $this->service = $this->loadGeoService($settings);
    }

    protected function getService($method)
    {
        if ($this->service)
            return $this->service;

        try {
            $this->setService($this->methodServiceMap[$method] ?? '');
        } catch (\Exception $e) {
            $this->setService('primary');
        }

        return $this->service;
    }

    protected function loadGeoService($settings)
    {
        $class = 'Tobuli\Helpers\GeoLocation\GeoServices\Geo' . ucfirst($settings['api'] ?? '');

        if (!class_exists($class, true)) {
            throw new \InvalidArgumentException('GeoService class not found!');
        }

        return new $class((new GeoSettings())
            ->setApiKey($settings['api_key'] ?? '')
            ->setApiUrl($settings['api_url'] ?? '')
            ->setAppId($settings['api_app_id'] ?? '')
            ->setAppSecret($settings['api_app_secret'] ?? '')
        );
    }

    protected function byAddressNormalize($address)
    {
        return [$address];
    }

    protected function listByAddressNormalize($address)
    {
        return [$address];
    }

    protected function byCoordinatesNormalize($lat, $lng)
    {
        if (!is_numeric($lat) || !is_numeric($lng)) {
            throw new \Exception('Bad coordinates input!');
        }

        $parameters = [];
        $parameters[0] = round($lat, 6);
        $parameters[1] = round($lng, 6);

        return $parameters;
    }

    public function flushCache()
    {
        return $this->cache ? $this->cache->flush() : null;
    }
}