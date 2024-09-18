<?php
/**
 * Created by PhpStorm.
 * User: antanas
 * Date: 18.2.14
 * Time: 16.31
 */

namespace Tobuli\Popups\Rules;

use CustomFacades\Repositories\BillingPlanRepo;
use Collective\Html\FormFacade;
use Tobuli\Entities\User;


class DemoUser extends BaseRule {
    public function getFields() {
        return [
            FormFacade::label('rules['.self::class.']', trans('front.demo')),
            FormFacade::hidden('rules['.self::class.'][active]', 1),
        ];
    }

    public  function doesApply() {
        if ( ! $this->user) return false;

        if ( ! $this->user->isDemo())
            return false;

        return true;
    }

}