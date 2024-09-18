<?php namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use CustomFacades\Settings;
use Tobuli\Exceptions\ValidationException;
use Validator;

class PluginsController extends BaseController {

    public function index()
    {
        $settings = Settings::get('plugins');

        $plugins = [];

        foreach($settings as $key => $plugin) {

            if ($key == 'beacons' && !config('addon.beacons'))
                continue;

            $plugins[] = (object)[
                'key'    => $key,
                'status' => $plugin['status'],
                'options'=> empty($plugin['options']) ? [] : $plugin['options'],
                'name'   => trans('front.' . $key)
            ];
        }

        return View::make('admin::Plugins.index')->with(compact('plugins'));
    }

    public function save()
    {
        $input = Request::all();

        try
        {
            $validator = Validator::make($input['plugins'], [
                'alert_sharing.options.duration.value' => 'required_if:alert_sharing.options.duration.active,1|integer',
            ]);

            if ($validator->fails())
                throw new ValidationException(['alert_sharing.options.duration.value' => 'The duration value must be an integer.']);

            Settings::set('plugins', $input['plugins']);

            return Redirect::route('admin.plugins.index')->withSuccess(trans('front.successfully_saved'));
        }
        catch (ValidationException $e)
        {
            return Redirect::route('admin.plugins.index')->withInput()->withErrors($e->getErrors());
        }
    }
}
