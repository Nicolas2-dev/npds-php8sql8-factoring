<?php

declare(strict_types=1);

namespace Modules\TwoCore\Library\Sform;

use Two\Foundation\AliasLoader;
use Two\Support\ServiceProvider;


class SformServiceProvider extends ServiceProvider
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
        // Register Sform Manager 
        $this->app->bindShared('two_sform', function ($app)
        {
            return new SformManager($app);
        });

        // Register Sform Manager facade
        $loader = AliasLoader::getInstance();

        $loader->alias('two_sform', 'Modules\TwoCore\Support\Facades\Sform');
    }

}