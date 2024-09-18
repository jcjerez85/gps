<?php

namespace Tobuli\History;


class MetaContainer
{
    protected $metas = [];

    public function __construct()
    {
    }

    public function set($key, $value)
    {
        $this->metas[$key] = $value;
    }

    public function get($key)
    {
        return $this->metas[$key];
    }

    public function has($key)
    {
        return array_key_exists($key, $this->metas);
    }

    public function all()
    {
        return $this->metas;
    }

    public function __destruct()
    {
        $this->metas = null;
    }
}