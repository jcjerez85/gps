<?php

namespace Tobuli\History\Actions;

use Formatter;
use Tobuli\History\Group;
use Tobuli\History\Stats\StatValue;

class GroupFuelFilling extends ActionGroup
{
    static public function required()
    {
        return [
            AppendFuelFilling::class
        ];
    }

    public function boot() {

    }

    public function proccess($position)
    {
        if (empty($position->fuel_filling))
            return;

        $group = new Group('fuel_filling');

        $fuelFormatter = clone Formatter::capacity();
        $fuelFormatter = $fuelFormatter->setUnit($position->fuel_filling['unit']);

        $map = [
            'previous' => 'fuel_level_previous',
            'current'  => 'fuel_level_current',
            'diff'     => 'fuel_level_difference',
        ];

        foreach ($map as $key => $value)
        {
            $stat = (new StatValue())->setFormatUnit($fuelFormatter);
            $stat->apply( $position->fuel_filling[$key] );

            $group->stats()->set($value, $stat);
        }

        $this->history->groupStart($group, $position);
        $this->history->groupEnd("fuel_filling", $position);
    }
}