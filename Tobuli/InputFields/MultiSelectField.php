<?php

namespace Tobuli\InputFields;

use Illuminate\Support\Arr;

class MultiSelectField extends SelectField
{
    public function getType(): string
    {
        return 'multiselect';
    }

    public function getHtmlName(): string
    {
        return parent::getHtmlName() . '[]';
    }

    public function render(array $options = [])
    {
        return \Form::select(
            $this->getHtmlName(),
            Arr::pluck($this->getOptions(), 'title', 'id'),
            $this->getDefault(),
            array_merge([
                'class' => 'form-control multiexpand',
                'multiple' => 'multiple',
                'data-live-search' => 'true',
                'data-actions-box' => 'true'
            ], $options)
        );
    }
}