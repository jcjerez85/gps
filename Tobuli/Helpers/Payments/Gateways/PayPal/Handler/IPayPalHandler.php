<?php

namespace Tobuli\Helpers\Payments\Gateways\PayPal\Handler;

/**
 * Interface IPayPalHandler
 *
 * @package Tobuli\Helpers\Payments\Gateways\PayPal\Handler
 */
interface IPayPalHandler
{
    /**
     *
     * @param \Tobuli\Helpers\Payments\Gateways\PayPal\Core\PayPalHttpConfig $httpConfig
     * @param string $request
     * @param mixed $options
     * @return mixed
     */
    public function handle($httpConfig, $request, $options);
}
