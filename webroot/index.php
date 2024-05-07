<?php
/**
 * Two - Index
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

//--------------------------------------------------------------------------
// Définir les chemins absolus pour les répertoires d'application
//--------------------------------------------------------------------------

define('BASEPATH', realpath(__DIR__.'/../') .DS);

define('APPPATH', realpath(__DIR__.'/../app/') .DS);

define('MODULEPATH', realpath(__DIR__.'/../modules/') .DS);

define('WEBPATH', realpath(__DIR__) .DS);

//--------------------------------------------------------------------------
// Charger le chargeur automatique Composer
//--------------------------------------------------------------------------

require BASEPATH .'vendor' .DS .'autoload.php';

//--------------------------------------------------------------------------
// Démarrez le Framework et obtenez l'instance d'Application
//--------------------------------------------------------------------------

$app = require_once APPPATH .'Platform' .DS .'Bootstrap.php';

//--------------------------------------------------------------------------
// Exécutez l'application
//--------------------------------------------------------------------------

$app->run();

// vd($app);
