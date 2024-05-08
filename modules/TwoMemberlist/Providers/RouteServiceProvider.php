<?php

namespace Modules\TwoMemberlist\Providers;

use Two\Routing\Router;
use Two\Packages\Support\Providers\RouteServiceProvider as ServiceProvider;


class RouteServiceProvider extends ServiceProvider
{
    /**
     * The controller namespace for the module.
     *
     * @var string|null
     */
    protected $namespace = 'Modules\TwoMemberlist\Controllers';


    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Two\Routing\Router  $router
     * @return void
     */
    public function boot(Router $router)
    {
        parent::boot($router);

        //
        $this->registerAssetRoutes();
    }

    /**
     * Define the asset routes for the application.
     *
     * @return void
     */
    protected function registerAssetRoutes()
    {
        $dispatcher = $this->app['assets.dispatcher'];

        $path = realpath(__DIR__ .'/../');

        require $path .DS .'Routes' .DS .'Assets.php';
    }

}
