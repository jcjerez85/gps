<?php


namespace Tobuli\Forwards\Connections;


use Illuminate\Support\Arr;

abstract class ForwardClient implements \Tobuli\Forwards\ForwardClient
{
    protected $config;

    public function __construct($config = null)
    {
        $this->setConfig($config);
    }

    /**
     * @param $config
     * @return $this
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @param string $key
     * @return array|\ArrayAccess|mixed
     */
    protected function get(string $key)
    {
        return Arr::get($this->config, $key);
    }
}