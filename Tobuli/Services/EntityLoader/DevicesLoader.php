<?php


namespace Tobuli\Services\EntityLoader;


use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use Tobuli\Entities\DeviceGroup;
use Tobuli\Entities\User;

class DevicesLoader extends EnityLoader
{
    /**
     * @var User
     */
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;

        $this->setQueryItems(
            $this->user->accessibleDevices()
                ->clearOrdersBy()
                ->orderBy('devices.name', 'asc')
        );

        $this->setRequestKey('devices');
    }

    protected function transform($device)
    {
        $item = new \stdClass();

        $item->id = $device->id;
        $item->name = $device->name;

        $group_id = $device->group_id;
        $group_id = is_null($device->pivot) ? $group_id : $device->pivot->group_id;
        $item->group_id = empty($group_id) ? 0 : $group_id;

        return $item;
    }
}