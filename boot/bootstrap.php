<?php
declare(strict_types=1);

use npds\support\str;
use npds\support\auth\users;
use npds\support\auth\authors;
use npds\support\utility\spam;
use npds\system\config\Config;
use npds\system\app\AliasLoader;
use npds\support\session\session;
use npds\system\database\Manager;
use npds\support\security\protect;
use npds\support\language\language;
use npds\support\metalang\metalang;
use npds\system\support\facades\Request;
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

// initialisation du schek spam boot
spam::spam_logs();

// include current charset
if (file_exists(__DIR__ . "/constants.php")) {
    include(__DIR__ . "/constants.php");
}

// include doctype
if (file_exists(__DIR__ . "/doctype.php")) {
    include(__DIR__ . "/doctype.php");
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

// Cookies - analyse et purge - shiney 07-11-2010
if (!empty($_SERVER)) {
    extract($_SERVER, EXTR_OVERWRITE);
}

//vd($_SERVER);

// Env
if (!empty($_ENV)) {
    extract($_ENV, EXTR_OVERWRITE);
}

//vd($_ENV);

// Files
if (!empty($_FILES)) {
    foreach ($_FILES as $key => $value) {
        $$key = $value['tmp_name'];
    }
}

//vd($_FILES);

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
$choice_user_language = Request::input('choice_user_language');

// choix de du language utilisateur via block language
if (isset($choice_user_language)) {
    if ($choice_user_language != '') {

        $user_cook_duration = Config::get('npds.user_cook_duration');

        if ($user_cook_duration <= 0) {
            $user_cook_duration = 1;
        }

        $languageslist = language::languageList();

        if ((stristr($languageslist, $choice_user_language)) and ($choice_user_language != ' ')) {
            setcookie('user_language', $choice_user_language, (time() + (3600 * $user_cook_duration)));
            Config::set('npds.user_language', $user_language = $choice_user_language);
        }
    }
}

// si multilanguage est actif
if ((Config::get('npds.multi_langue')) && isset($user_language)) {
    if (($user_language != '') and ($user_language != " ")) {
        
        $languageslist = language::languageList();
        
        $tmpML = stristr($languageslist, $user_language);
        $tmpML = explode(' ', $tmpML);
        
        if ($tmpML[0]) {
            Config::set('npds.language', $tmpML[0]);
        }
    }
}

// on recupre la language du site 
include('language/'. Config::get('npds.language') .'/language.php');

// db ancien system qui va disparaitre !!!!
include('support/deprecated/connexion.php');

$dblink = Mysql_Connexion();

$NPDS_Prefix = '';

// controle de la connection admin 
require_once("auth.inc.php");

$cookie = users::cookieUser();

// inbitilisation de la session
session::session_manage();

// gestion metalang a revoir !!!!
metalang::charg_metalang();

// initilisation du time zone A revoir avec la estion des dates a finaliser !!!!
if (function_exists("date_default_timezone_set")) {
    date_default_timezone_set("Europe/Paris");
}

// initialisation de la  locale du site A revoir !!!!
language::initLocale();
