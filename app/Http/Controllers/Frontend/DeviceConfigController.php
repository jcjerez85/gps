<?php

namespace App\Http\Controllers\Frontend;

use App\Exceptions\PermissionException;
use App\Http\Controllers\Controller;
use CustomFacades\ModalHelpers\DeviceModalHelper;
use CustomFacades\Repositories\DeviceRepo;
use CustomFacades\Validators\DeviceConfiguratorFormValidator;
use Tobuli\Entities\ApnConfig;
use Tobuli\Entities\Device;
use Tobuli\Entities\DeviceConfig;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Helpers\SMS\SMSGatewayManager;
use Tobuli\Services\DeviceConfigService;
use Auth;

class DeviceConfigController extends Controller
{
    private $configService;

    public function __construct(DeviceConfigService $configService)
    {
        $this->configService = $configService;

        parent::__construct();
    }

    public function index($deviceId = null)
    {
        if (! $this->user->able('configure_device')) {
            throw new PermissionException(['id' => trans('front.dont_have_permission')]);
        }

        $devices = $this->user
            ->devices_sms
            ->pluck('nameWithSimNumber', 'id');

        if (isset($deviceId) && is_null($devices->get($deviceId))) {
            throw new ValidationException([
                trans('validation.required', ['attribute' => trans('validation.attributes.sim_number')])
            ]);
        }

        $deviceConfigs = DeviceConfig::active()
            ->get()
            ->pluck('fullName', 'id');
        $apnConfigs = ApnConfig::active()
            ->get()
            ->pluck('name', 'id');

        return view('front::DeviceConfig.index', [
            'devices' => $devices,
            'device_id' => $deviceId,
            'device_configs' => $deviceConfigs,
            'apn_configs' => $apnConfigs,
        ]);
    }

    public function configure()
    {
        if (! $this->user->able('configure_device')) {
            throw new PermissionException(['id' => trans('front.dont_have_permission')]);
        }

        DeviceConfiguratorFormValidator::validate('configure', $this->data);

        $device = Device::find($this->data['device_id']);
        $this->checkException('devices', 'show', $device);

        $config = DeviceConfig::find($this->data['config_id']);

        $smsManager = new SMSGatewayManager();
        $gatewayArgs = settings('sms_gateway.use_as_system_gateway')
            ? ['request_method' => 'system']
            : null;

        $smsSenderService = $smsManager->loadSender(Auth::user(), $gatewayArgs);
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

    public function getApnData($id)
    {
        $item = ApnConfig::find($id, ['apn_name', 'apn_username', 'apn_password']);

        if (! $item) {
            return modalError(dontExist('front.apn_configuration'));
        }

        return ['success' => $item];
    }
}
