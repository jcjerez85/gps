<?php

namespace Tobuli\Popups\Rules;

use Collective\Html\FormFacade;

class FirstLogin extends BaseRule {
    public function getFields() {
        return [
            FormFacade::label('rules['.self::class.']', trans('front.first_login')),
            FormFacade::hidden('rules['.self::class.'][active]', 1),
        ];
    }

    public  function doesApply() {
        if ( ! $this->user) return false;

        return ! $this->user->isLoggedBefore();
    }

}