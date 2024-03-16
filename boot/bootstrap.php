<?php

use npds\system\cookie\cookie;
use npds\system\session\session;
use npds\system\language\language;
use npds\system\language\metalang;

include("grab_globals.php");

include("config/config.php");

include("library/language/multi-langue.php");
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