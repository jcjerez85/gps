<?php

namespace Tobuli\Helpers\Payments\Gateways\Paydunya;

class CustomData
{
    private $data = array();

    public function push($data_array = array())
    {
        $this->data = $data_array;
    }

    public function set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function get($name)
    {
        return $this->data[$name];
    }

    public function show()
    {
        return $this->data;
    }
}
