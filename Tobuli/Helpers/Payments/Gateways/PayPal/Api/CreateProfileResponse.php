<?php

namespace Tobuli\Helpers\Payments\Gateways\PayPal\Api;

/**
 * Class CreateProfileResponse
 *
 * Response schema for create profile api
 *
 * @package Tobuli\Helpers\Payments\Gateways\PayPal\Api
 *
 * @property string id
 */
class CreateProfileResponse extends WebProfile
{
    /**
     * ID of the payment web experience profile.
     * 
     *
     * @param string $id
     * 
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * ID of the payment web experience profile.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

}
