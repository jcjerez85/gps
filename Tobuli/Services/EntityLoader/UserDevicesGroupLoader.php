<?php


namespace Tobuli\Services\EntityLoader;


use Tobuli\Entities\DeviceGroup;
use Tobuli\Entities\User;

class UserDevicesGroupLoader extends DevicesGroupLoader
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;

        $this->setQueryItems(
            $this->user->devices()
                ->getQuery()
                ->clearOrdersBy()
                ->orderBy('group_id', 'asc')
                ->orderBy('devices.name', 'asc')
        );

        $this->setQueryGroups(
            DeviceGroup::where('user_id', $this->user->id)
        );
    }
}