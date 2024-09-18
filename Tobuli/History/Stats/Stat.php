<?php

namespace Tobuli\History\Stats;


abstract class Stat
{
    protected $value;

    protected $name;

    abstract public function apply($value);
    abstract protected function valid($value);

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function get()
    {
        return $this->value;
    }

    public function set($value)
    {
        $this->value = $value;
    }

    public function value()
    {
        return $this->value;
    }
}