<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use CustomFacades\Validators\LockStatusTableValidator;
use CustomFacades\Validators\LockStatusUnlockValidator;
use Illuminate\Pagination\LengthAwarePaginator;
use Tobuli\Entities\Device;
use Tobuli\History\DeviceHistory;
use Tobuli\History\Actions\AppendLockStatus;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\GroupLockOffStatus;
use Tobuli\Services\Commands\SendCommandService;
use Auth;

class LockStatusController extends Controller
{
    private $filterOptions;

    function __construct()
    {
        parent::__construct();

        $this->filterOptions = [
            'today' => trans('front.today'),
            'yesterday' => trans('front.yesterday'),
            'this_week' => trans('front.this_week'),
            'last_week' => trans('front.last_week'),
        ];
    }

    public function history($deviceId)
    {
        $device = Device::find($deviceId);
        $this->checkException('devices', 'show', $device);

        return view('front::LockStatus.index')->with([
            'deviceId' => $deviceId,
            'filterOptions' => $this->filterOptions,
        ]);
    }

    public function table($deviceId)
    {
        $device = Device::find($deviceId);
        $this->checkException('devices', 'show', $device);
        LockStatusTableValidator::validate('table', $this->data);

        $data = $this->getPaginatedData($deviceId, $this->data);

        return view('front::LockStatus.table')->with([
            'deviceId' => $deviceId,
            'filterOptions' => $this->filterOptions,
            'data' => $data,
        ]);
    }

    public function lockStatus($deviceId)
    {
        $device = Device::find($deviceId);
        $this->checkException('devices', 'show', $device);

        $lastPosition = $device->positionTraccar();


        $parameters = $lastPosition->parameters;


        $parameter = settings('plugins.locking_widget.options.parameter');
        $value_on  = settings('plugins.locking_widget.options.value_on');
        $value_off = settings('plugins.locking_widget.options.value_off');

        $status = null;

        if (isset($parameters[$parameter]) && $parameters[$parameter] == $value_on) {
            $status = true;
        }

        if (isset($parameters[$parameter]) && $parameters[$parameter] == $value_off) {
            $status = false;
        }

        if (is_null($status))
            return [
                'status' => -1,
                'message' => trans('front.unknown'),
            ];

        return [
            'status'  => $status,
            'message' => $status ? trans('front.locked') : trans('front.unlocked')
        ];
    }

    public function unlock($deviceId)
    {
        $device = Device::find($deviceId);
        $this->checkException('devices', 'show', $device);

        $types = [
            'gprs' => trans('front.gprs'),
        ];

        if (Auth::user()->canSendSMS() && ! empty($device->sim_number)) {
            $types['sms'] = trans('front.sms');
        }

        return view('front::LockStatus.unlock')->with(compact('deviceId', 'types'));
    }

    public function doUnlock(SendCommandService $sendCommandService)
    {
        LockStatusUnlockValidator::validate('unlock', $this->data);

        $deviceId = $this->data['id'];
        $device = Device::find($deviceId);
        $this->checkException('devices', 'show', $device);

        switch ($this->data['type']) {
            case 'sms':
                $result = $sendCommandService->sms($device, $this->data['message'], $this->user);
                break;
            case 'gprs':
                $command = [
                    'type' => 'custom',
                    'data' => $this->data['message'],
                ];
                $result = $sendCommandService->gprs($device, $command, $this->user);
                break;
        }

        $response = $result[0];
        $response['status'] = 2;

        return $response;
    }

    private function getPaginatedData($deviceId, $filterData, $limit = 10)
    {
        $page = request()->get('page', 1);
        $offset = $limit * ($page - 1);

        if (! isset($filterData['period'])) {
            $filterData['period'] = 'today';
        }

        $device = Device::find($deviceId);
        $this->checkException('devices', 'show', $device);

        $history = new DeviceHistory($device);
        $history->registerActions([
            AppendLockStatus::class,
            Duration::class,
            GroupLockOffStatus::class,
        ]);

        $period = getPeriodByPhrase($filterData['period']);
        $history->setRange($period['start'], $period['end']);

        $historyData = $history->get();
        $historyData = $historyData['groups']->all() ?? [];

        $data = array_map(function($obj) {
            return [
                'time' => $obj->getStartAt(),
                'duration' => $obj->stats()->human('duration'),
                'lat' => $obj->getStartPosition()->latitude,
                'lng' => $obj->getStartPosition()->longitude,
            ];
        }, $historyData);

        $data = collect($data);

        $items = new LengthAwarePaginator($data->slice($offset, $limit), $data->count(), $limit, $page, [
            'path'  => request()->url(),
            'query' => request()->query(),
        ]);

        return $items;
    }
}
