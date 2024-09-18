<?php

namespace Tobuli\InputFields;

use RuntimeException;

class MultiGroupSelectField extends MultiSelectField
{
    protected $function = null;
    protected $functionArgs = [];

    public function getType(): string
    {
        return 'multiselect-group';
    }

    public function getOptions(): array
    {
        if (!is_callable($this->function)) {
            throw new RuntimeException('Property `function` is not callable');
        }

        $options = $this->options;

        if ($this->optionsQuery) {
            $options += $this->optionsQuery->get()->all();
        }

        return ($this->function)($options, ...$this->functionArgs);
    }

    /**
     * @inheritDoc
     */
    public function setOptionsViaQuery($query, string $displayField = '', string $valueField = '')
    {
        $this->optionsQuery = $query;

        return $this;
    }

    public function setOptionsClosure($function, array $args): self
    {
        $this->function = $function;
        $this->functionArgs = $args;

        return $this;
    }

    public function render(array $options = [])
    {
        return \Form::select(
            $this->getHtmlName(),
            $this->getOptions(),
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