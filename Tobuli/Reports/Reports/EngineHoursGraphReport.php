<?php

namespace Tobuli\Reports\Reports;

use Carbon\Carbon;
use Formatter;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\EngineHours;
use Tobuli\History\Actions\FirstEngine;
use Tobuli\History\Actions\GroupDaily;
use Tobuli\History\Actions\GroupQuarterHour;
use Tobuli\History\Actions\LastEngine;
use Tobuli\Reports\DeviceHistoryReport;

class EngineHoursGraphReport extends DeviceHistoryReport
{
    const TYPE_ID = 64;

    protected $disableFields = ['geofences', 'speed_limit'];
    protected $disabledFormats = ['xls', 'xlsx'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return
            trans('front.engine_hours') . ' ' .
            trans('validation.attributes.daily') . ' ' .
            trans('front.graph');
    }

    protected function getActionsList()
    {
        return [
            FirstEngine::class,
            LastEngine::class,
            EngineHours::class,
            DriveStop::class,

            GroupDaily::class,
            GroupQuarterHour::class,
        ];
    }

    protected function getTable($data)
    {
        $rows = [];

        foreach ($data['groups']->all() as $group)
        {
            if ($group->getKey() !== GroupQuarterHour::KEY) {
                $data = $this->getDataFromGroup($group, [
                    'group_key',
                    'drive_distance',
                    'drive_duration',
                    'engine_hours',
                    'last_engine_time',
                    'first_engine_time',
                    'date',
                ]);

                $last  = $group->stats()->get('last_engine')->get();
                $first = $group->stats()->get('first_engine')->get();

                $data = array_merge($data, [
                    'duration' => Formatter::duration()->human(
                        strtotime($last->time ?? null) - strtotime($first->time ?? null)
                    ),
                    'first_engine_address' => $this->getAddress($first),
                    'last_engine_address' => $this->getAddress($last),
                ]);

                $data['duration'] = Formatter::duration()->human(
                    strtotime($last->time ?? null) - strtotime($first->time ?? null)
                );

                $rows[$group->getKey()] = empty($rows[$group->getKey()])
                    ? $data
                    : array_merge($rows[$group->getKey()], $data);

            } elseif ($group->stats()->has('engine_hours') && $engine_hours = $group->stats()->get('engine_hours')->get()) {
                $quarter = Carbon::parse($group->getStartPosition()->quarter);
                $date = $quarter->toDateString();
                $hour = $quarter->hour;
                $min = $quarter->hour * 60 + $quarter->minute;

                $engine_hours = $engine_hours / 60;

                if (empty($rows[$date]['hourly'][$hour]))
                    $rows[$date]['hourly'][$hour] = 0;

                $rows[$date]['hourly'][$hour] += $engine_hours;
                $rows[$date]['hourly'][$hour] = min($rows[$date]['hourly'][$hour], 60);
                $rows[$date]['quarter'][$min] = min($engine_hours * 100 / 15, 100);
            }
        }

        if ($this->getSkipBlankResults()) {
            $rows = array_filter($rows, function($row, $date) {
                return !(empty($row['hourly']) && empty($row['quarter']));
            }, ARRAY_FILTER_USE_BOTH);
        }

        return [
            'rows'   => $rows,
            'totals' => [],
        ];
    }

    protected function isEmptyResult($data)
    {
        if (empty($data))
            return true;

        if (!$data['root']->stats()->has('engine_hours'))
            return true;

        return empty($data['root']->stats()->get('engine_hours')->get());
    }
}