<?php


namespace Tobuli\Forwards\Connections\Pegasus;


use CustomFacades\Field;
use Tobuli\Forwards\Connections\ForwardForm;


class Form extends ForwardForm
{
    public function getAttributes()
    {
        return [
            Field::string('domain', trans('validation.attributes.domain'), $this->get('domain'))
                ->setRequired()
                ->setValidation('alpha_num')
                ->setDescription(' https://{your-domain}.peginstances.com/receivers/json'),
            Field::number('token', trans('validation.attributes.token'), $this->get('token'))
                ->setRequired()
                ->setValidation('string'),
        ];
    }

    protected function getDefaults()
    {
        return [
            'domain' => 'pegasus1'
        ];
    }
}