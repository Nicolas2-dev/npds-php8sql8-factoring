<?php

declare(strict_types=1);


namespace Npds\View;

use Npds\View\Factory;
use Npds\Support\ServiceProvider;


class ViewServiceProvider extends ServiceProvider
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
        $this->app->singleton('view', function ($app)
        {
            $factory = new Factory();

            $factory->share('__env', $factory);

            $factory->share('app', $app);

            return $factory;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('view');
    }
}
