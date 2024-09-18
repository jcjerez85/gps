<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use CustomFacades\ModalHelpers\UserSmsTemplateModalHelper;

class UserSmsTemplatesController extends Controller
{
    public function index()
    {
        $data = UserSmsTemplateModalHelper::get();

        if ($this->api)
            return ['items' => $data];

        return view('front::UserSmsTemplates.index')->with($data);
    }

    public function table()
    {
        $data = UserSmsTemplateModalHelper::get();

        return view('front::UserSmsTemplates.table')->with($data);
    }

    public function create()
    {
        $data = UserSmsTemplateModalHelper::createData();

        return is_array($data) && !$this->api ? view('front::UserSmsTemplates.create')->with($data) : $data;
    }

    public function store()
    {
        return UserSmsTemplateModalHelper::create();
    }

    public function edit()
    {
        $data = UserSmsTemplateModalHelper::editData();

        return is_array($data) && !$this->api ? view('front::UserSmsTemplates.edit')->with($data) : $data;
    }

    public function update()
    {
        return UserSmsTemplateModalHelper::edit();
    }

    public function getMessage()
    {
        $data = UserSmsTemplateModalHelper::getMessage();

        return isset($data['message']) ? (!$this->api ? $data['message'] : $data) : '';
    }

    public function doDestroy($id)
    {
        $data = UserSmsTemplateModalHelper::doDestroy($id);

        return is_array($data) ? view('front::UserSmsTemplates.destroy')->with($data) : $data;
    }

    public function destroy()
    {
        return UserSmsTemplateModalHelper::destroy();
    }
}
