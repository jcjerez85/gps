<?php

namespace Tobuli\Helpers\Payments\Gateways\PayPal\Api;

use Tobuli\Helpers\Payments\Gateways\PayPal\Common\PayPalModel;

/**
 * Class AgreementStateDescriptor
 *
 * Description of the current state of the agreement.
 *
 * @package Tobuli\Helpers\Payments\Gateways\PayPal\Api
 *
 * @property string note
 * @property \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Currency amount
 */
class AgreementStateDescriptor extends PayPalModel
{
    /**
     * Reason for changing the state of the agreement.
     *
     * @param string $note
     * 
     * @return $this
     */
    public function setNote($note)
    {
        $this->note = $note;
        return $this;
    }

    /**
     * Reason for changing the state of the agreement.
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * The amount and currency of the agreement.
     *
     * @param \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Currency $amount
     * 
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * The amount and currency of the agreement.
     *
     * @return \Tobuli\Helpers\Payments\Gateways\PayPal\Api\Currency
     */
    public function getAmount()
    {
        return $this->amount;
    }

}
