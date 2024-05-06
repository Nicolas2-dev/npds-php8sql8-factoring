<?php

declare(strict_types=1);

namespace App\Library\User;

use App\Library\User\UserManager;

use Npds\Foundation\AliasLoader;
use Npds\Support\ServiceProvider;


class UserServiceProvider extends ServiceProvider
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
        // Register UserManager 
        $this->app->singleton('npds.user', function ($app)
        {
            return new UserManager($app);
        });

        // Register facade
        $loader = AliasLoader::getInstance();

        $loader->alias('npds.user', 'App\Support\Facades\User');

    }
}