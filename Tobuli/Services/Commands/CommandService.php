<?php namespace Tobuli\Services\Commands;


use Tobuli\Entities\Device;
use Tobuli\Entities\User;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection AS EloquentCollection;

class CommandService
{
    /**
     * @var User
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param EloquentCollection|Device|Device[] $devices
     * @param false $intersect
     * @return Collection
     * @throws \Exception
     */
    public function getGprsCommands($devices, $intersect = false)
    {
        $devices = $this->normalizeCollection($devices)->load(['traccar']);

        $list = $this->merge(collect([
            (new DevicesCommandsProtocol())->get($devices, $intersect),
            (new DevicesCommandsGprsTemplates($this->user))->get($devices, $intersect),
        ]));

        return $this->filterCommands($list);
    }

    /**
     * @param EloquentCollection|Device|Device[] $devices
     * @param false $intersect
     * @return Collection
     * @throws \Exception
     */
    public function getSmsCommands($devices, $intersect = false)
    {
        $devices = $this->normalizeCollection($devices);

        $list = $this->merge(collect([
            (new DevicesCommandsSmsCustom($this->user))->get($devices, $intersect),
            (new DevicesCommandsSmsTemplates($this->user))->get($devices, $intersect),
        ]));

        return $this->filterCommands($list);
    }

    /**
     * @param EloquentCollection|Device|Device[] $devices
     * @return EloquentCollection
     * @throws \Exception
     */
    protected function normalizeCollection($devices) : EloquentCollection
    {
        switch(true) {
            case $devices instanceof EloquentCollection:
                return $devices;
            case $devices instanceof Device:
                return new EloquentCollection([$devices]);
            case is_array($devices):
                return new EloquentCollection($devices);
        }

        throw new \Exception('Devices type not support');
    }

    /**
     * @param Collection $list
     * @return Collection
     */
    protected function merge(Collection $list)
    {
        return $list->collapse();
    }

    /**
     * @param \Illuminate\Support\Collection $list
     * @return \Illuminate\Support\Collection
     */
    protected function filterCommands(Collection $list)
    {
        if (!$this->user->perm('send_command', 'edit')) {
            $list = $list->filter(function ($command) {
                return $command['type'] != 'custom';
            });
        }

        return collect($list->values()->all());
    }
}