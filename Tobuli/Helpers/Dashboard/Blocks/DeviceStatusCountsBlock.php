<?php namespace Tobuli\Helpers\Dashboard\Blocks;

use Tobuli\Lookups\Tables\DevicesExpiredLookupTable;
use Tobuli\Lookups\Tables\DevicesLookupTable;
use Tobuli\Lookups\Tables\DevicesNeverConnectedLookupTable;
use Tobuli\Lookups\Tables\DevicesOfflineLookupTable;
use Tobuli\Lookups\Tables\DevicesOnlineLookupTable;

class DeviceStatusCountsBlock extends Block
{
    protected function getName()
    {
        return 'device_status_counts';
    }

    protected function getContent()
    {
        return [
            'statuses' => [
                [
                    'label' => trans('front.count'),
                    'data' => $this->user->devices()->count(),
                    'url'  => DevicesLookupTable::route('index')
                ],
                [
                    'label' => trans('global.online'),
                    'data' => $this->user->devices()->online()->count(),
                    'url'  => DevicesOnlineLookupTable::route('index')
                ],
                [
                    'label' => trans('front.offline'),
                    'data' => $this->user->devices()->offline()->count(),
                    'url'  => DevicesOfflineLookupTable::route('index')
                ],
                [
                    'label' => trans('front.never_connected'),
                    'data' => $this->user->devices()->neverConnected()->count(),
                    'url' => DevicesNeverConnectedLookupTable::route('index')
                ],
                [
                    'label' => trans('front.expired'),
                    'data' => $this->user->devices()->expired()->count(),
                    'url' => DevicesExpiredLookupTable::route('index')
                ],
            ]
        ];

    }
}