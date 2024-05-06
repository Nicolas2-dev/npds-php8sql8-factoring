<?php

declare(strict_types=1);

namespace App\Library\Theme;

use App\Library\Theme\ThemeManager;

use Npds\Foundation\AliasLoader;
use Npds\Support\ServiceProvider;


class ThemeServiceProvider extends ServiceProvider
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
            return new ThemeManager($app);
        });

        // Register Page Ref Manager facade
        $loader = AliasLoader::getInstance();

        $loader->alias('npds.theme', 'App\Support\Facades\Theme');

    }
}