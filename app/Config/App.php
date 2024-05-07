<?php
/**
 * Two - App
 *
 * @author  Nicolas Devoy
 * @email   nicolas@Two-framework.fr 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

return array(
    /**
     * Mode débogage
     * Lorsque cette option est activée, les erreurs PHP réelles seront affichées.
     */
    'debug' => true, 

    /**
     * Le moteur de rendu des erreurs: "two, "whoops"
     */
    'exception_render' => 'whoops',

    /**
     * L'URL du site Web.
     */
    'url' => 'http://www.two.local/',

    /**
    * L'adresse e-mail de l'administrateur.
    */
    'email' => 'admin@Two.dev',

    /**
     * Le chemin du site Web.
     */
    'path' => '/',

    /**
     * Nom du site Web.
     */
    'name' => 'Two 1.0',

    /**
     * Le nom du thème par défaut ou false pour désactiver l'utilisation des thèmes.
     */
    'theme' => 'TwoFrontend',

    /**
     * La locale par défaut qui sera utilisée par la traduction.
     */
    'locale' => 'fr',

    /**
     * Le fuseau horaire par défaut de votre site Web.
     * http://www.php.net/manual/en/timezones.php
     */
    'timezone' => 'Europe/Paris',

    /**
     * La clé de cryptage.
     */
    'key' => 'SomeRandomStringThere_1234567890',

    /*
    |--------------------------------------------------------------- -------------------------
    | Configuration de la journalisation
    |--------------------------------------------------------------- -------------------------
    |
    | Ici, vous pouvez configurer les paramètres du journal pour votre application. Hors de
    | la boîte, Laravel utilise la bibliothèque de journalisation PHP Monolog. Cela donne
    | vous une variété de gestionnaires de journaux / formateurs puissants à utiliser.
    |
    | Paramètres disponibles : "single", "daily", "syslog", "errorlog"
    |
    */

    'log' => 'single',

    /**
     * La pile middleware de l'application.
     */
    'middleware' => array(
        'Two\Foundation\Http\Middleware\CheckForMaintenanceMode',
        'Two\Routing\Middleware\DispatchAssetFiles',
    ),

    /**
     * Les groupes de middleware de route de l'application.
     */
    'middlewareGroups' => array(
        'web' => array(
            'App\Middleware\HandleProfiling',
            'App\Middleware\EncryptCookies',
            'Two\Cookie\Middleware\AddQueuedCookiesToResponse',
            'Two\Session\Middleware\StartSession',
            'Two\Localization\Middleware\SetupLanguage',
            'Two\View\Middleware\ShareErrorsFromSession',
            'App\Middleware\VerifyCsrfToken',
            //'App\Middleware\MarkNotificationAsRead',
        ),
        'api' => array(
            'throttle:60,1',
        )
    ),

    /**
     * Middleware de route de l'Application.
     */
    'routeMiddleware' => array(
        'auth'     => 'Two\Auth\Middleware\Authenticate',
        'guest'    => 'App\Middleware\RedirectIfAuthenticated',
        'throttle' => 'Two\Routing\Middleware\ThrottleRequests',
    ),

    /**
     * Les fournisseurs de services enregistrés.
     */
    'providers' => array(
        'Two\Auth\AuthServiceProvider',
        'Two\Bus\BusServiceProvider',
        'Two\Broadcasting\BroadcastServiceProvider',
        'Two\Cache\CacheServiceProvider',
        'Two\Routing\RoutingServiceProvider',
        'Two\Cookie\CookieServiceProvider',
        'Two\Database\DatabaseServiceProvider',
        'Two\Encryption\EncryptionServiceProvider',
        'Two\Filesystem\FilesystemServiceProvider',
        'Two\Localization\LocalizationServiceProvider',
        'Two\Hashing\HashServiceProvider',
        'Two\Mail\MailServiceProvider',
        'Two\Notifications\NotificationServiceProvider',
        'Two\Packages\PackageServiceProvider',
        'Two\Pagination\PaginationServiceProvider',
        'Two\Queue\QueueServiceProvider',
        'Two\Redis\RedisServiceProvider',
        'Two\Session\SessionServiceProvider',
        'Two\Validation\ValidationServiceProvider',
        'Two\View\ViewServiceProvider',

        // Les fournisseurs Forge.
        'Two\Cache\ConsoleServiceProvider',
        'Two\Foundation\Providers\ConsoleSupportServiceProvider',
        'Two\Foundation\Providers\ForgeServiceProvider',
        'Two\Database\MigrationServiceProvider',
        'Two\Database\SeedingServiceProvider',
        'Two\Localization\ConsoleServiceProvider',
        'Two\Notifications\ConsoleServiceProvider',
        'Two\Packages\ConsoleServiceProvider',
        'Two\Routing\ConsoleServiceProvider',
        'Two\Session\ConsoleServiceProvider',

        // Les fournisseurs d'applications.
        'App\Providers\AppServiceProvider',
        'App\Providers\AuthServiceProvider',
        'App\Providers\EventServiceProvider',
        'App\Providers\RouteServiceProvider',
        'App\Providers\BroadcastServiceProvider',

        // shareds
        'Shared\RgpdCitron\RgpdCitronServiceProvider',
        'Shared\TinyMce\TinyMceServiceProvider',
    ),

    /**
     * Le chemin du manifeste des fournisseurs de services.
     */
    'manifest' => STORAGE_PATH .'framework',

    /**
     * Les alias de classe enregistrés.
     */
    'aliases' => array(

        // Les classes de support.
        'Arr'           => 'Two\Support\Arr',
        'Str'           => 'Two\Support\Str',

        // Le générateur de base de données.
        'Seeder'        => 'Two\Database\Seeder',

        // Les façades de soutien.
        'App'           => 'Two\Support\Facades\App',
        'Asset'         => 'Two\Support\Facades\Asset',
        'Auth'          => 'Two\Support\Facades\Auth',
        'Broadcast'     => 'Two\Support\Facades\Broadcast',
        'Bus'           => 'Two\Support\Facades\Bus',
        'Cache'         => 'Two\Support\Facades\Cache',
        'Config'        => 'Two\Support\Facades\Config',
        'Cookie'        => 'Two\Support\Facades\Cookie',
        'Crypt'         => 'Two\Support\Facades\Crypt',
        'DB'            => 'Two\Support\Facades\DB',
        'Event'         => 'Two\Support\Facades\Event',
        'File'          => 'Two\Support\Facades\File',
        'Forge'         => 'Two\Support\Facades\Forge',
        'Gate'          => 'Two\Support\Facades\Gate',
        'Hash'          => 'Two\Support\Facades\Hash',
        'Input'         => 'Two\Support\Facades\Input',
        'Language'      => 'Two\Support\Facades\Language',
        'Mailer'        => 'Two\Support\Facades\Mailer',
        'Notification'  => 'Two\Support\Facades\Notification',
        'Queue'         => 'Two\Support\Facades\Queue',
        'Redirect'      => 'Two\Support\Facades\Redirect',
        'Redis'         => 'Two\Support\Facades\Redis',
        'Request'       => 'Two\Support\Facades\Request',
        'Response'      => 'Two\Support\Facades\Response',
        'Route'         => 'Two\Support\Facades\Route',
        'Schedule'      => 'Two\Support\Facades\Schedule',
        'Schema'        => 'Two\Support\Facades\Schema',
        'Session'       => 'Two\Support\Facades\Session',
        'Validator'     => 'Two\Support\Facades\Validator',
        'Log'           => 'Two\Support\Facades\Log',
        'Url'           => 'Two\Support\Facades\Url',
        'Template'      => 'Two\Support\Facades\Template',
        'View'          => 'Two\Support\Facades\View',
        'Package'       => 'Two\Support\Facades\Package',

        //'Theme'         => 'Modules\TwoThemes\Support\Facades\Theme',
        //'Metatag'       => 'Modules\TwoCore\Support\Facades\Metatag',
    ),

);
