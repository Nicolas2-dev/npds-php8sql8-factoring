<?php

declare(strict_types=1);

namespace Npds\Translation;

use Npds\Support\ServiceProvider;
use Npds\Translation\TranslationManager;


class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('translator', function($app)
        {
            return new TranslationManager($app, $app['path'] .DS .'Language');
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('translator');
    }
}
