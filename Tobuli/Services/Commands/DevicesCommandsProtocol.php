<?php namespace Tobuli\Services\Commands;

use Illuminate\Support\Collection;
use Tobuli\Protocols\Manager as ProtocolsManager;
use Illuminate\Database\Eloquent\Collection AS EloquentCollection;

class DevicesCommandsProtocol implements DevicesCommands
{
    /**
     * @var ProtocolsManager
     */
    private $protocolsManager;


    public function __construct()
    {
        $this->protocolsManager = new ProtocolsManager();
    }

    /**
     * @param EloquentCollection $devices
     * @param bool $intersect
     * @return Collection
     */
    public function get(EloquentCollection $devices, bool $intersect) : Collection
    {
        $bag = collect();

        if ($intersect && $this->hasGprsTemplatesOnly($devices)) {
            return $bag;
        }

        $protocols = $devices->filter(function($device) {
            return ! $device->gprs_templates_only;
        })->pluck('protocol')->unique();

        foreach ($protocols as $protocol) {;
            $bag->push(
                collect($this->protocolsManager->protocol($protocol)->getCommands())->keyBy('type')
            );
        }

        if ($intersect) {
            $commands = $bag->pop();
            while ($next = $bag->pop()) {
                $commands = $next->intersectByKeys($commands);
            }
        } else {
            $commands = $bag->collapse();
        }

        if (empty($commands)) {
            $commands = collect();
        }

        return $commands->unique('type')->sortBy('title')->values();
    }

    /**
     * @param Collection $devices
     * @return bool
     */
    protected function hasGprsTemplatesOnly($devices)
    {
        return $devices->contains(function($device) {
            return $device->gprs_templates_only;
        });
    }
}