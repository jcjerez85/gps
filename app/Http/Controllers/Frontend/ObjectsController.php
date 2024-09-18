<?php namespace App\Http\Controllers\Frontend;

use App\Exceptions\PermissionException;
use App\Http\Controllers\Controller;
use App\Transformers\Device\DeviceMapFullTransformer;
use App\Transformers\Device\DeviceMapTransformer;
use CustomFacades\ModalHelpers\DeviceModalHelper;
use CustomFacades\Repositories\DeviceRepo;
use CustomFacades\Server;
use Formatter;
use FractalTransformer;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use ModalHelpers\SendCommandModalHelper;
use Tobuli\Services\UserOpenGroupService;

class ObjectsController extends Controller {

    const LOAD_LIMIT = 100;

    function __construct()
    {
        parent::__construct();

        Server::setMemoryLimit(config('server.device_memory_limit'));
    }

    public function index()
    {
        $version = Config::get('tobuli.version');
        $devices = [];

        if ($this->user->perm('devices', 'view')) {
            $devices = $this->user->devices()
                ->get();
        }

        if (!empty($devices))
            $devices = $devices->pluck('name', 'id')->all();

        $history = [
            'start' => Formatter::time()->convert(date('Y-m-d H:i:s'), 'Y-m-d'),
            'end' => Formatter::time()->convert(date('Y-m-d H:i:s'), 'Y-m-d'),
            'end_time' => '23:45',
        ];

        $dashboard = $this->user->getSettings('dashboard.enabled');        
        //dd("entre");

        return view('front::Objects.index')
            ->with(compact('devices', 'history', 'version', 'dashboard'));
    }

    public function items() {
        if ( ! $this->user->perm('devices', 'view') )
            throw new PermissionException();

        $full = request()->get('full');

        $devices = $this->user
            ->devices()
            ->filter(request()->all())
            ->wasConnected()
            ->when(!$full, function($query) {
                $query->visible();
            })
            ->clearOrdersBy()
            ->cursorPaginate(500);

        $transformer = $full ? DeviceMapFullTransformer::class : DeviceMapTransformer::class;

        return response()->json(
            FractalTransformer::cursorPaginate($devices, $transformer)->toArray()
        );
    }

    public function itemsSimple() {

        $deviceCollection = $this->user->devices()
            ->search(Request::input('search_phrase'))
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('front::Objects.itemsSimple')->with(compact('deviceCollection'));
    }

    public function itemsJson() {
        $data = DeviceModalHelper::itemsJson();

        return $data;
    }

    public function changeGroupStatus()
    {
        if (isDemoUser()) {
            return;
        }

        $id = $this->data['id'];

        (new UserOpenGroupService($this->user->deviceGroups()))
            ->changeStatus($id);
    }

    public function changeAlarmStatus()
    {
        if (!array_key_exists('id', $this->data) && array_key_exists('device_id', $this->data))
            $this->data['id'] = $this->data['device_id'];
        $item = DeviceRepo::find($this->data['id']);
        if (empty($item) || (!$item->users->contains($this->user->id) && !isAdmin()))
            return ['status' => 0];

        $position = $item->positions()->orderBy('time', 'desc')->first();

        $sendCommandModalHelper = new SendCommandModalHelper();
        $sendCommandModalHelper->setData([
            'device_id' => $item->id,
            'type' => $item->alarm == 0 ? 'alarmArm' : 'alarmDisarm'
        ]);
        $result = $sendCommandModalHelper->gprsCreate();

        $alarm = $item->alarm;

        if ($result['status'] == 1) {
            $tr = TRUE;
            $times = 1;
            $val = '';
            if (isset($position)) {
                while($tr && $times < 6) {
                    $positions = $item->positions()->where('time', '>', $position->time)->orderBy('time', 'asc')->get();
                    if ($times >= 5)
                        $positions = $item->positions()->select('other')->orderBy('time', 'desc')->limit(2)->get();
                    foreach ($positions as $pos) {
                        preg_match('/<'.preg_quote('alarm', '/').'>(.*?)<\/'.preg_quote('alarm', '/').'>/s', $pos->other, $matches);
                        if (!isset($matches['1']))
                            continue;

                        $val = $matches['1'];
                        if ($val == 'lt' || $val == 'mt' || $val == 'lf') {
                            $tr = FALSE;
                            break;
                        }
                    }

                    $times++;
                    sleep(1);
                }
            }

            $status = 0;

            if (!$tr) {
                if (($item->alarm == 0 && $val == 'lt') || ($item->alarm == 1 && $val == 'mt')) {
                    $status = 1;
                    $alarm = $item->alarm == 1 ? 0 : 1;
                    DeviceRepo::update($item->id, [
                        'alarm' => $alarm
                    ]);
                }
            }

            return ['status' => $status, 'alarm' => intval($alarm), 'error' => trans('front.unexpected_error')];
        }
        else {
            return ['status' => 0, 'alarm' => intval($alarm), 'error' => isset($result['error']) ? $result['error'] : ''];
        }
    }

    public function alarmPosition()
    {
        $item = DeviceRepo::find($this->data['id']);
        if (empty($item) || (!$item->users->contains($this->user->id) && !isAdmin()))
            return response()->json(['status' => 0]);

        $sendCommandModalHelper = new SendCommandModalHelper();
        $sendCommandModalHelper->setData([
            'device_id' => $item->id,
            'type' => 'positionSingle'
        ]);
        $result = $sendCommandModalHelper->gprsCreate();

        if ($result['status'] == 1)
            return ['status' => 1];
        else
            return ['status' => 0, 'error' => isset($result['error']) ? $result['error'] : ''];
    }
}
