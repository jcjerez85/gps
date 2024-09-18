<?php

namespace Tobuli\Services\Captcha\Captchas;

class NullCaptcha implements CaptchaInterface
{
    /**
     * Returns html code with captcha form
     *
     * @return string
     */
    public function render()
    {
        return '';
    }

    /**
     * Checks validity of captcha code
     *
     * @return bool
     */
    public function isValid()
    {
        return true;
    }

    /**
     * Get array of validation messages
     *
     * @return array
     */
    public function getMessages()
    {
        return [];
    }

    /**
     * Get request validator to check CAPTCHA's validity
     *
     * @return \Illuminate\Support\Facades\Validator
     */
    public function getValidator()
    {
        return null;
    }
}
