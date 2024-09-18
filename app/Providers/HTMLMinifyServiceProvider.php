<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Engines\CompilerEngine;

use Tobuli\Helpers\HtmlMinifyCompiler;

class HTMLMinifyServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;
        $app->view->getEngineResolver()->register(
            'blade.php',
            function () use ($app) {
                $cachePath = storage_path() . '/framework/views';
                $compiler  = new HtmlMinifyCompiler(
                    $this->app['config']->get('htmlminify'),
                    $app['files'],
                    $cachePath
                );
                return new CompilerEngine($compiler);
            }
        );
        $app->view->addExtension('blade.php', 'blade.php');
    }
}