<?php

namespace Tobuli\Services\Captcha\Captchas;

interface CaptchaInterface
{
    /**
     * Returns html code with captcha form
     *
     * @return string
     */
    public function render();

    /**
     * Checks validity of captcha code
     *
     * @return bool
     */
    public function isValid();

    /**
     * Get array of validation messages
     *
     * @return array
     */
    public function getMessages();

    /**
     * Get request validator to check CAPTCHA's validity
     *
     * @return \Illuminate\Support\Facades\Validator
     */
    public function getValidator();
}
