<?php

namespace Tobuli\Reports;

use Bugsnag\BugsnagLaravel\Facades\Bugsnag as Bugsnag;
use Formatter;
use Illuminate\Support\Arr;
use Tobuli\Entities\Geofence;
use Tobuli\Entities\UserDriver;
use Tobuli\History\Actions\AppendDiemRateGeofences;
use Tobuli\History\Actions\Drivers;
use Tobuli\History\DeviceHistory;
use Tobuli\History\Group;
use Tobuli\History\StatContainer;
use Tobuli\History\Stats\StatSum;
use Tobuli\Services\DiemRateService;

abstract class DeviceHistoryReport extends DeviceReport
{
    /**
     * @var Group
     */
    protected $group;

    abstract protected function getActionsList();

    private function prepareActionsList()
    {
        $actions = $this->getActionsList();

        if (Arr::get($this->metas('history'), 'history.drivers'))
            $actions[] = Drivers::class;

        return $actions;
    }

    protected function getStatTranslateList()
    {
        return [
            'duration' => trans('front.duration'),
            'drive_count' => trans('front.drive_count'),
            'stop_count' => trans('front.stop_count'),

            'distance' => trans('front.route_length'),
            'drive_distance' => trans('front.route_length'),
            'drive_duration' => trans('front.move_duration'),
            'stop_duration' => trans('front.stop_duration'),
            'overspeed_count' => trans('front.overspeeds_count'),
            'overspeed_duration' => trans('front.overspeed_duration'),
            'underspeed_count' => trans('front.underspeeds_count'),
            'underspeed_duration' => trans('front.underspeed_duration'),
            'harsh_acceleration_count' => trans('front.harsh_acceleration_count'),
            'harsh_breaking_count' => trans('front.harsh_braking_count'),
            'harsh_turning_count' => trans('front.harsh_turning_count'),
            'drivers' => trans('front.drivers'),
            'speed_max' => trans('front.top_speed'),
            'speed_avg' => trans('front.average_speed'),
            'speed_min' => trans('front.min_speed'),
            'engine_hours' => trans('validation.attributes.engine_hours'),
            'engine_idle' => trans('front.engine_idle'),
            'engine_work' => trans('front.engine_work'),
            'odometer'    => trans('front.odometer'),
            'loading_count' => trans('front.total_loading_amount'),
            'unloading_count' => trans('front.total_unloading_amount'),

            'fuel_consumption' => trans('front.fuel_consumption'),
            'fuel_consumption_gps' => trans('front.fuel_consumption') . ' (' . trans('front.gps') . ')' ,
            'odometer_start' => trans('front.odometer') . ' ' . trans('front.start') ,
            'odometer_end' => trans('front.odometer') . ' ' . trans('front.end'),
        ];
    }

    protected function getStatTranslate($key)
    {
        $list = $this->getStatTranslateList();

        return empty($list[$key]) ? $key : $list[$key];
    }

