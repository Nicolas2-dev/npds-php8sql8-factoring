<?php

namespace App\Library\Block;

use App\Library\Block\BoxeManager;
use App\Library\Block\BlockManager;

use Npds\Foundation\AliasLoader;
use Npds\Support\ServiceProvider;


class BlockServiceProvider extends ServiceProvider
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
        // Register BoxeManager 
        $this->app->singleton('npds.boxe', function ($app)
        {
            return new BoxeManager($app);
        });

        // Register BlockManager 
        $this->app->singleton('npds.block', function ($app)
        {
            return new BlockManager($app);
        });

        // Register facade
        $loader = AliasLoader::getInstance();

        $loader->alias('npds.boxe', 'App\Support\Facades\Boxe');
        $loader->alias('npds.block', 'App\Support\Facades\Block');
    }

}