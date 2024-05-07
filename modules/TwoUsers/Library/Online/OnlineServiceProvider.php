<?php

namespace Modules\TwoUsers\Library\Online;

use Two\Foundation\AliasLoader;
use Two\Support\ServiceProvider;
use Modules\TwoUsers\Library\Online\OnlineManager;


class OnlineServiceProvider extends ServiceProvider
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
        $this->app->bindShared('two_online', function ($app)
        {
            return new OnlineManager($app, $app['two_user']);
        });

        // Register Page Ref Manager facade
        $loader = AliasLoader::getInstance();

        $loader->alias('two_online', 'Modules\TwoUsers\Support\Facades\Online');
    }

}