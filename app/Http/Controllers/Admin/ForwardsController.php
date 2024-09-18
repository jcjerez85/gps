<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Tobuli\Entities\Forward;
use Tobuli\Entities\User;
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

        return View::make('admin::Forwards.index')->with(compact('items'));
    }

    public function table(Request $request)
    {
        $this->checkException('forwards', 'view');

        $items = $items = $this->getItems($request);

        return View::make('admin::Forwards.table')->with(compact('items'));
    }

    public function create()
    {
        $this->checkException('forwards', 'create');

        $item = new Forward();
        $item->user_id = $this->user->id;

        return View::make('admin::Forwards.create')->with(
            $this->getFormData($item)
        );
    }

    public function edit(Request $request, int $id = null)
    {
        $item = Forward::userControllable($this->user)->findOrFail($id ?: $request->get('id'));

        $this->checkException('forwards', 'edit', $item);

        return View::make('admin::Forwards.edit')->with(
            $this->getFormData($item)
        );
    }

    public function store(Request $request)
    {
        $this->checkException('forwards', 'store');

        return new JsonResponse([
            'status' => 1,
            'item' => $this->forwardService->store($request->all(), $request->input('user_id'))
        ]);
    }

    public function update(Request $request, int $id = null)
    {
        $item = Forward::userControllable($this->user)->findOrFail($id ?: $request->get('id'));

        $this->checkException('forwards', 'update', $item);

        return new JsonResponse([
            'status' => 1,
            'item' => $this->forwardService->update($item, $request->all(), $request->input('user_id'))
        ]);
    }

    public function destroy(Request $request)
    {
        $this->checkException('forwards', 'clean');

        $ids = (array)($request->get('id'));

        Forward::userControllable($this->user)->whereIn('id', $ids)->delete();

        return new JsonResponse(['status' => 1]);
    }

    protected function getFormData(Forward $item)
    {
        return [
            'item' => $item,
            'users' => User::userAccessible($this->user)->get()->pluck('email', 'id'),
            'types' => $this->forwardService->getTypes($item)
        ];
    }

    protected function getItems($request)
    {
        return Forward::userControllable($this->user)
            ->with('user')
            ->search($request->input('search_phrase'))
            ->toPaginator(
                25,
                $request->input('sorting.sort_by', 'title'),
                $request->input('sorting.sort', 'asc'),
            );
    }
}
