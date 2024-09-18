<?php

namespace App\Http\Controllers\Frontend;

use App\Exceptions\PermissionException;
use App\Http\Controllers\Controller;
use Tobuli\Entities\User;
use Tobuli\Services\UserSecondaryCredentialsService;

class SecondaryCredentialsController extends Controller
{
    protected ?User $actingUser;

    private UserSecondaryCredentialsService $uscService;
    private ?int $userId;

    public function __construct(?int $userId = null)
    {
        if (!config('auth.secondary_credentials')) {
            abort(404);
        }

        $this->userId = $userId;
        $this->uscService = new UserSecondaryCredentialsService();

        parent::__construct();
    }

    protected function afterAuth($user)
    {
        $this->actingUser = $this->userId
            ? User::userAccessible($user)->find($this->userId)
            : $user;

        if ($this->actingUser === null) {
            abort(404);
        }

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
        $credentials = $this->actingUser->secondaryCredentials()->paginate();

        if (request()->wantsJson()) {
            return $credentials;
        }

        return view('front::SecondaryCredentials.' . $view)->with(compact('credentials'));
    }

    public function create()
    {
        return view('front::SecondaryCredentials.create');
    }

    public function store()
    {
        $data = request()->all();

        $data['user_id'] = $this->actingUser->id;
        $notifyUser = !empty($data['account_created']);

        $item = $this->uscService->setNotifyUser($notifyUser)->store($data);

        return ['status' => 1, 'data' => $item->attributesToArray()];
    }

    public function edit()
    {
        $item = $this->actingUser->secondaryCredentials()->findOrFail(request()->id);

        return view('front::SecondaryCredentials.edit')->with(compact('item'));
    }

    public function update(): array
    {
        $item = $this->actingUser->secondaryCredentials()->findOrFail(request()->id);

        $data = request()->except('user_id');

        $notifyUser = !empty($data['password_generate']);

        $success = $this->uscService->setNotifyUser($notifyUser)->update($data, $item);

        return ['status' => (int)$success, 'data' => $item->attributesToArray()];
    }

    public function destroy(): array
    {
        $item = $this->actingUser->secondaryCredentials()->findOrFail(request()->id);

        $success = $item->delete();

        return ['status' => (int)$success];
    }
}
