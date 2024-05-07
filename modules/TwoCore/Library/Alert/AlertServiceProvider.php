<?php

namespace Modules\TwoCore\Library\Alert;

use Two\Foundation\AliasLoader;
use Two\Support\ServiceProvider;
use Modules\TwoCore\Library\Alert\AlertManager;


class AlertServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the Application Events.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the TwoCore Module Service Provider.
     *
     * This service provider is a convenient place to register your modules
     * services in the IoC container. If you wish, you may make additional
     * methods or service providers to keep the code more focused and granular.
     *
     * @return void
     */
    public function register()
    {
        // Register Page Ref Manager 
        $this->app->bindShared('two_alert', function ($app)
        {
            return new AlertManager($app);
        });

        // Register Page Ref Manager facade
        $loader = AliasLoader::getInstance();

        $loader->alias('two_alert', 'Modules\TwoCore\Support\Facades\Alert');
    }

}