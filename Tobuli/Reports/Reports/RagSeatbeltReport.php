<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Drivers;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\GroupDriver;
use Tobuli\History\Actions\Harsh;
use Tobuli\History\Actions\OverspeedStatic;
use Tobuli\History\Actions\Seatbelt;
use Tobuli\History\Actions\Speed;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class RagSeatbeltReport extends DeviceHistoryReport
{
    const TYPE_ID = 23;

    protected $disableFields = ['geofences', 'stops', 'show_addresses', 'zones_instead'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.rag').' / '.trans('front.seatbelt');
    }

    protected function getActionsList()
    {
        return [
            Duration::class,
            Distance::class,
            Speed::class,
            OverspeedStatic::class,
            Harsh::class,
            Drivers::class,
            Seatbelt::class,

            GroupDriver::class,
        ];
    }

    protected function getTable($data)
    {
        $rows = [];

        foreach ($data['groups']->all() as $group)
        {
            $row = $this->getDataFromGroup($group, [
                'drivers',
            ]);

            $distance = $group->stats()->get('distance')->get();
            $duration = $group->stats()->get('overspeed_duration')->get();
            $ha       = $group->stats()->get('harsh_acceleration_count')->get();
            $hb       = $group->stats()->get('harsh_breaking_count')->get();
            $sb       = $group->stats()->get('seatbelt_off_duration')->get();
            $speed_max= $group->stats()->get('speed_max')->get();

            $row['distance'] = round($distance, 2);
            $row['duration'] = $duration;
            $row['ha'] = $ha;
            $row['hb'] = $hb;
            $row['sb'] = $sb;
            $row['speed_max'] = round($speed_max);

            $row['score_overspeed'] = ($duration > 0 && $distance > 0) ? float($duration/10/$distance*100) : 0;
            $row['score_harsh_a']   = ($ha > 0 && $distance > 0) ? float($ha/$distance*100) : 0;
            $row['score_harsh_b']   = ($hb > 0 && $distance > 0) ? float($hb/$distance*100) : 0;
            $row['score_seatbelt']  = ($sb > 0 && $distance > 0) ? float($sb/10/$distance*100) : 0;
            $row['rag']             = $row['score_overspeed'] + $row['score_harsh_a'] + $row['score_harsh_b'] + $row['score_seatbelt'];

            $rows[] = $row;
        }

        return [
            'rows'   => $rows,
            'totals' => [],
        ];
    }

    protected function getTotals(Group $group, array $only = [])
    {
        return parent::getTotals($group, ['overspeed_count']);
    }
}