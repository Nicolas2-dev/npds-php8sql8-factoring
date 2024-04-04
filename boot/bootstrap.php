<?php
declare(strict_types=1);

use npds\system\config\Config;
use npds\system\cookie\cookie;
use npds\system\app\AliasLoader;
use npds\system\session\session;
use npds\system\database\Manager;
use npds\system\language\language;
use npds\system\language\metalang;
use npds\system\support\str;
use npds\system\utility\spam;
use npds\system\security\protect;
use npds\system\exception\ExceptionHandler;

require 'vendor/autoload.php';

// Load the configuration files.
foreach (glob('config/*.php') as $path) {
    $key = lcfirst(pathinfo($path, PATHINFO_FILENAME));

    if (($key[0] !== '_') and (!strstr($key, '.'))
    and (!strstr($key, 'cache.config'))
    and (!strstr($key, 'cache.timings'))
    and (!strstr($key, 'config'))
    and (!strstr($key, 'constants'))
    and (!strstr($key, 'doctype'))
    //and (!strstr($key, 'filemanager'))
    //and (!strstr($key, 'languages'))
    //and (!strstr($key, 'mailer'))
    and (!strstr($key, 'pages'))
    and (!strstr($key, 'sample.proxy'))
    and (!strstr($key, 'sample.rewrite_engine'))
    and (!strstr($key, 'section'))
    //and (!strstr($key, 'signat'))
    //and (!strstr($key, 'url_protect'))
    ) {
        Config::set($key, require($path));
    }  
}

//vd(Config::get('app.database.mysql_i'));
//vd(Config::get('mailer'));
//vd(Config::all());

if (!defined('NPDS_GRAB_GLOBALS_INCLUDED')) {
    define('NPDS_GRAB_GLOBALS_INCLUDED', 1);

    spam::spam_logs();

    // include current charset
    if (file_exists("constants.php")) {
        include("constants.php");
    }

    if (file_exists("doctype.php")) {
        include("doctype.php");
    }

    // Get values, slash, filter and extract
    if (!empty($_GET)) {
        array_walk_recursive($_GET, [str::class, 'addslashes_GPC']);
        reset($_GET); // no need

        array_walk_recursive($_GET, [protect::class, 'url']);
        extract($_GET, EXTR_OVERWRITE);
    }

    if (!empty($_POST)) {
        array_walk_recursive($_POST, [str::class, 'addslashes_GPC']);
        extract($_POST, EXTR_OVERWRITE);
    }

    // Cookies - analyse et purge - shiney 07-11-2010
    if (!empty($_COOKIE)) {
        extract($_COOKIE, EXTR_OVERWRITE);
    }

    if (isset($user)) {
        $ibid = explode(':', base64_decode($user));
        array_walk($ibid, [protect::class, 'url']);
        $user = base64_encode(str_replace("%3A", ":", urlencode(base64_decode($user))));
    }

    if (isset($user_language)) {
        $ibid = explode(':', $user_language);
        array_walk($ibid, [protect::class, 'url']);
        $user_language = str_replace("%3A", ":", urlencode($user_language));
    }

    if (isset($admin)) {
        $ibid = explode(':', base64_decode($admin));
        array_walk($ibid, [protect::class, 'url']);
        $admin = base64_encode(str_replace('%3A', ':', urlencode(base64_decode($admin))));
    }

    // Cookies - analyse et purge - shiney 07-11-2010
    if (!empty($_SERVER)) {
        extract($_SERVER, EXTR_OVERWRITE);
    }

    if (!empty($_ENV)) {
        extract($_ENV, EXTR_OVERWRITE);
    }

    if (!empty($_FILES)) {
        foreach ($_FILES as $key => $value) {
            $$key = $value['tmp_name'];
        }
    }
}

error_reporting(-1);

ini_set('display_errors', 'Off');

// Initialize the Exceptions Handler.
ExceptionHandler::initialize(true);


// Initialize the Aliases Loader.
AliasLoader::initialize();

//vd(Config::all());

$db = Manager::getInstance();

$db->connection()->setFetchMode(PDO::FETCH_ASSOC);


//include("config/config.php");
$NPDS_Prefix = '';
include_once('config/cache.config.php');
include_once('config/cache.timings.php');

// Multi-language
if (file_exists('storage/language/langcode.php')) {
    include('storage/language/langcode.php');
} else {
    $languageslist = language::languageList();
}

if (isset($choice_user_language)) {
    if ($choice_user_language != '') {

        $user_cook_duration = Config::get('app.user_cook_duration');

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



if ((Config::get('app.$multi_langue')) && isset($user_language)) {
    if (($user_language != '') and ($user_language != " ")) {
        $tmpML = stristr($languageslist, $user_language);
        $tmpML = explode(' ', $tmpML);
        
        if ($tmpML[0]) {
            Config::get('app.language', $tmpML[0]);
        }
    }
}
// Multi-language

$language = Config::get('app.language');

include("language/$language/language.php");

include('system/database/connexion.php');

/****************/
$dblink = Mysql_Connexion();

$mainfile = 1;

require_once("auth.inc.php");

if (isset($user)) {
    $cookie = cookie::cookiedecode($user);
}

session::session_manage();

$tab_langue = language::make_tab_langue();

global $meta_glossaire;
$meta_glossaire = metalang::charg_metalang();

if (function_exists("date_default_timezone_set")) {
    date_default_timezone_set("Europe/Paris");
}

language::initLocale();
