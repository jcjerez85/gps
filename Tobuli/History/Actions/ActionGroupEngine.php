<?php

namespace Tobuli\History\Actions;


abstract class ActionGroupEngine extends ActionGroup
{
    protected $state;

    abstract protected function onChange($position);

    static public function required()
    {
        return [
            AppendEngineStatus::class
        ];
    }

    public function boot() {}

    public function proccess($position)
    {
        if ($this->isChanged($position))
            $this->onChange($position);

        $this->state = $position->engine;
    }

    protected function isChanged($position)
    {
        return $this->state !== $position->engine;
    }
}