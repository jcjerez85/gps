<?php
namespace App\Policies\Action;

use Illuminate\Support\Str;

class ActionPolicyManager
{
    protected $policyMap;

    public function policyFor($action)
    {
        $className = '\App\Policies\Action\\'.Str::studly($action).'Policy';

        if (! class_exists($className)) {
            throw new \Exception("Action \"{$action}\" class \"{$className}\" not found");
        }

        return new $className();
    }
}