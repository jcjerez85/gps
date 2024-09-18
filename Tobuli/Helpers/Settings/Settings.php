<?php

namespace Tobuli\Helpers\Settings;

use Cache;

abstract class Settings {

    protected $prefix;

    protected $cache;

    protected $parent;

    abstract protected function _has($key);
    abstract protected function _get($key);
    abstract protected function _set($key, $value);

    public function __construct()
    {
        $this->cache = Cache::store('array');
    }

    public function get($key, $merge = true)
    {
        return $merge ? $this->merge($key) : $this->_get($key);

        return $this->cache->rememberForever(
            $this->getCahceKey($key),
            function() use($key, $merge) {
                return $merge ? $this->merge($key) : $this->_get($key);
            }
        );
    }

    public function set($key, $value)
    {
        $this->cache->flush();

        return $this->_set($key, $value);
    }

    public function has($key)
    {
        return $this->_has($key);
    }

    public function merge($key)
    {
        $parent_value = $this->parent ? $this->parent->get($key) : null;

        $value = $parent_value;

        if ($this->_has($key))
        {
            $value = $this->_get($key);

            if ($this->isMergeable($value))
            {
                $parent_value = $parent_value ? $parent_value : [];
                $value = array_merge_recursive_distinct($parent_value, $value);
            }
        }

        return $value;
    }

    public function setParent($parentSettings)
    {
        $this->parent = $parentSettings;
    }

    public function getCahceKey($key)
    {
        return $this->getPrefix() . $key;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    protected function isMergeable($value)
    {
        if ( ! is_array($value))
            return false;

        return true;
    }
}