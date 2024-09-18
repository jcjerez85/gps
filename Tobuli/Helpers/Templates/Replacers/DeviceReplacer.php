<?php

namespace Tobuli\Helpers\Templates\Replacers;

use Formatter;
use Tobuli\Entities\Device;

class DeviceReplacer extends Replacer
{
    /**
     * @param Device $device
     * @return array
     */
    public function replacers($device)
    {
        $sensorReplacer = (new DeviceSensorReplacer())->setUser($this->user)->setPrefix($this->prefix . '.sensor');

        $list = [
            'name',
            'imei',
            'sim_number',
            'device_model',
            'plate_number',
            'vin',
            'registration_number',
            'object_owner',
            'additional_notes',
            'expiration_date',
            'protocol',
        ];

        return array_merge($this->formatFields($device, $list), $sensorReplacer->replacers($device));
    }

    /**
     * @return array
     */
    public function placeholders()
    {
        return array_merge([
            $this->formatKey('name')                => 'Device name',
            $this->formatKey('imei')                => 'Device imei',
            $this->formatKey('sim_number')          => 'Device sim number',
            $this->formatKey('device_model')        => 'Device model',
            $this->formatKey('plate_number')        => 'Device plate number',
            $this->formatKey('vin')                 => 'Device vin',
            $this->formatKey('object_owner')        => 'Device owner',
            $this->formatKey('protocol')            => 'Device protocol',
            $this->formatKey('expiration_date')     => 'Device expiration date',
            $this->formatKey('registration_number') => 'Device registration number',
            $this->formatKey('additional_notes')    => 'Device additional notes',
        ],
            (new DeviceSensorReplacer())->setUser($this->user)->setPrefix($this->prefix . '.sensor')->placeholders()
        );
    }

    protected function expirationDateField($device)
    {
        return Formatter::time()->human($device->expiration_date);
    }
}