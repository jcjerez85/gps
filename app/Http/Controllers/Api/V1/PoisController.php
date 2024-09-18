<?php namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Transformers\ApiV1\PoiTransformer;
use Illuminate\Http\Request;
use Tobuli\Entities\Poi;
use Tobuli\Services\FractalSerializers\WithoutDataArraySerializer;
use Tobuli\Services\PoiUserService;
use FractalTransformer;

class PoisController extends Controller
{
    /**
     * @var PoiUserService
     */
    protected $service;

    protected function afterAuth($user)
    {
        $this->service = new PoiUserService($this->user);
    }

    public function index()
    {
        try {
            $this->checkException('poi', 'view');

            $pois = Poi::whereUserId($this->user->id)->with('mapIcon')->get();
        } catch (\Exception $e) {
            $pois = [];
        }

        return response()->json([
            'items' => [
                'mapIcons' => FractalTransformer::setSerializer(WithoutDataArraySerializer::class)
                    ->collection($pois, PoiTransformer::class)
                    ->toArray()
            ],
            'status' => 1
        ], 200);
    }

    public function store(Request $request)
    {
        $this->service->create($request->all());

        return response()->json([
            'status' => 1
        ], 200);
    }

    public function update(Request $request)
    {
        $item = Poi::find($request->get('id'));

        $this->service->edit($item, $request->all());

        return response()->json([
            'status' => 1
        ], 200);
    }

    public function changeActive(Request $request)
    {
        $id = $request->get('map_icon_id') ?? $request->get('id');

        $item = Poi::find($request->get('id'));

        $this->service->active($item, filter_var($request->get('active'), FILTER_VALIDATE_BOOLEAN) ? 1 : 0);

        return response()->json([
            'status' => 1
        ], 200);
    }

    public function destroy(Request $request)
    {
        $id = $request->get('map_icon_id') ?? $request->get('id');

        $item = Poi::find($id);

        $this->service->remove($item);

        return response()->json([
            'status' => 1
        ], 200);
    }
}
