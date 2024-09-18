<?php

$result = [
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    */

    'name' => 'GPS Server',
    'build' => '202310131456476203',
    'server' => env('server', 'localhost'),
    'admin_user' => env('admin_user', 'admin'),

    'force_https' => env('FORCE_HTTPS', false),

	/*
	|--------------------------------------------------------------------------
	| Application Debug Mode
	|--------------------------------------------------------------------------
	|
	| When your application is in debug mode, detailed error messages with
	| stack traces will be shown on every error that occurs within your
	| application. If disabled, a simple generic error page is shown.
	|
	*/

	'debug' => env('APP_DEBUG', false),

    'debug_blacklist' => [
        '_ENV' => array_keys($_ENV),
        '_SERVER' => array_keys($_ENV)
    ],

	/*
	|--------------------------------------------------------------------------
	| Application URL
	|--------------------------------------------------------------------------
	|
	| This URL is used by the console to properly generate URLs when using
	| the Artisan command line tool. You should set this to the root of
	| your application so that it is used when running Artisan tasks.
	|
	*/

	'url' => env('APP_URL', 'http://localhost'),

	/*
	|--------------------------------------------------------------------------
	| Application Timezone
	|--------------------------------------------------------------------------
	|
	| Here you may specify the default timezone for your application, which
	| will be used by the PHP date and date-time functions. We have gone
	| ahead and set this to a sensible default for you out of the box.
	|
	*/

	'timezone' => 'UTC',

	/*
	|--------------------------------------------------------------------------
	| Application Locale Configuration
	|--------------------------------------------------------------------------
	|
	| The application locale determines the default locale that will be used
	| by the translation service provider. You are free to set this value
	| to any of the locales which will be supported by the application.
	|
	*/

	'locale' => 'en',

	/*
	|--------------------------------------------------------------------------
	| Application Fallback Locale
	|--------------------------------------------------------------------------
	|
	| The fallback locale determines the locale to use when the current one
	| is not available. You may change the value to correspond to any of
	| the language folders that are provided through your application.
	|
	*/

	'fallback_locale' => 'en',

	/*
	|--------------------------------------------------------------------------
	| Encryption Key
	|--------------------------------------------------------------------------
	|
	| This key is used by the Illuminate encrypter service and should be set
	| to a random, 32 character string, otherwise these encrypted strings
	| will not be safe. Please do this before deploying an application!
	|
	*/

	'key' => env('APP_KEY', 'SomeRandomString'),

	'cipher' => 'AES-256-CBC', //@TODO: must regenerate key using key:generate command

	/*
	|--------------------------------------------------------------------------
	| Environment
	|--------------------------------------------------------------------------
	*/
	'env' => env('APP_ENV', 'production'),

	/*
	|--------------------------------------------------------------------------
	| Autoloaded Service Providers
	|--------------------------------------------------------------------------
	|
	| The service providers listed here will be automatically loaded on the
	| request to your application. Feel free to add your own services to
	| this array to grant expanded functionality to your applications.
	|
	*/

	'providers' => [

		/*
		 * Laravel Framework Service Providers...
		 */
		'Illuminate\Auth\AuthServiceProvider',
		'Illuminate\Bus\BusServiceProvider',
		'Illuminate\Cache\CacheServiceProvider',
		'Illuminate\Foundation\Providers\ConsoleSupportServiceProvider',
		'Illuminate\Cookie\CookieServiceProvider',
		'Illuminate\Database\DatabaseServiceProvider',
		'Illuminate\Encryption\EncryptionServiceProvider',
		'Illuminate\Filesystem\FilesystemServiceProvider',
		'Illuminate\Foundation\Providers\FoundationServiceProvider',
		'Illuminate\Hashing\HashServiceProvider',
		//'Illuminate\Mail\MailServiceProvider',
		'Illuminate\Pagination\PaginationServiceProvider',
		'Illuminate\Pipeline\PipelineServiceProvider',
		'Illuminate\Queue\QueueServiceProvider',
		'Illuminate\Redis\RedisServiceProvider',
		'Illuminate\Auth\Passwords\PasswordResetServiceProvider',
		'Illuminate\Session\SessionServiceProvider',
		'Illuminate\Translation\TranslationServiceProvider',
		'Illuminate\Validation\ValidationServiceProvider',
		'Illuminate\View\ViewServiceProvider',
		//'Illuminate\Html\HtmlServiceProvider',
        'Collective\Html\HtmlServiceProvider',
        'App\Providers\ConfigurableMailServiceProvider',
		'Maatwebsite\Excel\ExcelServiceProvider',
		'Bugsnag\BugsnagLaravel\BugsnagServiceProvider',
		'Illuminate\Broadcasting\BroadcastServiceProvider',
        'Barryvdh\DomPDF\ServiceProvider',
        //'App\Providers\HTMLMinifyServiceProvider',
        'Barryvdh\Snappy\ServiceProvider',
        'SimpleSoftwareIO\QrCode\QrCodeServiceProvider',
        'Yajra\DataTables\DatatablesServiceProvider',

		/*
		 * Application Service Providers...
		 */
		'App\Providers\AppServiceProvider',
        'App\Providers\AuthServiceProvider',
		'App\Providers\EventServiceProvider',
		'App\Providers\RouteServiceProvider',
        'App\Providers\SettingsServiceProvider',
        'App\Providers\ValidatorRulesServiceProvider',
		'Tobuli\Repositories\RepositoriesServiceProvider',
		'Fideloper\Proxy\TrustedProxyServiceProvider',
        'LaravelFCM\FCMServiceProvider',
        'Illuminate\Notifications\NotificationServiceProvider',
        'App\Providers\FractalTransformerServiceProvider',
        'App\Providers\PropertyPolicyServiceProvider',
        'App\Providers\FileSystemMacroProvider',
        'App\Providers\CollectionMacroProvider',
        'App\Providers\CacheServiceProvider',
        'App\Providers\FormatterServiceProvider',
        'App\Providers\TranslationServiceProvider',
        'App\Providers\FtpUserServiceProvider',
        'App\Providers\SharingServiceProvider',
        'App\Providers\ChecklistServiceProvider',
        'App\Providers\DeviceConfigServiceProvider',
        'App\Providers\DeviceConfigUpdateServiceProvider',
        'App\Providers\ActionPolicyManagerProvider',
        'App\Providers\LanguageServiceProvider',
        'App\Providers\MorphMapServiceProvider',
        'App\Providers\CustomValuesServiceProvider',
        'App\Providers\SimBlockingServiceProvider',
        'Mews\Captcha\CaptchaServiceProvider',
        'App\Providers\CaptchaServiceProvider',
        'App\Providers\AppearanceServiceProvider',
        'App\Providers\QueryBuilderMacrosProvider',
    ],

	/*
	|--------------------------------------------------------------------------
	| Class Aliases
	|--------------------------------------------------------------------------
	|
	| This array of class aliases will be registered when this application
	| is started. However, feel free to register as many as you wish as
	| the aliases are "lazy" loaded so they don't hinder performance.
	|
	*/

	'aliases' => [

		'App'       => 'Illuminate\Support\Facades\App',
        'Arr'       => Illuminate\Support\Arr::class,
		'Artisan'   => 'Illuminate\Support\Facades\Artisan',
		'Auth'      => 'Illuminate\Support\Facades\Auth',
		'Blade'     => 'Illuminate\Support\Facades\Blade',
		'Bus'       => 'Illuminate\Support\Facades\Bus',
		'Cache'     => 'Illuminate\Support\Facades\Cache',
		'Config'    => 'Illuminate\Support\Facades\Config',
		'Cookie'    => 'Illuminate\Support\Facades\Cookie',
		'Crypt'     => 'Illuminate\Support\Facades\Crypt',
		'DB'        => 'Illuminate\Support\Facades\DB',
		'Eloquent'  => 'Illuminate\Database\Eloquent\Model',
		'Event'     => 'Illuminate\Support\Facades\Event',
		'File'      => 'Illuminate\Support\Facades\File',
        'Gate'      => Illuminate\Support\Facades\Gate::class,
		'Hash'      => 'Illuminate\Support\Facades\Hash',
        'Http'      => Illuminate\Support\Facades\Http::class,
		'Inspiring' => 'Illuminate\Foundation\Inspiring',
		'Lang'      => 'Illuminate\Support\Facades\Lang',
		'Log'       => 'Illuminate\Support\Facades\Log',
		'Mail'      => 'Illuminate\Support\Facades\Mail',
		'Password'  => 'Illuminate\Support\Facades\Password',
		'Queue'     => 'Illuminate\Support\Facades\Queue',
		'Redirect'  => 'Illuminate\Support\Facades\Redirect',
		'Redis'     => 'Illuminate\Support\Facades\Redis',
		'Request'   => 'Illuminate\Support\Facades\Request',
		'Response'  => 'Illuminate\Support\Facades\Response',
		'Route'     => 'Illuminate\Support\Facades\Route',
		'Schema'    => 'Illuminate\Support\Facades\Schema',
		'Session'   => 'Illuminate\Support\Facades\Session',
		'Storage'   => 'Illuminate\Support\Facades\Storage',
        'Str'       => Illuminate\Support\Str::class,
		'URL'       => 'Illuminate\Support\Facades\URL',
		'Validator' => 'Illuminate\Support\Facades\Validator',
		'View'      => 'Illuminate\Support\Facades\View',

        'Carbon'    => 'Carbon\Carbon',
		'Form'      => 'Collective\Html\FormFacade',
		'HTML'      => 'Collective\Html\HtmlFacade',
		'Excel'     => 'Maatwebsite\Excel\Facades\Excel',
		'PDF'       => 'Barryvdh\DomPDF\Facade',
		'Bugsnag'   => 'Bugsnag\BugsnagLaravel\Facades\Bugsnag',
        'Settings'  => 'CustomFacades\Settings',
        'FCM'       => 'LaravelFCM\Facades\FCM',
        'FractalTransformer' => 'CustomFacades\FractalTransformerServiceFacade',
        'Formatter' => 'Tobuli\Helpers\Formatter\Facades\Formatter',
        'QrCode'    => 'SimpleSoftwareIO\QrCode\Facades\QrCode',
        'Language'  => 'CustomFacades\Language',
        'Datatables' => 'Yajra\DataTables\Facades\DataTables',
        'Notification' => 'Illuminate\Support\Facades\Notification',
        'Appearance'  => 'CustomFacades\Appearance',
        'Field' => 'CustomFacades\Field',
	],
];

if (env('APP_ENV') === 'local')
{
    $result['providers'][] = 'Barryvdh\Debugbar\ServiceProvider';
    $result['providers'][] = 'Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider';

    $result['aliases']['Debugbar'] = 'Barryvdh\Debugbar\Facade';

}

return $result;
