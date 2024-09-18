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


class BillingPlan extends BaseRule {

    public $shortcodes = [
        '{billing_plan_name}' => 'getPlanName',
    ];

    public function getPlanName()
    {
        $plan = BillingPlanRepo::find($this->rule->field_value);

        return $plan ? $plan->title : trans('admin.no_plan');
    }

    public function getFields()
    {
        $fields =  BillingPlanRepo::all()->pluck('title','id');
        $fields->prepend(trans('admin.no_plan'));

        $value = $this->rule ? $this->rule->field_value : null;

        return [
            FormFacade::label('rules['.self::class.']', trans('front.plan')),
            FormFacade::select('rules['.self::class.'][billing_plan_id]',$fields , $value, ['class' => 'form-control']),
        ];
    }

    public function doesApply()
    {
        if (!$this->user) return false;


        if ($this->rule->field_value == 0) {
            $this->rule->field_value = null;
        }

        if ($this->user->billing_plan_id != $this->rule->field_value) {
            return false;
        }

        return true;
    }

}