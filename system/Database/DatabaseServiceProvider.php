<?php

declare(strict_types=1);

namespace Npds\Database;

use Npds\Database\ORM\Model;
use Npds\Support\ServiceProvider;


class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the Application events.
     *
     * @return void
     */
    public function boot()
    {
        $resolver = $this->app['db'];

        $events = $this->app['events'];

        Model::setConnectionResolver($resolver);

        Model::setEventDispatcher($events);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('db', function ($app)
        {
            return new DatabaseManager($app);
        });
    }
}
