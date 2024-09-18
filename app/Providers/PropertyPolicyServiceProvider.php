<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;


class PropertyPolicyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('\App\Policies\Property\PropertyPolicyManager', function ($app) {
            return new \App\Policies\Property\PropertyPolicyManager();
        });
    }
}
