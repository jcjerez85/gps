<?php namespace ModalHelpers;

use CustomFacades\Repositories\RouteRepo;
use CustomFacades\Validators\RouteFormValidator;
use Tobuli\Exceptions\ValidationException;

class RouteModalHelper extends ModalHelper
{
    public function get()
    {
        try {
            $this->checkException('routes', 'view');

            $routes = RouteRepo::whereUserId($this->user->id);
        } catch (\Exception $e) {
            $routes = [];
        }

        return compact('routes');
    }

    public function create()
    {
        $this->checkException('routes', 'store');

        $this->validate('create');

        RouteRepo::create($this->data + ['user_id' => $this->user->id]);

        return ['status' => 1];
    }

    public function edit()
    {
        $item = RouteRepo::find($this->data['id']);

        $this->checkException('routes', 'update', $item);

        $this->validate('update');

        RouteRepo::updateWithPolyline($item->id, $this->data);

        return ['status' => 1];
    }

    private function validate($type)
    {
        RouteFormValidator::validate($type, $this->data);
    }

    public function changeActive()
    {
        $item = RouteRepo::find($this->data['id']);

        $this->checkException('routes', 'active', $item);

        $active = (isset($this->data['active']) && filter_var($this->data['active'], FILTER_VALIDATE_BOOLEAN)) ? 1 : 0;

        RouteRepo::update($item->id, ['active' => $active]);

        return ['status' => 1];
    }

    public function destroy()
    {
        $id = array_key_exists('route_id', $this->data) ? $this->data['route_id'] : $this->data['id'];

        $item = RouteRepo::find($id);

        $this->checkException('routes', 'remove', $item);

        RouteRepo::delete($id);

        return ['status' => 1];
    }
}