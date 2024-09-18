<?php

namespace Tobuli\Popups\Rules;

use Collective\Html\FormFacade;

class UserCreatedBefore extends BaseRule {
    public function getFields()
    {
        $value = $this->rule ? $this->rule->field_value : null;

        return [
            FormFacade::label('rules['.self::class.']', trans('admin.user_created_before_days')),
            FormFacade::text('rules['.self::class.'][days]', $value, ['class' => 'form-control']),
        ];
    }

    public  function doesApply()
    {
        if (!$this->user)
            return false;

        $days = $this->rule->field_value;

        if (time() - strtotime($this->user->created_at) > ($days * 86400) )
            return false;

        return true;
    }

}