<?php


namespace Tobuli\History\Actions;


class AppendDiemRateGeofencesOverwrite extends ActionAppend
{
    static public function required()
    {
        return [
            AppendDiemRateGeofences::class
        ];
    }


    public function boot()
    {
        $this->history->setGeofences(AppendDiemRateGeofences::getDiemRateGeofences());
    }

    public function proccess(&$position)
    {
        $position->geofences = $position->diem_rate_geofences;
    }
}