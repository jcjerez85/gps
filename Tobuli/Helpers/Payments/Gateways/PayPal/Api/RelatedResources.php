<?php

namespace Tobuli\Helpers\Payments\Gateways\PayPal\Api;

use Tobuli\Helpers\Payments\Gateways\PayPal\Common\PayPalModel;

/**
 * Class RelatedResources
 *
 * Each one representing a financial transaction (Sale, Authorization, Capture, Refund) related to the payment.
 *
 * @package Tobuli\Helpers\Payments\Gateways\PayPal\Api
 *
 * @property \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Sale sale
 * @property \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Authorization authorization
 * @property \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Order order
 * @property \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Capture capture
 * @property \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Refund refund
 */
class RelatedResources extends PayPalModel
{
    /**
     * Sale transaction
     *
     * @param \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Sale $sale
     * 
     * @return $this
     */
    public function setSale($sale)
    {
        $this->sale = $sale;
        return $this;
    }

    /**
     * Sale transaction
     *
     * @return \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Sale
     */
    public function getSale()
    {
        return $this->sale;
    }

    /**
     * Authorization transaction
     *
     * @param \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Authorization $authorization
     * 
     * @return $this
     */
    public function setAuthorization($authorization)
    {
        $this->authorization = $authorization;
        return $this;
    }

    /**
     * Authorization transaction
     *
     * @return \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Authorization
     */
    public function getAuthorization()
    {
        return $this->authorization;
    }

    /**
     * Order transaction
     *
     * @param \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Order $order
     * 
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Order transaction
     *
     * @return \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Capture transaction
     *
     * @param \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Capture $capture
     * 
     * @return $this
     */
    public function setCapture($capture)
    {
        $this->capture = $capture;
        return $this;
    }

    /**
     * Capture transaction
     *
     * @return \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Capture
     */
    public function getCapture()
    {
        return $this->capture;
    }

    /**
     * Refund transaction
     *
     * @param \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Refund $refund
     * 
     * @return $this
     */
    public function setRefund($refund)
    {
        $this->refund = $refund;
        return $this;
    }

    /**
     * Refund transaction
     *
     * @return \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Refund
     */
    public function getRefund()
    {
        return $this->refund;
    }

}
