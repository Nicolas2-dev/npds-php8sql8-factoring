<?php

declare(strict_types=1);

namespace Npds\Cookie;

use Npds\Support\ServiceProvider;


class CookieServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('cookie', function ($app)
        {
            $config = $app['config']['session'];

            return with(new CookieJar)->setDefaultPathAndDomain($config['path'], $config['domain']);
        });
    }
}
