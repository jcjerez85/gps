<?php namespace Tobuli\Traits;

use Tobuli\Entities\SentCommand;

trait SentCommandActor
{
    public function sentCommands()
    {
        return $this->morphMany(SentCommand::class, 'actor');
    }
}