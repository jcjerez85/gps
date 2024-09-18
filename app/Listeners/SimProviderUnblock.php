<?php

namespace App\Listeners;

use Exception;
use App\Events\Device\DeviceEventInterface;
use Tobuli\Services\SimBlockingService;

class SimProviderUnblock
{
    /**
     * @var SimBlockingService
     */
    private $blockingService;

    public function __construct()
    {
        $this->blockingService = new SimBlockingService();
    }

    public function handle(DeviceEventInterface $event)
    {
        if (! settings('plugins.sim_blocking.status')) {
            return;
        }

        try {
            $this->blockingService->unblock($event->device);
        } catch (Exception $e) {}
    }
}
