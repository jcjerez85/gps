<?php

namespace Tobuli\InputFields;

class TextField extends AbstractField
{
    public function getType(): string
    {
        return 'text';
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