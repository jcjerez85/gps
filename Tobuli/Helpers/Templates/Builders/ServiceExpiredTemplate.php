<?php

namespace Tobuli\Helpers\Templates\Builders;

use Tobuli\Entities\DeviceService;
use Tobuli\Helpers\Templates\Replacers\DeviceReplacer;
use Tobuli\Helpers\Templates\Replacers\ServiceReplacer;

class ServiceExpiredTemplate extends TemplateBuilder
{
    /**
     * @param DeviceService $service
     * @return array
     */
    protected function variables($service)
    {
        $deviceReplacer = (new DeviceReplacer())->setUser($this->user)->setPrefix('device');
        $serviceReplacer = (new ServiceReplacer())->setUser($this->user)->setPrefix('service');

        return array_merge(
            $serviceReplacer->replacers($service),
            $deviceReplacer->replacers($service->device)
        );
    }

    /**
     * @return array
     */
    protected function placeholders()
    {
        $deviceReplacer = (new DeviceReplacer())->setUser($this->user)->setPrefix('device');
        $serviceReplacer = (new ServiceReplacer())->setUser($this->user)->setPrefix('service');

        return array_merge(
            $serviceReplacer->placeholders(),
            $deviceReplacer->placeholders()
        );
    }
}