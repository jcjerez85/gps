<?php

namespace Tobuli\History\Actions;

abstract class ActionGroupLockStatus extends ActionGroup
{
    protected $state;

    abstract protected function onChange($position, $lastStatus);

    static public function required()
    {
        return [
            AppendLockStatus::class
        ];
    }

    public function boot() {}

    public function proccess($position)
    {
        if ($this->isChanged($position)) {
            $this->onChange($position, $this->state);
        }

        $this->state = $position->lock_status;
    }

    protected function isChanged($position)
    {
        return $this->state !== $position->lock_status;
    }
}
