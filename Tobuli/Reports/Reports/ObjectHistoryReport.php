<?php namespace Tobuli\Reports\Reports;

use Formatter;
use Illuminate\Database\QueryException;
use Tobuli\Reports\DeviceReport;

class ObjectHistoryReport extends DeviceReport
{
    const TYPE_ID = 25;

    protected $validation = ['devices' => 'same_protocol'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.object_history');
    }

    public static function isEnabled()
    {
        return settings('plugins.object_history_report.status');
    }

    protected function processPosition($position, & $parameters, $sensors)
    {
        foreach($position->parameters as $key => $value)
        {
            if (empty($key))
                continue;

            if (in_array($key, $parameters))
                continue;

            $parameters[] = $key;
        }

        return [
            'server_time'=> Formatter::time()->human($position->server_time),
            'time'       => Formatter::time()->human($position->time),
            'speed'      => Formatter::speed()->human($position->speed),
            'altitude'   => Formatter::altitude()->human($position->altitude),
            'latitude'   => $position->latitude,
            'longitude'  => $position->longitude,
            'location'   => $this->getLocation($position, $this->getAddress($position)),
            'parameters' => $position->parameters,
            'sensors'    => $sensors->mapWithKeys(function($sensor) use ($position) {
                return [$sensor->id => $sensor->getValueFormated($position->other, false)];
            })
        ];
    }

    protected function generateDevice($device)
    {
        $parameters = [];
        $rows = [];
        $sensors = $device->sensors->filter(function($sensor){
            return $sensor['add_to_history'];
        });

        try {
            $device->positions()
                ->orderliness('asc')
                ->whereBetween('time', [$this->date_from, $this->date_to])
                ->chunk(2000,
                    function ($positions) use (& $rows, & $parameters, $sensors) {
                        foreach ($positions as $position) {
                            $rows[] = $this->processPosition($position, $parameters, $sensors);
                        }
                    });
        } catch (QueryException $e) {}

        if (empty($rows))
            return [
                'meta'  => $this->getDeviceMeta($device),
                'error' => trans('front.nothing_found_request'),
                'parameters' => $parameters,
                'sensors'    => $sensors
            ];

        return [
            'meta'       => $this->getDeviceMeta($device),
            'table'      => [
                'rows' => $rows,
            ],
            'parameters' => $parameters,
            'sensors'    => $sensors
        ];
    }
}