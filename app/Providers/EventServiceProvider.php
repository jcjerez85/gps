<?php

namespace App\Providers;

use App\Events\UserPasswordChanged;
use App\Events\UserSecondaryCredentialsPasswordChanged;
use App\Listeners\UserPasswordChangedListener;
use App\Listeners\UserSecondaryCredentialsPasswordChangedListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Illuminate\Auth\Events\Login' => [
            'App\Handlers\Events\AuthLoginEventHandler',
            'App\Listeners\SetActingUser',
        ],
        'Illuminate\Auth\Events\Logout' => [
            'App\Handlers\Events\AuthLogoutEventHandler',
        ],
        'App\Events\NewMessage' => [
            'App\Listeners\NewMessageListener'
        ],
        'App\Events\TranslationUpdated' => [
            'App\Listeners\TranslationUpdatedListener',
        ],
        'App\Events\TaskStatusChange' => [
            'App\Listeners\TaskStatusChangeListener',
        ],
        'App\Events\Device\DeviceSubscriptionRenew' => [
            'App\Listeners\SimProviderUnblock',
            'App\Listeners\SimExpirationRenew',
        ],
        'App\Events\Device\DeviceSubscriptionActivate' => [
            'App\Listeners\SimProviderUnblock',
            'App\Listeners\SimExpirationRenew',
        ],
        'App\Events\Device\DeviceSubscriptionExpire' => [
            'App\Listeners\SimProviderBlock',
        ],
        'App\Events\Device\DeviceDisabled' => [
            'App\Listeners\SimProviderBlock',
        ],
        'App\Events\Device\DeviceEnabled' => [
            'App\Listeners\SimProviderUnblock',
        ],
        'App\Events\DevicePositionChanged' => [
            'App\Listeners\GeofenceMoveListener',
        ],
        'App\Events\DeviceEngineChanged' => [
            'App\Listeners\DeviceResetDriverListener',
            'App\Listeners\DeviceResetRfidSensorListener'
        ],
        'App\Events\PositionResultRetrieved' => [
            'App\Listeners\PositionResultRetrievedListener',
        ],
        UserSecondaryCredentialsPasswordChanged::class => [
            UserSecondaryCredentialsPasswordChangedListener::class
        ],
        UserPasswordChanged::class => [
            UserPasswordChangedListener::class
        ],
    ];
}
