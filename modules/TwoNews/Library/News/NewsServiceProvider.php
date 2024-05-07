<?php

namespace Modules\TwoNews\Library\News;

use Two\Foundation\AliasLoader;
use Two\Support\ServiceProvider;


class NewsServiceProvider extends ServiceProvider
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
        // Register Metalang Manager 
        $this->app->bindShared('two_news', function ($app)
        {
            return new NewsManager($app);
        });

        // Register Metatag Manager facade
        $loader = AliasLoader::getInstance();

        $loader->alias('two_news', 'Modules\TwoCore\Support\Facades\News');
    }

}