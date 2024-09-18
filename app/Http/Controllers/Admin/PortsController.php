<?php namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Tobuli\Helpers\Tracker;
use Tobuli\Validation\AdminTrackerPortsFormValidator;
use Tobuli\Exceptions\ValidationException;

class PortsController extends BaseController {
    /**
     * @var AdminTrackerPortsFormValidator
     */
    private $adminTrackerPortsFormValidator;

    function __construct(AdminTrackerPortsFormValidator $adminTrackerPortsFormValidator) {
        parent::__construct();
        $this->adminTrackerPortsFormValidator = $adminTrackerPortsFormValidator;
    }

    public function index(Request $request) {
        $ports = DB::table('tracker_ports')->get()->all(); //since lar5.3 get() method returns collection instead of array. all() method converts it to array

        return View::make('admin::Ports.'.($request->ajax() ? 'table' : 'index'))->with(compact('ports'));
    }

    public function edit($name) {
        $item = DB::table('tracker_ports')->where('name', '=', $name)->first();

        $settings = settings("protocols.{$item->name}");

        return View::make('admin::Ports.edit')->with(compact('item', 'settings'));
    }

    public function update($port_name, Request $request) {
        $input = $request->all();
        $item = DB::table('tracker_ports')->where('name', '=', $port_name)->first();

        $port = trim($input['port']);
        $extras = $input['extra'];

        $this->adminTrackerPortsFormValidator->validate('update', $input, $item->name);

        $arr = [];
        foreach ($extras as $extra) {
            $name = trim($extra['name']);
            $value = trim($extra['value']);
            if (empty($name) || empty($value))
                continue;

            $arr[$name] = $value;
        }

        DB::table('tracker_ports')->where('name', '=', $port_name)->update([
            'active' => isset($input['active']),
            'port' => $port,
            'extra' => json_encode($arr)
        ]);

        $settings = settings("protocols.$port_name");
        $settings = empty($settings) ? [] : $settings;
        $settings = array_merge($settings, $request->input('settings', []));
        settings("protocols.$port_name", $settings);


        return response()->json(['status' => 1]);
    }

    public function doUpdateConfig() {
        return View::make('admin::Ports.do_update_config');
    }

    public function updateConfig() {
        $tracker = new Tracker();
        $tracker->config()->update();
        $tracker->actor($this->user)->restart();

        Session::flash('message', trans('admin.successfully_updated_restarted'));

        return response()->json(['status' => 1]);
    }

    public function doResetDefault() {
        return View::make('admin::Ports.do_reset_default');
    }

    public function resetDefault() {
        DB::table('tracker_ports')->delete();
        parsePorts();

        $tracker = new Tracker();
        $tracker->config()->update();
        $tracker->actor($this->user)->restart();

        Session::flash('message', trans('admin.successfully_reset_default'));

        return response()->json(['status' => 1]);
    }
}
