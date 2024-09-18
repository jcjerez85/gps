<?php

namespace Tobuli\History;

use Formatter;
use Tobuli\History\Stats\Distance;
use Tobuli\History\Stats\Duration AS DurationStat;

class Group
{
    use Metaable {
        Metaable::__construct as private __metaConstruct;
    }

    protected $id;

    protected $key;

    protected $start_position;

    protected $end_position;

    protected $stats;

    protected $route;

    protected $last_close = true;

    public function __construct($key)
    {
        $this->id  = uniqid();

        $this->key = $key;

        $this->stats = new StatContainer();

        $this->route = new Route();

        $this->__metaConstruct();
    }

    public function ID()
    {
        return $this->id;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getStartAt()
    {
        if ( ! $this->start_position)
            return null;

        return Formatter::time()->human($this->start_position->time);
    }

    public function getEndAt()
    {
        if ( ! $this->end_position)
            return null;

        return Formatter::time()->human($this->end_position->time);
    }

    public function getStartPosition()
    {
        return $this->start_position;
    }

    public function getEndPosition()
    {
        return $this->end_position;
    }

    public function setStartPosition($position)
    {
        $this->start_position = $position;
    }

    public function setEndPosition($position)
    {
        $this->end_position = $position;
    }

    public function setLastClose(bool $value)
    {
        $this->last_close = $value;
    }

    public function hasStat($key)
    {
        return $this->stats->has($key);
    }

    public function getStat($key)
    {
        return $this->stats->get($key);
    }

    public function getStats()
    {
        return $this->stats;
    }

    public function & stats()
    {
        return $this->stats;
    }

    public function applyStat($key, $value)
    {
        if ($this->isOpen()) {
            $this->stats->apply($key, $value);

            return;
        }

        $stat = $this->stats->get($key);

        if ($stat instanceof Distance || $stat instanceof DurationStat)
            $this->stats->apply($key, $value);

    }

    public function applyArray($array)
    {
        $this->stats->applyArray($array);
    }

    public function isLastClose()
    {
        return $this->last_close;
    }

    public function isClose()
    {
        return ! $this->isOpen();
    }

    public function isOpen()
    {
        return empty($this->end_position);
    }

    /**
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return Route
     */
    public function & route()
    {
        return $this->route;
    }

    /**
     * @param array $properties
     * @return array
     */
    public function filterProperties(array $properties)
    {
        $result = [];

        foreach ($properties as $property)
            $result[$property] = $this->{$property};

        return $result;
    }

    /**
     * @param array $properties
     * @return bool
     */
    public function matchProperties(array $properties)
    {
        foreach ($properties as $property => $value) {
            if ($value !== $this->{$property})
                return false;
        }

        return true;
    }

    public function __destruct()
    {
        $this->id = null;
        $this->key = null;
        $this->stats = null;
        $this->route = null;
        $this->start_position = null;
        $this->end_position = null;
    }
}