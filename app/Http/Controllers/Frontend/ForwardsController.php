<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Tobuli\Entities\Forward;
use Tobuli\Services\ForwardService;

class ForwardsController extends Controller
{
    /**
     * @var ForwardService
     */
    private $forwardService;

    public function __construct()
    {
        parent::__construct();

        $this->forwardService = new ForwardService();
    }

    public function index(Request $request)
    {
        $this->checkException('forwards', 'view');

        $items = $this->getItems($request);

        if ($request->wantsJson())
            return $items;

        return View::make('front::Forwards.index')->with(compact('items'));
    }

    public function table(Request $request)
    {
        $this->checkException('forwards', 'view');

        $items = $items = $this->getItems($request);

        return View::make('front::Forwards.table')->with(compact('items'));
    }

    public function create()
    {
        $this->checkException('forwards', 'create');

        return View::make('front::Forwards.create')->with([
            'types' => $this->forwardService->getTypes(new Forward())
        ]);
    }

    public function edit(Request $request, int $id = null)
    {
        $item = Forward::userAccessible($this->user)->findOrFail($id ?: $request->get('id'));

        $this->checkException('forwards', 'edit', $item);

        return View::make('front::Forwards.edit')->with([
            'item' => $item,
            'types' => $this->forwardService->getTypes($item)
        ]);
    }

    public function store(Request $request)
    {
        $this->checkException('forwards', 'store');

        return new JsonResponse([
            'status' => 1,
            'item' => $this->forwardService->store($request->all(), $this->user)
        ]);
    }

    public function update(Request $request, int $id = null)
    {
        $item = Forward::userAccessible($this->user)->findOrFail($id ?: $request->get('id'));

        $this->checkException('forwards', 'update', $item);

        return new JsonResponse([
            'status' => 1,
            'item' => $this->forwardService->update($item, $request->all())
        ]);
    }

    public function destroy(Request $request)
    {
        $this->checkException('forwards', 'clean');

        $ids = (array)($request->get('id'));

        Forward::userAccessible($this->user)->whereIn('id', $ids)->delete();

        return new JsonResponse(['status' => 1]);
    }

    protected function getItems($request)
    {
        return Forward::userAccessible($this->user)
            ->search($request->input('s'))
            ->toPaginator(
                15,
                $request->input('sorting.sort_by', 'title'),
                $request->input('sorting.sort', 'asc'),
            );
    }
}
