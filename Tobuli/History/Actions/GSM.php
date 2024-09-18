<?php namespace Tobuli\History\Actions;

use Tobuli\History\Stats\StatValue;

class GSM extends ActionStat
{

    static public function required()
    {
        return [
            AppendGSMSensor::class,
        ];
    }

    public function boot()
    {
        $sensor = $this->getSensor('gsm');

        if ( ! $sensor) return;

        $this->registerStat('gsm', (new StatValue()));
    }

    public function proccess($position)
    {
        if (is_null($position->gsm))
            return;

        if ($position->gsm)
            $this->history->applyStat('gsm', $position->gsm);
    }
}