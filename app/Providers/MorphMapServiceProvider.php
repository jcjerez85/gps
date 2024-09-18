<?php namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class MorphMapServiceProvider extends ServiceProvider {

    public function boot()
    {
        Relation::morphMap([
            'device'       => \Tobuli\Entities\Device::class,
            'user'         => \Tobuli\Entities\User::class,
            'billing_plan' => \Tobuli\Entities\BillingPlan::class,
            'device_plan'  => \Tobuli\Entities\DevicePlan::class,
        ]);
    }

    public function register() {}
}
