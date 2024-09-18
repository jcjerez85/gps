<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use CustomFacades\Repositories\TrackerPortRepo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use Tobuli\Entities\CommandTemplate;
use Tobuli\Entities\Device;
use Tobuli\Entities\DeviceType;
use Tobuli\Entities\UserGprsTemplate;
use Tobuli\Entities\UserSmsTemplate;

class CommandTemplatesController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();

        $sort = $input['sorting'] ?? ['sort_by' => 'title', 'sort' => 'asc'];

        $items = CommandTemplate::common()
            ->search($input['search_phrase'] ?? null)
            ->filter($input)
            ->toPaginator(20, $sort['sort_by'], $sort['sort']);

        return $this->api
            ? $items
            : View::make('admin::CommandTemplates.' . ($request->ajax() ? 'table' : 'index'))
                ->with(compact('items', 'input') + $this->getFormData());
    }

    public function create()
    {
        return View::make('admin::CommandTemplates.create')
            ->with($this->getFormData());
    }

    public function edit(int $id = null)
    {
        $item = $this->getItem($id);

        return View::make('admin::CommandTemplates.edit')
            ->with(compact('item') + $this->getFormData());
    }

    private function getFormData(): array
    {
        $types = [
            UserGprsTemplate::TYPE  => "GPRS",
            UserSmsTemplate::TYPE   => "SMS",
        ];
        $protocols = TrackerPortRepo::getProtocolList();
        $adapties = CommandTemplate::getAdapties();
        $devices = Device::all();
        $deviceTypes = DeviceType::active()->get()->pluck('title', 'id');

        return compact('types', 'protocols', 'adapties', 'devices', 'deviceTypes');
    }

    public function store(Request $request)
    {
        $item = new CommandTemplate();

        return $this->saveItem($item);
    }

    public function update(Request $request, int $id = null)
    {
        $item = $this->getItem($id ?: $request->get('id'));

        return $this->saveItem($item);
    }

    private function saveItem(CommandTemplate $item)
    {
        $item->type = $this->data['type'];
        $item->fill($this->data);
        $item->save();

        $item->devices()->sync(Arr::get($this->data, 'devices', []));
        $item->deviceTypes()->sync(Arr::get($this->data, 'device_types', []));

        return new JsonResponse(['status' => 1]);
    }

    public function destroy(Request $request, int $id = null)
    {
        $ids = (array)($id ?: $request->get('id'));
        CommandTemplate::common()->whereIn('id', $ids)->delete();

        return new JsonResponse(['status' => 1]);
    }

    private function getItem($id)
    {
        return CommandTemplate::common()->findOrFail($id);
    }
}
