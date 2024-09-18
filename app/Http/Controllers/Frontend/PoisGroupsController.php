<?php namespace App\Http\Controllers\Frontend;

use App\Exceptions\ResourseNotFoundException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tobuli\Entities\Poi;
use Tobuli\Entities\PoiGroup;
use Tobuli\Services\PoiGroupService;

class PoisGroupsController extends Controller
{
    /**
     * @var PoiGroupService
     */
    protected $poiGroupService;

    public function __construct(PoiGroupService $poiGroupService)
    {
        parent::__construct();

        $this->poiGroupService = $poiGroupService;
    }

    public function create()
    {
        $this->checkException('pois_groups', 'create');

        $data = [
            'pois' => Poi::where('user_id', $this->user->id)->get(),
        ];

        return view('front::PoisGroups.create')->with($data);
    }

    public function store(Request $request)
    {
        $this->checkException('pois_groups', 'store');

        $data = array_merge($request->all(), ['user_id' => $this->user->id]);

        $item = $this->poiGroupService->create($data);

        $this->poiGroupService->syncItems($item, $data['pois']);

        return response()->json(['status' => 1, 'id' => $item->id]);
    }

    public function edit($id)
    {
        $item = PoiGroup::find($id);

        $this->checkException('pois_groups', 'edit', $item);

        $data = [
            'item' => $item,
            'pois' => Poi::where('user_id', $this->user->id)->get(),
        ];

        return view('front::PoisGroups.edit')->with($data);
    }

    public function update(Request $request, $id)
    {
        $item = PoiGroup::find($id);

        $this->checkException('pois_groups', 'edit', $item);

        $data = array_merge($request->all(), ['user_id' => $this->user->id]);

        $this->poiGroupService->update($item, $data);
        $this->poiGroupService->syncItems($item, $data['pois']);

        return response()->json([
            'id'     => $item->id,
            'status' => 1,
        ]);
    }

    public function changeStatus(Request $request) {
        $item = PoiGroup::find($request->get('id'));

        $this->checkException('pois_groups', 'active', $item);

        $this->poiGroupService->openStatus($item, !$item->open);

        return response()->json([
            'status' => 1,
        ]);
    }


}
