<?php

namespace Themes\TwoBackend\Providers;

use Two\Foundation\AliasLoader;
use Two\Support\Facades\Config;
use Themes\TwoBackend\Library\Theme;
use Themes\TwoBackend\Library\ThemeOptions;
use Two\Packages\Support\Providers\ThemeServiceProvider as ServiceProvider;


class ThemeServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the Application Events.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__ .'/../');

        // Configure the Package.
        $this->package('Themes/TwoBackend', 'two_backend', $path);

        // Bootstrap the Theme.
        require $path .DS .'Bootstrap.php';
        //
        $this->app->singleton('two_theme_options_backend', function ($app) {
            return ThemeOptions::instance($app, $app['two_user']);
        });

        // Register the Facades.
        $loader = AliasLoader::getInstance();

        $loader->alias('two_theme_options_backend', 'Themes\TwoBackend\Support\Facades\ThemeOptions');
    }

    /**
     * Register the TwoBackend Theme Service Provider.
     *
     * This service provider is a convenient place to register your themes
     * services in the IoC container. If you wish, you may make additional
     * methods or service providers to keep the code more focused and granular.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        return array('two_theme_options_backend');
    }

}
