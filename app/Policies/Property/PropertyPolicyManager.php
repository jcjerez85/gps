<?php

namespace App\Policies\Property;

use Illuminate\Database\Eloquent\Model;
use Tobuli\Entities\Device;
use Tobuli\Entities\User;

class PropertyPolicyManager
{
    protected $policyMap = [
        User::class => UserPropertiesPolicy::class,
        Device::class => DevicePropertiesPolicy::class
    ];

    public function policyFor(Model $entity)
    {
        return new $this->policyMap[get_class($entity)];
    }
}