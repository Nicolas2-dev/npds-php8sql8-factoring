<?php

namespace Modules\TwoCore\Library\Assets;

use Two\Foundation\AliasLoader;
use Two\Support\ServiceProvider;
use Modules\TwoCore\Library\Assets\AssetsManager;


class AssetsServiceProvider extends ServiceProvider
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
        // Register Page Ref Manager 
        $this->app->bindShared('two_assets', function ($app)
        {
            return new AssetsManager($app);
        });

        // Register Page Ref Manager facade
        $loader = AliasLoader::getInstance();

        $loader->alias('two_assets', 'Modules\TwoCore\Support\Facades\CmsAssets');
    }

}