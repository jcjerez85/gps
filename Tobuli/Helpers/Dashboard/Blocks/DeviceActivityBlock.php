<?php namespace Tobuli\Helpers\Dashboard\Blocks;

class DeviceActivityBlock extends Block
{
    protected function getName()
    {
        return 'device_activity';
    }

    protected function getContent()
    {
        $all = $this->user->devices()->count();

        if (empty($all))
            return null;

        $online = $this->user->devices()->online()->count();
        $offline = $all - $online;

        return [
            // 'online'  => round($this->calcPercentage($all, $online), 1),
            // 'offline' => round($this->calcPercentage($all, $offline), 1),
            'online'  => round($online,0),
            'offline' => round($offline,0),
        ];
    }

    private function calcPercentage($all, $part)
    {
        if (empty($all))
            return 0;

        return (($part) / $all) * 100;
    }
}