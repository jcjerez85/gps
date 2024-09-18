<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use CustomFacades\ModalHelpers\UserGprsTemplateModalHelper;

class UserGprsTemplatesController extends Controller
{
    public function index()
    {
        $data = UserGprsTemplateModalHelper::get();

        if ($this->api)
            return ['items' => $data];

        return view('front::UserGprsTemplates.index')->with($data);
    }

    public function table()
    {
        $data = UserGprsTemplateModalHelper::get();

        return view('front::UserGprsTemplates.table')->with($data);
    }

    public function create()
    {
        $data = UserGprsTemplateModalHelper::createData();

        return is_array($data) && !$this->api ? view('front::UserGprsTemplates.create')->with($data) : $data;
    }

    public function store()
    {
        return UserGprsTemplateModalHelper::create();
    }

    public function edit()
    {
        $data = UserGprsTemplateModalHelper::editData();

        return is_array($data) && !$this->api ? view('front::UserGprsTemplates.edit')->with($data) : $data;
    }

    public function update()
    {
        return UserGprsTemplateModalHelper::edit();
    }

    public function getMessage()
    {
        $data = UserGprsTemplateModalHelper::getMessage();

        return isset($data['message']) ? $data['message'] : '';
    }

    public function doDestroy($id)
    {
        $data = UserGprsTemplateModalHelper::doDestroy($id);

        return is_array($data) ? view('front::UserGprsTemplates.destroy')->with($data) : $data;
    }

    public function destroy()
    {
        return UserGprsTemplateModalHelper::destroy();
    }
}