    protected function getDeviceHistoryData($device)
    {
        $history = new DeviceHistory($device);

        if ( ! empty($this->parameters))
            $history->setConfig($this->parameters);

        $history->setConfig([
            'stop_seconds'      => $this->stop_seconds,
            'speed_limit'       => $this->speed_limit,
            'extend_start'      => $this->extendStart(),

            'stop_speed'        => $device->min_moving_speed,
            'min_fuel_fillings' => $device->min_fuel_fillings,
            'min_fuel_thefts'   => $device->min_fuel_thefts,
        ]);

        $history->setGeofences($this->geofences);

        $history->registerActions(
            $this->prepareActionsList()
        );

        $history->setRange($this->date_from, $this->date_to);

        try {
            return $history->get();
        } catch (\PDOException $e) {
            Bugsnag::notifyException($e);
            return null;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function getStatusTranslate(Group $group)
    {
        switch ($group->getKey()) {
            case 'drive':
                return trans('front.moving');
            case 'stop':
                return trans('front.stopped');
            case 'geofence_in':
                return trans('front.zone_in');
            case 'geofence_out':
                return trans('front.zone_out');
            default:
                return 'group:' . $group->getKey();
        }
    }

    protected function getDataFromGroup(Group $group, $keys)
    {
        if (is_string($keys))
            $keys = [$keys];

        $result = [];

        foreach ($keys as $key) {
            switch ($key) {
                case 'timestamp':
                    $result[$key] = Formatter::time()->timestamp($group->getStartPosition()->time);
                    break;
                case 'date':
                    $result[$key] = Formatter::date()->convert($group->getStartPosition()->time);
                    break;
                case 'address':
                    $this->getAddress($group->getStartPosition());
                    break;
                case 'group_key':
                    $result[$key] = $group->getKey();
                    break;
                case 'status':
                    $result[$key] = $this->getStatusTranslate($group);
                    break;
                case 'start_time_at':
                    $result[$key] = $group->getStartPosition() ? Formatter::dtime()->human($group->getStartPosition()->time) : null;
                    break;
                case 'start_date_at':
                    $result[$key] = $group->getStartPosition() ? Formatter::date()->human($group->getStartPosition()->time) : null;
                    break;
                case 'start_at':
                    $result[$key] = $group->getStartAt();
                    break;
                case 'end_time_at':
                    $result[$key] = $group->getEndPosition() ? Formatter::dtime()->human($group->getEndPosition()->time) : null;
                    break;
                case 'end_date_at':
                    $result[$key] = $group->getEndPosition() ? Formatter::date()->human($group->getEndPosition()->time) : null;
                    break;
                case 'end_at':
                    $result[$key] = $group->getEndAt();
                    break;
                case 'location':
                case 'location_start':
                    $result[$key] = $this->getLocation($group->getStartPosition());
                    break;
                case 'location_end':
                    $result[$key] = $this->getLocation($group->getEndPosition());
                    break;
                case 'fuel_level_start_list':
                    $stats = $group->stats()->like('fuel_level_start_');

                    foreach ($stats as $stat) {
                        $result[$key][] = [
                            'title' => trans('front.fuel_level_start') . " ({$stat->getName()})",
                            'value' => $stat->human()
                        ];
                    }
                    break;
                case 'fuel_level_end_list':
                    $stats = $group->stats()->like('fuel_level_end_');

                    foreach ($stats as $stat) {
                        $result[$key][] = [
                            'title' => trans('front.fuel_level_end') . " ({$stat->getName()})",
                            'value' => $stat->human()
                        ];
                    }
                    break;
                case 'fuel_avg_list':
                    $statDistance = $group->stats()->get('distance');
                    $distance = $statDistance->get();

                    $stats = $group->stats()->like('fuel_consumption_');

                    foreach ($stats as $stat) {
                        Formatter::fuelAvg()->setMeasure(
                            $stat->getFormatUnit()->getMeasure()
                            . '/' .
                            $statDistance->getFormatUnit()->getMeasure()
                        );
                        $value = $distance ? ($stat->get() / $distance) : $stat->get();

                        $result[$key][] = [
                            'title' => trans('front.fuel_avg') . " ({$stat->getName()})",
                            'value' => Formatter::fuelAvg()->human($value)
                        ];
                    }
                    break;
                case 'fuel_avg':
                    if ($group->stats()->has('fuel_consumption')) {

                        $statDistance = $group->stats()->get('distance');
                        $distance = $statDistance->get();

                        $stat = $group->stats()->get('fuel_consumption');

                        Formatter::fuelAvg()->setMeasure(
                            $stat->getFormatUnit()->getMeasure()
                            . '/' .
                            $statDistance->getFormatUnit()->getMeasure()
                        );
                        $value = $distance ? ($stat->get() / $distance) : $stat->get();

                        $result[$key] = Formatter::fuelAvg()->human($value);
                    } else {
                        $result[$key] = null;
                    }
                    break;
                case 'diem_rate':
                    if (!$group->hasStat($key)) {
                        $group->stats()->set($key, (new StatSum())->setFormatUnit(\Formatter::currency()));

                        if (isset($group->geofence_id) && $diemRate = AppendDiemRateGeofences::getDiemRateGeofence($group->geofence_id)) {
                            $startTime = $group->getStartPosition()->time;
                            $endTime   = $group->getEndPosition() ? $group->getEndPosition()->time : $this->date_to;

                            $startTime = Formatter::time()->convert($startTime);
                            $endTime   = Formatter::time()->convert($endTime);

                            $group->stats()->apply(
                                $key,
                                DiemRateService::getAmountForInterval($diemRate, $startTime, $endTime)
                            );
                        }
                    }

                    $result[$key] = $group->stats()->human($key);
                    break;
                case 'fuel_consumption_list':
                    $stats = $group->stats()->like('fuel_consumption_');

                    foreach ($stats as $stat) {
                        $result[$key][] = [
                            'title' => trans('front.fuel_consumption') . " ({$stat->getName()})",
                            'value' => $stat->human()
                        ];
                    }
                    break;
                case 'fuel_price_list':
                    $stats = $group->stats()->like('fuel_price_');

                    foreach ($stats as $stat) {
                        $result[$key][] = [
                            'title' => trans('front.fuel_cost') . " ({$stat->getName()})",
                            'value' => $stat->human()
                        ];
                    }
                    break;
                case 'odometer_diff_list':
                    $stats = $group->stats()->like('odometer_diff_');

                    foreach ($stats as $stat) {
                        $result[$key][] = [
                            'title' => $stat->getName(),
                            'value' => $stat->human()
                        ];
                    }
                    break;
                case 'group_geofence':
                    $result[$key] = runCacheEntity(Geofence::class, $group->geofence_id)->implode('name', ', ');
                    break;
                case 'first_drive_time':
                case 'last_drive_time':
                case 'first_engine_time':
                case 'last_engine_time':
                    $_key = str_replace('_time', '', $key);
                    if ($group->stats()->has($_key) && $_position = $group->stats()->get($_key)->get()) {
                        $result[$key] = Formatter::time()->human($_position->time);
                    } else
                        $result[$key] = null;
                    break;

                default:
                    if ($group->stats()->has($key))
                        $result[$key] = $group->stats()->human($key);
                    else
                        $result[$key] = null;
                    //$result[$key] = "$key::n/a";
            }
        }

        return $result;
    }

    protected function getTotals(Group $group, array $only = [])
    {
        $totals = [];

        if ($only)
            $stats = $group->stats()->only($only);
        else
            $stats = $group->stats()->all();

        foreach ($stats as $key => $value)
        {
            $totals[$key] = [
                'title' => $this->getStatTranslate($key),
                'value' => $value->human(),
            ];
        }

        if( $data = $this->getDataFromGroup($group, 'fuel_consumption_list'))
        {
            $totals['fuel_consumption_list'] = [
                'title' => '',
                'value' => $data['fuel_consumption_list'],
            ];
        }

        if( $data = $this->getDataFromGroup($group, 'fuel_price_list'))
        {
            $totals['fuel_price_list'] = [
                'title' => '',
                'value' => $data['fuel_price_list'],
            ];
        }

        if( $data = $this->getDataFromGroup($group, 'odometer_diff_list'))
        {
            $totals['odometer_diff_list'] = [
                'title' => '',
                'value' => $data['odometer_diff_list'],
            ];
        }

        $totals['start'] = [
            'title' => trans('front.route_start'),
            'value' => $group->getStartAt(),
        ];

        $totals['end'] = [
            'title' => trans('front.route_end'),
            'value' => $group->getEndAt(),
        ];

        return $totals;
    }

    protected function getTable($data) {return null;}

    protected function getMap($data) {
        return [
            'start'     => [$data['root']->getStartPosition()->latitude, $data['root']->getStartPosition()->longitude],
            'end'       => [$data['root']->getEndPosition()->latitude, $data['root']->getEndPosition()->longitude],
            'polylines' => $data['root']->getRoute()->getPolylines(),
        ];
    }

    protected function generate()
    {
        $this->group = new Group('report');

        $this->getDevicesQuery()->chunk(1000, function ($devices) {
            foreach ($devices as $device) {
                $data = $this->generateDevice($device);

                if ($this->getSkipBlankResults() && empty($data))
                    continue;

                $this->items[] = $data ? $data : [
                    'meta' => $this->getDeviceMeta($device),
                    'error' => trans('front.nothing_found_request')
                ];
            }
        });

        $this->totals = $this->getDataFromGroup($this->group, $this->group->stats()->keys());
    }

    protected function isEmptyResult($data)
    {
        return empty($data) || empty($data['root']->getStartPosition());
    }

    protected function generateDevice($device)
    {
        if ($error = $this->precheckError($device))
            return [
                'meta' => $this->getDeviceMeta($device),
                'error' => $error
            ];

        $data = $this->getDeviceHistoryData($device);

        if ($this->isEmptyResult($data))
            return null;

        return [
            'meta' => $this->getDeviceMeta($device) + $this->getHistoryMeta($data['root']),
            'table'  => $this->getTable($data),
            'totals' => $this->getTotals($data['root'])
        ];
    }

    protected function getHistoryMeta($group)
    {
        $metas = $this->metas('history');

        if (empty($metas)) {
            return [];
        }

        $result = [];

        foreach ($metas as $key => $meta) {
            $meta['key'] = $key;
            $meta['value'] = $this->getHistoryMetaValue($meta, $group);
            $result[$key] = $meta;
        }

        return $result;
    }

    protected function getHistoryMetaValue($meta, $group)
    {
        if(!$group->stats()->has($meta['attribute']))
            return '-';

        return $group->stats()->human($meta['attribute']);
    }

    /**
     * @return bool|int
     */
    protected function extendStart()
    {
        return false;
    }
}