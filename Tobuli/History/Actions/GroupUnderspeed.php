<?php

namespace Tobuli\History\Actions;


class GroupUnderspeed extends ActionGroup
{
    static public function required()
    {
        return [
            AppendPosition::class,
            AppendDuration::class,
            AppendUnderspeed::class
        ];
    }

    public function boot(){}

    public function proccess($position)
    {
        if ($this->isEnd($position))
            $this->history->groupEnd('underspeed', $position);

        if ($this->isStart($position))
            $this->history->groupStart('underspeed', $position);
    }

    protected function isUnderspeed($position)
    {
        return $position->underspeeding;
    }

    protected function isStart($position)
    {
        return $position->underspeeding == 1;
    }

    protected function isEnd($position)
    {
        $previous = $this->history->getPrevPosition();

        if ( ! $previous)
            return false;

        if ( ! $this->isUnderspeed($previous))
            return false;

        if ($this->isUnderspeed($position))
            return false;

        return true;
    }
}