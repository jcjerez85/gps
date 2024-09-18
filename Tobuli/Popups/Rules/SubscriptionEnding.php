<?php
/**
 * Created by PhpStorm.
 * User: antanas
 * Date: 18.2.14
 * Time: 16.31
 */

namespace Tobuli\Popups\Rules;
use Collective\Html\FormFacade;
use Tobuli\Entities\User;


class SubscriptionEnding extends BaseRule
{
    public $shortcodes = [
        '{subscription_days_left}' => 'getSubscriptionDaysLeft',
    ];

    public function getSubscriptionDaysLeft()
    {
        if (!$this->user)
            return null;

        if (is_null($this->user->subscription_expiration))
            return null;

        $time = strtotime($this->user->subscription_expiration) - time();

        if ($time < 0)
            return 0;

        return round($time / 86400);
    }

    public function getFields()
    {
        $value = $this->rule ? $this->rule->field_value : null;

        return [
            FormFacade::label('rules['.self::class.']', trans('admin.subscription_ends_in')),
            FormFacade::text('rules['.self::class.'][days]', $value, ['class' => 'form-control']),
        ];
    }

    public  function doesApply()
    {
        if (!$this->user)
            return false;

        $daysLeft = $this->rule->field_value;

        if (is_null($this->user->subscription_expiration))
            return false;

        if ($this->user->subscription_expiration == '0000-00-00 00:00:00')
            return false;

        if (strtotime($this->user->subscription_expiration) - time() > ($daysLeft*86400) )
            return false;

        return true;
    }

}