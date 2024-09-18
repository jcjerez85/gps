<?php

namespace Tobuli\Services;

use App\Exceptions\DeviceLimitException;
use App\Jobs\DeleteDatabaseTable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tobuli\Entities\Device;
use Tobuli\Entities\DeviceGroup;
use Tobuli\Entities\User;
use CustomFacades\Server;

class DeviceService
{
    /**
     * @var DeviceConfigService
     */
    private $configService;

    /**
     * @var CustomValuesService
     */
    private $customValueService;

    /**
     * @var DeviceUsersService
     */
    private $deviceUsersService;

    public function __construct(
        DeviceConfigService $configService,
        CustomValuesService $customValueService,
        DeviceUsersService $deviceUsersService
    )
    {
        $this->configService = $configService;
        $this->customValueService = $customValueService;
        $this->deviceUsersService = $deviceUsersService;
    }

    public function getDefaults()
    {
        $expirationDate = '0000-00-00 00:00:00';

        if (settings('plugins.create_only_expired_objects.status')) {
            $expirationOffset = settings('plugins.create_only_expired_objects.options.offset') ?? 0;
            $expirationOffsetType = settings('plugins.create_only_expired_objects.options.offset_type') ?? 'days';
            $expirationDate = date('Y-m-d H:i:s', strtotime(" + {$expirationOffset} {$expirationOffsetType}"));
        }

        return [
            'active'              => true,
            'imei'                => null,
            'group_id'            => 0,
            'timezone_id'         => null,
            'fuel_price'          => 0,
            'fuel_quantity'       => 0,
            'fuel_measurement_id' => 1,
            'device_icons_type'   => 'arrow',
            'min_fuel_fillings'   => 10,
            'min_fuel_thefts'     => 10,
            'min_moving_speed'    => 6,
            'tail_length'         => 5,
            'expiration_date'     => $expirationDate,
            'installation_date'   => settings('plugins.create_only_expired_objects.status')
                ? date('Y-m-d')
                : '0000-00-00',
            'icon_colors'         => [
                'moving'  => 'green',
                'stopped' => 'red',
                'offline' => 'red',
                'engine'  => 'yellow',
            ]
        ];
    }

    public function normalize($data)
    {
        if ($data['device_icons_type'] == 'arrow') {
            $data['icon_id'] = 0;
        }

        if (empty($data['fuel_quantity'])) {
            $data['fuel_quantity'] = 0;
        }

        if (empty($data['fuel_price'])) {
            $data['fuel_price'] = 0;
        }

        $data['gprs_templates_only'] = empty($data['gprs_templates_only']) ? 0 : 1;

        if ( ! empty($data['sim_activation_date']) && settings('plugins.annual_sim_expiration.status')) {
            $data['sim_expiration_date'] = Carbon::createFromTimestamp(strtotime($data['sim_activation_date']))
                ->addDays(settings('plugins.annual_sim_expiration.options.days'))
                ->toDateString();
        }

        return $data;
    }

    public function filterEditables(User $user, $data)
    {
        return onlyEditables(new Device(), $user, $data);
    }

    public function create($data)
    {
        if ($this->deviceUsersService->isLimitReached()) {
            throw new DeviceLimitException();
        }

        $data = array_merge($this->getDefaults(), $data);
        $data = $this->normalize($data);

        beginTransaction();

        try {
            $device = new Device($data);

            if (isset($data['kind'])) {
                $device->kind = $data['kind'];
            }

            $device->save();

            $customValues = $data['custom_fields'] ?? [];
            $this->customValueService->saveCustomValues($device, $customValues);

            if (!empty($data['user_id'])) {
                $this->syncUsersWithGod($device, $data['user_id']);
            }

            $device->createPositionsTable();

            commitTransaction();

        } catch (\Exception $e) {
            rollbackTransaction();

            throw $e;
        }

        return $device;
    }

    public function update(Device $device, $data)
    {
        //$data = $this->normalize($data);

        beginTransaction();

        try {
            $device->update($data);

            $customValues = $data['custom_fields'] ?? [];
            $this->customValueService->saveCustomValues($device, $customValues);

            $this->syncUsersWithGod($device, $data['user_id']);

            commitTransaction();

        } catch (\Exception $e) {
            rollbackTransaction();

            throw $e;
        }

        return $device;
    }

    public function addUser(Device $device, $user, $group = 0) {
        $user_id = $user instanceof User ? $user->id : (int)$user;
        $group_id = $group instanceof DeviceGroup ? $group->id : (int)$group;

        $device->users()->sync([
            $user_id => [
                'group_id' => empty($group_id) ? 0 : $group_id
            ]
        ], false);
    }

    public function removeUser(Device $device, $user) {
        $user_id = $user instanceof User ? $user->id : (int)$user;

        $device->users()->detach($user_id);
    }

    public function syncUsersWithGod(Device $device, $users) {
        $this->deviceUsersService->syncUsers($device, $users, true);
    }

    public function syncUsers(Device $device, $users) {
        $this->deviceUsersService->syncUsers($device, $users);
    }

    public function saveImage(Device $device, $image)
    {
        $path = Str::finish(Device::IMAGE_PATH, '/');

        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $this->deleteExistingImages($device, $path);

        $filename = $device->id.'.'.Str::random().'.'.$image->getClientOriginalExtension();

        if (! $image->move($path, $filename)) {
            throw new \Exception(trans('global.failed_file_save'));
        }
    }

    public function deleteImage(Device $device)
    {
        $path = Str::finish(Device::IMAGE_PATH, '/');

        if (! File::exists($path)) {
            return;
        }

        $this->deleteExistingImages($device, $path);
    }

    private function deleteExistingImages(Device $device, $path)
    {
        $existingFiles = File::glob("{$path}{$device->id}.*");

        if (! empty($existingFiles)) {
            File::delete($existingFiles);
        }
    }

    public function delete(Device $device)
    {
        beginTransaction();

        try {
            $device->users()->sync([]);
            $device->events()->delete();
            $device->sensors()->delete();
            $device->services()->delete();
            DB::table('user_drivers')->where('device_id', $device->id)->update(['device_id' => null]);

            if ($device->traccar) {
                $positionTable = $device->positions()->getRelated();
                $device->traccar->delete();
            }

            $device->delete();

            if (!empty($positionTable)) {
                dispatch(new DeleteDatabaseTable($positionTable->getTable(), $positionTable->getConnectionName()));
            }

            commitTransaction();
        } catch (\Exception $e) {
            rollbackTransaction();

            throw $e;
        }
    }
}
