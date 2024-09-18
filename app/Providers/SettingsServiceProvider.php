<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Tobuli\Helpers\Settings\SettingsConfig;
use Tobuli\Helpers\Settings\SettingsDB;

class SettingsServiceProvider extends ServiceProvider {

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\Tobuli\Helpers\Settings\SettingsDB::class, function ($app) {
            $settings = new SettingsDB();
            $settings->setParent(new SettingsConfig());

            return $settings;
        });
    }

}