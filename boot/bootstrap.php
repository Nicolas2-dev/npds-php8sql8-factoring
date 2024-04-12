<?php
declare(strict_types=1);

use npds\system\auth\users;
use npds\system\support\str;
use npds\system\theme\theme;
use npds\system\auth\authors;
use npds\system\http\Request;
use npds\system\utility\spam;
use npds\system\config\Config;
use npds\system\cookie\cookie;
use npds\system\app\AliasLoader;
use npds\system\session\session;
use npds\system\database\Manager;
use npds\system\security\protect;
use npds\system\language\language;
use npds\system\language\metalang;
use npds\system\language\metafunction;
use npds\system\exception\ExceptionHandler;

// Load autolad
require 'vendor/autoload.php';

// Load the configuration files.
foreach (glob('config/*.php') as $path) {
    $key = lcfirst(pathinfo($path, PATHINFO_FILENAME));

    if (($key[0] !== '_') and (!strstr($key, '.'))
    and (!strstr($key, 'config'))
    and (!strstr($key, 'pages'))
    and (!strstr($key, 'sample.proxy'))
    and (!strstr($key, 'sample.rewrite_engine'))
    and (!strstr($key, 'section'))
    ) {
        Config::set($key, require($path));
    }  
}

// grab global
if (!defined('NPDS_GRAB_GLOBALS_INCLUDED')) {
    define('NPDS_GRAB_GLOBALS_INCLUDED', 1);

    // initialisation du schek spam boot
    spam::spam_logs();

    // include current charset
    if (file_exists(__DIR__."/constants.php")) {
        include(__DIR__."/constants.php");
    }

    // include doctype
    if (file_exists(__DIR__."/doctype.php")) {
        include(__DIR__."/doctype.php");
    }

    // Get values, slash, filter and extract
    if (!empty($_GET)) {
        array_walk_recursive($_GET, [str::class, 'addslashes_GPC']);
        reset($_GET); // no need

        array_walk_recursive($_GET, [protect::class, 'url']);
        extract($_GET, EXTR_OVERWRITE);
    }

    // Post values, slash, filter and extract
    if (!empty($_POST)) {
        array_walk_recursive($_POST, [str::class, 'addslashes_GPC']);
        extract($_POST, EXTR_OVERWRITE);
    }

    // Cookies - analyse et purge - shiney 07-11-2010
    if (!empty($_COOKIE)) {
        extract($_COOKIE, EXTR_OVERWRITE);
    }

    // extract cookie user
    $user = users::extractUser();

    // extract cookie user_language
    if (isset($user_language)) {
        $ibid = explode(':', $user_language);
        array_walk($ibid, [protect::class, 'url']);
        $user_language = str_replace("%3A", ":", urlencode($user_language));
    }

    // extract cookie admin
    $admin = authors::extractAdmin();

    //if (isset($admin)) {
    if ($admin == true) {
        $ibid = explode(':', base64_decode($admin));
        array_walk($ibid, [protect::class, 'url']);
        $admin = base64_encode(str_replace('%3A', ':', urlencode(base64_decode($admin))));
    }

    // Cookies - analyse et purge - shiney 07-11-2010
    if (!empty($_SERVER)) {
        extract($_SERVER, EXTR_OVERWRITE);
    }

    // Env
    if (!empty($_ENV)) {
        extract($_ENV, EXTR_OVERWRITE);
    }

    // Files
    if (!empty($_FILES)) {
        foreach ($_FILES as $key => $value) {
            $$key = $value['tmp_name'];
        }
    }
}

// initialise errror reporting
error_reporting(-1);
ini_set('display_errors', 'Off');

// Initialize the Exceptions Handler.
ExceptionHandler::initialize(true);

// Initialize the Aliases Loader.
AliasLoader::initialize();

// initialisation de la database 
with(Manager::getInstance())->connection()->setFetchMode(PDO::FETCH_ASSOC);

// Multi-language
if (file_exists('storage/language/langcode.php')) {
    include('storage/language/langcode.php');
} else {
    $languageslist = language::languageList();
}

// choix de du language utilisateur via block language
if (isset($choice_user_language)) {
    if ($choice_user_language != '') {

        $user_cook_duration = Config::get('npds.user_cook_duration');

        if ($user_cook_duration <= 0) {
            $user_cook_duration = 1;
        }

        $timeX = time() + (3600 * $user_cook_duration);
        
        if ((stristr($languageslist, $choice_user_language)) and ($choice_user_language != ' ')) {
            setcookie('user_language', $choice_user_language, $timeX);
            $user_language = $choice_user_language;
        }
    }
}

// si multilanguage est actif
if ((Config::get('npds.$multi_langue')) && isset($user_language)) {
    if (($user_language != '') and ($user_language != " ")) {
        $tmpML = stristr($languageslist, $user_language);
        $tmpML = explode(' ', $tmpML);
        
        if ($tmpML[0]) {
            Config::get('npds.language', $tmpML[0]);
        }
    }
}

// on recupre la language du site 
$language = Config::get('npds.language');
include("language/$language/language.php");


// db ancien system qui va disparaitre !!!!
include('system/database/connexion.php');

$dblink = Mysql_Connexion();

$NPDS_Prefix = '';

// controle de la connection admin 
require_once("auth.inc.php");

$cookie = users::cookieUser();

// inbitilisation de la session
session::session_manage();

// tableau des language a  revoir va disparaitre prochainement !!!!
$tab_langue = language::make_tab_langue();

// gestion metalang a revoir !!!!
global $meta_glossaire;
$meta_glossaire = metalang::charg_metalang();

// initilisation du time zone A revoir avec la estion des dates a finaliser !!!!
if (function_exists("date_default_timezone_set")) {
    date_default_timezone_set("Europe/Paris");
}

// initialisation de la  locale du site A revoir !!!!
language::initLocale();
