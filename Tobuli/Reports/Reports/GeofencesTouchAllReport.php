<?php

namespace Tobuli\Reports\Reports;

use Formatter;
use Illuminate\Support\Arr;
use Tobuli\Entities\Geofence;
use Tobuli\History\Actions\AppendDateUserZone;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\GeofencesIn;
use Tobuli\History\Actions\GroupGeofenceInOut;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class GeofencesTouchAllReport extends DeviceHistoryReport
{
    const TYPE_ID = 31;

    protected $validation = ['geofences' => 'required'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.geofence_touch_all');
    }

    protected function getActionsList()
    {
        $list = [
            AppendDateUserZone::class,
            Duration::class,
            Distance::class,
            GeofencesIn::class,

            GroupGeofenceInOut::class,
        ];

        return $list;
    }

    protected function getTable($data)
    {
        $lefts = new Group('lefts');
        $left = null;

        $days = [];

        foreach ($data['groups']->all() as $group)
        {
            $date = $group->getStartPosition()->date;

            if ($left && $left->getStartPosition()->date != $date)
                $left = null;

            if ($group->getKey() == 'geofence_out') {
                if (is_null($left))
                    $left = $group;
                else
                    $left->stats()->applyArray($group->stats()->only(['distance', 'duration']));

                continue;
            } elseif ($group->getKey() == 'geofence_in') {
                if ( ! is_null($left))
                    $left->stats()->applyArray($group->stats()->only(['distance']));
            }

            if (is_null($left))
                continue;

            if ($group->geofence_id == $left->geofence_id) {
                $left = null;
                continue;
            }

            if (empty($days[$date]))
                $days[$date] = [];

            $days[$date][] = [
                'date'        => Formatter::date()->convert($group->getStartPosition()->time),
                'enter_name'  => runCacheEntity(Geofence::class, $group->geofence_id)->implode('name', ', '),
                'enter_time'  => $group->getStartAt(),
                'left_name'   => runCacheEntity(Geofence::class, $left->geofence_id)->implode('name', ', '),
                'left_time'   => $left->getStartAt(),
                'distance'    => $left->stats()->human('distance'),
                'duration'    => $left->stats()->human('duration'),
                'geofence_id' => $left->geofence_id,
                'geofences'   => [
                    $group->geofence_id,
                    $left->geofence_id,
                ],

                'left'       => $left,
            ];

            //$lefts->stats()->applyArray($left->stats()->only(['distance']));

            $left = null;
        }

        $rows = Arr::collapse(array_filter($days, function($rows, $day) {

            $geofences = Arr::pluck($rows, 'geofences');
            $geofences = Arr::collapse($geofences);
            $geofences = array_unique($geofences);

            return count($geofences) >= count($this->geofences);
        }, ARRAY_FILTER_USE_BOTH));

        foreach ($rows as $row)
            $lefts->stats()->applyArray($row['left']->stats()->only(['distance']));

        $this->group->applyArray($lefts->stats()->only(['distance']));

        return [
            'rows'   => $rows,
            'totals' => $this->getDataFromGroup($lefts, ['distance']),
        ];
    }

    protected function isEmptyResult($data)
    {
        if ( ! $data['root']->stats()->has('geofences_in'))
            return true;

        $geofences_in = $data['root']->stats()->get('geofences_in')->get();

        return count($geofences_in) < count($this->geofences);
    }
}