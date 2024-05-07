<?php
/**
 * Two - TinyMceServiceProvider
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

declare(strict_types=1);

namespace Shared\TinyMce;

use Shared\TinyMce\TinyMce;
use Two\Foundation\AliasLoader;
use Two\Support\ServiceProvider;


class TinyMceServiceProvider extends ServiceProvider
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
    public function boot()
    {

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
        $this->app->singleton('two.rgpd', function () {
            return new TinyMce();
        });

        // Register the Facades.
        $loader = AliasLoader::getInstance();

        $loader->alias('two-editeur-tinymce', 'shared\Editeur\Support\Facades\TinyMce');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('two-editeur-tinymce');
    }
}
