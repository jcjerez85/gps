<?php

namespace Tobuli\Reports;

use Formatter;
use Tobuli\Entities\DeviceGroup;

abstract class DeviceReport extends Report
{
    protected $devicesQuery;

    public function setDevicesQuery($query)
    {
        $this->devicesQuery = $query;
    }

    public function getDevicesQuery()
    {
        $query = $this->devicesQuery;

        if (!$query->isJoined("user_device_pivot")) {
            $query->leftJoin('user_device_pivot', function ($join) {
                $join
                    ->on('user_device_pivot.device_id', '=', 'devices.id')
                    ->where('user_device_pivot.user_id', '=', $this->user->id);
            });
        }

        $query->addSelect('user_device_pivot.group_id AS group_id');

        return $query;
    }

    public function getDeviceNames()
    {
        return $this->getDevicesQuery()
            ->get()
            ->pluck('name')
            ->all();
    }

    /**
     * @return array
     */
    protected function defaultMetas()
    {
        return [
            'device.name' => trans('validation.attributes.device_id'),
        ];
    }

    protected function getDeviceMeta($device)
    {
        $metas = $this->metas('device');

        if (empty($metas)) {
            return [];
        }

        $result = [];

        foreach ($metas as $key => $meta) {
            $meta['key'] = $key;
            $meta['value'] = $this->getDeviceMetaValue($meta, $device);
            $result[$key] = $meta;
        }

        return $result;
    }

    protected function getDeviceMetaValue($meta, $device)
    {
        try {
            list($relation, $attribute) = explode('.', $meta['attribute'], 2);
        } catch (\Exception $e) {
            $relation = null;
            $attribute = $meta['attribute'];
        }

        if ($relation == 'custom_fields') {
            return $device->getCustomValue((int) $attribute);
        }

        if ($attribute == 'group_id') {
            return runCacheEntity(DeviceGroup::class, $device->group_id)->implode('title', ', ');
        }

        if ($attribute == 'expiration_date') {
            return Formatter::time()->human($device->expiration_date);
        }

        return $device->{$attribute};
    }

    protected function precheckError($device)
    {
        return null;
    }

    protected function generate()
    {
        $this->getDevicesQuery()->chunk(1000, function ($devices) {
            foreach ($devices as $device) {
                $data = $this->generateDevice($device);

                if ($this->getSkipBlankResults() && empty($data)) {
                    continue;
                }

                $this->items[] = $data ? $data : [
                    'meta' => $this->getDeviceMeta($device),
                    'error' => trans('front.nothing_found_request')
                ];
            }
        });
    }
}
