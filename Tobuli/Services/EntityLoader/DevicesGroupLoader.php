<?php


namespace Tobuli\Services\EntityLoader;


use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use Tobuli\Entities\DeviceGroup;
use Tobuli\Entities\User;

class DevicesGroupLoader extends EnityGroupLoader
{
    /**
     * @var User
     */
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;

        $this->setQueryItems(
            $this->user->accessibleDevicesWithGroups()
                ->clearOrdersBy()
                ->orderBy('user_device_pivot.group_id', 'asc')
                ->orderBy('devices.name', 'asc')
        );

        $this->setQueryGroups(
            DeviceGroup::where('user_id', $this->user->id)
        );
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