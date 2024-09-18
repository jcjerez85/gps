<?php

namespace Tobuli\Reports\Reports;

use Formatter;
use Illuminate\Support\Str;
use Tobuli\Entities\Geofence;
use Tobuli\History\Actions\AppendDateUserZone;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\FirstDrive;
use Tobuli\History\Actions\GeofencesIn;
use Tobuli\History\Actions\GroupDaily;
use Tobuli\History\Actions\GroupDailySplit;
use Tobuli\History\Actions\GroupGeofenceIn;
use Tobuli\History\Actions\LastDrive;
use Tobuli\History\Actions\SpeedCondition;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class CartDailyCleaningReport extends DeviceHistoryReport
{
    const TYPE_ID = 60;

    protected $disableFields = ['speed_limit', 'stops', 'show_addresses', 'zones_instead'];

    public function getInputParameters(): array
    {
        return [
            \Field::number('speed_break', trans('validation.attributes.speed_limit') . ' (' . trans('front.kph') . ')', '10')
                ->setRequired()
                ->addValidation('numeric')
            ,
            \Field::number(
                'distance_limit',
                trans('validation.attributes.distance_limit') . ' (' . trans('front.mt') . ')',
                '100'
            )
                ->setRequired()
                ->addValidation('numeric')
            ,
            \Field::string('shift_start', trans('validation.attributes.shift_start'), '05:30')
                ->setRequired()
                ->addValidation('date_format:H:i')
            ,
        ];
    }

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.cart_daily_cleaning');
    }

    public static function isEnabled()
    {
        return settings('plugins.report_cart_cleaning_daily.status');
    }

    protected function getActionsList()
    {
        return [
            AppendDateUserZone::class,
            Duration::class,
            Distance::class,
            GeofencesIn::class,
            SpeedCondition::class,
            FirstDrive::class,
            LastDrive::class,

            GroupDailySplit::class,
            GroupDaily::class,
            GroupGeofenceIn::class,
        ];
    }

    protected function getTable($data)
    {
        $rows = [];
        $total = new Group('device_total');
        $activeDays = 0;

        $groups = $data['groups']->all();

        foreach ($groups as $key => $group) {
            $date = Formatter::date()->convert($group->getStartPosition()->time);

            if (empty($rows[$date])) {
                $rows[$date] = [
                    'date'           => $date,
                    'shift_start'    => $this->parameters['shift_start'] ?? '05:30',
                    'start_time'     => Formatter::dtime()->convert($group->getStartPosition()->time),
                    'end_time'       => Formatter::dtime()->convert($group->getEndPosition()->time),
                    'geofences'      => [],
                ];
            }

            if (Str::startsWith($group->getKey(), 'geofence_in')) {
                $rows[$date]['geofences'][] = [
                    'name'       => runCacheEntity(Geofence::class, $group->geofence_id)->implode('name', ', '),
                    'enter_time' => Formatter::dtime()->convert($group->getStartPosition()->time),
                    'leave_time' => Formatter::dtime()->convert($group->getEndPosition()->time),
                ];
            } else {
                $day_distance = $group->stats()->get('speed_below_distance')->value();
                $min_distance = $this->parameters['distance_limit'] * 0.001 ?? 0;
                $activeDay    = $day_distance > $min_distance;
                $activeDays  +=  $activeDay ? 1 : 0;

                if ($activeDay) {

                    $total->applyArray($group->stats()->all());

                    $rows[$date] = array_merge($rows[$date], [
                        'start_time' => Formatter::dtime()->convert($group->getStartPosition()->time),
                        'end_time' => Formatter::dtime()->convert($group->getEndPosition()->time),
                        'speed_below_distance' => $group->stats()->format('speed_below_distance'),
                        'speed_below_duration' => $group->stats()->human('speed_below_duration'),
                        'speed_above_distance' => $group->stats()->format('speed_above_distance'),
                        'speed_above_duration' => $group->stats()->human('speed_above_duration'),
                    ]);
                } else {
                    $rows[$date] = array_merge($rows[$date], [
                        'error' => trans('front.nothing_found_request')
                    ]);
                }
            }
        }

        return [
            'rows'   => $rows,
            'totals' => [
                'active_days' => $activeDays,
                'speed_below_distance' => $total->stats()->format('speed_below_distance'),
                'speed_below_duration' => $total->stats()->human('speed_below_duration'),
                'speed_above_distance' => $total->stats()->format('speed_above_distance'),
                'speed_above_duration' => $total->stats()->human('speed_above_duration'),
            ],
        ];
    }
}