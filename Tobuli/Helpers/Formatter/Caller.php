<?php


namespace Tobuli\Helpers\Formatter;

use Tobuli\Helpers\Formatter\Unit\Unit;

class Caller
{
    protected $object;

    protected $unit;

    public function __construct(Unit & $unit, & $object)
    {
        $this->object = $object;
        $this->unit = $unit;
    }

    public function method($method)
    {
        $this->method = $method;

        return $this;
    }

    public function call($value, $method = null)
    {
        if (is_null($method))
            $method = $this->method;

        return call_user_func([$this->unit, $method], $value);
    }

    public function __get($name)
    {
        $value = $this->object->{$name};

        return $this->call($value);
    }

    public function __call($name, $arguments)
    {
        $value = call_user_func([$this->object, $name], $arguments);

        return $this->call($value);
    }
/*
    public function __toString()
    {
        return (string) $this->call((string) $this->object, 'human');
    }
*/
}