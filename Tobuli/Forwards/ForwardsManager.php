<?php


namespace Tobuli\Forwards;


use Illuminate\Support\Collection;

class ForwardsManager
{
    public static array $types = [
        Connections\Custom\Custom::class,
        Connections\Pegasus\Pegasus::class,
        Connections\MacroPoint\MacroPoint::class,
    ];

    /**
     * @param $type
     * @return ForwardConnection|null
     */
    public function resolveType($type)
    {
        foreach (self::$types as $class) {
            if ($class::getType() !== $type) {
                continue;
            }

            return new $class();
        }

        return null;
    }

    /**
     * @return ForwardConnection[]
     */
    public function getList(): Collection
    {
        $list = collect();

        foreach (self::$types as $typeId => $class) {
            $list->push(new $class());
        }

        return $list;
    }

    /**
     * @return ForwardConnection[]
     */
    public function getEnabledList(): Collection
    {
        return $this->getList()->filter(function(ForwardConnection $forward) {
            return $forward::isEnabled();
        });
    }

    public function count(): int
    {
        return $this->getEnabledList()->count();
    }
}