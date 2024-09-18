<?php namespace Tobuli\Helpers\Templates\Builders;

use Tobuli\Entities\Device;
use Tobuli\Entities\Event;
use Tobuli\Helpers\Templates\Replacers\DeviceReplacer;

class ExpiringDeviceTemplate extends TemplateBuilder
{
    /**
     * @param Event $event
     * @return array
     */
    protected function variables($event)
    {
        $deviceReplacer = (new DeviceReplacer())->setUser($this->user)->setPrefix('device');

        return array_merge([
            '[days]'   => settings('main_settings.expire_notification.days_before'),
        ], $deviceReplacer->replacers($event->device));
    }

    protected function placeholders()
    {
        return array_merge([
            '[days]'   => 'Days before expiration',
        ], (new DeviceReplacer())->setPrefix('device')->placeholders());
    }

}