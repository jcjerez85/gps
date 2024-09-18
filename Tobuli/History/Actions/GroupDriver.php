<?php

namespace Tobuli\History\Actions;


use Tobuli\History\Group;

class GroupDriver extends ActionGroup
{
    protected $current_id;

    static public function required()
    {
        return [
            AppendDriver::class
        ];
    }

    public function boot() {}

    public function proccess($position)
    {
        if ($this->isChange($position))
        {
            $this->history->groupEnd("driver", $position);

            $group = new Group('driver');
            $group->driver_id = $this->current_id;

            $this->history->groupStart($group, $position);
        }
    }

    protected function isChange($position)
    {
        $driver_id = $position->driver ? $position->driver->id : 0;

        if (is_null($this->current_id))
        {
            $this->current_id = $driver_id;

            return true;
        }

        if ($this->current_id == $driver_id)
            return false;

        $this->current_id = $driver_id;

        return true;
    }
}