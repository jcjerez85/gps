<?php

namespace Tobuli\Helpers\LbsLocation\Service;

/**
 * @link https://combain.com/api
 */
class CombainLbs extends AbstractStandardLbs
{
    protected $serviceUrl = 'https://apiv2.combain.com';

    public function __construct(array $settings)
    {
        parent::__construct($settings);

        $this->client->options['CURLOPT_RETURNTRANSFER'] = true;
        $this->client->headers = ['Content-Type' => 'application/json'];
    }
}