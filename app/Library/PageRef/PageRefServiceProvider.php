<?php

namespace App\Library\PageRef;

use App\Library\PageRef\PageRefManager;

use Npds\Foundation\AliasLoader;
use Npds\Support\ServiceProvider;


class PageRefServiceProvider extends ServiceProvider
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
        // Register PageRefManager 
        $this->app->singleton('npds.pageref', function ($app)
        {
            return new PageRefManager($app);
        });

        // Register facade
        $loader = AliasLoader::getInstance();

        $loader->alias('npds.pageref', 'App\Support\Facades\PageRef');
    }

}