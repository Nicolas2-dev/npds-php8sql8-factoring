<?php

namespace Modules\TwoCore\Library\Metatags;

use Two\Foundation\AliasLoader;
use Two\Support\Facades\Config;
use Two\Support\ServiceProvider;
//use Modules\TwoCore\Support\Facades\Metatag;
use Modules\TwoCore\Library\Metatags\MetatagManager;



class MetatagsServiceProvider extends ServiceProvider
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
        // Register Metatag Manager 
        $this->app->bindShared('two_metatag', function ($app)
        {
            $metatags = Config::get('two_core::metas');
            
            return new MetatagManager($app, $metatags);
        });

        // Register Metatag Manager facade
        $loader = AliasLoader::getInstance();

        $loader->alias('two_metatag', 'Modules\TwoCore\Support\Facades\Metatag');
    }

}