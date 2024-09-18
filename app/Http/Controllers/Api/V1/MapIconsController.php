<?php namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use App\Transformers\ApiV1\MapIconTransformer;
use Tobuli\Entities\MapIcon;
use Tobuli\Services\FractalSerializers\WithoutDataArraySerializer;
use FractalTransformer;

class MapIconsController extends Controller
{
    public function index()
    {
        if (!$this->user->perm('poi', 'edit'))
            return response()->json([
                'perm'   => 0,
                'status' => 0,
            ], 403);

        return response()->json([
            'items' => FractalTransformer::setSerializer(WithoutDataArraySerializer::class)
                ->collection(MapIcon::all(), MapIconTransformer::class)
                ->toArray(),
            'status' => 1
        ], 200);
    }
}
