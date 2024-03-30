<?php

use npds\system\config\Config;
use npds\system\cookie\cookie;
use npds\system\app\AliasLoader;
use npds\system\session\session;
use npds\system\database\Manager;
use npds\system\language\language;
use npds\system\language\metalang;
use npds\system\support\facades\DB;
use npds\system\exception\ExceptionHandler;


    error_reporting(-1);

    ini_set('display_errors', 'Off');

include("grab_globals.php");


// Load the configuration files.
foreach (glob('config/*.php') as $path) {
    $key = lcfirst(pathinfo($path, PATHINFO_FILENAME));

    if (($key[0] !== '_') and (!strstr($key, '.'))
    and (!strstr($key, 'cache.config'))
    and (!strstr($key, 'cache.timings'))
    and (!strstr($key, 'config'))
    and (!strstr($key, 'constants'))
    and (!strstr($key, 'doctype'))
    and (!strstr($key, 'filemanager'))
    and (!strstr($key, 'language'))
    and (!strstr($key, 'mailer'))
    and (!strstr($key, 'pages'))
    and (!strstr($key, 'sample.proxy'))
    and (!strstr($key, 'sample.rewrite_engine'))
    and (!strstr($key, 'section'))
    and (!strstr($key, 'signat'))
    and (!strstr($key, 'url_protect'))
    ) {
        Config::set($key, require($path));
    }  
}

// Initialize the Exceptions Handler.
ExceptionHandler::initialize(true);

// Initialize the Aliases Loader.
AliasLoader::initialize();

//vd(Config::all());

$db = Manager::getInstance();

$db->connection()->setFetchMode(PDO::FETCH_ASSOC);



// $users = DB::select('SELECT * FROM users');

// vd($users);

// $query = DB::table('users');

// $users = $query->where('uname', '!=', 'Anonyme')
//     ->limit(2)
//     ->orderBy('uname', 'desc')
//     ->get(array('uid', 'name', 'uname', 'email'));


// var_dump($users, $users[0]->uname);


// $query = DB::table('users');

// $user = $query->find(2);

// $query = DB::table('users');

// $users = $query->where('uname', '!=', 'anonyme')
//     //->limit('2')
//     //->orderBy('uname', 'desc')
//     ->get();

// $users = DB::table('users')->select('uid', 'uname', 'email')
// ->where('uname', '!=', 'admin')
// ->orderBy('uname', 'desc')
// ->limit(2)
// ->get();


// vd($users);

include("config/config.php");
include_once('config/cache.config.php');
include_once('config/cache.timings.php');

// Multi-language
if (file_exists('storage/language/langcode.php')) {
    include('storage/language/langcode.php');
} else {
    $languageslist = languageList();
}

if (isset($choice_user_language)) {
    if ($choice_user_language != '') {
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

if (($multi_langue) && isset($user_language)) {
    if (($user_language != '') and ($user_language != " ")) {
        $tmpML = stristr($languageslist, $user_language);
        $tmpML = explode(' ', $tmpML);
        
        if ($tmpML[0]) {
            $language = $tmpML[0];
        }
    }
}
// Multi-language

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
