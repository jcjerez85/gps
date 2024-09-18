<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use CustomFacades\ModalHelpers\EventModalHelper;

class EventsController extends Controller {

    public function index()
    {
        $data = $this->data;
        $data['user_id'] = $this->user->id;

        $events = EventModalHelper::lookup($data);

        return $this->api ? ['status' => 1, 'items' => $events] : view('front::Events.index')->with(['events' => $events]);
    }

    public function doDestroy() {
        return view('front::Events.destroy')->with([
            'id' => request()->get('id', null)
        ]);
    }

    public function destroy()
    {
        EventModalHelper::destroy(request()->get('id', null));

        return ['status' => 1];
    }
}