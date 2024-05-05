<?php

declare(strict_types=1);

namespace Npds\Events;

use Npds\Support\ServiceProvider;


class EventServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('events', function ($app)
        {
            return new Dispatcher($app);
        });
    }
}