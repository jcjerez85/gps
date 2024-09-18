<?php

namespace Tobuli\Reports\Reports;

use Formatter;
use Tobuli\Entities\Geofence;
use Tobuli\History\Actions\AppendDateUserZone;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\GeofencesIn;
use Tobuli\History\Actions\GroupDailySplit;
use Tobuli\History\Actions\GroupGeofenceIn;
use Tobuli\History\Group;

class GeofencesTouchAllReport2 extends GeofencesTouchAllReport
{
    const TYPE_ID = 44;

    protected $disableFields = ['speed_limit', 'stops', 'show_addresses', 'zones_instead'];

    private $rows = [];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.geofence_touch_all_2');
    }

    protected function getActionsList()
    {
        $list = [
            AppendDateUserZone::class,
            Duration::class,
            Distance::class,
            GeofencesIn::class,
            DriveStop::class,

            GroupDailySplit::class,
            GroupGeofenceIn::class,
        ];

        return $list;
    }

    protected function getTable($data)
    {
        $groups = $data['groups']->all();

        foreach ($groups as $key => $group) {
            if ( ! isset($total)) {
                $total = new Group('device_total');
                $total->setStartPosition($group->getStartPosition());
            }

            if ($total->getStartPosition()->date != $group->getStartPosition()->date) {
                $total->setEndPosition($group->getEndPosition());
                $this->insertRow($total);

                $total = new Group('device_total');
                $total->setStartPosition($group->getStartPosition());
            }

            $total->applyArray($group->stats()->all());
        }

        if (is_null($total->getEndPosition())) {
            $total->setEndPosition($group->getEndPosition());
            $this->insertRow($total);
        }

        return [
            'rows'   => $this->rows,
            'totals' => [],
        ];
    }

    private function insertRow($group)
    {
        if ( ! $group->stats()->has('geofences_in')) {
            return;
        }

        $geofences = $group->stats()->get('geofences_in')->get();

        if (count($geofences) < count($this->geofences)) {
            return;
        }

        $this->group->applyArray($group->stats()->only(['distance']));

        $date = Formatter::date()->convert($group->getStartPosition()->time);

        $this->rows[$date][] = [
            'date'           => $date,
            'geofences'      => runCacheEntity(Geofence::class, $geofences)->implode('name', ', '),
            'drive_duration' => $group->stats()->human('drive_duration'),
            'stop_duration'  => $group->stats()->human('stop_duration'),
            'distance'       => $group->stats()->human('distance'),
        ];
    }

    protected function generate()
    {
        $this->group = new Group('report');

        $this->getDevicesQuery()->chunk(1000, function ($devices) {
            foreach ($devices as $device) {
                $item = $this->generateDevice($device);

                if (isset($item['table']['rows'])) {
                    foreach ($item['table']['rows'] as $date => $rows) {
                        $copy = $item;

                        $copy['table']['rows'] = $rows;

                        $this->items[$date][] = $copy;
                    }
                }

                $this->rows = [];
            }
        });

        $this->totals = $this->getDataFromGroup($this->group, $this->group->stats()->keys());
    }
}