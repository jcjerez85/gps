<?php

namespace Tobuli\History\Actions;


abstract class ActionGroupMoving extends ActionGroup
{
    protected $state;

    abstract protected function onChange($position);

    static public function required()
    {
        return [
            AppendMoveState::class
        ];
    }

    public function boot() {}

    public function proccess($position)
    {
        if ($this->isChanged($position))
            $this->onChange($position);

        $this->state = $position->moving;
    }

    protected function isChanged($position)
    {
        return $this->state !== $position->moving;
    }
}