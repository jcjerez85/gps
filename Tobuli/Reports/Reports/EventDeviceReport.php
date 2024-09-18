<?php

namespace Tobuli\Reports\Reports;

use Formatter;
use Illuminate\Support\Arr;
use Tobuli\Entities\Event;
use Tobuli\Reports\DeviceReport;

class EventDeviceReport extends DeviceReport
{
    const TYPE_ID = 8;

    protected $disableFields = ['geofences', 'speed_limit', 'stops'];

    public function __construct()
    {
        parent::__construct();

        $this->formats[] = 'csv';
    }

    public function getInputParameters(): array
    {
        $inputParameters = [
            \Field::multiSelect('event_types', trans('validation.attributes.type'))
                ->setOptions(Event::getTypeTitles()->pluck('title', 'type')->all())
                ->setValidation('array')
        ];

        if ($this->user->isManager()) {
            $inputParameters[] = \Field::select('subusers', trans('front.subusers'), 0)
                ->setOptions([0 => trans('global.no'), 1 => trans('global.yes')])
                ->setValidation('in:0,1');
        }

        return $inputParameters;
    }

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.events');
    }

    protected function beforeGenerate()
    {
        parent::beforeGenerate();

        if (!$this->getSkipBlankResults())
            return;

        $query = $this->getDevicesQuery()->whereHas('events', function($q){
            $q->whereBetween('time', [$this->date_from, $this->date_to]);

            $subusers = $this->parameters['subusers'] ?? false;

            if ($subusers && $this->user->isManager()) {
                $q->whereIn('user_id', function ($q) {
                    $q->select('users.id')
                        ->from('users')
                        ->where('users.id', $this->user->id)
                        ->orWhere('users.manager_id', $this->user->id);
                });
            } else {
                $q->where('user_id', $this->user->id);
            }

            if ($types = Arr::get($this->parameters, 'event_types'))
                $q->whereIn('type', $types);
        });

        $this->setDevicesQuery($query);
    }

    protected function generateDevice($device)
    {
        $query = Event::with(['geofence'])
            ->whereBetween('time', [$this->date_from, $this->date_to])
            ->where('device_id', $device->id)
            ->where(function($query){
                $subusers = $this->parameters['subusers'] ?? false;

                if ($subusers && $this->user->isManager()) {
                    $query->whereIn('user_id', function ($q) {
                        $q->select('users.id')
                            ->from('users')
                            ->where('users.id', $this->user->id)
                            ->orWhere('users.manager_id', $this->user->id);
                    });
                } else {
                    $query->where('user_id', $this->user->id);
                }
            })
            ->orderBy('time', 'asc');

        if ($types = Arr::get($this->parameters, 'event_types'))
            $query->whereIn('type', $types);

        $events = $query->get();

        if ($events->isEmpty())
            return null;

        $totals = [];

        foreach ($events as & $event) {
            $event['time']     = Formatter::time()->human($event['time']);
            $event['location'] = $this->getLocation((object)[
                'latitude' => $event['latitude'],
                'longitude' => $event['longitude']
            ]);
            $event['driver'] = Arr::get($event, 'additional.driver_name');

            if (empty($totals[$event['message']]))
                $totals[$event['message']] = [
                    'title' => trans('front.total') . ' ' . $event['message'],
                    'value' => 0,
                ];

            if (empty($this->totals[$event['message']]))
                $this->totals[$event['message']] = 0;

            $this->totals[$event['message']]++;
        }

        return [
            'meta' => $this->getDeviceMeta($device),
            'table' => [
                'rows' => $events
            ],
        ];
    }

    protected function toCSVData($file)
    {
        foreach ($this->getItems() as $item) {
            $metas = Arr::pluck($item['meta'], 'value');

            if (empty($item['table']['rows']))
                continue;

            foreach ($item['table']['rows'] as $row) {
                $values = $metas;
                $values[] = $row['time'];
                $values[] = $row['message'];
                $values[] = strip_tags($row['location']);

                fputcsv($file, $values);
            }
        }
    }
}