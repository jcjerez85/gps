<?php

namespace App\Http\Controllers\Admin;

use CustomFacades\Repositories\ApnConfigRepo;
use CustomFacades\Validators\AdminApnConfiguratorFormValidator;
use Illuminate\Support\Facades\Request;
use Tobuli\Entities\ApnConfig;

class ApnConfigController extends BaseController
{
    public function index()
    {
        $input = Request::all();
        $items = ApnConfigRepo::searchAndPaginate($input, 'name');

        return view('admin::ApnConfig.'.(Request::ajax() ? 'table' : 'index'), [
            'items' => $items,
        ]);
    }

    public function create()
    {
        return view('admin::ApnConfig.create');
    }

    public function store()
    {
        AdminApnConfiguratorFormValidator::validate('create', $this->data);

        $this->data['edited'] = 1;
        ApnConfig::create($this->data);

        return response()->json(['status' => 1]);
    }

    public function edit($id)
    {
        $item = ApnConfig::find($id);

        if (!$item) {
            return modalError(dontExist('front.apn_configuration'));
        }

        return view('admin::ApnConfig.edit', [
            'item' => $item,
        ]);
    }

    public function update()
    {
        AdminApnConfiguratorFormValidator::validate('update', $this->data);

        $model = ApnConfig::find($this->data['id']);
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
