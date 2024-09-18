<?php

namespace Tobuli\Helpers\Templates\Builders;

use Formatter;
use Tobuli\Entities\Event;
use Tobuli\Helpers\Templates\Replacers\DeviceReplacer;
use Tobuli\Helpers\Templates\Replacers\DriverReplacer;

class EventTemplate extends TemplateBuilder
{
    /**
     * @param Event $event
     * @return array
     */
    protected function variables($event)
    {
        $deviceReplacer = (new DeviceReplacer())->setUser($this->user)->setPrefix('device');
        $driverReplacer = (new DriverReplacer())->setUser($this->user)->setPrefix('driver');

        return array_merge([
            '[preview]'   => googleMapUrl($event->latitude, $event->longitude),
            '[alert]'     => $event->alert->name ?? null,
            '[event]'     => $event->message,
            '[address]'   => $event->getAddress(),
            '[position]'  => $event->latitude . ', ' . $event->longitude,
            '[heading]'   => $event->course,
            '[lat]'       => $event->latitude,
            '[lon]'       => $event->longitude,

            '[altitude]'  => Formatter::altitude()->human($event->altitude),
            '[speed]'     => Formatter::speed()->human($event->speed),
            '[time]'      => Formatter::time()->human($event->time),

            '[geofence]'  => $event->geofence ? $event->geofence->name : null,
        ],
            $deviceReplacer->replacers($event->device),
            $driverReplacer->replacers($event->device->driver));
    }

    /**
     * @return array
     */
    protected function placeholders()
    {
        return array_merge([
            '[event]'    => 'Event title',
            '[alert]'    => 'Alert title',
            '[geofence]' => 'Geofence name',
            '[address]'  => 'Address',
            '[position]' => 'Position/Point',
            '[lat]'      => 'Latitude',
            '[lon]'      => 'Longitude',
            '[heading]'  => 'Heading/Course',
            '[preview]'  => 'Google map link',
            '[altitude]' => 'Altitude',
            '[speed]'    => 'Speed',
            '[time]'     => 'Time',
        ],
            (new DeviceReplacer())->setPrefix('device')->placeholders(),
            (new DriverReplacer())->setPrefix('driver')->placeholders());
    }
}