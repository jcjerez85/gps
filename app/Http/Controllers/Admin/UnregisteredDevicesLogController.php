<?php namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Request;
use Tobuli\Entities\UnregisteredDevice;

class UnregisteredDevicesLogController extends BaseController {
    function __construct() {
        parent::__construct();
    }

    public function index()
    {
        $items = UnregisteredDevice::orderBy('date', 'desc')->paginate(50);

        return view('admin::UnregisteredDevicesLog.' . (Request::ajax() ? 'table' : 'index'))->with(compact('items'));
    }

    public function destroy() {
        $id = Request::input('id');

        $ids = is_array( $id ) ? $id : [ $id ];

        UnregisteredDevice::whereIn('imei', $ids)->delete();

        return ['status' => 1];
    }
}
