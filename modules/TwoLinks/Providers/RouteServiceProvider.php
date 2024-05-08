<?php

namespace Modules\TwoLinks\Providers;

use Two\Routing\Router;
use Two\Packages\Support\Providers\RouteServiceProvider as ServiceProvider;


class RouteServiceProvider extends ServiceProvider
{
    /**
     * The controller namespace for the module.
     *
     * @var string|null
     */
    protected $namespace = 'Modules\TwoLinks\Controllers';


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
     * Define the routes Admin for the module.
     *
     * @param  \Two\Routing\Router $router
     * @return void
     */
    public function map(Router $router)
    {
        $router->group(array('namespace' => $this->namespace), function ($router)
        {
            $basePath = $this->guessPackageRoutesPath();

            if (is_readable($path = $basePath .DS .'Admin.php')) {
                $router->group(array('prefix' => 'admin'), function ($router) use ($path)
                {
                    require $path;
                });
            }
        });

        parent::map($router);
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
