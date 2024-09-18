<?php

namespace Tobuli\History\Actions;

use Illuminate\Support\Facades\Cache;

class AppendDiemRate extends ActionAppend
{
    public static function required()
    {
        return [
            AppendDiemRateGeofences::class,
            AppendDuration::class,
        ];
    }

    public function boot()
    {
    }

    public function proccess(&$position)
    {
        if (empty($position->diem_rate_geofences)) {
            return;
        }

        $prevPos = $this->getPrevPosition();

        if (!$prevPos || empty($prevPos->diem_rate_geofences)) {
            return;
        }

        foreach ($position->diem_rate_geofences as $geofence_id) {
            if (!in_array($geofence_id, $prevPos->diem_rate_geofences))
                continue;

            $position->diem_rate = $position->duration * $this->getGeofenceDiemRate($geofence_id);

            break;
        }
    }

    private function getGeofenceDiemRate($id)
    {
        return Cache::store('array')->sear('diem_rate_geofences_per_sec.' . $id, function () use ($id) {
            return Cache::store('array')
                ->get('diem_rate_geofences')
                ->where('id', $id)
                ->first()
                ->diemRate;
        });
    }
}