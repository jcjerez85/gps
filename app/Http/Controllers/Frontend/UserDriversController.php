<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use CustomFacades\ModalHelpers\UserDriverModalHelper;

class UserDriversController extends Controller
{
    public function index() {
        $data = UserDriverModalHelper::get();

        if ($this->api)
            return ['items' => $data];

        return view('front::UserDrivers.index')->with($data);
    }

    public function table() {
        $data = UserDriverModalHelper::get();

        return view('front::UserDrivers.table')->with($data);
    }

    public function activityLog($id, $table = null)
    {
        $data = UserDriverModalHelper::activityLog($id);

        $table = $table ? 'Table' : '';

        return view('front::UserDrivers.activityLog' . $table)->with($data);
    }

    public function create()
    {
        $data = UserDriverModalHelper::createData();

        return !$this->api ? view('front::UserDrivers.create')->with($data) : $data;
    }

    public function store()
    {
        return UserDriverModalHelper::create();
    }

    public function edit()
    {
        $data = UserDriverModalHelper::editData();

        return is_array($data) && !$this->api ? view('front::UserDrivers.edit')->with($data) : $data;
    }

    public function update()
    {
        return UserDriverModalHelper::edit();
    }

    public function doDestroy($id)
    {
        $data = UserDriverModalHelper::doDestroy($id);

        return is_array($data) ? view('front::UserDrivers.destroy')->with($data) : $data;
    }

    public function destroy()
    {
        return UserDriverModalHelper::destroy();
    }

    public function doUpdate( $id ) {
        return UserDriverModalHelper::editField($id);
    }

}
