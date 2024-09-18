<?php namespace Tobuli\Services\Commands;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Tobuli\Entities\User;
use Tobuli\Entities\UserGprsTemplate;
use Tobuli\Protocols\Manager as ProtocolsManager;

class DevicesCommandsGprsTemplates implements DevicesCommands
{
    /**
     * @var ProtocolsManager
     */
    private $protocolsManager;

    /**
     * @var User
     */
    private $user;

    public function __construct(User $user)
    {
        $this->protocolsManager = new ProtocolsManager();
        $this->user = $user;
    }

    /**
     * @param EloquentCollection $devices
     * @param bool $intersect
     * @return Collection
     */
    public function get(EloquentCollection $devices, bool $intersect) : Collection
    {
        $templates = UserGprsTemplate::userAccessible($this->user)
            ->byDevices($devices, $intersect)
            ->orderBy('title')
            ->get();

        $displayMessage = $this->user->perm('send_command', 'edit') && !$this->hasGprsTemplatesOnly($devices);

        $commands = $this->protocolsManager->protocol(null)
            ->getTemplateCommands($templates, $displayMessage);

        return collect($commands);
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