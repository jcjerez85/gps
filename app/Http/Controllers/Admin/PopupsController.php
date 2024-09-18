<?php namespace App\Http\Controllers\Admin;

use App\Notifications\PopupNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Tobuli\Entities\Popup;
use Tobuli\Services\NotificationService;

class PopupsController extends BaseController {

    private $section = 'popups';

    private $notificationService;

    public function __construct(NotificationService $notificationService) {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request) {
        $items = Popup::userControllable($request->user())->with(['user'])->toPaginator(
            $request->input('limit', 25),
            $request->input('sorting.sort_by', 'id'),
            $request->input('sorting.sort', 'desc')
        );

        return View::make('admin::'.ucfirst($this->section).'.' . ($request->ajax() ? 'table' : 'index'))
            ->with(['section'=>$this->section, 'items'  => $items]);
    }

    public function create() {
        return View::make('admin::'.ucfirst($this->section).'.' . 'create')
            ->with(['item' => new Popup(),'positions' => Popup::getPositions()]);
    }

    public function edit(Request $request, $id) {
        $item = Popup::userControllable($request->user())->find($id);

        if (empty($item))
            return modalError(dontExist('global.event'));

        return View::make('admin::'.ucfirst($this->section).'.edit')
            ->with(['item' => $item, 'positions' => Popup::getPositions()]);
    }

    public function store(Request $request)
    {
        if ($this->notificationService->save( $request->all(), $request->user() )) {
            return Response::json(['success'=>true, 'status' => 1]);
        }

        return Response::json(['success'=>false, 'status' => 1]);
    }

    public function storePreview(Request $request)
    {
        $popup = $this->notificationService->fill($request->except('id'), $request->user());
        $popup->id = microtime(true) * 1000;

        $notification = new PopupNotification($popup);

        try {
            Notification::send($request->user(), $notification);
        } catch (\Exception $e) {
        }

        return Response::json(['status' => 1]);
    }

    public function update(Request $request)
    {
        if ($this->notificationService->save( $request->all(), $request->user() )) {
            return Response::json(['success'=>true, 'status' => 1]);
        }

        return Response::json(['success'=>false, 'status' => 1]);
    }

    public function destroy(Request $request, int $id = null)
    {
        $ids = (array)($id ?: $request->get('id'));
        Popup::userControllable($request->user())->whereIn('id', $ids)->delete();

        return Response::json(['status' => 1]);
    }
}
