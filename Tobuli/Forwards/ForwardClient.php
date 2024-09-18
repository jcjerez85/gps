<?php


namespace Tobuli\Forwards;


use Tobuli\Entities\Device;
use Tobuli\Entities\TraccarPosition;

interface ForwardClient
{
    public function setConfig(array $config);
    public function process(Device $device, TraccarPosition $position);
    public function send();
}