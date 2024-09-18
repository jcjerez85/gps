<?php namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Tobuli\Entities\User;
use Tobuli\Services\EntityLoader\DevicesGroupLoader;

class ClientDevicesController extends BaseController
{
    protected $devicesLoader;

    protected function afterAuth($user)
    {
        $this->devicesLoader = new DevicesGroupLoader($user);
        $this->devicesLoader->setRequestKey('objects');
    }

    public function index(Request $request)
    {
        $items = $this->devicesLoader->get();

        return response()->json($items);
    }

    public function get(Request $request, $user_id)
    {
        $user = User::find($user_id);

        $this->checkException('users', 'view', $user);

        $this->devicesLoader->setQueryStored($user->devices());

        $items = $this->devicesLoader->get();

        return response()->json($items);
    }
}
