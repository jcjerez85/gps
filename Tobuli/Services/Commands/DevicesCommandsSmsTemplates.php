<?php namespace Tobuli\Services\Commands;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Tobuli\Entities\User;
use Tobuli\Entities\UserSmsTemplate;
use Tobuli\Protocols\Manager as ProtocolsManager;

class DevicesCommandsSmsTemplates implements DevicesCommands
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
        $templates = UserSmsTemplate::userAccessible($this->user)
            ->byDevices($devices, $intersect)
            ->orderBy('title')
            ->get();

        $commands = $this->protocolsManager->protocol(null)
            ->getTemplateCommands($templates, true);

        return collect($commands);
    }
}