<?php namespace Tobuli\Helpers\Dashboard\Blocks;

use Carbon\Carbon;
use Tobuli\Lookups\Tables\DevicesIdleLookupTable;
use Tobuli\Lookups\Tables\DevicesInactiveLookupTable;
use Tobuli\Lookups\Tables\DevicesMoveLookupTable;
use Tobuli\Lookups\Tables\DevicesNeverConnectedLookupTable;
use Tobuli\Lookups\Tables\DevicesOfflineLookupTable;
use Tobuli\Lookups\Tables\DevicesParkLookupTable;
use Tobuli\Lookups\Tables\DevicesStopLookupTable;

class DeviceOverviewBlock extends Block
{
    protected function getName()
    {
        return 'device_overview';
    }

    protected function getContent()
    {
        $event_type = $this->user->getSettings("dashboard.blocks.device_overview.options.event_type");

        $events = $this->user
            ->events()
            ->with(['device', 'geofence'])
            ->latest()
            ->limit(7)
            ->where(function($query) use ($event_type){
                if ($event_type)
                    $query->where('type', $event_type);
            })
            ->get();

        $devices = $this->user->devices();

        return [
            'statuses' => $this->getStatuses($devices),
            'total'    => (clone $devices)->count(),
            'events'   => $events,
            'event_type' => $event_type,
        ];
    }

    protected function getStatuses($devices)
    {
        return [
            [
                'label' => trans('front.move'),
                'data' => (clone $devices)->move()->count(),
                'color' => '#52BE80',
                'url' => DevicesMoveLookupTable::route('index')
            ],
            [
                'label' => trans('front.idle'),
                'data' => (clone $devices)->idle()->count(),
                'color' => '#F7DC6F',
                'url' => DevicesIdleLookupTable::route('index')
            ],
            [
                'label' => trans('front.stop'),
                'data' => (clone $devices)->park()->count(),
                'color' => '#EC7063',
                'url' => DevicesParkLookupTable::route('index')
            ],
            [
                'label' => trans('front.offline'),
                'data' => (clone $devices)->offline()->count(),
                'color' => '#5DADE2',
                'url'  => DevicesOfflineLookupTable::route('index')
            ],
            [
                'label' => trans('front.inactive'),
                'data' => (clone $devices)->inactive()->count(),
                'color' => '#D7DBDD',
                'url' => DevicesInactiveLookupTable::route('index')
            ],
            [
                'label' => trans('front.never_connected'),
                'data' => (clone $devices)->neverConnected()->count(),
                'color' => '#AF7AC5',
                'url' => DevicesNeverConnectedLookupTable::route('index')
            ]
        ];
    }
}