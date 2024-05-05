<?php

declare(strict_types=1);

namespace Npds\Sform;

use Npds\Sform\Sform;
use Npds\Support\ServiceProvider;


class SformServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the Application events.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('npds.sform', function ($app)
        {
            return Sform::instance();
        });
    }
}