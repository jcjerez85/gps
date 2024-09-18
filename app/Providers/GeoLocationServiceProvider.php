<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Tobuli\Helpers\GeoLocation\GeoLocation;

class GeoLocationServiceProvider extends ServiceProvider
{
	/**
	 * @return void
	 */
	public function register()
	{
        $this->app->singleton('Tobuli\Helpers\GeoLocation\GeoLocation', function ($app) {
            return new GeoLocation();
        });
	}

}
