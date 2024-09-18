<?php

namespace Tobuli\History\Actions;

abstract class ActionAppend extends Action
{
    const RADIO = 10000;

    abstract public function proccess( & $position);
}