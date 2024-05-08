<?php


use Modules\TwoCore\Support\Protect;
use Modules\TwoCore\Support\Sanitize;
use Modules\TwoCore\Support\Facades\Language;
use Modules\TwoCore\Support\Facades\Metalang;



/*
|--------------------------------------------------------------------------
| Module Bootstrap
|--------------------------------------------------------------------------
|
| Here is where you can register all of the Bootstrap for the Module.
*/

// include current charset
if (file_exists(module_path('TwoCore', 'Constants.php'))) {
    include module_path('TwoCore', 'Constants.php');
}

// include doctype
if (file_exists(module_path('TwoCore', 'Doctype.php'))) {
    include module_path('TwoCore', 'Doctype.php');
}

// initialisation du schek spam boot
// Spam::spam_logs();

// // Get values, slash, filter
if (!empty($_GET)) {
    array_walk_recursive($_GET, [Sanitize::class, 'addslashes_GPC']);
    array_walk_recursive($_GET, [Protect::class, 'url']);
}

// // Post values, slash, filter
if (!empty($_POST)) {
    array_walk_recursive($_POST, [Sanitize::class, 'addslashes_GPC']);
    array_walk_recursive($_POST, [Protect::class, 'url']);
}

// // Cookies - analyse et purge - shiney 07-11-2010
// if (!empty($_COOKIE)) {
//     extract($_COOKIE, EXTR_OVERWRITE);
// }

// // extract cookie user
// $user = Users::extractUser();

// // extract cookie user_language
// if (isset($user_language)) {
//     $ibid = explode(':', $user_language);
//     array_walk($ibid, [Protect::class, 'url']);
//     $user_language = str_replace("%3A", ":", urlencode($user_language));
// }

// // extract cookie admin
// $admin = Authors::extractAdmin();

// // Cookies - analyse et purge - shiney 07-11-2010
// if (!empty($_SERVER)) {
//     extract($_SERVER, EXTR_OVERWRITE);
// }

// //vd($_SERVER);

// // Env
// if (!empty($_ENV)) {
//     extract($_ENV, EXTR_OVERWRITE);
// }

// //vd($_ENV);

// // Files
// if (!empty($_FILES)) {
//     foreach ($_FILES as $key => $value) {
//         $$key = $value['tmp_name'];
//     }
// }

// //vd($_FILES);


// // Multi-language
// $choice_user_language = Request::input('choice_user_language');

// // choix de du language utilisateur via block language
// if (isset($choice_user_language)) {
//     if ($choice_user_language != '') {

//         $user_cook_duration = Config::get('npds.user_cook_duration');

//         if ($user_cook_duration <= 0) {
//             $user_cook_duration = 1;
//         }

//         $languageslist = Language::languageList();

//         if ((stristr($languageslist, $choice_user_language)) and ($choice_user_language != ' ')) {
//             setcookie('user_language', $choice_user_language, (time() + (3600 * $user_cook_duration)));
//             Config::set('npds.user_language', $user_language = $choice_user_language);
//         }
//     }
// }

// // si multilanguage est actif
// if ((Config::get('npds.multi_langue')) && isset($user_language)) {
//     if (($user_language != '') and ($user_language != " ")) {
        
//         $languageslist = Language::languageList();
        
//         $tmpML = stristr($languageslist, $user_language);
//         $tmpML = explode(' ', $tmpML);
        
//         if ($tmpML[0]) {
//             Config::set('npds.language', $tmpML[0]);
//         }
//     }
// }

// // on recupre la language du site 
// include(APPPATH .'language/'. Config::get('npds.language') .'/language.php');

// $cookie = Users::cookieUser();

// // inbitilisation de la session
// Session::session_manage();

//
Metalang::charg_metalang();

// // initilisation du time zone A revoir avec la estion des dates a finaliser !!!!
// if (function_exists("date_default_timezone_set")) {
//     date_default_timezone_set("Europe/Paris");
// }




// // initialisation de la  locale du site A revoir !!!!
// Language::initLocale();

