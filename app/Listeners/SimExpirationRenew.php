<?php

namespace App\Listeners;

use Exception;
use App\Events\Device\DeviceEventInterface;
use Tobuli\Services\SimBlockingService;

class SimExpirationRenew
{
    public function handle(DeviceEventInterface $event)
    {
        if (! settings('plugins._renew_sim_expiration.status')) {
            return;
        }

        $event->device->update([
            'sim_expiration_date' => $event->device->expiration_date,
        ]);
    }
}
