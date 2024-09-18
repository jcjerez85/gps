<?php

namespace Tobuli\Helpers\Payments\Gateways\Twocheckout;

class TwocheckoutConfig
{
    private static $settings = null;

    public static function getApiUrl()
    {
        return self::getSettingsParameter('api_url');
    }

    public static function getFrontUrl()
    {
        return self::getSettingsParameter('front_url');
    }

    public static function getSecretKey()
    {
        return self::getSettingsParameter('secret_key');
    }

    public static function getMerchantCode()
    {
        return self::getSettingsParameter('merchant_code');
    }

    public static function isDemoMode()
    {
        return self::getSettingsParameter('demo_mode');
    }

    private static function getSettingsParameter(string $parameter)
    {
        if (self::$settings === null) {
            self::$settings = settings('payments.twocheckout');
        }

        return self::$settings[$parameter];
    }
}
