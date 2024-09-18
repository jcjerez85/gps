<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use CustomFacades\ModalHelpers\CustomEventModalHelper;
use CustomFacades\ModalHelpers\SensorModalHelper;
use Collective\Html\FormFacade as Form;

class CustomEventsController extends Controller
{
    public function index() {
        $data = CustomEventModalHelper::get();

        if ($this->api)
            return ['items' => $data];

        return view('front::CustomEvents.index')->with($data);
    }

    public function table() {
        $data = CustomEventModalHelper::get();

        return view('front::CustomEvents.table')->with($data);
    }

    public function create()
    {
        $data = CustomEventModalHelper::createData();

        return !$this->api ? view('front::CustomEvents.create')->with($data) : $data;
    }

    public function store()
    {
        return CustomEventModalHelper::create();
    }

    public function edit()
    {
        $data = CustomEventModalHelper::editData();

        return is_array($data) && !$this->api ? view('front::CustomEvents.edit')->with($data) : $data;
    }

    public function update()
    {
        return CustomEventModalHelper::edit();
    }

    public function getProtocols()
    {
        $protocols = SensorModalHelper::getProtocols();

        return !$this->api ? Form::select('event_protocol', $protocols, null, ['class' => 'form-control']) : apiArray($protocols);
    }

    public function getEvents()
    {
        $events = SensorModalHelper::getEvents();

        return !$this->api ? Form::select('event_id', $events, null, ['class' => 'form-control']) : apiArray($events);
    }

    public function getEventsByDevices()
    {
        $devices = isset($this->data['devices']) ? $this->user->devices()->whereIn('devices.id', $this->data['devices']) : [];

        $events = CustomEventModalHelper::getGroupedEvents($devices);

        array_walk($events, function(&$v){ $v['items'] = apiArray($v['items']); });

        return $events;
    }

    public function doDestroy($id)
    {
        $data = CustomEventModalHelper::doDestroy($id);

        return is_array($data) ? view('front::CustomEvents.destroy')->with($data) : $data;
    }

    public function destroy()
    {
        return CustomEventModalHelper::destroy();
    }

}
