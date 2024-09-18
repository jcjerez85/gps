<?php

namespace Tobuli\Services;

use CustomFacades\Validators\AdminDiemRateValidator;
use DateTime;
use http\Exception\RuntimeException;
use Tobuli\Entities\DiemRate;

class DiemRateService
{
    /**
     * @var GeofenceService
     */
    protected $geofenceService;

    public function __construct()
    {
        $this->geofenceService = new GeofenceService();
    }

    protected function normalize(array &$data)
    {
        if (!empty($data['rates']) || empty($data['periods'])) {
            return;
        }

        $data['rates'] = [];

        $amounts = $data['amounts'] ?? [];
        $periods = $data['periods'];

        foreach ($periods as $i => $period) {
            if (!strlen($period)) {
                continue;
            }

            $data['rates'][] = [
                'period' => $period,
                'amount' => $amounts[$i] ?? null,
            ];
        }
    }

    public function save(array $data, DiemRate $diemRate = null): DiemRate
    {
        if ($diemRate === null) {
            $diemRate = new DiemRate();
        }

        $this->normalize($data);

        try {
            beginTransaction();

            AdminDiemRateValidator::validate('save', $data, $diemRate->id ?? 0);

            usort($data['rates'], function ($item1, $item2) {
                return $item1['amount'] <=> $item2['amount'];
            });

            $diemRate->fill($data);
            $diemRate->save();

            $data['name'] = $diemRate->title;

            if ($geofence = $diemRate->geofence) {
                $this->geofenceService->edit($geofence, $data);
            } else {
                $geofence = $this->geofenceService->create($data);

                $geofence->diemRate()->associate($diemRate);
                $geofence->save();
            }
            commitTransaction();
        } catch (\Exception $e) {
            rollbackTransaction();
            throw $e;
        }

        return $diemRate;
    }

    public function delete(DiemRate $diemRate)
    {
        if ($diemRate->geofence) {
            $this->geofenceService->delete($diemRate->geofence);
        }

        $diemRate->delete();
    }

    public static function getAmountForInterval(DiemRate $diemRate, $startDate, $endDate)
    {
        $startDate = \Carbon::parse($startDate);
        $endDate = \Carbon::parse($endDate);

        if ($startDate->gt($endDate)) {
            throw new \Exception('Diem rate start date greater than end date');
        }

        if ($startDate->isSameDay($endDate)) {
            $diffHour = ceil(($endDate->getTimestamp() - $startDate->getTimestamp()) / 3600);

            return self::getAmountForHours($diemRate, $diffHour);
        }

        $startDateMidnight = $startDate->isMidnight()
            ? \Carbon::parse($startDate)
            : \Carbon::parse($startDate)->addDay()->setTime(0, 0, 0);

        $endDateMidnight = $endDate->isMidnight()
            ? \Carbon::parse($endDate)
            : \Carbon::parse($endDate)->setTime(0, 0, 0);
        
        $startDateHours = ceil(($startDateMidnight->getTimestamp() - $startDate->getTimestamp()) / 3600);
        $endDateHours = ceil(($endDate->getTimestamp() - $endDateMidnight->getTimestamp()) / 3600);
        $dayDiff = $endDateMidnight->diffInDays($startDateMidnight);

        return self::getAmountForHours($diemRate, $startDateHours)
            + self::getAmountForHours($diemRate, $endDateHours)
            + self::getMaxDiemAmount($diemRate) * $dayDiff;
    }

    public static function getAmountForHours(DiemRate $diemRate, int $hours)
    {
        foreach ($diemRate->rates as $rate) {
            $amount = $rate['amount'];

            if ($hours > $rate['period']) {
                continue;
            }

            return $amount;
        }

        return $amount ?? 0;
    }

    public static function getMaxDiemAmount(DiemRate $diemRate)
    {
        $rates = $diemRate->rates;

        $max = array_pop($rates);

        return $max['amount'];
    }
}