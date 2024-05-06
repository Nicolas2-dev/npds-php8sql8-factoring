<?php

declare(strict_types=1);

namespace App\Library\Sform;

use App\Libary\Sform\SformManager;

use Npds\Foundation\AliasLoader;
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
        // Register SformManager 
        $this->app->singleton('npds.sform', function ($app)
        {
            return new SformManager();
        });

        // Register facade
        $loader = AliasLoader::getInstance();

        $loader->alias('npds.sform', 'App\Support\Facades\Sform');

    }
}