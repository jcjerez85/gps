<?php

namespace App\Http\Controllers\Frontend;

use App\Exceptions\PermissionException;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use CustomFacades\GeoLocation;
use CustomFacades\ModalHelpers\SendCommandModalHelper;
use CustomFacades\Repositories\DeviceRepo;
use Illuminate\Http\Request;
use Tobuli\Entities\File\DeviceCameraMedia;
use Tobuli\History\DeviceHistory;

class DeviceWidgetsController extends Controller
{
    public function location($device_id)
    {
        $device = DeviceRepo::find($device_id);

        $this->checkException('devices', 'show', $device);

        try {
            $location = GeoLocation::byCoordinates($device->lat, $device->lng);
        } catch (\Exception $e) {
            $location = null;
        }

        return view('front::Widgets.location')->with([
            'location' => $location ? $location->toArray() : null
        ]);
    }

    public function cameras($device_id)
    {
        $device = DeviceRepo::find($device_id);

        $this->checkException('devices', 'show', $device);

        $images = [];
        $cameras = $device->deviceCameras()->showWidget()->get();

        foreach ($cameras as $camera) {
            $image = DeviceCameraMedia::setEntity($camera)->findLatest();

            if ($image) {
                $images[$camera->id] = [
                    'camera_name' => $camera->name,
                    'image' => $image,
                    'device_id' => $camera->device_id,
                ];
            }
        }

        return view('front::Widgets.camera')->with([
            'images' => $images,
        ]);
    }

    public function image($device_id)
    {
        $device = DeviceRepo::find($device_id);

        $this->checkException('devices', 'show', $device);

        $image = $device->image;

        return view('front::Widgets.image')->with([
            'device' => $device,
            'image' => $image ? url($image) : null,
        ]);
    }

    public function recentEvents($device_id)
    {
        $device = DeviceRepo::find($device_id);
        $this->checkException('devices', 'show', $device);

        $recent_events = $device->events()
            ->where('user_id', $this->user->id)
            ->when(settings('plugins.event_section_alert.status'), function($query) {
                $query->with('alert');
            })
            ->latest()
            ->limit(10)
            ->get();

        return view('front::Widgets.recent_events')->with([
            'device' => $device,
            'recent_events' => $recent_events,
        ]);
    }

    public function fuelGraph($device_id)
    {
        $device = DeviceRepo::find($device_id);

        $this->checkException('devices', 'show', $device);

        try {
            $data = $this->sensorsData($device, ['fuel_tank', 'fuel_tank_calibration'], Carbon::now()->subDay(1), Carbon::now());
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return view('front::Widgets.fuel_graph')->with([
            'data'  => empty($data) ? null : $data,
            'error' => empty($error) ? null : $error
        ]);
    }

    public function templateWebhook($device_id)
    {
        if ( ! $this->user->perm('widget_template_webhook', 'view')) {
            throw new PermissionException();
        }

        $device = DeviceRepo::find($device_id);

        $this->checkException('devices', 'show', $device);

        $templates = \DB::table('templates')
            ->where([
                'device_id' => $device->id
            ])
            ->get()
            ->pluck('template_name', 'template_id');

        if (!$templates->isEmpty())
            $templates->prepend(trans('front.nothing_selected'), '0');

        return view('front::Widgets.template_webhook')->with([
            'templates' => $templates->all(),
            'device' => $device
        ]);
    }

    public function templateWebhookSend(Request $request, $device_id)
    {
        if ( ! $this->user->perm('widget_template_webhook', 'view')) {
            throw new PermissionException();
        }

        $device = DeviceRepo::find($device_id);

        $this->checkException('devices', 'show', $device);

        $template = \DB::table('templates')
            ->where([
                'device_id' => $device->id,
                'template_id' => $request->get('template_id')
            ])
            ->first();

        if (!$template)
            return response()->json([
                'status' => 0
            ]);

        $curl = new \Curl;

        $response = $curl->post(config('addon.widget_template'), [
            'device_id' => $device->id,
            'template_id' => $template->template_id,
            'wox_user_id' => $this->user->id,
        ]);

        return response()->json([
            'status' => 1
        ]);
    }

    public function gprsCommands($device_id)
    {
        $device = DeviceRepo::find($device_id);
        $this->checkException('devices', 'show', $device);
        $this->checkException('send_command', 'view');

        $commands = SendCommandModalHelper::getCommands($device);

        $commands = $commands->filter(function($command) {
            if (isset($command['attributes']) && strpos($command['type'], 'template_') !== 0) {
                return false;
            }

            return true;
        });

        return view('front::Widgets.gprs_command')->with([
            'device_id' => $device->id,
            'commands' => $commands,
        ]);
    }

    private function sensorsData($device, $types, $from, $to) {
        $sensors = $device->sensors->filter(function($sensor) use ($types) {
            return in_array($sensor->type, $types);
        });

        if ($sensors->isEmpty())
            throw new \Exception( dontExist('front.sensor') );

        $history = new DeviceHistory($device);
        $history->setSensors($sensors);
        $history->setRange($from, $to);
        $history->registerActions([]);
        $history->get();

        return $history->getSensorsData();
    }
}
