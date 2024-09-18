<?php

namespace Tobuli\History\Actions;

use Tobuli\Entities\UserDriver;
use Tobuli\History\Stats\StatModelList;

class Drivers extends ActionStat
{
    static public function required()
    {
        return [
            AppendDriver::class
        ];
    }

    public function boot()
    {
        $this->registerStat('drivers', (new StatModelList(UserDriver::class, 'name_with_rfid')));
    }

    public function proccess($position)
    {
        if ($position->driver)
            $this->history->applyStat('drivers', $position->driver->id);
    }
}