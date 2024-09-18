<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Transformers\Geofence\GeofenceMapTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Tobuli\Entities\DiemRate;
use Tobuli\Entities\Geofence;
use Tobuli\Services\DiemRateService;

class DiemRatesController extends Controller
{
    private $diemRateService;

    public function __construct(DiemRateService $diemRateService)
    {
        $this->diemRateService = $diemRateService;

        parent::__construct();
    }

    public function index(Request $request)
    {
        $items = DiemRate::search($request->input('search_phrase'))
            ->toPaginator(
                $request->input('limit', 25),
                $request->input('sorting.sort_by', 'title'),
                $request->input('sorting.sort', 'desc')
            );

        return $this->api
            ? $items
            : View::make('admin::DiemRates.' . ($request->ajax() ? 'table' : 'index'))
                ->with(compact('items'));
    }

    public function create()
    {
        return $this->getForm(new DiemRate(['active' => 1]), 'admin::DiemRates.create');
    }

    public function edit(int $id = null)
    {
        return $this->getForm(DiemRate::find($id), 'admin::DiemRates.edit');
    }

    public function store()
    {
        $this->diemRateService->save($this->data);

        return ['status' => 1];
    }

    public function update(int $id = null)
    {
        $item = DiemRate::find($this->data['id']);

        $this->diemRateService->save($this->data, $item);

        return ['status' => 1];
    }

    public function destroy(Request $request)
    {
        $diemRates = DiemRate::whereIn('id', $request->get('id') ?? [])->get();

        foreach ($diemRates as $diemRate) {
            $this->diemRateService->delete($diemRate);
        }

        return ['status' => 1];
    }

    private function getForm(DiemRate $item, string $view)
    {
        $periodUnits = [
            DiemRate::PERIOD_DAY    => trans('front.day'),
            DiemRate::PERIOD_HOUR   => trans('front.hour'),
        ];

        $geofences = \FractalTransformer::collection(
            Geofence::has('diemRate')->get(),
            GeofenceMapTransformer::class
        )->toArray()['data'];

        return View::make($view)->with(compact(
            'item',
            'periodUnits',
            'geofences'
        ));
    }

}