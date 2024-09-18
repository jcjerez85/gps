<?php


namespace Tobuli\Helpers\Formatter\Unit;


class ModelList extends Unit
{
    protected $class;

    protected $property;

    public function __construct($class, $property = null)
    {
        $this->class = $class;
        $this->property = $property ?? 'name';
    }

    public function human($value)
    {
        return runCacheEntity($this->class, $value)->implode($this->property, ", ");
    }
}