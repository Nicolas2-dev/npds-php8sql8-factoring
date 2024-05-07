<?php

namespace Modules\TwoPush\Providers;

use Two\Routing\Router;
use Two\Packages\Support\Providers\RouteServiceProvider as ServiceProvider;


class RouteServiceProvider extends ServiceProvider
{
    /**
     * The controller namespace for the module.
     *
     * @var string|null
     */
    protected $namespace = 'Modules\TwoPush\Controllers';


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
    }
}
