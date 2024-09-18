<?php

namespace ModalHelpers;

use App\Exceptions\DeviceLimitException;
use App\Exceptions\PermissionException;
use App\Transformers\ApiV1\DeviceFullJsonTransformer;
use App\Transformers\ApiV1\DeviceFullTransformer;
use App\Transformers\Device\DeviceMapFullTransformer;
use App\Transformers\Device\DeviceMapTransformer;
use Carbon\Carbon;
use CustomFacades\ModalHelpers\SensorModalHelper;
use CustomFacades\ModalHelpers\ServiceModalHelper;
use CustomFacades\Repositories\DeviceGroupRepo;
use CustomFacades\Repositories\DeviceIconRepo;
use CustomFacades\Repositories\DeviceRepo;
use CustomFacades\Repositories\DeviceSensorRepo;
use CustomFacades\Repositories\EventRepo;
use CustomFacades\Repositories\SensorGroupRepo;
use CustomFacades\Repositories\TimezoneRepo;
use CustomFacades\Repositories\UserRepo;
use CustomFacades\Repositories\DeviceCameraRepo;
use CustomFacades\Validators\DeviceConfiguratorFormValidator;
use CustomFacades\Validators\DeviceFormValidator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tobuli\Entities\ApnConfig;
use Tobuli\Entities\Device;
use Tobuli\Entities\DeviceConfig;
use Tobuli\Entities\DeviceType;
use Tobuli\Entities\User;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Helpers\SMS\SMSGatewayManager;
use Tobuli\Services\AlertSoundService;
use Tobuli\Services\CustomValuesService;
use Tobuli\Services\DeviceConfigService;
use Formatter;
use Tobuli\Services\DeviceSensorsService;
use Tobuli\Services\DeviceService;
use Tobuli\Services\DeviceUsersService;
use Tobuli\Services\FractalSerializers\WithoutDataArraySerializer;
use Tobuli\Services\FractalTransformerService;
use Illuminate\Support\Facades\Storage;

class DeviceModalHelper extends ModalHelper
{
    private $device_fuel_measurements = [];
    private $configService;
    private $customValueService;
    private $deviceService;

    /**
     * @var DeviceUsersService
     */
    private $deviceUsersService;

    /**
     * @var FractalTransformerService
     */
    private $transformerService;

    public function __construct(
        DeviceConfigService $configService,
        DeviceService $deviceService,
        CustomValuesService $customValueService,
        FractalTransformerService $transformerService
    ) {
        parent::__construct();

        $this->device_fuel_measurements = [
            [
                'id' => 1,
                'title' => trans('front.l_km'),
                'fuel_title' => trans('front.liters'),
                'distance_title' => '100 ' . strtolower(trans('front.kilometers')),
                'cost_title' => strtolower(trans('front.liter')),
            ],
            [
                'id' => 2,
                'title' => trans('front.mpg'),
                'fuel_title' => trans('front.miles'),
                'distance_title' => strtolower(trans('front.gallon')),
                'cost_title' => strtolower(trans('front.gallon')),
            ],
            [
                'id' => 3,
                'title' => trans('front.kwh_km'),
                'fuel_title' => trans('front.kwhs'),
                'distance_title' => strtolower(trans('front.kilometer')),
                'cost_title' => strtolower(trans(trans('front.kwh'))),
            ],
            [
                'id' => 4,
                'title' => trans('front.l_h'),
                'fuel_title' => trans('front.liters'),
                'distance_title' => strtolower(trans('front.hour')),
                'cost_title' => strtolower(trans('front.liter')),
            ],
            [
                'id' => 5,
                'title' => trans('front.km_l'),
                'fuel_title' => trans('front.kilometers'),
                'distance_title' => strtolower(trans('front.liter')),
                'cost_title' => strtolower(trans('front.liter')),
            ],
        ];

        $this->configService = $configService;
        $this->deviceService = $deviceService;
        $this->customValueService = $customValueService;
        $this->transformerService = $transformerService->setSerializer(WithoutDataArraySerializer::class);
        $this->deviceUsersService = new DeviceUsersService();
    }

