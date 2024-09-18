<?php

namespace Tobuli\History\Actions;

use Formatter;

class AppendShiftCheck extends ActionAppend
{
    private string $shiftStart;
    private string $shiftFinish;
    private bool $shiftPassesDay;

    private function resolveTime(array $parameters, $key): string
    {
        if (empty($parameters[$key])) {
            throw new \InvalidArgumentException("`$key` parameter missing");
        }

        $time = Formatter::time()->reverse($parameters[$key], 'H:i:s');

        if ($time === false) {
            throw new \InvalidArgumentException("`$key` invalid value: $parameters[$key]");
        }

        return $time;
    }

    public function boot()
    {
        $parameters = $this->history->allConfig();

        $this->shiftStart = $this->resolveTime($parameters, 'shift_start');
        $this->shiftFinish = $this->resolveTime($parameters, 'shift_finish');

        $this->shiftPassesDay = $this->shiftStart > $this->shiftFinish;
    }

    public function proccess(&$position)
    {
        $time = (new \DateTime($position->time))->format('H:i:s');

        $position->inShift = $this->isTimeInShift($time);
    }

    private function isTimeInShift($time): bool
    {
        return $this->shiftPassesDay
            ? $time >= $this->shiftStart || $time < $this->shiftFinish
            : $time >= $this->shiftStart && $time < $this->shiftFinish;
    }
}