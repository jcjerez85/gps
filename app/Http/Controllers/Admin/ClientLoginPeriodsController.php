<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\PermissionException;
use Illuminate\Support\Facades\View;
use Tobuli\Entities\User;
use Tobuli\Services\ScheduleService;

class ClientLoginPeriodsController extends BaseController
{
    public function get(int $id)
    {
        $item = $id ? User::find($id) : new User();

        $this->checkException('users', 'view', $item);

        if (!$this->user->can('view', $item, 'login_periods')) {
            throw new PermissionException();
        }

        $loginPeriods = (new ScheduleService($item->login_periods ?? []))->getFormSchedules($this->user);

        return View::make('admin::Clients.login_periods')->with(compact('item', 'loginPeriods'));
    }
}
