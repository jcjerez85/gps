<?php

namespace Tobuli\History;


use Illuminate\Support\Arr;

class GroupContainer
{
    /**
     * @var Group[]
     */
    protected $groups = [];

    protected $actives = [];

    protected $reference;

    public function add(Group $group)
    {
        $this->groups[] = $group;
    }

    /**
     * @param Group $group
     * @return Group
     */
    public function open(Group $group)
    {
        $this->add($group);

        $this->lastToActives();

        return $this->last();
    }

    public function close($key, $position, $properties = [])
    {
        if (empty($this->actives))
            return;

        foreach ($this->actives as $i => $index)
        {
            if ($this->groups[$index]->getKey() != $key)
                continue;

            if ($properties && !$this->groups[$index]->matchProperties($properties))
                continue;

            $this->groups[$index]->setEndPosition($position);
        }
    }

    public function disactiveClosed()
    {
        if (empty($this->actives))
            return;

        foreach ($this->actives as $i => $index)
        {
            if ($this->groups[$index]->isClose())
                unset($this->actives[$i]);
        }
    }

    /**
     * @return Group[]
     */
    public function actives()
    {
        if (empty($this->actives))
            return [];

        return array_filter($this->groups, function($k) {
            return in_array($k, $this->actives);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @return Group[]
     */
    public function all()
    {
        return $this->groups;
    }

    /**
     * @return Group
     */
    public function & last()
    {
        return $this->groups[key($this->groups)];
    }

    /**
     * @return Group
     */
    public function first()
    {
        return Arr::first($this->groups);
    }

    /**
     * @return boolean
     */
    public function hasActives()
    {
        return ! empty($this->actives);
    }

    /**
     * @param null $key
     * @return Group
     */
    public function merge($key = null)
    {
        $merged = new Group('merge');

        foreach ($this->groups as $group)
        {
            if ( ! is_null($key) && $group->getKey() != $key)
                continue;

            $position = $group->getStartPosition();
            $mergedPos = $merged->getStartPosition();

            if (!$mergedPos || ($position && $mergedPos->time > $position->time)) {
                $merged->setStartPosition($position);
            }

            $position = $group->getEndPosition();
            $mergedPos = $merged->getEndPosition();

            if (!$mergedPos || ($position && $mergedPos->time < $position->time)) {
                $merged->setEndPosition($position);
            }

            $merged->applyArray($group->stats()->all());
        }

        return $merged;
    }

    public function applyStat($key, $value)
    {
        foreach ($this->actives as $index)
        {
            $this->groups[$index]->applyStat($key, $value);
        }
    }

    public function applyStatOnGroup($groupKey, $statKey, $value)
    {
        foreach ($this->groups as $group) {
            if ($group->getKey() === $groupKey) {
                $group->applyStat($statKey, $value);

                return;
            }
        }
    }

    /**
     * @param array $properties
     * @param GroupContainer|null $groups
     * @return GroupContainer
     */
    public function mergeByProperties(array $properties, GroupContainer $groups = null)
    {
        if (is_null($groups))
            $groups = new GroupContainer();

        foreach ($this->groups as $_group)
        {
            $group = $groups->findByProperties($_group->filterProperties($properties));

            if (!$group) {
                $group = new Group($_group->getKey());
                $group->setMetaContainer($_group->getMetaContainer());
                $group->setStartPosition($_group->getStartPosition());
                $group->setEndPosition($_group->getEndPosition());
                $group->stats()->_clone( $_group->stats()->all() );
                foreach ($group->stats()->keys() as $key)
                    $group->stats()->apply($key, null);

                $groups->add($group);
            }

            $groups->applyStatsByProperties($_group->filterProperties($properties), $_group->stats());
        }

        return $groups;
    }

    /**
     * @param array $properties
     * @return Group|null
     */
    public function findByProperties(array $properties)
    {
        foreach ($this->groups as $group) {
            if (!$group->matchProperties($properties))
                continue;

            return $group;
        }

        return null;
    }

    /**
     * @param array $properties
     * @param StatContainer $stats
     */
    public function applyStatsByProperties(array $properties, StatContainer $stats)
    {
        foreach ($this->groups as & $group) {
            if (!$group->matchProperties($properties))
                continue;

            $group->applyArray($stats->all());
        }
    }

    protected function lastToActives()
    {
        end($this->groups);
        $this->actives[] = key($this->groups);

        //$this->resetReference();
    }

    protected function resetReference()
    {
        unset($this->reference);

        if (count($this->actives) == 1) {
            $index = last($this->actives);
            $this->reference = & $this->groups[$index];
        }
    }

    public function __destruct()
    {
        $this->groups = null;
        $this->actives = null;
        unset($this->reference);
    }
}