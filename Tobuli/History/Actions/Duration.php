<?php

namespace Tobuli\History\Actions;


use Tobuli\History\Stats\Duration AS StatsDuration;

class Duration extends ActionStat
{
    static public function required()
    {
        return [
            AppendDuration::class
        ];
    }

    public function boot()
    {
        $this->registerStat('duration', new StatsDuration());
    }

    public function proccess($position)
    {
        $this->history->applyStat("duration", $position->duration);
    }
}