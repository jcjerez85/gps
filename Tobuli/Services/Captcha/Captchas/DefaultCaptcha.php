<?php

namespace Tobuli\Services\Captcha\Captchas;

use Illuminate\Support\Facades\Validator;
use Mews\Captcha\Facades\Captcha;

class DefaultCaptcha implements CaptchaInterface
{
    /**
     * Returns html code with captcha form
     *
     * @return string
     */
    public function render()
    {
        return view('front::Captcha.Captchas.default', [
            'captcha_img' => Captcha::src('flat'),
        ]);
    }

    /**
     * Checks validity of captcha code
     *
     * @return bool
     */
    public function isValid()
    {
        return ! $this->getValidator()->fails();
    }

    /**
     * Get array of validation messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->getValidator()
            ->messages()
            ->all();
    }

    /**
     * Get request validator to check CAPTCHA's validity
     *
     * @return \Illuminate\Support\Facades\Validator
     */
    public function getValidator()
    {
        return Validator::make(request()->all(), [
                'captcha' => 'required|captcha',
            ],
            [
                'captcha.captcha' => trans('validation.wrong_captcha'),
            ]);
    }
}
