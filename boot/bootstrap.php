<?php

use npds\system\cookie\cookie;
use npds\system\session\session;
use npds\system\language\language;
use npds\system\language\metalang;

include("grab_globals.php");

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

// var_dump(language::getLocale2(), getLocaleIso());