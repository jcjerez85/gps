<?php namespace App\Providers;

use App\Services\Mail\MailManager;
use Illuminate\Mail\MailServiceProvider;
use Illuminate\Support\Arr;

class ConfigurableMailServiceProvider extends MailServiceProvider {

    /**
     * Register the Swift Transport instance.
     *
     * @return void
     */
    protected function registerIlluminateMailer()
    {
        $this->loadConfig();

        $this->app->singleton('mail.manager', function ($app) {
            return new MailManager($app);
        });

        $this->app->bind('mailer', function ($app) {
            return $app->make('mail.manager')->mailer();
        });
    }

    protected function loadConfig()
    {
        try {
            $settings = settings('email');
        } catch (\Exception $e) {
            $settings = [];
        }

        $config = [
            'driver' => Arr::get($settings, 'provider', 'mail'),
            'host'   => Arr::get($settings, 'smtp_server_host', ''),
            'port'   => Arr::get($settings, 'smtp_server_port', ''),
            'from'   => [
                'address' => Arr::get($settings, 'noreply_email', config('mail.from.address')),
                'name'    => Arr::get($settings, 'from_name', config('mail.from.name')),
            ],
            'encryption' => Arr::get($settings, 'smtp_security', ''),
            'auth'       => Arr::get($settings, 'smtp_authentication', 1),
            'username'   => Arr::get($settings, 'smtp_username', ''),
            'password'   => Arr::get($settings, 'smtp_password', ''),
        ];

        if ( $config['driver'] == 'smtp' && empty($settings['use_smtp_server']) )
            $config['driver'] = 'mail';

        switch ($config['driver']) {
            case 'smtp':
                if ( ! $config['auth']) {
                    unset($config['username'], $config['password']);
                }
                break;
            case 'sendgrid':
                $this->app['config']->set('services.sendgrid', [
                    'secret' => Arr::get($settings, 'api_key', '')
                ]);
                break;
            case 'postmark':
                $this->app['config']->set('services.postmark', [
                    'secret' => Arr::get($settings, 'api_key', '')
                ]);
                break;
            case 'gpswoxmailer':
                $this->app['config']->set('services.gpswoxmailer', [
                    'api_key' => Arr::get($settings, 'api_key', '')
                ]);
                break;
            case 'mailgun':
                switch (Arr::get($settings, 'region', null)) {
                    case 'eu':
                        $endpoint = 'api.eu.mailgun.net';
                        break;
                    default:
                        $endpoint = null;
                }

                $this->app['config']->set('services.mailgun', [
                    'secret' => Arr::get($settings, 'api_key', ''),
                    'domain' => Arr::get($settings, 'domain', ''),
                    'endpoint' => $endpoint,
                ]);
                break;
        }

        $this->app['config']->set('mail', $config);
    }
}