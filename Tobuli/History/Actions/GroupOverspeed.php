<?php

namespace Tobuli\History\Actions;


class GroupOverspeed extends ActionGroup
{
    static public function required()
    {
        return [
            AppendOverspeeding::class,
        ];
    }

    public function boot(){}

    public function proccess($position)
    {
        if ($this->isEnd($position))
            $this->history->groupEnd('overspeed', $position);

        if ($this->isStart($position))
            $this->history->groupStart('overspeed', $position);
    }

    protected function isOverspeed($position)
    {
        return isset($position->overspeeding) && $position->overspeeding;
    }

    protected function isStart($position)
    {
        return isset($position->overspeeding) && $position->overspeeding == 1;
    }

    protected function isEnd($position)
    {
        $previous = $this->history->getPrevPosition();

        if ( ! $previous)
            return false;

        if ( ! $this->isOverspeed($previous))
            return false;

        if ($previous->speed_limit !== $position->speed_limit)
            return true;

        if ($this->isOverspeed($position))
            return false;

        return true;
    }
}