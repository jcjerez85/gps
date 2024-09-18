<?php

namespace Tobuli\Helpers\Payments\Gateways\PayPal\Api;

use Tobuli\Helpers\Payments\Gateways\PayPal\Common\PayPalModel;

/**
 * Class Transactions
 *
 * 
 *
 * @package Tobuli\Helpers\Payments\Gateways\PayPal\Api
 *
 * @property \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Amount amount
 */
class Transactions extends PayPalModel
{
    /**
     * Amount being collected.
     * 
     *
     * @param \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Amount $amount
     * 
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Amount being collected.
     *
     * @return \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

}
