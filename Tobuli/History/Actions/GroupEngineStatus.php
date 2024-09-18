<?php

namespace Tobuli\History\Actions;


class GroupEngineStatus extends ActionGroupEngine
{
    protected function onChange($position)
    {
        $this->history->groupEnd($position->engine ? 'engine_off' : 'engine_on', $position);
        $this->history->groupStart($position->engine ? 'engine_on' : 'engine_off', $position);
    }
}