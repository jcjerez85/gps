<?php

namespace App\Http\Controllers\Frontend;

use App\Exceptions\PermissionException;
use App\Http\Controllers\Controller;
use CustomFacades\ModalHelpers\SensorModalHelper;
use CustomFacades\Repositories\UserRepo;
use CustomFacades\Validators\BeaconFormValidator;
use Tobuli\Entities\Device;
use Tobuli\Entities\DeviceGroup;
use Tobuli\Entities\DeviceIcon;
use Tobuli\Entities\SensorGroup;
use Tobuli\Entities\User;
use Tobuli\Services\DeviceService;

class BeaconsController extends Controller
{
    private $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        if (!settings('plugins.beacons.status')) {
            abort(404);
        }

        parent::__construct();

        $this->deviceService = $deviceService;
    }

    private function getCreateData(): array
    {
        $icons_type = [
            'arrow' => trans('front.arrow'),
            'rotating' => trans('front.rotating_icon'),
            'icon' => trans('front.icon'),
            'carros' => trans('Carros'),
            'jeepetas' => trans('Jeepetas'),
            'motos' => trans('Motos'),
            'pasolas' => trans('Pasolas'),
            'fordweel' => trans('Fordweel'),
            'construccion' => trans('Construcción'),
            'camiones' => trans('Camiones'),
            'camionetas' => trans('Camionetas'),
            'bus' => trans('Bus'),
            'cruzroja' => trans('Cruzrojas'),
            'bicicletas' => trans('Bicicletas'),
            'barcos' => trans('Barcos'),
            'aviones' => trans('Aviones'),
            'celulares' => trans('Celular'),
            'personas' => trans('Personas'),
            'animales' => trans('Animales'),
            'plantaselectricas' => trans('Plantaelectricas'),
            'otros' => trans('Otros')
        ];

        $device_icon_colors = [
            'green'  => trans('front.green'),
            'yellow' => trans('front.yellow'),
            'red'    => trans('front.red'),
            'blue'   => trans('front.blue'),
            'orange' => trans('front.orange'),
            'black'  => trans('front.black'),
        ];

        $device_icons = DeviceIcon::whereNull('user_id')
            ->orWhere('user_id', $this->user->id)
            ->orderBy('order', 'desc')
            ->orderBy('id', 'ASC')
            ->get();

        $device_icons_grouped = [];

        foreach ($device_icons as $dicon) {
            if ($dicon['type'] == 'arrow') {
                continue;
            }

            if (!array_key_exists($dicon['type'], $device_icons_grouped)) {
                $device_icons_grouped[$dicon['type']] = [];
            }

            $device_icons_grouped[$dicon['type']][] = $dicon;
        }

        $users = User::userAccessible($this->user)->orderBy('email', 'ASC');

        $device_groups = DeviceGroup::where('user_id', $this->user->id)
            ->pluck('title', 'id')
            ->prepend(trans('front.ungrouped'), '0')
            ->all();

        $sensor_groups = isAdmin()
            ? SensorGroup::orderBy('title', 'ASC')
                ->pluck('title', 'id')
                ->prepend(trans('front.none'), '0')
                ->all()
            : [];

        if ($this->api) {
            $device_groups = apiArray($device_groups);
            $sensor_groups = apiArray($sensor_groups);
            $users = $users->toArray();
        }

        return compact(
            'device_groups', 'sensor_groups', 'users', 'icons_type', 'device_icons_grouped', 'device_icon_colors'
        );
    }

    public function create()
    {
        $this->checkException('devices', 'store');

        $data = $this->getCreateData();

        return $this->api ? $data : view('front::Beacons.create')->with($data);
    }

    public function store()
    {
        $this->checkException('devices', 'store');

        if ($this->user->perm('custom_device_add', 'view'))
            throw new PermissionException();

        $this->normalize();

        BeaconFormValidator::validate('create', $this->data);
        
        $this->data['kind'] = Device::KIND_BEACON;

        return ['status' => 1, $this->deviceService->create($this->data)];
    }

    private function getEditData(Device $item): array
    {
        $users = UserRepo::getUsers($this->user);

        $sel_users = $item->users->pluck('id', 'id')->all();
        $group_id = null;

        if ($item->users->contains($this->user->id)) {
            foreach ($item->users as $deviceUser) {
                if ($deviceUser->id == $this->user->id) {
                    $group_id = $deviceUser->pivot->group_id;
                    break;
                }
            }
        }

        $icons_type = [
            'arrow' => trans('front.arrow'),
            'rotating' => trans('front.rotating_icon'),
            'icon' => trans('front.icon'),
            'carros' => trans('Carros'),
            'jeepetas' => trans('Jeepetas'),
            'motos' => trans('Motos'),
            'pasolas' => trans('Pasolas'),
            'fordweel' => trans('Fordweel'),
            'construccion' => trans('Construcción'),
            'camiones' => trans('Camiones'),
            'camionetas' => trans('Camionetas'),
            'bus' => trans('Bus'),
            'cruzroja' => trans('Cruzrojas'),
            'bicicletas' => trans('Bicicletas'),
            'barcos' => trans('Barcos'),
            'aviones' => trans('Aviones'),
            'celulares' => trans('Celular'),
            'personas' => trans('Personas'),
            'animales' => trans('Animales'),
            'plantaselectricas' => trans('Plantaelectricas'),
            'otros' => trans('Otros')
        ];

        $device_icon_colors = [
            'green'  => trans('front.green'),
            'yellow' => trans('front.yellow'),
            'red'    => trans('front.red'),
            'blue'   => trans('front.blue'),
            'orange' => trans('front.orange'),
            'black'  => trans('front.black'),
        ];

        $device_icons = DeviceIcon::whereNull('user_id')
            ->orWhere('user_id', $this->user->id)
            ->orderBy('order', 'desc')
            ->orderBy('id', 'ASC')
            ->get();

        $device_icons_grouped = [];

        

        foreach ($device_icons as $dicon) {
            
            //dd($dicon['type']);

            if ($dicon['type'] == 'arrow') {
                continue;
            }

            if (!array_key_exists($dicon['type'], $device_icons_grouped)) {
                $device_icons_grouped[$dicon['type']] = [];
            }

            $device_icons_grouped[$dicon['type']][] = $dicon;
        }

        $device_groups = DeviceGroup::where('user_id', $this->user->id)
            ->pluck('title', 'id')
            ->prepend(trans('front.ungrouped'), '0')
            ->all();

        $sensors = SensorModalHelper::paginated($item->id);

        $sensor_groups = isAdmin()
            ? SensorGroup::orderBy('title', 'ASC')
                ->pluck('title', 'id')
                ->prepend(trans('front.none'), '0')
                ->all()
            : [];

        if ($this->api) {
            $device_groups = apiArray($device_groups);
            $users = $users->toArray();
        }

        $device_id = $item->id;

        return compact(
            'item', 'device_id', 'device_groups', 'sensor_groups', 'device_icons', 'sensors', 'users', 'sel_users',
            'group_id', 'icons_type', 'device_icons_grouped', 'device_icon_colors'
        );
    }

    public function edit($id = null)
    {
        $beacon = Device::kindBeacon()->find($id);

        $this->checkException('devices', 'edit', $beacon);

        $data = $this->getEditData($beacon);

        return $this->api ? $data : view('front::Beacons.edit')->with($data);
    }

    public function update($id = null)
    {
        $beacon = Device::kindBeacon()->find($id);

        $this->checkException('devices', 'edit', $beacon);

        $this->normalize();

        BeaconFormValidator::validate('update', $this->data, $beacon->id);

        $this->deviceService->update($beacon, $this->data);

        return ['status' => 1, 'id' => $beacon->id];
    }

    private function normalize()
    {
        $this->data['group_id'] = empty($this->data['group_id']) ? null : $this->data['group_id'];
    }
}
