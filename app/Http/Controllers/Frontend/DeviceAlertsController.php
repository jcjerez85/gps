<?php namespace App\Http\Controllers\Frontend;

use App\Transformers\ApiV1\DeviceAlertListTransformer;
use Formatter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Tobuli\Entities\Alert;
use Tobuli\Entities\Device;
use Tobuli\Exceptions\ValidationException;

use FractalTransformer;

class DeviceAlertsController extends Controller
{

    public function index(Request $request, $device_id)
    {
        $this->checkException('alerts', 'view');

        $device = Device::find($device_id);
        $this->checkException('devices', 'show', $device);

        $alerts = $device->alerts()
            ->withCount('devices')
            ->where('alerts.user_id', $this->user->id)
            ->paginate();

        if ($this->api) {
            return response()->json(
                FractalTransformer::paginate($alerts, DeviceAlertListTransformer::class)->toArray()
            );
        }

        return view('front::DeviceAlerts.index')->with([
            'device' => $device,
            'alerts' => $alerts,
        ]);
    }

    public function table(Request $request, $device_id)
    {
        $this->checkException('alerts', 'view');

        $device = Device::find($device_id);
        $this->checkException('devices', 'show', $device);

        $alerts = $device->alerts()
            ->withCount('devices')
            ->where('alerts.user_id', $this->user->id)
            ->paginate();

        return view('front::DeviceAlerts.table')->with([
            'device' => $device,
            'alerts' => $alerts,
        ]);
    }

    public function editTimePeriod(Request $request, $device_id, $alert_id)
    {
        $device = Device::find($device_id);
        $this->checkException('devices', 'show', $device);

        $alert = $device->alerts()->find($alert_id);
        $this->checkException('alerts', 'show', $alert);

        $data = [
            'device' => $device,
            'alert'  => $alert,
        ];

        return view('front::DeviceAlerts.time_period')->with($data);
    }

    public function updateTimePeriod(Request $request, $device_id, $alert_id)
    {
        $device = Device::find($device_id);
        $this->checkException('devices', 'show', $device);

        $alert = $device->alerts()->find($alert_id);
        $this->checkException('alerts', 'show', $alert);

        $validator = Validator::make(request()->all(), [
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors());
        }

        $activeFrom = $request->get('date_from');
        $activeTo   = $request->get('date_to');

        $device->alerts()->updateExistingPivot($alert->id, [
            'active_from' => $activeFrom ? Formatter::time()->reverse($activeFrom) : null,
            'active_to'   => $activeTo ? Formatter::time()->reverse($activeTo) : null,
        ]);

        return response()->json(['status' => 1]);
    }
}
