<?php namespace Tobuli\Services;

use Tobuli\Services\StreetviewProviders\DefaultStreetview;
use Tobuli\Services\StreetviewProviders\GoogleStreetview;
use Tobuli\Services\StreetviewProviders\MapillaryStreetview;

class StreetviewService
{
    private static $providersList = [
        'default'   => DefaultStreetview::class,
        'google'    => GoogleStreetview::class,
        'mapillary' => MapillaryStreetview::class,
    ];

    private $provider;

    public function __construct()
    {
        $provider = settings('main_settings.streetview_api');
        $provider_class = self::$providersList[$provider] ?? null;

        if (is_null($provider_class))
            throw new \Exception("Streetview API provider not found! ($provider)");

        $this->provider = new $provider_class;
    }

    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->provider, $method], $parameters);
    }

}