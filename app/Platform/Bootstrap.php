<?php
/**
 * Two - Bootstrap
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

use Two\Foundation\Application;
use Two\Environment\EnvironmentVariables;
use Two\Config\Repository as ConfigRepository;
use Two\Foundation\AliasLoader;
use Two\Support\Facades\Facade;
use Two\Http\Request;

use Symfony\Component\HttpFoundation\BinaryFileResponse;


//--------------------------------------------------------------------------
// Définir la version de l'application.
//--------------------------------------------------------------------------

define('VERSION', '1.0.0');

//--------------------------------------------------------------------------
// Définir les options de rapport d'erreurs PHP.
//--------------------------------------------------------------------------

error_reporting(-1);

//--------------------------------------------------------------------------
// Définir le limiteur de cache de session PHP.
//--------------------------------------------------------------------------

session_cache_limiter('');

//--------------------------------------------------------------------------
// Utiliser en interne l'encodage UTF-8.
//--------------------------------------------------------------------------

if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('utf-8');
}

//--------------------------------------------------------------------------
// Charger la configuration globale.
//--------------------------------------------------------------------------

require APPPATH .'Config.php';

$app = new Application();

//--------------------------------------------------------------------------
// Détecter l'environnement d'application.
//--------------------------------------------------------------------------

$env = $app->detectEnvironment(array(
    'local' => array(
        'darkstar',
        'darkstar.localdomain',
        'darkstar.example.org',
        'NEO14A-4WH64'
    ),
));

//--------------------------------------------------------------------------
// Lier les chemins.
//--------------------------------------------------------------------------

$paths = array(
    'base'    => BASEPATH,
    'app'     => APPPATH,
    'module'  => MODULEPATH,
    'public'  => WEBPATH,
    'storage' => STORAGE_PATH,
);

$app->bindInstallPaths($paths);

//--------------------------------------------------------------------------
// Lier l'application dans le conteneur.
//--------------------------------------------------------------------------

$app->instance('app', $app);

//--------------------------------------------------------------------------
// Lier l'interface du gestionnaire d'exceptions.
//--------------------------------------------------------------------------

$app->singleton(
    'Two\Foundation\Exceptions\HandlerInterface', 'App\Platform\Exceptions\Handler'
);

//--------------------------------------------------------------------------
// Charger les façades du cadre.
//--------------------------------------------------------------------------

Facade::clearResolvedInstances();

Facade::setFacadeApplication($app);

//--------------------------------------------------------------------------
// Enregistrer les alias de façade dans des classes complètes.
//--------------------------------------------------------------------------

$app->registerCoreContainerAliases();

//--------------------------------------------------------------------------
// Enregistrer les variables d'environnement.
//--------------------------------------------------------------------------

with($envVariables = new EnvironmentVariables(
    $app->getEnvironmentVariablesLoader()

))->load($env);

//--------------------------------------------------------------------------
// Enregistrez le gestionnaire de configuration.
//--------------------------------------------------------------------------

$app->instance('config', $config = new ConfigRepository(
    $app->getConfigLoader(), $env
));

//--------------------------------------------------------------------------
// Enregistrer la gestion des exceptions d'application.
//--------------------------------------------------------------------------

$app->startExceptionHandling();

//if ($env !== 'testing') {
    ini_set('display_errors', 'On');
//}

//--------------------------------------------------------------------------
// Enregistrez le middleware d'application.
//--------------------------------------------------------------------------

$app->middleware(
    $config->get('app.middleware', array())
);

//--------------------------------------------------------------------------
// Définir le fuseau horaire par défaut à partir de la configuration.
//--------------------------------------------------------------------------

date_default_timezone_set(
    $config->get('app.timezone', 'Europe/London')
);

//--------------------------------------------------------------------------
// Enregistrez le chargeur d'alias.
//--------------------------------------------------------------------------

$aliases = $config->get('app.aliases', array());

AliasLoader::getInstance($aliases)->register();

//--------------------------------------------------------------------------
// Activer le remplacement de la méthode HTTP.
//--------------------------------------------------------------------------

Request::enableHttpMethodParameterOverride();

//--------------------------------------------------------------------------
// Activer la confiance de l'en-tête de type X-Sendfile.
//--------------------------------------------------------------------------

BinaryFileResponse::trustXSendfileTypeHeader();

//--------------------------------------------------------------------------
// Enregistrez les principaux fournisseurs de services.
//--------------------------------------------------------------------------

$app->getProviderRepository()->load(
    $app, $config->get('app.providers', array())
);

//--------------------------------------------------------------------------
// Enregistrer les fichiers de démarrage démarrés.
//--------------------------------------------------------------------------

$app->booted(function() use ($app, $env)
{

//--------------------------------------------------------------------------
// Charger le script de démarrage de l'environnement.
//--------------------------------------------------------------------------

$path = $app['path'] .DS .'Platform' .DS .'Environment' .DS .ucfirst($env) .'.php';

if (is_readable($path)) require $path;

//--------------------------------------------------------------------------
// Charger le script d'amorçage.
//--------------------------------------------------------------------------

$path = $app['path'] .DS .'Bootstrap.php';

if (is_readable($path)) require $path;

});

//--------------------------------------------------------------------------
// Retourner la demande.
//--------------------------------------------------------------------------

return $app;
