<?php
/**
 * Two - RouteServiceProvider
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

namespace App\Providers;

use Two\Routing\Router;
use Two\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;


class RouteServiceProvider extends ServiceProvider
{
    /**
     * Cet espace de noms est appliqué aux routes du contrôleur dans votre fichier de routes.
     *
     * @var string
     */
    protected $namespace = 'App\Controllers';


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
     * Define the routes for the application.
     *
     * @param  \Two\Routing\Router  $router
     * @return void
     */
    public function map(Router $router)
    {
        $path = app_path('Routes');

        $router->group(array('prefix' => 'api', 'middleware' => 'api', 'namespace' => $this->namespace), function ($router) use ($path)
        {
            require $path .DS .'Api.php';
        });

        $router->group(array('middleware' => 'web', 'namespace' => $this->namespace), function ($router) use ($path)
        {
            require $path .DS .'Web.php';
        });
    }


    /**
     * Define the asset routes for the application.
     *
     * @return void
     */
    protected function registerAssetRoutes()
    {
        $dispatcher = $this->app['assets.dispatcher'];

        require app_path('Routes') .DS .'Assets.php';
    }
}
