<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\TemperaturesMinMax;
use Tobuli\History\DeviceHistory;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceSensorDataReport;

class TemperatureWithStatsReport extends DeviceSensorDataReport
{
    const TYPE_ID = 77;

    protected $disableFields = ['geofences', 'speed_limit', 'stops'];
    protected $formats = ['html'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    protected function getActionsList()
    {
        return [TemperaturesMinMax::class];
    }

    protected function getStats(DeviceHistory $history): array
    {
        $temperatureMin = trans('front.temperature') . ' ' . trans('validation.attributes.min_value');
        $temperatureMax = trans('front.temperature') . ' ' . trans('validation.attributes.max_value');

        $root = $history->root();

        $groupData = $this->getDataFromGroup($root, ['start_at', 'end_at']);

        $data = [];
        $totals = [
            trans('validation.attributes.started_at') => $groupData['start_at'],
            trans('validation.attributes.ended_at') => $groupData['end_at'],
        ];

        foreach ($history->sensors as $sensor) {
            $data[$sensor->id] = $totals;

            if ($root->hasStat('temperature_max_' . $sensor->id)) {
                $data[$sensor->id][$temperatureMax] = $root->getStat('temperature_max_' . $sensor->id)->human();
                $data[$sensor->id][$temperatureMin] = $root->getStat('temperature_min_' . $sensor->id)->human();
            }
        }

        return $data;
    }

    public function title()
    {
        return trans('front.temperature_with_summary');
    }

    protected function getSensorTypes()
    {
        return ['temperature_calibration', 'temperature'];
    }
}