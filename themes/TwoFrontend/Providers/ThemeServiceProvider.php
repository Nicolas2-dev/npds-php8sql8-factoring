<?php

namespace Themes\TwoFrontend\Providers;


use Two\Foundation\AliasLoader;

use Themes\TwoFrontend\Library\ThemeOptions;
use Two\Packages\Support\Providers\ThemeServiceProvider as ServiceProvider;


class ThemeServiceProvider extends ServiceProvider
{

    /**
     * Indique si le chargement du provider est différé.
     *
     * @var bool
     */
    protected $defer = true;


    /**
     * Bootstrap the Application Events.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__ .'/../');

        // Configure the Package.
        $this->package('Themes/TwoFrontend', 'two_frontend', $path);

        // Bootstrap the Theme.
        require $path .DS .'Bootstrap.php';

        //
        $this->app->singleton('two_theme_options_frontend', function ($app) {
            return ThemeOptions::instance($app, $app['two_user']);
        });

        // Register the Facades.
        $loader = AliasLoader::getInstance();

        $loader->alias('two_theme_options_frontend', 'Themes\TwoFrontend\Support\Facades\ThemeOptions');
    }

    /**
     * Register the TwoFrontend Theme Service Provider.
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

        return array('two_theme_options_frontend');
    }

}
