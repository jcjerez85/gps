<?php

namespace Tobuli\History;

use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Boolean;
use Tobuli\History\Actions\Action;
use Tobuli\History\Stats\Skippable;
use Tobuli\History\Stats\Stat;

class ActionContainer
{
    protected $actions = [];

    /**
     * @return array
     */
    public function get()
    {
        arsort($this->actions);

        return array_keys($this->actions);
    }

    /**
     * @param string|array<string> $actionClasses
     * @return $this
     * @throws \Exception
     */
    public function add($actionClasses)
    {
        if ( ! is_array($actionClasses))
            $actionClasses = [$actionClasses];

        foreach ($actionClasses as $class) {
            $this->countCallers($this->resolveActionClass($class));
        }

        return $this;
    }

    /**
     * @param string $class
     * @param int $weight
     * @param array $list
     * @throws \Exception
     */
    protected function countCallers(string $class, int $weight = 1, $list = [])
    {
        if (in_array($class, $list)) {
            throw new \Exception("Infinity loop for '$class'");
        } else {
            $list[] = $class;
        }

        if (empty($this->actions[$class]))
            $this->actions[$class] = $class::RADIO;

        $this->actions[$class] += $weight;

        foreach ($class::required() as $require) {
            $this->countCallers($require, $weight + 1, $list);
        }

        foreach ($class::after() as $after) {
            if (!array_key_exists($after, $this->actions))
                continue;

            $this->countCallers($after, $weight + 1, $list);
        }
    }

    /**
     * @param $class
     * @return string
     * @throws \Exception
     */
    protected function resolveActionClass($class)
    {
        if ( ! class_exists($class))
            $class = "Tobuli\History\Actions\\" . Str::studly($class);

        if ( ! class_exists($class)) {
            throw new \Exception("DeviceHistory action class '$class' not found");
        }

        if (!is_subclass_of($class, Action::class)) {
            throw new \Exception("'$class' not extend DeviceHistory action class");
        }

        return $class;
    }

    public function __destruct()
    {
        unset($this->actions);
    }
}