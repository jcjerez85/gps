<?php

namespace Tobuli\InputFields;

class DatetimeField extends AbstractField
{
    private $format = '';

    public function getType(): string
    {
        return 'datetime';
    }

    public function toArray(): array
    {
        return parent::toArray() + ['format' => $this->format];
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
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