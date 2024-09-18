<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Transformers\Poi\PoiMapTransformer;
use FractalTransformer;
use Illuminate\Http\Request;
use Tobuli\Entities\MapIcon;
use Tobuli\Entities\Poi;
use Tobuli\Entities\PoiGroup;
use Tobuli\Services\PoiUserService;

class PoisController extends Controller
{
    protected PoiUserService $service;

    protected function afterAuth($user)
    {
        $this->service = new PoiUserService($this->user);
    }

    public function index()
    {
        $this->checkException('poi', 'view');

        $items = Poi::userOwned($this->user)->with(['mapIcon'])->paginate(500);

        return response()->json(
            FractalTransformer::paginate($items, PoiMapTransformer::class)->toArray()
        );
    }

    public function create()
    {
        $this->checkException('poi', 'store');

        $data = $this->getFormData();

        return view('front::Pois.create')->with($data);
    }

    public function store(Request $request)
    {
        $item = $this->service->create($request->all());

        return ['status' => 1] + FractalTransformer::item($item, PoiMapTransformer::class)->toArray();
    }

    public function edit(int $id)
    {
        $item = Poi::find($id);

        $this->checkException('poi', 'edit', $item);

        $data = $this->getFormData();
        $data['item'] = $item;

        return view('front::Pois.edit')->with($data);
    }

    public function update(Request $request, ?int $id = null)
    {
        $item = Poi::find($id);

        $this->service->edit($item, $request->all());

        return ['status' => 1] + FractalTransformer::item($item, PoiMapTransformer::class)->toArray();
    }

    private function getFormData(): array
    {
        $mapIcons = MapIcon::all();

        $poiGroups = PoiGroup::where(['user_id' => $this->user->id])->get()
            ->pluck('title', 'id')
            ->prepend(trans('front.ungrouped'), '0')
            ->all();

        return compact('mapIcons', 'poiGroups');
    }

    public function changeActive(Request $request)
    {
        $id = $request->get('id');
        $status = filter_var($request->get('active'), FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

        if (is_array($id)) {
            $items = Poi::findMany($id);

            $this->service->activeMulti($items, $status);
        } else {
            $item = Poi::find($id);

            $this->service->active($item, $status);
        }

        return ['status' => 1];
    }

    public function destroy(Request $request)
    {
        $item = Poi::find($request->get('id'));

        $this->service->remove($item);

        return ['status' => 1];
    }
}
