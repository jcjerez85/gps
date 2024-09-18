<?php


namespace Tobuli\Forwards\Connections\Custom;


use CustomFacades\Field;
use Tobuli\Forwards\Connections\ForwardForm;


class Form extends ForwardForm
{
    public function getAttributes()
    {
        return [
            Field::string('ip', trans('validation.attributes.ip'), $this->get('ip'))
                ->setRequired()
                ->setValidation('ip'),
            Field::number('port', trans('validation.attributes.port'), $this->get('port'))
                ->setRequired()
                ->setValidation('numeric'),
        ];
    }
}