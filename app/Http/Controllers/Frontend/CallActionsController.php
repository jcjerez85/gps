<?php
namespace App\Http\Controllers\Frontend;

use App\Exceptions\ResourseNotFoundException;
use App\Http\Controllers\Controller;
use CustomFacades\Repositories\UserRepo;
use CustomFacades\Validators\CallActionFormValidator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use Tobuli\Entities\CallAction;
use Tobuli\Entities\Device;
use Tobuli\Entities\Event;
use Formatter;

class CallActionsController extends Controller {

    public function index()
    {
        $this->checkException('call_actions', 'view');
        $items = CallAction::byUser()
            ->paginate(15);

        $filterValues = CallAction::getFilterValues();

        $users = UserRepo::getUsers($this->user)
            ->pluck('email', 'id')
            ->prepend('-- '.trans('admin.select').' --', '0')
            ->toArray();
        $devices = $this->user
            ->accessibleDevices()
            ->whereIn('id', $filterValues['devices'])
            ->pluck('name', 'id')
            ->toArray();

        $alerts = $this->user
            ->alerts()
            ->whereIn('id', $filterValues['alerts'])
            ->pluck('name', 'id')
            ->toArray();

        $event_types = Event::getTypeTitles()
            ->filter(function($value) use($filterValues) {
                return in_array($value['type'], $filterValues['events']);
            })
            ->pluck('title', 'type')
            ->prepend('-- '.trans('admin.select').' --', '0')
            ->toArray();


        return view('front::CallAction.index')
            ->with(compact('items', 'users', 'devices', 'alerts', 'event_types'));
    }

    public function table()
    {
        $this->checkException('call_actions', 'view');

        $items = $this->getFilteredData();

        return view('front::CallAction.table')
            ->with(compact('items'));
    }

    public function create($device_id)
    {
        $this->checkException('call_actions', 'create');
        $device = Device::find($device_id);
        $this->checkException('devices', 'show', $device);

        $events = $device->events()
            ->latest()
            ->take(25)
            ->get()
            ->pluck('time_with_message', 'id');

        $responseTypes = Arr::pluck(CallAction::getResponseTypes(), 'title', 'type');

        return view('front::CallAction.create')
            ->with(compact('events', 'responseTypes', 'device_id'));
    }

    public function createByEvent($event_id)
    {
        $this->checkException('call_actions', 'create');

        $event = Event::find($event_id);

        if (! $event) {
            throw new ResourseNotFoundException(trans('validation.attributes.event'));
        }

        $this->checkException('devices', 'show', $event->device);

        $events = $event->device->events()
            ->latest()
            ->take(25)
            ->get()
            ->pluck('message', 'id');

        $responseTypes = Arr::pluck(CallAction::getResponseTypes(), 'title', 'type');
        $device_id = $event->device->id;

        return view('front::CallAction.create')
            ->with(compact('events', 'responseTypes', 'device_id', 'event_id'));
    }

    public function store()
    {
        $this->checkException('call_actions', 'store');

        $event = Event::find($this->data['event_id'] ?? null);

        if (! $event) {
            throw new ResourseNotFoundException(trans('validation.attributes.event'));
        }

        $device = Device::find($this->data['device_id']);
        $this->checkException('devices', 'show', $device);

        $this->data['alert_id'] =  $event->alert_id ?? null;
        $this->data['user_id'] = $this->user->id ?? null;
        $this->data['device_id'] = $event->device_id ?? null;
        $this->data['called_at'] = is_null($this->data['called_at'])
            ? $this->data['called_at']
            : Formatter::time()->reverse($this->data['called_at']);
        CallActionFormValidator::validate('create', $this->data);

        CallAction::create($this->data);

        return ['status' => 1];
    }

    public function edit($id)
    {
        $item = CallAction::find($id);
        $this->checkException('call_actions', 'edit', $item);
        $device = Device::find($item->device_id);
        $this->checkException('devices', 'show', $device);

        $events = $device->events()
            ->latest()
            ->take(25)
            ->get()
            ->pluck('message', 'id');

        $responseTypes = Arr::pluck(CallAction::getResponseTypes(), 'title', 'type');

        return view('front::CallAction.update')
            ->with(compact('item', 'events', 'responseTypes'));
    }

    public function update($id)
    {
        $item = CallAction::find($id);
        $this->checkException('call_actions', 'update', $item);
        $this->data['user_id'] = $item->user_id;

        $event = Event::find($this->data['event_id'] ?? null);

        if (! $event) {
            throw new ResourseNotFoundException(trans('validation.attributes.event'));
        }

        $this->data['alert_id'] =  $event->alert_id;
        $this->data['called_at'] = is_null($this->data['called_at'])
            ? $this->data['called_at']
            : Formatter::time()->reverse($this->data['called_at']);

        CallActionFormValidator::validate('update', $this->data);

        $item->update($this->data);

        return ['status' => 1];
    }

    public function destroy($id)
    {
        $item = CallAction::find($id);
        $this->checkException('call_actions', 'remove', $item);
        $item->delete();

        return ['status' => 1];
    }

    private function getFilteredData()
    {
        $filters = array_filter($this->data['filter'], function($value) {
            return ! empty($value);
        });

        $query = CallAction::filter($filters);

        if (! empty($filters['date_from'])) {
            $query->where('called_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->where('called_at', '<=', $filters['date_to']);
        }

        return $query->paginate(15);
    }

    private function formatEventsArray($events)
    {
        $result = [];

        foreach ($events as $event) {
            $result[$event->id] = $event->formatMessage();
        }

        return $result;
    }
}
