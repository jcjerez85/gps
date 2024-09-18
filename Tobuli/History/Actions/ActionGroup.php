<?php

namespace Tobuli\History\Actions;

abstract class ActionGroup extends Action
{
    const RADIO = 1000;

    abstract public function proccess($position);
}