<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\AppendSpeedLimitGeofence;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Drivers;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\GeofencesIn;
use Tobuli\History\Actions\GroupDriver;
use Tobuli\History\Actions\Harsh;
use Tobuli\History\Actions\Overspeed;
use Tobuli\History\Actions\Speed;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class RagGeofencesReport extends DeviceHistoryReport
{
    const TYPE_ID = 70;

    protected $disableFields = ['stops', 'zones_instead'];
    protected $validation = ['geofences' => 'required'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.rag_geofences');
    }

    public static function isEnabled()
    {
        return settings('plugins.geofences_speed_limit.status');
    }

    protected function getActionsList()
    {
        return [
            Duration::class,
            Distance::class,
            Speed::class,
            AppendSpeedLimitGeofence::class,
            Overspeed::class,
            Harsh::class,
            Drivers::class,
            GeofencesIn::class,

            GroupDriver::class,
        ];
    }

    protected function getTotals(Group $group, array $only = [])
    {
        return [];
    }

    protected function getTable($data)
    {
        $rows = [];

        foreach ($data['groups']->all() as $group) {
            $distance = $group->stats()->get('distance')->get();
            $od = $group->stats()->get('overspeed_count')->get();
            $ha = $group->stats()->get('harsh_acceleration_count')->get();
            $hb = $group->stats()->get('harsh_breaking_count')->get();
            $ht = $group->stats()->get('harsh_turning_count')->get();

            $row = $this->getDataFromGroup($group, ['drivers', 'geofences_in']);
            $row['distance'] = round($distance, 2);

            $row['overspeed_count'] = $od;
            $row['ha'] = $ha;
            $row['hb'] = $hb;
            $row['ht'] = $ht;

            $row['score_overspeed'] = $distance > 0 ? round($od / $distance * 100) : 0;
            $row['score_harsh_a']   = $distance > 0 ? round($ha / $distance * 100) : 0;
            $row['score_harsh_b']   = $distance > 0 ? round($hb / $distance * 100) : 0;
            $row['score_harsh_t']   = $distance > 0 ? round($ht / $distance * 100) : 0;
            $row['rag'] = max(
                0,
                100 - $row['score_overspeed'] - $row['score_harsh_a'] - $row['score_harsh_b'] - $row['score_harsh_t']
            );

            $rows[] = $row;
        }

        return [
            'rows' => $rows,
            'totals' => [],
        ];
    }
}