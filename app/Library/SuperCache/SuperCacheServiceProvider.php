<?php

declare(strict_types=1);

namespace App\Library\SuperCache;

use App\Library\Supercache\SuperCacheEmpty;
use App\Library\Supercache\SuperCacheManager;

use Npds\Foundation\AliasLoader;
use Npds\Support\ServiceProvider;


class SuperCacheServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the Application events.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Register SuperCacheManager 
        $this->app->singleton('npds.super.cache.manager', function ($app)
        {
            return new SuperCacheManager($app['config']['cache']);
        });

        // Register SuperCacheEmptyManager
        $this->app->singleton('npds.super.cache.empty', function ($app)
        {
            return new SuperCacheEmpty();
        });

        // Register facade
        $loader = AliasLoader::getInstance();

        $loader->alias('npds.super.cache.manager', 'App\Support\Facades\SuperCacheManager');
        $loader->alias('npds.super.cache.empty', 'App\Support\Facades\superCacheEmpty');
    }
}