<?php

namespace Tobuli\Helpers\Payments;


use Illuminate\Support\Str;

class Payments
{
    private $gateway;

    public function __construct($gateway = null)
    {
        if ($gateway)
            $this->setGateway($gateway);
    }

    public function __call($method, $parameters)
    {
        if ( ! $this->gateway) {
            throw new \Exception('Payment gateway is not set!');
        }

        return call_user_func_array([$this->gateway, $method], $parameters);
    }

    public function setGateway($gateway)
    {
        $gateway_class = 'Tobuli\Helpers\Payments\Gateways\\' . ucfirst(Str::camel($gateway)) . 'Gateway';

        if ( ! class_exists($gateway_class, true)) {
            throw new \Exception('Payment gateway class "'.$gateway_class.'" not found!');
        }

        if (is_null($this->gateway) || get_class($this->gateway) != $gateway_class) {
            $this->gateway = new $gateway_class;
        }

        return $this;
    }
}