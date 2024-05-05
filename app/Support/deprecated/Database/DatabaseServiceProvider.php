<?php

declare(strict_types=1);

namespace Npds\Database;

use PDO;
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
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('npds.db', function ($app)
        {
            $manager = Manager::getInstance();
            $manager->connection()->setFetchMode(PDO::FETCH_ASSOC);

            return $manager;
        });
    }
}