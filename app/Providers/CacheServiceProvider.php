<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Tobuli\Cache\SqliteStore;

class CacheServiceProvider extends ServiceProvider
{

	public function boot()
	{
        Cache::extend('sqlite', function ($app) {
            return Cache::repository(new SqliteStore);
        });
	}


	public function register() {}
}
