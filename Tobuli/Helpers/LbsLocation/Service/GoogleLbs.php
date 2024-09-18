<?php

namespace Tobuli\Helpers\LbsLocation\Service;

/**
 * @link https://developers.google.com/maps/documentation/geolocation/overview
 */
class GoogleLbs extends AbstractStandardLbs
{
    protected $serviceUrl = 'https://www.googleapis.com/geolocation/v1/geolocate';
    protected $errorPhraseKeyInvalid = 'API_KEY_INVALID';

    public function __construct(array $settings)
    {
        parent::__construct($settings);

        $this->client->options['CURLOPT_SSL_VERIFYPEER'] = false;
    }
}