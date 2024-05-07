<?php

namespace Modules\TwoPush\Providers;

use Two\Packages\Support\Providers\ModuleServiceProvider as ServiceProvider;


class ModuleServiceProvider extends ServiceProvider
{
    /**
     * The additional provider class names.
     *
     * @var array
     */
    protected $providers = array(
        'Modules\TwoPush\Providers\AuthServiceProvider',
        'Modules\TwoPush\Providers\EventServiceProvider',
        'Modules\TwoPush\Providers\RouteServiceProvider',
    );


    /**
     * Bootstrap the Application Events.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__ .'/../');

        // Configure the Package.
        $this->package('Modules/TwoPush', 'two_push', $path);

        // Bootstrap the Package.
        $path = $path .DS .'Bootstrap.php';

        $this->bootstrapFrom($path);
    }

    /**
     * Register the TwoPush Module Service Provider.
     *
     * This service provider is a convenient place to register your modules
     * services in the IoC container. If you wish, you may make additional
     * methods or service providers to keep the code more focused and granular.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        //
    }

}
