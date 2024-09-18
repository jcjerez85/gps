<?php

namespace Tobuli\Helpers\Payments\Gateways\PayPal\Api;

/**
 * Class InvoiceAddress
 *
 * Base Address object used as billing address in a payment or extended for Shipping Address.
 *
 * @package Tobuli\Helpers\Payments\Gateways\PayPal\Api
 *
 * @property \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Phone phone
 */
class InvoiceAddress extends BaseAddress
{
    /**
     * Phone number in E.123 format.
     *
     * @param \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Phone $phone
     * 
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Phone number in E.123 format.
     *
     * @return \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

}
