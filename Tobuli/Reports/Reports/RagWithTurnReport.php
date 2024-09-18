<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Drivers;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\GroupDriver;
use Tobuli\History\Actions\Harsh;
use Tobuli\History\Actions\OverspeedStatic;
use Tobuli\History\Actions\Speed;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class RagWithTurnReport extends DeviceHistoryReport
{
    const TYPE_ID = 63;

    protected $disableFields = ['geofences', 'stops', 'show_addresses', 'zones_instead'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.rag') . ' 2';
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
            $ht       = $group->stats()->get('harsh_turning_count')->get();

            $row['distance'] = round($distance, 2);
            $row['duration'] = $duration;
            $row['ha'] = $ha;
            $row['hb'] = $hb;
            $row['ht'] = $ht;

            $row['score_overspeed'] = ($duration > 0 && $distance > 0) ? float($duration/10/$distance*100) : 0;
            $row['score_harsh_a']   = ($ha > 0 && $distance > 0) ? float($ha/$distance*100) : 0;
            $row['score_harsh_b']   = ($hb > 0 && $distance > 0) ? float($hb/$distance*100) : 0;
            $row['score_harsh_t']   = ($ht > 0 && $distance > 0) ? float($ht/$distance*100) : 0;
            $row['rag']             = $row['score_overspeed'] + $row['score_harsh_a'] + $row['score_harsh_b']  + $row['score_harsh_t'];

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