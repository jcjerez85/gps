<?php

namespace Tobuli\History\Actions;

abstract class ActionQuit extends ActionAppend
{
    abstract protected function isQuitable($position): bool;

    public function proccess(&$position)
    {
        if ($this->isQuitable($position)) {
            $position->quit = true;
        }
    }
}