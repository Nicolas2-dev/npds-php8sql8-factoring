<?php

return array(

    /**
     * Debug Mode
     */
    'debug' => true, // When enabled the actual PHP errors will be shown.

    /**
     * The Website URL.
     */
    'url' => 'http://www.npds.local/',

    /**
     * Website Name.
     */
    'name' => 'Npds MVC Framework',

    /**
     * The default Timezone for your website.
     * http://www.php.net/manual/en/timezones.php
     */
    'timezone' => 'Europe/Paris',

    /*
     * Application Default Locale.
     */
    'locale' => 'en',

    /*
     * Application Fallback Locale.
     */
    'fallbackLocale' => 'en',

    /**
     * The Encryption Key.
     * This page can be used to generate key
     */
    'key' => 'SomeRandomStringThere_1234567890',

    /**
     * NPDS_Key
     */

    'NPDS_Key' => 'SomeRandomStringThere_1234567890',


    /**
     * The Platform's Middleware stack.
     */
    'middleware' => array(
        'App\Middleware\DispatchAssetFiles',
    ),

    /**
     * The Platform's route Middleware Groups.
     */
    'middlewareGroups' => array(
        'web' => array(
            'App\Middleware\SetupLanguage',
            'App\Middleware\HandleProfiling',
            'App\Middleware\EncryptCookies',
            'Npds\Cookie\Middleware\AddQueuedCookiesToResponse',
            'Npds\Session\Middleware\StartSession',
            'Npds\View\Middleware\ShareErrorsFromSession',
        ),
        'api' => array(
            'throttle:60,1',
        ),
    ),

    /**
     * The Platform's route Middleware.
     */
    'routeMiddleware' => array(
        'auth'     => 'Npds\Auth\Middleware\Authenticate',
        'guest'    => 'App\Middleware\RedirectIfAuthenticated',
        'throttle' => 'Npds\Routing\Middleware\ThrottleRequests',
        'csrf'     => 'App\Middleware\VerifyCsrfToken',
    ),

    /**
     * The registered Service Providers.
     */
    'providers' => array(

        // the Core system Npds
        'Npds\Auth\AuthServiceProvider',
        'Npds\Database\DatabaseServiceProvider',
        'Npds\Routing\RoutingServiceProvider',
        'Npds\Cookie\CookieServiceProvider',
        'Npds\Session\SessionServiceProvider',
        'Npds\Encryption\EncryptionServiceProvider',
        'Npds\Filesystem\FilesystemServiceProvider',
        'Npds\Hashing\HashServiceProvider',
        'Npds\Cache\CacheServiceProvider',
        'Npds\Pagination\PaginationServiceProvider',
        'Npds\Translation\TranslationServiceProvider',
        'Npds\Validation\ValidationServiceProvider',
        'Npds\View\ViewServiceProvider',

        // The Application Providers
        'App\Providers\AppServiceProvider',
        'App\Providers\EventServiceProvider',
        'App\Providers\RouteServiceProvider',
    ),


    'manifest' => storage_path(),

    
    /**
     * The registered Class Aliases.
     */
    'aliases' => array(
 
        /**
         * Facades sysem
         */
        'App'       => 'Npds\Support\Facades\App',
        'Auth'      => 'Npds\Support\Facades\Auth',
        'Cache'     => 'Npds\Support\Facades\Cache',
        'Config'    => 'Npds\Support\Facades\Config',
        'Cookie'    => 'Npds\Support\Facades\Cookie',
        'Crypt'     => 'Npds\Support\Facades\Crypt',
        'DB'        => 'Npds\Support\Facades\DB',
        'Event'     => 'Npds\Support\Facades\Event',
        'File'      => 'Npds\Support\Facades\File',
        'Forge'     => 'Npds\Support\Facades\Forge',
        'Hash'      => 'Npds\Support\Facades\Hash',
        'Input'     => 'Npds\Support\Facades\Input',
        'Lang'      => 'Npds\Support\Facades\Lang',
        'Log'       => 'Npds\Support\Facades\Log',
        'Redirect'  => 'Npds\Support\Facades\Redirect',
        'Response'  => 'Npds\Support\Facades\Response',
        'Route'     => 'Npds\Support\Facades\Route',
        'Schedule'  => 'Npds\Support\Facades\Schedule',
        'Session'   => 'Npds\Support\Facades\Session',
        'Url'       => 'Npds\Support\Facades\Url',
        'Validator' => 'Npds\Support\Facades\Validator',
        'View'      => 'Npds\Support\Facades\View',
    ),
);