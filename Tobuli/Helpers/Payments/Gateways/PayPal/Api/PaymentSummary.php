<?php

namespace Tobuli\Helpers\Payments\Gateways\PayPal\Api;

use Tobuli\Helpers\Payments\Gateways\PayPal\Common\PayPalModel;

/**
 * Class PaymentSummary
 *
 * Payment/Refund break up
 *
 * @package Tobuli\Helpers\Payments\Gateways\PayPal\Api
 *
 * @property \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Currency paypal
 * @property \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Currency other
 */
class PaymentSummary extends PayPalModel
{
    /**
     * Total Amount paid/refunded via PayPal.
     *
     * @param \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Currency $paypal
     * 
     * @return $this
     */
    public function setPaypal($paypal)
    {
        $this->paypal = $paypal;
        return $this;
    }

    /**
     * Total Amount paid/refunded via PayPal.
     *
     * @return \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Currency
     */
    public function getPaypal()
    {
        return $this->paypal;
    }

    /**
     * Total Amount paid/refunded via other sources.
     *
     * @param \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Currency $other
     * 
     * @return $this
     */
    public function setOther($other)
    {
        $this->other = $other;
        return $this;
    }

    /**
     * Total Amount paid/refunded via other sources.
     *
     * @return \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Currency
     */
    public function getOther()
    {
        return $this->other;
    }

}
