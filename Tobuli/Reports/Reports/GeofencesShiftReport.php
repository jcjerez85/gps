<?php

namespace Tobuli\Reports\Reports;

use Carbon\Carbon;
use Formatter;
use Tobuli\Entities\Geofence;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\GroupGeofenceIn;
use Tobuli\Reports\DeviceHistoryReport;

class GeofencesShiftReport extends DeviceHistoryReport
{
    const TYPE_ID = 28;

    protected $disableFields = ['speed_limit', 'stops', 'show_addresses', 'zones_instead'];
    protected $validation = ['geofences' => 'required'];

    public function getInputParameters(): array
    {
        return [
            \Field::select('shift_start', trans('validation.attributes.shift_start'), '08:00')
                ->setOptions(getSelectTimeRange())
                ->setRequired()
                ->addValidation('date_format:H:i')
            ,
            \Field::select('shift_finish', trans('validation.attributes.shift_finish'), '17:00')
                ->setOptions(getSelectTimeRange())
                ->setRequired()
                ->addValidation('date_format:H:i')
            ,
            \Field::select('shift_start_tolerance', trans('validation.attributes.shift_start_tolerance'))
                ->setOptions(config('tobuli.minutes'))
                ->setRequired()
                ->addValidation('integer')
            ,
            \Field::select('shift_finish_tolerance', trans('validation.attributes.shift_finish_tolerance'))
                ->setOptions(config('tobuli.minutes'))
                ->setRequired()
                ->addValidation('integer')
            ,
            \Field::number('excessive_exit', trans('validation.attributes.excessive_exit'), 10)
                ->setRequired()
                ->addValidation('integer')
            ,
        ];
    }

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.geofence_in_out').' (Shift)';
    }

    protected function getActionsList()
    {
        $list = [
            Duration::class,

            GroupGeofenceIn::class,
        ];

//        if ($this->zones_instead)
//            $list[] = GeofencesIn::class;

        return $list;
    }

    protected function getTable($data)
    {
        $parameters   = $this->parameters;
        $out_limit    = $parameters['excessive_exit'];
        $shift_start  = $parameters['shift_start'];
        $shift_finish = $parameters['shift_finish'];

        $late_entry   = Carbon::parse($shift_start)->addMinutes($parameters['shift_start_tolerance'])->format('H:i');
        $late_exit    = Carbon::parse($shift_finish)->subMinutes($parameters['shift_finish_tolerance'])->format('H:i');

        $rows = [];

        $result = [];

        foreach ($data['groups']->all() as $group)
        {
            $date = Formatter::date()->convert($group->getStartPosition()->time);

            if (empty($result[$date][$group->geofence_id]))
            {
                $result[$date][$group->geofence_id] = [
                    'geofence' => runCacheEntity(Geofence::class, $group->geofence_id)->implode('name', ', '),
                    'shift'    => $late_entry . ' - ' . $late_exit,
                    'first_in' => $group->getStartAt(),
                    'last_out' => null,
                    'count' => 0,
                ];
            }

            $result[$date][$group->geofence_id]['last_out'] = $group->getEndAt();
            $result[$date][$group->geofence_id]['count']++;
        }

        foreach ($result as $day => $geofences) {
            $time_in  = strtotime($day . ' ' . $late_entry);
            $time_out = strtotime($day . ' ' . $late_exit);

            foreach ($geofences as $geofence_id => $values) {
                if ($values['count'] >= $out_limit ||
                    strtotime($values['first_in']) > $time_in ||
                    strtotime($values['last_out']) < $time_out)
                {
                    $rows[] = $result[$day][$geofence_id];
                }
            }
        }

        return [
            'rows'   => $rows,
            'totals' => [],
        ];
    }

    protected function isEmptyResult($data)
    {
        if (empty($data['root']->getStartPosition()))
            return true;

        $item = $this->getTable($data);

        return empty($item['rows']);
    }
}