<?php

namespace Tobuli\History\Actions;

abstract class ActionStat extends Action
{
    const RADIO = 1;

    abstract public function proccess($position);
}