<?php

namespace App\Http\Controllers\Admin;

use CustomFacades\Repositories\DeviceConfigRepo;
use CustomFacades\Validators\AdminDeviceConfiguratorFormValidator;
use Illuminate\Support\Facades\Request;
use Tobuli\Entities\DeviceConfig;

class DeviceConfigController extends BaseController
{
    public function index()
    {
        $input = Request::all();
        $items = DeviceConfigRepo::searchAndPaginate($input, 'brand');

        return view('admin::DeviceConfig.'.(request()->ajax() ? 'table' : 'index'), [
            'items' => $items,
        ]);
    }

    public function create()
    {
        return view('admin::DeviceConfig.create');
    }

    public function store()
    {
        AdminDeviceConfiguratorFormValidator::validate('create', $this->data);

        $this->data['edited'] = 1;
        DeviceConfig::create($this->data);

        return response()->json(['status' => 1]);
    }

    public function edit($id)
    {
        $item = DeviceConfigRepo::find($id);

        if (!$item) {
            return modalError(dontExist('front.device_configuration'));
        }

        $commands = $item->commands;

        return view('admin::DeviceConfig.edit', [
            'item' => $item,
            'commands' => $commands,
        ]);
    }

    public function update()
    {
        AdminDeviceConfiguratorFormValidator::validate('update', $this->data);

        $model = DeviceConfig::find($this->data['id']);
        $model->fill($this->data);

        if ($model->isDirty()) {
            $model->edited = 1;
        }

        if (isset($this->data['use_default'])) {
            $model->edited = ! $this->data['use_default'];
        }

        $model->save();

        return response()->json(['status' => 1]);
    }
}
