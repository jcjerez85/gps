<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ActionPolicyManagerProvider extends ServiceProvider
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
        $this->app->singleton('\App\Policies\Action\ActionPolicyManager', function ($app) {
            return new \App\Policies\Action\ActionPolicyManager();
        });
    }
}
