<?php namespace Tobuli\Reports\Reports;

use Formatter;
use Illuminate\Database\QueryException;
use Tobuli\Reports\DeviceReport;

class AutomonCustomReport extends DeviceReport
{
    const TYPE_ID = 27;

    public static function isEnabled()
    {
        return settings('plugins.automon_report.status');
    }

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return 'Automon ' . trans('global.custom');
    }

    protected function generateDevice($device)
    {
        $this->journeys = [];
        $this->journey = null;
        $this->current_state = null;
        $this->last_state = null;
        $this->last_item = null;
        $this->item_changed = null;
        $this->repeat = 0;
        $this->tmp = null;

        try {
            $device->positions()
                ->orderliness('asc')
                ->whereBetween('time', [$this->date_from, $this->date_to])
                ->chunk(2000,
                    function ($positions) {
                        foreach ($positions as $position) {
                            $this->processPosition($position);
                        }
                    });
        } catch (QueryException $e) {}

        if ( $this->journey ) {
            $this->journeys[] = $this->journey;
        }

        foreach ($this->journeys as $i => $journey)
        {
            $this->journeys[$i]['distance']      = Formatter::distance()->human($journey['distance']);
            $this->journeys[$i]['move_duration'] = Formatter::duration()->human($journey['move_duration']);
            $this->journeys[$i]['stop_duration'] = Formatter::duration()->human($journey['stop_duration']);

            if ( !empty($journey['begin']) && !empty($journey['end'])) {
                $this->journeys[$i]['duration'] = Formatter::duration()->human($journey['duration']);
            } else {
                $this->journeys[$i]['duration'] = null;
            }
        }

        if (empty($this->journeys))
            return null;

        return [
            'meta'   => $this->getDeviceMeta($device),
            'device' => [
                'time' => Formatter::time()->human(date('Y-m-d H:i:s')),
                'address' => $this->getAddress($device->positionTraccar()),
            ],
            'journeys' => $this->journeys
        ];
    }

    protected function processPosition($position)
    {
        $state = $this->getState($position->other);
        $position->motion = $state['motion'];
        $position->state = $state['state'];

        if (empty($this->last_item))
            $this->last_item = $position;

        if ( is_null($position->state) )
            return;

        if ( ! in_array($position->state, [0,1]) )
            return;


        if ( ! $this->journey) {
            $this->journey = [
                'state' => $position->state,
                'distance' => 0,
                'duration' => 0,
                'move_duration' => 0,
                'stop_duration' => 0,
            ];
        }


        $distance = getDistance($position->latitude, $position->longitude, $this->last_item->latitude, $this->last_item->longitude);
        $time = strtotime($position->time) - strtotime($this->last_item->time);

        $move_duration = $position->motion == 'true' ? $time : 0;
        $stop_duration = $position->motion != 'true' ? $time : 0;

        $this->journey['distance'] += $distance;
        $this->journey['duration'] += $time;
        $this->journey['move_duration'] += $move_duration;
        $this->journey['stop_duration'] += $stop_duration;


        if ($this->last_item->state == $position->state && $position->motion == 'false')
        {
            ++$this->repeat;

            if (!empty($this->tmp)) {
                $this->tmp['distance'] += $distance;
                $this->tmp['duration'] += $time;
                $this->tmp['move_duration'] += $move_duration;
                $this->tmp['stop_duration'] += $stop_duration;
            }
        } else {
            $this->repeat = 0;
            $this->item_changed = $position;

            $this->tmp = [
                'distance' => $distance,
                'duration' => $time,
                'move_duration' => $move_duration,
                'stop_duration' => $stop_duration,
            ];
        }

        $this->last_item = $position;

        if ($this->repeat < 5) {
            return;
        }

        if ($this->journey['state'] == $position->state) {
            return;
        }

        // 1 -> 0 journy end
        if ( $this->item_changed->state == 0 )
        {
            $this->journey['end'] = [
                'timestamp' => strtotime($this->item_changed->time),
                'time' => Formatter::time()->human($this->item_changed->time),
                'address' => $this->getAddress($this->item_changed),
            ];

            if (!empty($this->tmp)) {
                $this->journey['distance'] -= $this->tmp['distance'];
                $this->journey['duration'] -= $this->tmp['duration'];
                $this->journey['move_duration'] -= $this->tmp['move_duration'];
                $this->journey['stop_duration'] -= $this->tmp['stop_duration'];
            }

            $this->journeys[] = $this->journey;
            $this->journey = null;
        }

        // 0 -> 1 journy begin
        if ( $this->item_changed->state == 1 )
        {
            $this->journeys[] = $this->journey;

            $this->journey = [
                'state' => $this->item_changed->state,
                'distance' => 0,
                'duration' => 0,
                'move_duration' => 0,
                'stop_duration' => 0,
                'begin' => [
                    'timestamp' => strtotime($this->item_changed->time),
                    'time' => Formatter::time()->human($this->item_changed->time),
                    'address' => $this->getAddress($this->item_changed),
                ]
            ];

            if (!empty($this->tmp)) {
                $this->journey = array_merge($this->journey, $this->tmp);
            }
        }

        $this->tmp = null;
    }

    protected function getState($other)
    {
        $motion = null;
        preg_match('/<motion>(.*?)<\/motion>/s', $other, $matches);
        if ( isset($matches[1]) )
            $motion = $matches[1];

        $state = null;
        preg_match('/<adc1>(.*?)<\/adc1>/s', $other, $matches);
        if ( isset($matches[1]))
            $state = $matches[1] > 10000 ? 1 : 0;

        return [
            'state' => $state,
            'motion' => $motion,
        ];
    }

}