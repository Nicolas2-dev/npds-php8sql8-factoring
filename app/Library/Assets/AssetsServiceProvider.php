<?php

namespace App\Library\Assets;

use App\Library\Assets\AssetsManager;

use Npds\Foundation\AliasLoader;
use Npds\Support\ServiceProvider;


class AssetsServiceProvider extends ServiceProvider
{

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
        $this->app->singleton('npds.assets', function ($app)
        {
            return new AssetsManager($app);
        });

        // Register Page Ref Manager facade
        $loader = AliasLoader::getInstance();

        $loader->alias('npds.assets', 'App\Support\Facades\Assets');
    }

}