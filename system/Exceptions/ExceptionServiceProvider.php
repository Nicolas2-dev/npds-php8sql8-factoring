<?php

declare(strict_types=1);

namespace Npds\Exceptions;

use Npds\Support\ServiceProvider;


class ExceptionServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('exception', function ($app)
        {
            return new Handler($app);
        });
    }
}