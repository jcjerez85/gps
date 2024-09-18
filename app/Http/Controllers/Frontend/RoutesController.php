<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Transformers\Route\RouteMapTransformer;
use FractalTransformer;
use Tobuli\Entities\Route;
use Tobuli\Services\RouteUserService;

class RoutesController extends Controller
{
    private RouteUserService $service;

    protected function afterAuth($user)
    {
        $this->service = new RouteUserService($user);
    }

    public function index()
    {
        $this->checkException('routes', 'view');

        $items = Route::userOwned($this->user)->paginate(500);

        return response()->json(
            FractalTransformer::paginate($items, RouteMapTransformer::class)->toArray()
        );
    }

    public function create()
    {
        $this->checkException('routes', 'store');

        return view('front::Routes.create');
    }

    public function store()
    {
        $this->data['coordinates'] = $this->data['polyline'];

        $item = $this->service->create($this->data);

        return ['status' => 1] + FractalTransformer::item($item, RouteMapTransformer::class)->toArray();
    }

    public function edit(int $id)
    {
        $item = Route::find($id);

        $this->checkException('routes', 'edit', $item);

        return view('front::Routes.edit')->with(['item' => $item]);
    }

    public function update(?int $id = null)
    {
        $this->data['coordinates'] = $this->data['polyline'];

        $item = Route::find($id);

        $this->service->edit($item, $this->data);

        return ['status' => 1] + FractalTransformer::item($item, RouteMapTransformer::class)->toArray();
    }

    public function changeActive()
    {
        $item = Route::find($this->data['id']);

        $this->checkException('routes', 'active', $item);

        $active = (isset($this->data['active']) && filter_var($this->data['active'], FILTER_VALIDATE_BOOLEAN)) ? 1 : 0;

        $this->service->active($item, $active);

        return ['status' => 1];
    }

    public function destroy()
    {
        $id = array_key_exists('route_id', $this->data) ? $this->data['route_id'] : $this->data['id'];

        $item = Route::find($id);

        $this->service->remove($item);

        return ['status' => 1];
    }
}
