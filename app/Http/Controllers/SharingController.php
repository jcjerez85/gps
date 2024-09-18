<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Exceptions\ResourseNotFoundException;
use App\Http\Controllers\Frontend\AddressController;
use App\Transformers\Device\DeviceMapFullTransformer;
use App\Transformers\Device\DeviceMapTransformer;
use Illuminate\Support\Facades\Config;
use Tobuli\Entities\Sharing;
use Tobuli\Services\FractalSerializers\WithoutDataArraySerializer;
use Tobuli\Services\FractalTransformerService;
use FractalTransformer;

class SharingController extends Controller
{
    private $sharing;

    /**
     * @var FractalTransformerService
     */
    private $transformerService;

    public function __construct(FractalTransformerService $transformerService)
    {
        parent::__construct();

        $this->sharing = Sharing::where('hash', request()->route()->parameter('hash'))->first();

        if (!$this->sharing || !$this->sharing->isActive()) {
            throw new ResourseNotFoundException(trans('admin.map'));
        }

        if ( ! $this->sharing->user) {
            throw new ResourseNotFoundException(trans('global.user'));
        }

        if ( ! $this->sharing->user->perm('sharing', 'view')) {
            throw new PermissionException();
        }

        setActingUser($this->sharing->user);

        $this->transformerService = $transformerService->setSerializer(WithoutDataArraySerializer::class);
    }

    public function index($hash)
    {
        $devices = $this->transformerService
            ->collection($this->sharing->activeDevices, DeviceMapFullTransformer::class)
            ->toArray();

        return view('front::Layouts.sharing')->with(compact('devices'));
    }

    public function devices()
    {
        $devices = $this->sharing->activeDevices()
            ->filter(request()->all())
            ->wasConnected()
            ->clearOrdersBy()
            ->cursorPaginate(500);

        $transformer = request()->get('full') ? DeviceMapFullTransformer::class : DeviceMapTransformer::class;

        return response()->json(
            $this->transformerService->cursorPaginate($devices, $transformer)->toArray()
        );
    }

    public function devicesLatest()
    {
        $time = intval(($time = request()->get('time')) ? $time : time() - 5);

        $items = $this->sharing->activeDevices()
            ->connectedAfter(date('Y-m-d H:i:s', $time))
            ->clearOrdersBy()
            ->get()
            ->map(function($device) {
                return $this->transformerService->item($device, DeviceMapTransformer::class)->toArray();
            });

        return [
            'items' => $items,
            'events' => [],
            'time' => $time,
            'version' => Config::get('tobuli.version')
        ];
    }

    public function address()
    {
        return app(AddressController::class)->get();
    }
}
