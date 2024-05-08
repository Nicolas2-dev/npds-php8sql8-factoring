<?php

namespace Modules\TwoCore\Library\Metalang;

use Two\Foundation\AliasLoader;
use Two\Support\ServiceProvider;
use Modules\TwoCore\Library\Metalang\MetaLangManager;


class MetalangServiceProvider extends ServiceProvider
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
        // bug sur confid::get('two_core') ->config non charger !!!
        //Metalang::charg_metalang();        
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
        $this->app->bindShared('two_metalang', function ($app)
        {
            return new MetaLangManager($app, $app['request']);
        });

        // Register Metatag Manager facade
        $loader = AliasLoader::getInstance();

        $loader->alias('two_metalang', 'Modules\TwoCore\Support\Facades\Metalang');
    }

}