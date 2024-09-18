<?php namespace ModalHelpers;

use CustomFacades\Repositories\UserDriverRepo;
use CustomFacades\Repositories\UserRepo;
use CustomFacades\Validators\UserDriverFormValidator;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Tobuli\Entities\UserDriver;
use Tobuli\Exceptions\ValidationException;

use Tobuli\Entities\Device;

class UserDriverModalHelper extends ModalHelper
{
    public function get()
    {
        $this->checkException('drivers', 'view');

        $drivers = UserDriver::userAccessible($this->user)
            ->with('device')
            ->search(request()->input('search'))
            ->toPaginator(
                request()->input('limit', 10),
                request()->input('sorting.sort_by', 'id'),
                request()->input('sorting.sort', 'desc')
            );

        if ($this->api) {
            $drivers = $drivers->toArray();
            $drivers['url'] = route('api.get_user_drivers');
        }

        return compact('drivers');
    }

    public function createData()
    {
        $this->checkException('drivers', 'create');

        $devices = $this->user->devices;

        return compact('devices');
    }

    public function create()
    {
        $this->checkException('drivers', 'store');

        $this->validate('create');

        $driver = UserDriverRepo::create($this->data + ['user_id' => $this->user->id]);

        $driver->devices()->sync(Arr::get($this->data, 'devices', []));

        $setCurrent = Arr::get($this->data, 'current');
        $device_id = Arr::get($this->data, 'device_id');

        if ($setCurrent && $device = $this->user->devices()->find($device_id)) {
            $device->changeDriver($driver);
        }

        return ['status' => 1, 'item' => $driver];
    }

    public function editData()
    {
        $id = array_key_exists('user_driver_id', $this->data) ? $this->data['user_driver_id'] : request()->route('user_drivers');

        $item = UserDriverRepo::find($id);

        $this->checkException('drivers', 'edit', $item);

        $devices = $this->user->devices;

        return compact('item', 'devices');
    }

    public function edit()
    {
        $driver = UserDriverRepo::find($this->data['id']);

        $this->checkException('drivers', 'update', $driver);

        $this->validate('update', $driver->id);

        $setCurrent = Arr::get($this->data, 'current');
        $device_id = Arr::get($this->data, 'device_id');

        if ($setCurrent) {
            if ($device_id && $device = $this->user->devices()->find($device_id)) {
                $driver->changeDevice($device);
            } else {
                $driver->changeDevice(null);
            }
        }

        UserDriverRepo::update($driver->id, $this->data);
        $driver->devices()->sync(Arr::get($this->data, 'devices', []));

        return ['status' => 1];
    }

    public function editField($id)
    {
        $driver = UserDriverRepo::find($id);

        $this->checkException('drivers', 'update', $driver);

        $this->validate('silentUpdate', $driver->id);

        UserDriverRepo::update($driver->id, $this->data);

        return ['status' => 1];
    }

    private function validate($type, $id = null)
    {
        UserDriverFormValidator::validate($type, $this->data, $id);
    }

    public function doDestroy($id)
    {
        $item = UserDriverRepo::find($id);

        $this->checkException('drivers', 'remove', $item);

        return compact('item');
    }

    public function destroy()
    {
        $id = array_key_exists('user_driver_id', $this->data) ? $this->data['user_driver_id'] : $this->data['id'];
        $item = UserDriverRepo::find($id);

        $this->checkException('drivers', 'remove', $item);

        UserDriverRepo::delete($id);

        return ['status' => 1];
    }

    public function activityLog($id)
    {
        $driver = UserDriverRepo::find($id);

        $this->checkException('drivers', 'view', $driver);

        $filters = $this->data['filter'] ?? [];
        $startFrom = $filters['start_from'] ?? null;
        $startTo = $filters['start_to'] ?? null;

        $query = DB::query()
            ->select(['devices.name AS device', 'date AS start'])
            ->selectSub(function (Builder $query) use ($id) {
                $query->select('date')
                    ->from('user_driver_position_pivot AS end')
                    ->whereColumn('main.device_id', 'end.device_id')
                    ->where(function (Builder $query) {
                        $query->whereColumn('main.driver_id', '!=', 'end.driver_id')
                            ->orWhereNull('end.driver_id');
                    })
                    ->whereColumn('main.date', '<', 'end.date')
                    ->orderBy('end.date', 'ASC')
                    ->limit(1);
            }, 'end')
            ->from('user_driver_position_pivot AS main')
            ->leftJoin('devices', 'devices.id', 'main.device_id')
            ->where('main.driver_id', $id)
            ->orderBy('main.date', 'DESC');

        if ($startFrom) {
            $query->where('main.date', '>=', \Formatter::time()->reverse($startFrom));
        }

        if ($startTo) {
            $query->where('main.date', '<=', \Formatter::time()->reverse($startTo));
        }

        $logs = $query->paginate();

        return compact('driver', 'logs');
    }
}