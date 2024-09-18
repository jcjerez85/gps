<?php

namespace App\Http;

use App\Http\Middleware\ConfirmedAction;
use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\Referer;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{

    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode',
        'App\Http\Middleware\ServerActive',
        'App\Http\Middleware\TrustProxies',
        \Fruitcake\Cors\HandleCors::class,
	];


	/**
	 * The application's route middleware.
	 *
	 * @var array
	 */
	protected $routeMiddleware = [
	    'confirmed_action' => ConfirmedAction::class,
	    'auth' => 'App\Http\Middleware\Authenticate',
		'auth.basic' => 'Illuminate\Auth\Middleware\AuthenticateWithBasicAuth',
        'auth.api' => 'App\Http\Middleware\ApiAuthenticate',
        'auth.tracker' => 'App\Http\Middleware\TrackerAuth',
        'auth.admin' => 'App\Http\Middleware\AdminAuthenticate',
        'auth.manager' => 'App\Http\Middleware\ManagerAuthenticate',
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'guest' => 'App\Http\Middleware\RedirectIfAuthenticated',
        'active_subscription' => 'App\Http\Middleware\ActiveSubscription',
        'server_active' => 'App\Http\Middleware\ServerActive',
        'bindings' => 'Illuminate\Routing\Middleware\SubstituteBindings',
        'throttle' => 'Illuminate\Routing\Middleware\ThrottleRequests',
        'captcha' => 'App\Http\Middleware\Captcha',
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'verified' => EnsureEmailIsVerified::class,
    ];

    protected $middlewareGroups = [
        'web' => [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            // \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\Referer::class,
            \App\Http\Middleware\SetLang::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\EnsureEmailIsVerified::class,
            \App\Http\Middleware\TokenAuth::class,

        ],

        'api' => [
            \App\Http\Middleware\ApiActive::class,
            \App\Http\Middleware\SetLang::class,
            \App\Http\Middleware\EnsureEmailIsVerified::class,
            // 'throttle:60,1',
            // 'bindings',
        ],

        'app' => [

        ],
    ];
}
