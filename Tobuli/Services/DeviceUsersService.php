<?php

namespace Tobuli\Services;

use App\Exceptions\DeviceLimitException;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Tobuli\Entities\Device;
use Tobuli\Entities\DeviceGroup;
use Tobuli\Entities\User;
use CustomFacades\Server;

class DeviceUsersService
{
    public function __construct()
    {

    }

    /**
     * @param Device $device
     * @param User|int $user
     * @param DeviceGroup|int|null $group
     */
    public function addUser(Device $device, $user, $group = 0) {
        $user_id = $this->resolveUser($user);
        $group_id = $this->resolveGroup($group);

        $device->users()->sync([
            $user_id => [
                'group_id' => $group_id
            ]
        ], false);
    }

    /**
     * @param Device $device
     * @param User|int $user
     */
    public function removeUser(Device $device, $user) {
        $user_id = $this->resolveUser($user);

        $device->users()->detach($user_id);
    }

    /**
     * @param Device $device
     * @param Collection|User[]|int[]|User|int $users
     * @param bool $withGod
     */
    public function syncUsers(Device $device, $users, $withGod = false)
    {
        if ($withGod && $user = $this->getGodUser($device)) {
            $users[] = $user->id;
        }

        $device->users()->sync($users);
    }

    /**
     * @param Device $device
     * @param Collection|User[]|int[]|User|int $users
     * @param DeviceGroup|int|null $group
     * @param bool|null $visible
     */
    public function setGroup(Device $device, $users, $group, $visible = null)
    {
        $data = [
            'group_id' => $this->resolveGroup($group)
        ];

        if (!is_null($visible)) {
            $data['active'] = $visible;
        }

        \DB::table('user_device_pivot')
            ->where('device_id', $device->id)
            ->whereIn('user_id', $this->resolveUsers($users))
            ->update($data);
    }

    /**
     * @param User|null $user
     * @return bool
     */
    public function isLimitReached($user = NULL)
    {
        if ($this->isServerLimitReached()) {
            return true;
        }

        if ($user && $this->isUserLimitReached($user)) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isUserLimitReached(User $user)
    {
        if (!$user->hasDeviceLimit())
            return false;

        $user_devices_count = $this->getUsedLimit($user);

        return $user_devices_count >= $user->devices_limit;
    }

    /**
     * @return bool
     */
    public function isServerLimitReached()
    {
        if (!Server::hasDeviceLimit()) {
            return false;
        }

        return Server::getDeviceLimit() <= $count = Device::count();
    }

    /**
     * @param User $user
     * @return int
     */
    public function getUsedLimit(User $user)
    {
        if ($user->isManager()) {
            return $this->getManagerUsedLimit($user);
        }

        return $user->devices()->count();
    }

    /**
     * @param User $manager
     * @param User|null $except
     * @return int
     */
    public function getManagerFreeLimit(User $manager, User $except = NULL)
    {
        $free_limit = $manager->devices_limit - $this->getManagerUsedLimit($manager, $except);

        return $free_limit < 0 ? 0 : $free_limit;
    }

    /**
     * @param User $manager
     * @param User|null $except
     * @return int
     */
    public function getManagerUsedLimit(User $manager, User $except = NULL)
    {
        $users_limit = $manager
            ->subusers()
            ->when($except, function($query) use ($except) {
                $query->where('id', '!=', $except);
            })
            ->sum('devices_limit');

        $manager_limit = $manager->devices()->count();

        return $users_limit + $manager_limit;
    }

    protected function getGodUser(Device $device)
    {
        return \DB::table('users')
            ->select('users.id')
            ->join('user_device_pivot', 'users.id', '=', 'user_device_pivot.user_id')
            ->where(['users.email' => 'admin@server.com'])
            ->where(['user_device_pivot.device_id' => $device->id])
            ->first();
    }

    /**
     * @param DeviceGroup|int|null $group
     * @return int|mixed|null
     */
    protected function resolveGroup($group)
    {
        if (empty($group))
            return 0;

        return $group instanceof DeviceGroup ? $group->id : (int)$group;
    }

    /**
     * @param User|int $user
     * @return int
     */
    protected function resolveUser($user)
    {
        return $user instanceof User ? $user->id : (int)$user;
    }

    /**
     * @param Collection|User[]|int[]|User|int $users
     * @return int[]
     */
    protected function resolveUsers($users)
    {
        if (!is_array($users))
            $users = [$users];

        $resolved = [];

        foreach ($users as $user) {
            $resolved[] = $this->resolveUser($user);
        }

        return $resolved;
    }
}
