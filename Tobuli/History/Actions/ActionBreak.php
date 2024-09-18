<?php

namespace Tobuli\History\Actions;

abstract class ActionBreak extends ActionAppend
{
    /**
     * @param $position
     * @return boolean
     */
    abstract protected function isBreakable($position);

    public function proccess(&$position)
    {
        if ($this->isBreakable($position))
            $position->break = true;
    }
}