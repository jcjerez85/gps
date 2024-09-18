<?php

namespace App\Policies\Property;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Tobuli\Entities\User;

abstract class PropertyPolicy
{
    /**
     * Policy for.
     */
    protected $entity;

    /**
     * Filterable properties.
     */
    protected $editable = [];
    protected $viewable = [];

    protected $permissions = [];

    public function __construct()
    {
        $this->permissions = config('tobuli.permissions');
    }

    public function edit(User $user, Model $model, $property)
    {
        if ( ! in_array($property, $this->editable))
            return true;

        if (false === $this->permission($user, $property, 'edit'))
            return false;

        if (false === $this->propertyEditable($user, $model, $property))
            return false;

        return $this->_edit($user, $model, $property);
    }

    public function view(User $user, Model $model, $property)
    {
        if ( ! in_array($property, $this->viewable))
            return true;

        if ( false === $this->permission($user, $property, 'view'))
            return false;

        if ( false === $this->propertyViewable($user, $model, $property))
            return false;

        return $this->_view($user, $model, $property);
    }

    public function notEditables(User $user, Model $model)
    {
        $excepts = [];

        foreach ($this->editable as $property) {
            if ($this->edit($user, $model, $property))
                continue;

            $excepts[] = $property;
        }

        return $excepts;
    }

    public function notViewables(User $user, Model $model)
    {
        $excepts = [];

        foreach ($this->viewable as $property) {
            if ($this->view($user, $model, $property))
                continue;

            $excepts[] = $property;
        }

        return $excepts;
    }

    protected function propertyEditable(User $user, Model $model, $property)
    {
        $property_policy_method = Str::camel($property) . "EditPolicy";

        if ( ! method_exists($this, $property_policy_method))
            return null;

        return call_user_func_array([$this, $property_policy_method], [$user, $model]);
    }

    protected function propertyViewable(User $user, Model $model, $property)
    {
        $property_policy_method = Str::camel($property) . "ViewPolicy";

        if ( ! method_exists($this, $property_policy_method))
            return null;

        return call_user_func_array([$this, $property_policy_method], [$user, $model]);
    }

    protected function permission(User $user, $property, $permission)
    {
        if ( ! isset($this->permissions["$this->entity.$property"]))
            return null;

        return $user->perm("$this->entity.$property", $permission);
    }

    protected function _edit(User $user, Model $model, $property)
    {
        return true;
    }

    protected function _view(User $user, Model $model, $property)
    {
        return true;
    }
}