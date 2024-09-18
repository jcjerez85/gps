<?php

namespace Tobuli\History\Actions;

class AppendLoadChangeIfHasGeofences extends AppendLoadChange
{
    public static function required()
    {
        $required = parent::required();
        $required[] = AppendGeofences::class;

        return $required;
    }

    public function proccess(&$position)
    {
        if (empty($position->geofences)) {
            return;
        }

        parent::proccess($position);
    }
}