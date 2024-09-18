<?php


namespace Tobuli\History\Actions;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Tobuli\Entities\Geofence;

class AppendDiemRateGeofences extends ActionAppend
{
    public function boot()
    {
    }

    public function proccess(&$position)
    {
        $geofences = self::getDiemRateGeofences();

        $position->diem_rate_geofences = $geofences->filter(function (Geofence $geofence) use ($position) {
            return $geofence->pointIn($position);
        })->keys()->toArray();
    }

    public static function getDiemRateGeofences(): Collection
    {
        return Cache::store('array')->sear('diem_rate_geofences', function() {
            return Geofence::with('diemRate')
                ->whereHas('diemRate', function($query) {
                    $query->active();
                })
                ->get()
                ->keyBy('id');
        });
    }

    /**
     * @param $id
     * @return null|\Tobuli\Entities\DiemRate
     */
    public static function getDiemRateGeofence($id)
    {
        $geofence = self::getDiemRateGeofences()->where('id', $id)->first();

        return $geofence ? $geofence->diemRate : null;
    }
}