<?php
/**
 * Two - RgpdCitronServiceProvider
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

declare(strict_types=1);

namespace Shared\RgpdCitron;

use Two\Foundation\AliasLoader;
use Two\Support\ServiceProvider;


class RgpdCitronServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * 
     */
    public $config;

    /**
     * 
     */
    public function boot()
    {
        $path = realpath(__DIR__ . '/');

        // On charge le fichier de constants.
        $this->loadConstant($path);
    }

    /**
     * Register the Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $path = realpath(__DIR__ . '/');

        // 
        $this->loadConfig($path);

        $config = $this->config;

        $this->app->singleton('two-rgpd', function () use ($config) {
            return new RgpdCitron($config);
        });

        // Register the Facades.
        $loader = AliasLoader::getInstance();

        $loader->alias('two-rgpd', 'shared\RgpdCitron\Support\Facades\Rgpd');
    }

    /**
     * On charge le fichier de configuration.
     *
     * @return void
     */
    private function loadConfig($path)
    {
        $this->config = include_once ($path .DS. 'config' .DS. 'Config.php');
    }

    /**
     * On charge le fichier de configuration.
     *
     * @return void
     */
    private function loadConstant($path)
    {
        include_once ($path .DS. 'config' .DS. 'Constants.php');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('two-rgpd');
    }
}
