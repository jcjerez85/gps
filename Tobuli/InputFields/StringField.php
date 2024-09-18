<?php

namespace Tobuli\InputFields;

class StringField extends AbstractField
{
    public function getType(): string
    {
        return 'string';
    }

    public function render(array $options = [])
    {
        return \Form::text(
            $this->getHtmlName(),
            $this->getDefault(),
            array_merge(['class' => 'form-control'], $options)
        );
    }
}