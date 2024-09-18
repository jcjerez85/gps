<?php

namespace Tobuli\Services\SimProviders;

use Tobuli\Entities\Device;

interface SimProviderInterface
{
    public function block(Device $device);

    public function unblock(Device $device);
}
