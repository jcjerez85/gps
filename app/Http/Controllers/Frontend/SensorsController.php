<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use CustomFacades\ModalHelpers\SensorModalHelper;
use CustomFacades\Repositories\DeviceRepo;
use CustomFacades\Repositories\DeviceSensorRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Form;
use Tobuli\Services\Sensors\ParameterSuggestionService;

class SensorsController extends Controller {

    public function index($device_id = NULL)
    {
        if (is_null($device_id))
            $device_id = empty($this->data['device_id']) ? null : $this->data['device_id'];
        
        $data = SensorModalHelper::paginated($device_id);
        
        return !$this->api ? view('front::Sensors.index')->with(['sensors' => $data, 'device_id' => $device_id]) : $data;
    }

    public function create($device_id = NULL)
    {
        if (is_null($device_id))
            $device_id = empty($this->data['device_id']) ? null : $this->data['device_id'];
        
        $data = array_merge(SensorModalHelper::createData($device_id), [
            'route' => 'sensors.store'
        ]);

        return !$this->api ? view('front::Sensors.create')->with($data) : $data;
    }

    public function store()
    {
        return SensorModalHelper::create();
    }

    public function edit($id = null)
    {
        $data = array_merge(SensorModalHelper::editData(), [
            'route' => ['sensors.update', $id]
        ]);

        return is_array($data) && !$this->api ? view('front::Sensors.edit')->with($data) : $data;
    }

    public function update()
    {
        return SensorModalHelper::edit();
    }

    public function getProtocols()
    {
        $protocols = SensorModalHelper::getProtocols();

        return !$this->api ? Form::select('event_protocol', $protocols, null, ['class' => 'form-control']) : ['items' => $protocols];
    }

    public function getEvents()
    {
        $events = SensorModalHelper::getEvents();

        return !$this->api ? Form::select('event_id', $events, null, ['class' => 'form-control']) : apiArray($events);
    }

    public function doDestroy($id)
    {
        $item = DeviceSensorRepo::find($id);
        $device = DeviceRepo::find($item->device_id);


        if (empty($item) || (!isAdmin() && !$device->users->contains(Auth::User()->id)))
            return modal(dontExist('front.sensor'), 'danger');

        return view('front::Sensors.destroy')->with(compact('item'));
    }

    public function destroy()
    {
        return SensorModalHelper::destroy();
    }

    public function getEngineHours($device_id = NULL)
    {
        if (is_null($device_id))
            $device_id = empty($this->data['device_id']) ? null : $this->data['device_id'];

        $data = SensorModalHelper::getVirtualEngineHours($device_id);

        return is_array($data) && !$this->api ? view('front::Sensors.engine_hours')->with($data) : $data;
    }

    public function setEngineHours($device_id = NULL)
    {
        if (is_null($device_id))
            $device_id = empty($this->data['device_id']) ? null : $this->data['device_id'];

        return SensorModalHelper::setVirtualEngineHours($device_id);
    }

    public function parameterSuggestion(Request $request, ParameterSuggestionService $parameterSuggestionService)
    {
        return $parameterSuggestionService->suggest($request->param, $request->device_id);
    }
}
