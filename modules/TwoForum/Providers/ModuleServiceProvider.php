<?php

namespace Modules\TwoForum\Providers;

use Two\Packages\Support\Providers\ModuleServiceProvider as ServiceProvider;


class ModuleServiceProvider extends ServiceProvider
{
    /**
     * The additional provider class names.
     *
     * @var array
     */
    protected $providers = array(
        'Modules\TwoForum\Providers\AuthServiceProvider',
        'Modules\TwoForum\Providers\EventServiceProvider',
        'Modules\TwoForum\Providers\RouteServiceProvider',
    );


    /**
     * Bootstrap the Application Events.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__ .'/../');

        // Configure the Package.
        $this->package('Modules/TwoForum', 'two_forum', $path);

        // Bootstrap the Package.
        $bootstrap = $path .DS .'Bootstrap.php';

        $this->bootstrapFrom($bootstrap);

        // Chargement du helper
        $this->boot_helper($path);

        // Chargement des block
        $this->boot_box($path);
    }

    /**
     * Register the TwoCore Module Service Provider.
     *
     * This service provider is a convenient place to register your modules
     * services in the IoC container. If you wish, you may make additional
     * methods or service providers to keep the code more focused and granular.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

    }

    /** 
     * Helpers the Package.
     * 
     * @return void
     */
    private function boot_helper($path)
    {
        // 
        $path = $path .DS .'Support' .DS. 'helpers.php';

        $this->bootstrapFrom($path);
    }

    /**
     * Box the Package.
     * 
     * @return void
     */
    private function boot_box($path)
    {
        $path = $path .DS .'Support' .DS. 'boxe.php';

        $this->bootstrapFrom($path); 
    }

}
