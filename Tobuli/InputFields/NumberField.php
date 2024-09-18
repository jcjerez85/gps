<?php

namespace Tobuli\InputFields;

class NumberField extends AbstractField
{
    /**
     * @var null|int|float
     */
    protected $min = null;

    /**
     * @var null|int|float
     */
    protected $max = null;
    protected $step = null;

    public function toArray(): array
    {
        return parent::toArray() + array_filter([
                'min' => $this->min,
                'max' => $this->max,
                'step' => $this->step,
            ], function ($value) {
                return $value !== null;
            });
    }

    public function getType(): string
    {
        return 'integer';
    }

    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param  null|int|float  $min
     * @return $this
     */
    public function setMin($min): self
    {
        $this->min = $min;

        $min === null
            ? $this->removeValidation('min:' . $min)
            : $this->addValidation('min:' . $min);

        return $this;
    }

    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param  null|int|float  $max
     * @return $this
     */
    public function setMax($max): self
    {
        $this->max = $max;

        $max === null
            ? $this->removeValidation('max:' . $max)
            : $this->addValidation('max:' . $max);

        return $this;
    }

    public function getStep()
    {
        return $this->step;
    }

    public function setStep($step): self
    {
        $this->step = $step;

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