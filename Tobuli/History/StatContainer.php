<?php

namespace Tobuli\History;

use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Boolean;
use Tobuli\History\Stats\Skippable;
use Tobuli\History\Stats\Stat;

class StatContainer
{
    protected $list = [];

    /**
     * @return Stat[]
     */
    public function all()
    {
        return $this->list;
    }

    public function keys()
    {
        return array_keys($this->list);
    }

    public function set($key, Stat $stat)
    {
        if ( ! empty($this->list[$key]))
            throw new \Exception("DeviceHistory stat '$key' already set");

        $this->list[$key] = $stat;
    }

    /**
     * @return Stat
     */
    public function get($key)
    {
        return $this->list[$key];
    }

    /**
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->list);
    }

    public function apply($key, $value)
    {
        $this->get($key)->apply($value);
    }

    public function copy($stats)
    {
        foreach($stats as $key => $stat)
            $this->set($key, $stat);
    }

    public function value($key)
    {
        return $this->get($key)->value();
    }

    /**
     * @return Stat[]
     */
    public function like($prefix)
    {
        return array_filter($this->list, function($stat, $key) use ($prefix){
            return Str::startsWith($key, $prefix);
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function _clone($stats)
    {
        foreach($stats as $key => $stat)
            $this->set($key, clone $stat);
    }

    /**
     * @return Stat[]
     */
    public function except($keys)
    {
        return array_filter($this->list, function($stat, $key) use ($keys){
            return ! in_array($key, $keys);
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * @return Stat[]
     */
    public function only($keys)
    {
        return array_filter($this->list, function($stat, $key) use ($keys){
            return in_array($key, $keys);
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * @return string
     */
    public function human($key)
    {
        if ( ! $this->has($key))
            return '-';

        return $this->get($key)->human();
    }

    public function format($key)
    {
        if ( ! $this->has($key))
            return null;

        return $this->get($key)->format();
    }

    public function applyContainer(StatContainer $container)
    {
        $this->applyArray($container->all());
    }

    public function applyArray(array $array)
    {
        foreach ($array as $key => $stat)
        {
            if ( ! $this->has($key)) {
                if ($stat instanceof Skippable) {
                    $stat->setSkipped();
                }
                $this->set($key, $stat);
            } else {
                $this->apply($key, $stat->value());
            }
        }
    }

    public function __destruct()
    {
        unset($this->list);
    }
}