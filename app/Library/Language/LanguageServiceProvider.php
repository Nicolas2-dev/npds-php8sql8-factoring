<?php

namespace App\Library\Language;

use App\Support\Language\LanguageManager;

use Npds\Foundation\AliasLoader;
use Npds\Support\ServiceProvider;


class LanguageServiceProvider extends ServiceProvider
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
        // Register LanguageManager 
        $this->app->singleton('npds.language', function ($app)
        {
            return new LanguageManager($app);
        });

        // Register facade
        $loader = AliasLoader::getInstance();

        $loader->alias('npds.language', 'App\Support\Facades\Language');
    }

}