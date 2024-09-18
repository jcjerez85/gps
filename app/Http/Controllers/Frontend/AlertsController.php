<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use CustomFacades\ModalHelpers\AlertModalHelper;
use Tobuli\Entities\Alert;

class AlertsController extends Controller
{
    public function index()
    {
        $data = AlertModalHelper::get();

        return !$this->api ? view('front::Alerts.index')->with($data) : ['status' => 1, 'items' => $data];
    }

    public function index_modal()
    {
        return $this->getList('index_modal');
    }

    public function table()
    {
        return $this->getList('table');
    }

    public function getList(string $view)
    {
        $this->checkException('alerts', 'view');

        $sort = $this->data['sorting'] ?? [];
        $sortCol = $sort['sort_by'] ?? 'name';
        $sortDir = $sort['sort'] ?? 'asc';

        $items = Alert::userOwned($this->user)
            ->search($this->data['search_phrase'] ?? null)
            ->select(['id', 'active', 'name', 'type'])
            ->withCount('devices')
            ->toPaginator(15, $sortCol, $sortDir);

        return view('front::Alerts.' . $view)->with(compact('items'));
    }

    public function create()
    {
        $data = AlertModalHelper::createData();

        return is_array($data) && !$this->api ? view('front::Alerts.create')->with($data) : $data;
    }

    public function store()
    {
        return AlertModalHelper::create();
    }

    public function edit()
    {
        $data = AlertModalHelper::editData();

        return is_array($data) && !$this->api ? view('front::Alerts.edit')->with($data) : $data;
    }

    public function update()
    {
        return AlertModalHelper::edit();
    }

    public function changeActive($active = null)
    {
        $ids = $this->data['id'];

        if (!is_array($ids)) {
            $ids = (array)$ids;
        }

        $items = Alert::whereIn('id', $ids)->get()
            ->filter(fn($alert) => $this->user->can('active', $alert));

        if ($active === null) {
            $active = (isset($this->data['active']) && filter_var($this->data['active'], FILTER_VALIDATE_BOOLEAN)) ? 1 : 0;
        }

        Alert::whereIn('id', $items->pluck('id')->all())
            ->update(['active' => $active]);

        return ['status' => 1];
    }

    public function doDestroy($id = null) {
        $data = AlertModalHelper::doDestroy($id);

        return is_array($data) ? view('front::Alerts.destroy')->with($data) : $data;
    }

    public function destroy()
    {
        return AlertModalHelper::destroy();
    }

    public function getCommands()
    {
        return AlertModalHelper::getCommands();
    }

    public function syncDevices()
    {
        return AlertModalHelper::syncDevices();
    }

    public function summary()
    {
        $data = AlertModalHelper::summary(request()->get('date_from'), request()->get('date_to'));

        return ['status' => 1, 'items' => $data];
    }
}