<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Tobuli\Helpers\Language;

class LanguageServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Language::class, function ($app) {
            return new Language( 'en' );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Language::class];
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            \CustomFacades\Language::set( settings('main_settings.default_language') );
        } catch (\Exception $exception) {}
    }
}
