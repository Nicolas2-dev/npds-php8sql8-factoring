<?php

namespace App\Library\Groupe;

use App\Library\Groupe\GroupeManager;

use Npds\Foundation\AliasLoader;
use Npds\Support\ServiceProvider;


class GroupeServiceProvider extends ServiceProvider
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
        // Register GroupeManager 
        $this->app->singleton('npds.groupe', function ($app)
        {
            return new GroupeManager($app);
        });

        // Register facade
        $loader = AliasLoader::getInstance();

        $loader->alias('npds.groupe', 'App\Support\Facades\Groupe');
    }

}