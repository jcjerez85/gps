<?php

namespace Tobuli\InputFields;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;

class SelectField extends AbstractField
{
    /**
     * @var array|Arrayable
     */
    protected $options = [];

    /**
     * @var Builder
     */
    protected $optionsQuery = null;

    /**
     * @var string
     */
    protected $valueField;

    /**
     * @var string
     */
    protected $displayField;

    public function toArray(): array
    {
        return parent::toArray() + ['options' => $this->getOptions()];
    }

    public function toHtml(array $options)
    {
        if ($this->template) {
            return view($this->template)->make($this);
        }

        return \Form::select();
    }

    public function getType(): string
    {
        return 'select';
    }

    /**
     * @param array|Arrayable $options
     * @return $this
     */
    public function setOptions($options): self
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions(): array
    {
        $options = $this->options instanceof Arrayable
            ? $this->options->toArray()
            : $this->options;

        if ($this->optionsQuery) {
            $options += $this->optionsQuery->pluck($this->displayField, $this->valueField)->all();
        }

        return toOptions($options);
    }

    /**
     * @param Builder|\Illuminate\Database\Eloquent\Builder $query
     * @required static
     */
    public function setOptionsViaQuery($query, string $displayField, string $valueField = 'id')
    {
        $this->optionsQuery = $query;
        $this->valueField = $valueField;
        $this->displayField = $displayField;

        return $this;
    }

    public function render(array $options = [])
    {
        return \Form::select(
            $this->getHtmlName(),
            Arr::pluck($this->getOptions(), 'title', 'id'),
            $this->getDefault(),
            array_merge(['class' => 'form-control', 'data-live-search' => 'true'], $options)
        );
    }
}