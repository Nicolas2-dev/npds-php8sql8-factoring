<?php

declare(strict_types=1);

namespace Npds\Filesystem;

use Npds\Support\ServiceProvider;


class FilesystemServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('files', function ()
        {
            return new Filesystem();
        });
    }

}
