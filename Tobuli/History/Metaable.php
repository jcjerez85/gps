<?php


namespace Tobuli\History;

trait Metaable
{
    protected $meta;

    public function __construct()
    {
        $this->meta = new MetaContainer();
    }

    public function getMetaContainer()
    {
        return $this->meta;
    }

    public function setMetaContainer(MetaContainer $meta)
    {
        $this->meta = $meta;
    }

    public function __get($name)
    {
        return $this->meta->get($name);
    }

    public function __set($name, $value)
    {
        $this->meta->set($name, $value);
    }

    public function __isset($name)
    {
        return $this->meta->has($name);
    }
}