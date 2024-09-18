<?php

namespace Tobuli\Helpers\Formatter;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tobuli\Entities\Timezone;

class DST
{
    protected $timezone;
    protected $DST;
    protected $yearCache = [];

    public function byTimezone(Timezone $timezone, $DST = null)
    {
        $this->timezone = $timezone;
        $this->DST = $DST;
    }

    public function isUTC()
    {
        if ($this->timezone->zone != '+0hours')
            return false;

        if ($this->DST)
            return false;

        return true;
    }

    public function apply($value)
    {
        return $this->calculateDST($value, false);
    }

    public function applyReverse($value)
    {
        return $this->calculateDST($value, true);
    }

    public function getZone($time, $reverse = false)
    {
        $zone = $reverse
            ? $this->timezone->reversedZone
            : $this->timezone->zone;

        $range = $this->getDSTRange($time);

        if (! $range) {
            return $zone;
        }

        if ($time > $range['from'] && $time < $range['to']) {
            $zone = $reverse
                ? $this->timezone->reversedDSTZone
                : $this->timezone->DSTZone;
        }

        return $zone;
    }

    private function calculateDST($value, $reverse = false)
    {
        $time = strtotime($value);

        $zone = $this->getZone($time, $reverse);

        return strtotime($zone, $time);
    }

    private function getDSTRange($time)
    {
        if (! $this->DST) {
            return null;
        }

        $year = date('Y', $time);

        if (isset($this->yearCache[$year])) {
            return $this->yearCache[$year];
        }

        $DST = $this->calculateDSTRange($this->DST, $time);

        $fromDate = strtotime($year.'-'.$DST->date_from);
        $toDate = strtotime($year.'-'.$DST->date_to);

        if ($fromDate > $toDate) {
            $toDate = strtotime('+1 year', $toDate);
        }

        return $this->yearCache[$year] = [
            'from' => $fromDate,
            'to' => $toDate,
        ];
    }

    public function getUserDSTRange($user)
    {
        return Cache::store('array')
            ->rememberForever("users.{$user->id}.dst_range", function () use ($user) {
                $userDST = DB::table('users_dst')
                    ->select('users_dst.*', 'timezones_dst.from_period', 'timezones_dst.from_time',
                        'timezones_dst.to_period', 'timezones_dst.to_time')
                    ->leftJoin('timezones_dst', 'users_dst.country_id', '=', 'timezones_dst.id')
                    ->where('users_dst.user_id', '=', $user->id)
                    ->whereNotNull('users_dst.type')
                    ->first();

                return $this->calculateDSTRange($userDST);
            });
    }

    /**
     * Calculates DST date range for given DST on given time
     *
     * @param object|null $DST  users_dst table object with joined from/to data from timezones_dst table
     * @param int $time unix timestamp
     * @return object
     */
    private function calculateDSTRange($DST, $time = null)
    {
        if (empty($DST)) {
            return $DST;
        }

        if (! $time) {
            $time = time();
        }

        $year = date('Y', $time);

        if ($DST->type == 'automatic') {
            if (! empty($DST->from_period)) {
                $DST->date_from = date("m-d", strtotime($DST->from_period." ".$year)).' '.$DST->from_time;
                $DST->date_to = date("m-d", strtotime($DST->to_period." ".$year)).' '.$DST->to_time;
            }
        } elseif ($DST->type == 'other') {
            $dateFrom = "{$DST->week_pos_from} {$DST->week_day_from} of ".$DST->month_from." ".$year."";
            $dateTo = "{$DST->week_pos_to} {$DST->week_day_to} of ".$DST->month_to." ".$year."";

            $DST->date_from = date("m-d", strtotime($dateFrom)).' '.$DST->time_from;
            $DST->date_to = date("m-d", strtotime($dateTo)).' '.$DST->time_to;
        }

        return $DST;
    }
}
