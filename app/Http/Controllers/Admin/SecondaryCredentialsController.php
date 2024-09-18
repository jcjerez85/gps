<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\PermissionException;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Tobuli\Entities\User;
use Tobuli\Entities\UserSecondaryCredentials;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Services\UserSecondaryCredentialsService;

class SecondaryCredentialsController extends BaseController
{
    private UserSecondaryCredentialsService $uscService;

    public function __construct()
    {
        if (!config('auth.secondary_credentials')) {
            abort(404);
        }

        $this->uscService = new UserSecondaryCredentialsService();

        parent::__construct();
    }

    protected function afterAuth($user)
    {
        if (!$user->isMainLogin()) {
            throw new PermissionException();
        }
    }

    public function index()
    {
        return $this->getList('index');
    }

    public function table()
    {
        return $this->getList('table');
    }

    private function getList(string $view)
    {
        $input = request()->input();

        $items = UserSecondaryCredentials::with('user')
            ->search($input['search_phrase'] ?? '')
            ->toPaginator(20, 'email', 'asc');

        return View::make('admin::SecondaryCredentials.' . $view)
            ->with(compact('items', 'input'));
    }

    public function create()
    {
        $users = $this->getUsers();

        return view('front::SecondaryCredentials.create')->with(compact('users'));
    }

    public function store()
    {
        $data = request()->all();

        $this->validateUser($data);

        $notifyUser = !empty($data['account_created']);

        $item = $this->uscService->setNotifyUser($notifyUser)->store($data);

        return ['status' => 1, 'data' => $item->attributesToArray()];
    }

    public function edit(int $id)
    {
        $users = $this->getUsers();
        $item = $this->getQuery()->findOrFail($id);

        return view('front::SecondaryCredentials.edit')->with(compact('item', 'users'));
    }

    public function update(int $id): array
    {
        $item = $this->getQuery()->findOrFail($id);

        $data = request()->all();

        $this->validateUser($data);

        $notifyUser = !empty($data['password_generate']);

        $success = $this->uscService->setNotifyUser($notifyUser)->update($data, $item);

        return ['status' => (int)$success, 'data' => $item->attributesToArray()];
    }

    public function destroy($id = null)
    {
        $ids = Request::get('id', $id);

        if (empty($ids)) {
            return Response::json(['status' => 1]);
        }

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $this->getQuery()->whereIn('id', $ids)->delete();

        return Response::json(['status' => 1]);
    }

    private function getQuery()
    {
        return UserSecondaryCredentials::userAccessible($this->user);
    }

    private function getUsers(): array
    {
        return User::userAccessible($this->user)
            ->orderBy('email')
            ->pluck('email', 'id')
            ->prepend('-- ' . trans('admin.select') . ' --', '')
            ->all();
    }

    private function validateUser(array $data)
    {
        if (!isset($data['user_id'])) {
            return;
        }

        $user = User::userAccessible($this->user)->find($data['user_id']);

        if ($user === null) {
            throw new ValidationException(['user_id' => trans('front.user_not_found')]);
        }
    }
}
