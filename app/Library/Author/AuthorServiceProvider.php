<?php

namespace App\Library\Author;

use App\Library\author\AuthorManager;

use Npds\Foundation\AliasLoader;
use Npds\Support\ServiceProvider;


class AuthorServiceProvider extends ServiceProvider
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
        // Register AuthorManager 
        $this->app->singleton('npds.author', function ($app)
        {
            return new AuthorManager($app);
        });

        // Register facade
        $loader = AliasLoader::getInstance();

        $loader->alias('npds.author', 'App\Support\Facades\Author');
    }

}