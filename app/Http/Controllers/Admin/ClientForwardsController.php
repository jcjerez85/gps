<?php namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Tobuli\Entities\Forward;
use Tobuli\Entities\User;


class ClientForwardsController extends BaseController
{
    function __construct() {
        parent::__construct();
    }

    public function get(Request $request, $id = null)
    {
        $item = User::find($id);

        $this->checkException('users', 'view', $item);

        $forwards = Forward::userAccessible($this->user)
            ->orWhere(function($query) use ($item) {
                $query->userAccessible($item);
            })
            ->get()
            ->pluck('title', 'id');

        return View::make('admin::Clients.forwards')->with(compact('item', 'forwards'));
    }
}
