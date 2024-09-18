<?php

namespace Tobuli\Helpers\LbsLocation\Service;

/**
 * @link https://ichnaea.readthedocs.io/en/latest/api/geolocate.html
 */
class MozillaLbs extends AbstractStandardLbs
{
    protected $serviceUrl = 'https://location.services.mozilla.com/v1/geolocate';

    public function __construct(array $settings)
    {
        parent::__construct($settings);

        $this->client->options['CURLOPT_SSL_VERIFYPEER'] = false;
    }
}