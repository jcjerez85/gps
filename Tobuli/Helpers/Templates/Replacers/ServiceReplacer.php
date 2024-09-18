<?php

namespace Tobuli\Helpers\Templates\Replacers;

use Formatter;
use Tobuli\Entities\Device;
use Tobuli\Entities\DeviceService;

class ServiceReplacer extends Replacer
{
    /**
     * @param DeviceService $service
     * @return array
     */
    public function replacers($service)
    {
        $list = [
            'name',
            'description',
            'left',
            'expires',
        ];

        return $this->formatFields($service, $list);
    }

    /**
     * @return array
     */
    public function placeholders()
    {
        return [
            $this->formatKey('name')        => 'Service name',
            $this->formatKey('description') => 'Service description',
            $this->formatKey('left')        => 'Service left quantity',
            $this->formatKey('expires')     => 'Expires at quantity',
        ];
    }

    protected function leftField($service) {
        return $service->left_formated();
    }

    protected function expiresField($service) {
        return $service->expires_formated();
    }
}