    public function createData()
    {
        $perm = request()->get('perm');

        if ($perm == null || ($perm != null && $perm != 1)) {
            if ($perm != null && $perm != 2) {
                if ($this->deviceUsersService->isLimitReached($this->user)) {
                    throw new DeviceLimitException();
                }
            }

            $this->checkException('devices', 'create');
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

        $fuel_detect_sec_after_stop_options = [
            '' => '-- ' . trans('admin.select') . ' --',
            60 => '1 ' . trans('front.minute_short'),
            120 => '2 ' . trans('front.minute_short'),
            180 => '3 ' . trans('front.minute_short'),
            300 => '5 ' . trans('front.minute_short'),
        ];

        $device_icons = DeviceIconRepo::getMyIcons($this->user->id);
        $device_icons_grouped = [];

        foreach ($device_icons as $dicon) {
            //dd($dicon['type'] );
            if ($dicon['type'] == 'arrow') {
                continue;
            }

            if (!array_key_exists($dicon['type'], $device_icons_grouped)) {
                $device_icons_grouped[$dicon['type']] = [];
            }

            $device_icons_grouped[$dicon['type']][] = $dicon;
        }

        $users = UserRepo::getUsers($this->user);

        $device_groups = DeviceGroupRepo::getWhere(['user_id' => $this->user->id])
            ->pluck('title', 'id')
            ->prepend(trans('front.ungrouped'), '0')
            ->all();

        $expiration_date_select = [
            '0000-00-00 00:00:00' => trans('front.unlimited'),
            '1' => trans('validation.attributes.expiration_date')
        ];

        $timezones = TimezoneRepo::order()
            ->pluck('title', 'id')
            ->prepend(trans('front.default'), '0')
            ->all();

        $timezones_arr = [];

        foreach ($timezones as $key => &$timezone) {
            $timezone = str_replace('UTC ', '', $timezone);

            if ($this->api) {
                array_push($timezones_arr, ['id' => $key, 'value' => $timezone]);
            }
        }

        $sensor_groups = [];

        if (isAdmin()) {
            $sensor_groups = SensorGroupRepo::getWhere([], 'title')
                ->pluck('title', 'id')
                ->prepend(trans('front.none'), '0')
                ->all();
        }

        $device_fuel_measurements = $this->device_fuel_measurements;

        $device_fuel_measurements_select =  [];

        foreach ($device_fuel_measurements as $dfm) {
            $device_fuel_measurements_select[$dfm['id']] = $dfm['title'];
        }

        if ($this->api) {
            $timezones = $timezones_arr;
            $device_groups = apiArray($device_groups);
            $sensor_groups = apiArray($sensor_groups);
            $users = $users->toArray();
        }

        $device_configs = [];
        $apn_configs = [];

        if ($this->user->able('configure_device')) {
            $device_configs = DeviceConfig::active()
                ->get()
                ->pluck('fullName', 'id');
            $apn_configs = ApnConfig::active()
                ->get()
                ->pluck('name', 'id');
        }

        $device_types = DeviceType::active()->get()->pluck('title', 'id')->prepend(trans('front.none'), '');

        return compact(
            'device_groups',
            'sensor_groups',
            'device_fuel_measurements',
            'device_icons',
            'users',
            'timezones',
            'expiration_date_select',
            'device_fuel_measurements_select',
            'icons_type',
            'device_icons_grouped',
            'device_icon_colors',
            'device_configs',
            'apn_configs',
            'device_types',
            'fuel_detect_sec_after_stop_options'
        );
    }

    public function create()
    {
        $fileUrl = null; // Initialize $fileUrl
        //dd($this->data['file']);
        $this->checkException('devices', 'store');

        if ($this->deviceUsersService->isLimitReached($this->user)) {
            throw new DeviceLimitException();
        }

        $this->data['imei'] = isset($this->data['imei']) ? trim($this->data['imei']) : null;
        $this->data['group_id'] = !empty($this->data['group_id']) ? $this->data['group_id'] : null;
        $this->data['timezone_id'] = empty($this->data['timezone_id']) ? NULL : $this->data['timezone_id'];
        $this->data['snap_to_road'] = isset($this->data['snap_to_road']);
        $this->data['fuel_quantity'] = empty($this->data['fuel_quantity']) ? 0 : $this->data['fuel_quantity'];

        if (!empty($this->data['sim_activation_date']) && settings('plugins.annual_sim_expiration.status')) {
            $this->data['sim_expiration_date'] = Carbon::createFromTimestamp(strtotime($this->data['sim_activation_date']))
                ->addDays(settings('plugins.annual_sim_expiration.options.days'))
                ->toDateString();
        }

        if (array_key_exists('enable_expiration_date', $this->data) && empty($this->data['enable_expiration_date'])) {
            $this->data['expiration_date'] = '0000-00-00 00:00:00';
        }

        if (isset($this->data['forward']['ip'])) {
            $this->data['forward']['ip'] = implode(';', semicol_explode($this->data['forward']['ip'])); // clear empty
        }

        $this->data = onlyEditables(new Device(), $this->user, $this->data);

        if (!empty($this->data['expiration_date']) && $this->data['expiration_date'] != '0000-00-00 00:00:00') {
            $this->data['expiration_date'] = Formatter::time()->reverse($this->data['expiration_date']);
        }

        if (settings('plugins.create_only_expired_objects.status') && !$this->user->perm('device.expiration_date', 'edit')) {
            $expirationOffset = settings('plugins.create_only_expired_objects.options.offset') ?? 0;
            $expirationOffsetType = settings('plugins.create_only_expired_objects.options.offset_type') ?? 'days';
            $expirationDate = date('Y-m-d H:i:s', strtotime(" + {$expirationOffset} {$expirationOffsetType}"));

            $this->data['expiration_date'] = $expirationDate;
            $this->data['installation_date'] = date('Y-m-d');
        }

        if (array_key_exists('device_icons_type', $this->data) && $this->data['device_icons_type'] == 'arrow') {
            $this->data['icon_id'] = 0;
        }

        $this->usersReachedLimit();

        DeviceFormValidator::validate('create', $this->data);

        $item_ex = DeviceRepo::whereImei($this->data['imei']);

        if (!empty($item_ex)) {
            throw new ValidationException(['imei' => str_replace(':attribute', trans('validation.attributes.imei_device'), trans('validation.unique'))]);
        }

        $this->setAbleUsers();

        beginTransaction();

        try {

            //Guardar el Achrivo en el server
            //FILE UPDATE
            if (!empty($this->data['file']) && $this->data['file'] instanceof \Illuminate\Http\UploadedFile) {
                $file = $this->data['file'];

                // Almacenar el archivo en el directorio public/uploads
                $filePath = $file->store('public/uploads');

                // Obtener la URL pública del archivo
                $fileUrl = asset(Storage::url($filePath));
                //echo 'Archivo subido correctamente. Ruta: ' . $fileUrl;
            }
            $item['file_path'] = $fileUrl;

            $this->data['file_path'] = $fileUrl;

            if (empty($this->data['user_id'])) {
                $this->data['user_id'] = ['0' => $this->user->id];
            }

            if (empty($item_ex)) {
                if (empty($this->data['fuel_quantity'])) {
                    $this->data['fuel_quantity'] = 0;
                }

                if (empty($this->data['fuel_price'])) {
                    $this->data['fuel_price'] = 0;
                }

                $this->data['gprs_templates_only'] = (array_key_exists('gprs_templates_only', $this->data) && $this->data['gprs_templates_only'] == 1 ? 1 : 0);

                $device_icon_colors = [
                    'green'  => trans('front.green'),
                    'yellow' => trans('front.yellow'),
                    'red'    => trans('front.red'),
                    'blue'   => trans('front.blue'),
                    'orange' => trans('front.orange'),
                    'black'  => trans('front.black'),
                ];

                $this->data['icon_colors'] = [
                    'moving' => 'green',
                    'stopped' => 'red',
                    'offline' => 'red',
                    'engine' => 'yellow',
                ];

                if (array_key_exists('icon_moving', $this->data) && array_key_exists($this->data['icon_moving'], $device_icon_colors)) {
                    $this->data['icon_colors']['moving'] = $this->data['icon_moving'];
                }

                if (array_key_exists('icon_stopped', $this->data) && array_key_exists($this->data['icon_stopped'], $device_icon_colors)) {
                    $this->data['icon_colors']['stopped'] = $this->data['icon_stopped'];
                }

                if (array_key_exists('icon_offline', $this->data) && array_key_exists($this->data['icon_offline'], $device_icon_colors)) {
                    $this->data['icon_colors']['offline'] = $this->data['icon_offline'];
                }

                if (array_key_exists('icon_engine', $this->data) && array_key_exists($this->data['icon_engine'], $device_icon_colors)) {
                    $this->data['icon_colors']['engine'] = $this->data['icon_engine'];
                }

                $device = DeviceRepo::create($this->data);

                $this->deviceSyncUsers($device);
                $this->createSensors($device, Arr::get($this->data, 'device_type_id'));

                $device->createPositionsTable();
            } else {
                DeviceRepo::update($item_ex->id, $this->data);
                $device = DeviceRepo::find($item_ex->id);
                $device->users()->sync($this->data['user_id']);
            }

            if ($this->user->can('edit', $device, 'custom_fields')) {
                $customValues = $this->data['custom_fields'] ?? null;
                $this->customValueService->saveCustomValues($device, $customValues);
            }

            commitTransaction();
        } catch (\Exception $e) {
            rollbackTransaction();

            throw $e;
        }

        if ($this->data['configure_device'] ?? false) {
            $this->configureDevice($device);
        }

        return ['status' => 1, 'id' => $device->id,];
    }

    public function editData()
    {        
        $device_id = $this->data['id']
            ?? request()->route('id')
            ?? $this->data['device_id']
            ?? null;

        $item = Device::find($device_id);


        $this->checkException($item && $item->isBeacon() ? 'beacons' : 'devices', 'edit', $item);

        $users = UserRepo::getUsers($this->user);

        $sel_users = $item->users->pluck('id', 'id')->all();
        $group_id = null;

        $timezone_id = $item->timezone_id;
        //$timezone_id = null;
        if ($item->users->contains($this->user->id)) {
            foreach ($item->users as $item_user) {
                if ($item_user->id == $this->user->id) {
                    $group_id = $item_user->pivot->group_id;
                    //$timezone_id = $item_user->pivot->timezone_id;
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

        $fuel_detect_sec_after_stop_options = [
            '' => '-- ' . trans('admin.select') . ' --',
            60 => '1 ' . trans('front.minute_short'),
            120 => '2 ' . trans('front.minute_short'),
            180 => '3 ' . trans('front.minute_short'),
            300 => '5 ' . trans('front.minute_short'),
        ];

        $device_icons = DeviceIconRepo::getMyIcons($this->user->id);
        //dd($device_icons[0]);

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

        $device_groups = DeviceGroupRepo::getWhere(['user_id' => $this->user->id])
            ->pluck('title', 'id')
            ->prepend(trans('front.ungrouped'), '0')
            ->all();

        $sensors = SensorModalHelper::paginated($item->id);
        $services = ServiceModalHelper::paginated($item->id);
        $expiration_date_select = [
            '0000-00-00 00:00:00' => trans('front.unlimited'),
            '1' => trans('validation.attributes.expiration_date')
        ];


        $has_sensors = DeviceSensorRepo::getWhereInWhere([
            'odometer',
            'acc',
            'engine',
            'ignition',
            'engine_hours'
        ], 'type', ['device_id' => $item->id]);

        $detects = parseSensorsSelect($item->sensors);
        $engine_hours = $detects['detect_engine_hours'];
        $detect_engine = $detects['detect_engine'];
        $detect_distance = $detects['detect_distance'];
        $detect_speed = $detects['detect_speed'];
        unset($item->sensors);

        $timezones = TimezoneRepo::order()
            ->pluck('title', 'id')
            ->prepend(trans('front.default'), '0')
            ->all();

        foreach ($timezones as $key => &$timezone) {
            $timezone = str_replace('UTC ', '', $timezone);
        }

        $sensor_groups = [];

        if (isAdmin()) {
            $sensor_groups = SensorGroupRepo::getWhere([], 'title')
                ->pluck('title', 'id')
                ->prepend(trans('front.none'), '0')
                ->all();
        }

        $device_fuel_measurements = $this->device_fuel_measurements;

        $device_fuel_measurements_select =  [];

        foreach ($device_fuel_measurements as $dfm) {
            $device_fuel_measurements_select[$dfm['id']] = $dfm['title'];
        }

        if ($this->api) {
            $device_groups = apiArray($device_groups);
            $timezones = apiArray($timezones);
            $users = $users->toArray();
        }

        $device_cameras = DeviceCameraRepo::searchAndPaginate(['filter' => ['device_id' => $device_id]], 'id', 'desc', 10);

        $device_types = DeviceType::active()->get()->pluck('title', 'id')->prepend(trans('front.none'), '');

        return compact(
            'device_id',
            'engine_hours',
            'detect_engine',
            'detect_distance',
            'detect_speed',
            'device_groups',
            'sensor_groups',
            'item',
            'device_fuel_measurements',
            'device_icons',
            'sensors',
            'services',
            'expiration_date_select',
            'timezones',
            'users',
            'sel_users',
            'group_id',
            'timezone_id',
            'device_fuel_measurements_select',
            'icons_type',
            'device_icons_grouped',
            'device_icon_colors',
            'device_cameras',
            'device_types',
            'fuel_detect_sec_after_stop_options'
        );
    }

    public function edit()
    {
        $this->data['id'] = $this->data['id']
            ?? $this->data['device_id']
            ?? null;

        //dd($this->data['id']);

        $item = Device::find($this->data['id']);
        
        //dd($item);
        $fileUrl = null; // Initialize $fileUrl

        //FILE UPDATE
        if (!empty($this->data['file']) && $this->data['file'] instanceof \Illuminate\Http\UploadedFile) {
            $file = $this->data['file'];

            // Almacenar el archivo en el directorio public/uploads
            $filePath = $file->store('public/uploads');

            // Obtener la URL pública del archivo
            $fileUrl = asset(Storage::url($filePath));
            //echo 'Archivo subido correctamente. Ruta: ' . $fileUrl;
            $item['file_path'] = $fileUrl;
            $item->save();
        }
       

        $this->checkException('devices', 'update', $item);
        //dd($item->isBeacon());

        if ($item->isBeacon())
            throw new ValidationException(['id' => 'Device is kind of beacon.']);

        if (!empty($this->data['sim_activation_date']) && settings('plugins.annual_sim_expiration.status')) {
            $this->data['sim_expiration_date'] = Carbon::createFromTimestamp(strtotime($this->data['sim_activation_date']))
                ->addDays(settings('plugins.annual_sim_expiration.options.days'))
                ->toDateString();
        }

        if (array_key_exists('enable_expiration_date', $this->data) && empty($this->data['enable_expiration_date'])) {
            $this->data['expiration_date'] = '0000-00-00 00:00:00';
        }

        if (!empty($this->data['expiration_date']) && $this->data['expiration_date'] != '0000-00-00 00:00:00') {
            $this->data['expiration_date'] = Formatter::time()->reverse($this->data['expiration_date']);
        }

        if (isset($this->data['forward']['ip'])) {
            $this->data['forward']['ip'] = implode(';', semicol_explode($this->data['forward']['ip'])); // clear empty
        }


        $this->checkException('devices', 'update', $item);

        $this->data = onlyEditables($item, $this->user, $this->data);

        $this->data['group_id'] = !empty($this->data['group_id']) ? $this->data['group_id'] : null;
        $this->data['snap_to_road'] = isset($this->data['snap_to_road']);
        $this->data['fuel_quantity'] = empty($this->data['fuel_quantity']) ? 0 : $this->data['fuel_quantity'];

        $this->setAbleUsers($item);

        $prev_timezone_id = $item->timezone_id;

        if (!empty($this->data['timezone_id']) && $this->data['timezone_id'] != 57 && $item->isCorrectUTC()) {
            throw new ValidationException(['timezone_id' => 'Device time is correct. Check your timezone Setup -> Main -> Timezone']);
        }

        if (array_key_exists('device_icons_type', $this->data) && $this->data['device_icons_type'] == 'arrow') {
            $this->data['icon_id'] = 0;
        }

        $this->usersReachedLimit($item);

        //echo $item->id;
        //dd( $this->data);

        DeviceFormValidator::validate('update', $this->data, $item->id);
        //dd( $this->data);

        beginTransaction();

        try {
            $device_icon_colors = [
                'green'  => trans('front.green'),
                'yellow' => trans('front.yellow'),
                'red'    => trans('front.red'),
                'blue'   => trans('front.blue'),
                'orange' => trans('front.orange'),
                'black'  => trans('front.black'),
            ];

            $this->data['icon_colors'] = [
                'moving' => 'green',
                'stopped' => 'red',
                'offline' => 'red',
                'engine' => 'yellow',
            ];

            if (array_key_exists('icon_moving', $this->data) && array_key_exists($this->data['icon_moving'], $device_icon_colors)) {
                $this->data['icon_colors']['moving'] = $this->data['icon_moving'];
            }

            if (array_key_exists('icon_stopped', $this->data) && array_key_exists($this->data['icon_stopped'], $device_icon_colors)) {
                $this->data['icon_colors']['stopped'] = $this->data['icon_stopped'];
            }

            if (array_key_exists('icon_offline', $this->data) && array_key_exists($this->data['icon_offline'], $device_icon_colors)) {
                $this->data['icon_colors']['offline'] = $this->data['icon_offline'];
            }

            if (array_key_exists('icon_engine', $this->data) && array_key_exists($this->data['icon_engine'], $device_icon_colors)) {
                $this->data['icon_colors']['engine'] = $this->data['icon_engine'];
            }



            //DTRefactor
            //DeviceRepo::update($item->id, $this->data);
            //dd($this->data);
            $item->update($this->data);

            $this->deviceSyncUsers($item);
            $this->createSensors($item);

            if ($this->user->can('edit', $item, 'custom_fields')) {
                $customValues = $this->data['custom_fields'] ?? null;
                $this->customValueService->saveCustomValues($item, $customValues);
            }

            commitTransaction();
        } catch (\Exception $e) {
            rollbackTransaction();

            throw $e;
        }

        if ($prev_timezone_id != $item->timezone_id) {
            $item->applyPositionsTimezone();
        }

        return ['status' => 1, 'id' => $item->id];
    }

    public function resetAppUuid(int $id): array
    {
        /** @var Device $item */
        $item = Device::findOrFail($id);

        $this->checkException('devices', 'edit', $item);

        $item->app_uuid = null;
        $success = $item->save();

        return ['status' => (int)$success, 'id' => $item->id];
    }

    public function destroy()
    {
        $imei = $this->data['imei'] ?? null;

        if (!is_null($imei)) {
            $item = DeviceRepo::whereImei($imei);
        } else {
            $device_id = $this->data['id'] ?? $this->data['device_id'] ?? null;
            $item = DeviceRepo::find($device_id);
        }

        $this->checkException('devices', 'remove', $item);

        $this->deviceService->delete($item);

        return ['status' => 1, 'id' => $item->id, 'deleted' => 1];
    }

    public function detach()
    {
        $device_id = $this->data['id']
            ?? $this->data['device_id']
            ?? null;

        $item = DeviceRepo::find($device_id);

        $this->checkException('devices', 'own', $item);

        $item->users()->detach($this->user->id);

        return ['status' => 1];
    }

    public function changeActive()
    {
        $validator = Validator::make($this->data, [
            'id' => 'required_without:group_id',
            'group_id' => 'required_without:id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors());
        }

        $active = (isset($this->data['active']) && filter_var($this->data['active'], FILTER_VALIDATE_BOOLEAN)) ? 1 : 0;

        $query = DB::table('user_device_pivot')
            ->where('user_id', $this->user->id);

        if (array_key_exists('group_id', $this->data)) {
            $group_id = $this->data['group_id'];
            $group_id = is_array($group_id) ? $group_id : [$group_id];

            array_walk($group_id, function (&$item) {
                $item = intval($item);
            });

            $query->whereIn('group_id', $group_id);
        } else {
            if ($id = $this->data['id']) {
                $id = is_array($id) ? $id : [$id];
                $query->whereIn('device_id', $id);
            }
        }

        $updateCount = $query->update([
            'active' => $active
        ]);

        return ['status' => $updateCount ? 1 : 0];
    }

    public function itemsJson()
    {
        $this->checkException('devices', 'view');

        $time = time();

        if (empty($this->data['time'])) {
            $this->data['time'] = $time - 5;
        }

        $this->data['time'] = intval($this->data['time']);

        $query = empty($this->data['id']) ? $this->user->devices() : $this->user->accessibleDevices();

        $items = $query
            ->filter($this->data)
            ->updatedAfter(date('Y-m-d H:i:s', $this->data['time']))
            ->clearOrdersBy()
            ->get();

        $transformer = $this->api ? DeviceFullJsonTransformer::class : DeviceMapTransformer::class;

        $items = $this->transformerService->collection($items, $transformer)->toArray();


        $eventTime = ($time - $this->data['time'] > 300) ? $time - 300 : $this->data['time'];
        $events = EventRepo::getHigherTime($this->user->id, $eventTime, $this->data['id'] ?? null);

        $events = $events->map(function ($event) {
            $_event = $event->toArray();

            $_event['sound'] = Arr::get($event, 'alert.notifications.sound.active', false)
                ? AlertSoundService::getAsset(Arr::get($event, 'alert.notifications.sound.input'))
                : null;
            $_event['color'] = Arr::get($event, 'alert.notifications.color.input');
            $_event['delay'] = Arr::get($event, 'alert.notifications.auto_hide.active', true) ? 10 : 0;
            $_event['time'] = Formatter::time()->convert($_event['time']);
            $_event['speed'] = Formatter::speed()->format($_event['speed']);
            $_event['altitude'] = Formatter::altitude()->format($_event['altitude']);
            $_event['message'] = $event->title;
            $_event['device_name'] = $event->device->name ?? null;

            unset($_event['geofence'], $_event['device'], $_event['alert']);

            $_event['device'] = $event->device ? [
                'id'   => $event->device->id,
                'name' => htmlentities($event->device->name)
            ] : null;

            if ($event->geofence) {
                $_event['geofence'] = [
                    'id' => $event->geofence->id,
                    'name' => htmlentities($event->geofence->name)
                ];
            }

            return $_event;
        });

        return ['items' => $items, 'events' => $events, 'time' => $time, 'version' => Config::get('tobuli.version')];
    }


    private function reachedUserDeviceLimit($user, $exceed_only = false)
    {
        if (is_null($user->devices_limit)) {
            return false;
        }

        $count = $this->deviceUsersService->getUsedLimit($user);

        if ($exceed_only) {
            return $count > $user->devices_limit;
        }

        return $count >= $user->devices_limit;
    }

    # Sensor groups
    private function createSensors($device, $device_type_id = null)
    {
        if (!isAdmin()) {
            return;
        }

        if (empty($this->data['sensor_group_id']) && $device_type_id && $deviceType = DeviceType::find($device_type_id)) {
            $this->data['sensor_group_id'] = $deviceType->sensor_group_id;
        }

        if (!isset($this->data['sensor_group_id'])) {
            return;
        }

        $sensorsService = new DeviceSensorsService();
        $sensorsService->addSensorGroup($device, $this->user, $this->data['sensor_group_id']);
    }

    public function deviceSyncUsers($device)
    {
        if (isset($this->data['user_id'])) {
            $this->deviceUsersService->syncUsers($device, $this->data['user_id'], !$this->user->isGod());
        }

        $this->deviceUsersService->setGroup($device, $this->user, $this->data['group_id']);
    }

    private function usersReachedLimit($device = null)
    {
        if (empty($this->data['user_id'])) {
            return;
        }

        $userIds = is_array($this->data['user_id']) ? $this->data['user_id'] : [$this->data['user_id']];

        $users = User::whereIn('id', $userIds)
            ->whereNotNull('devices_limit')
            ->with(['devices' => function ($q) use ($device) {
                $q->where('user_device_pivot.device_id', $device ? $device->id : null);
            }])
            ->get();

        $users = $users->filter(function ($user) {
            $hasThisDevice = !$user->devices->isEmpty();

            return $this->reachedUserDeviceLimit($user, $hasThisDevice);
        });

        if (!$users->isEmpty()) {
            throw new ValidationException(['user_id' => trans('validation.attributes.devices_limit') . ': ' . $users->implode('email', ', ')]);
        }
    }

    private function configureDevice(Device $device)
    {
        if (!$this->user->able('configure_device')) {
            throw new PermissionException(['id' => trans('front.dont_have_permission')]);
        }

        DeviceConfiguratorFormValidator::validate('configure', $this->data);

        $config = DeviceConfig::find($this->data['config_id']);

        $smsManager = new SMSGatewayManager();
        $gatewayArgs = settings('sms_gateway.use_as_system_gateway')
            ? ['request_method' => 'system']
            : null;

        $smsSenderService = $smsManager->loadSender($this->user, $gatewayArgs);
        $apnData = request()->all(['apn_name', 'apn_username', 'apn_password']);

        if ($this
            ->configService
            ->setSmsManager($smsSenderService)
            ->configureDevice($device->sim_number, $apnData, $config->commands)
        ) {
            return ['status' => 2];
        }

        throw new \Exception(trans('validation.cant_configure_device'));
    }

    private function setAbleUsers($device = null)
    {
        if (!isAdmin())
            unset($this->data['user_id']);

        if (!$this->user->can('edit', new User()))
            unset($this->data['user_id']);

        if (!empty($this->data['user_id'])) {
            $this->data['user_id'] = array_combine($this->data['user_id'], $this->data['user_id']);

            if ($device && $this->user->isManager()) {
                $users = $this->user->subusers()->pluck('id', 'id')->all() + [$this->user->id => $this->user->id];

                foreach ($device->users as $user) {
                    if (array_key_exists($user->id, $users) && !array_key_exists($user->id, $this->data['user_id'])) {
                        unset($this->data['user_id'][$user->id]);
                    }

                    if (!array_key_exists($user->id, $users) && !array_key_exists($user->id, $this->data['user_id'])) {
                        $this->data['user_id'][$user->id] = $user->id;
                    }
                }
            }
        }
    }
}
