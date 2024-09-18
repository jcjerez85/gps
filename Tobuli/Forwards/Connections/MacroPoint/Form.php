<?php

namespace Tobuli\Forwards\Connections\MacroPoint;

use CustomFacades\Field;
use Tobuli\Forwards\Connections\ForwardForm;

class Form extends ForwardForm
{
    public function getAttributes(): array
    {
        return [
            Field::string('username', trans('validation.attributes.username'), $this->get('username'))
                ->setRequired(),
            Field::string('password', trans('validation.attributes.password'), $this->get('password'))
                ->setRequired(),
            Field::string('mpid', 'MPID', $this->get('mpid'))
                ->setRequired(),
        ];
    }
}