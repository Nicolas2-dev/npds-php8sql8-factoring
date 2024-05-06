<?php

namespace App\Library\Metalang;

use App\Library\Metalang\MetalangManager;
use App\Library\Metalang\MetaFunctionManager;

use Npds\Foundation\AliasLoader;
use Npds\Support\ServiceProvider;


class MetaLangServiceProvider extends ServiceProvider
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
        // Register MetalangManager 
        $this->app->singleton('npds.metalang', function ($app)
        {
            return new MetalangManager($app);
        });

        // Register MetaFunctionManager 
        $this->app->singleton('npds.metafunction', function ($app)
        {
            return new MetaFunctionManager($app);
        });

        // Register facade
        $loader = AliasLoader::getInstance();

        $loader->alias('npds.metalang', 'App\Support\Facades\Metalang');
        $loader->alias('npds.metafunction', 'App\Support\Facades\MetaFunction');
    }

}