<?php


use App\Support\Utility\Spam;
use App\Support\Utility\Sanitize;
use App\Support\Security\Protect;
 
/**
 * initialisation du schek spam boot
 */
Spam::spam_logs();

/**
 * Get values, slash, filter.
 */
if (!empty($_GET)) {
    array_walk_recursive($_GET, [Sanitize::class, 'addslashes_GPC']);
    array_walk_recursive($_GET, [Protect::class, 'url']);
}

/**
 * Post values, slash, filter.
 */
if (!empty($_POST)) {
    array_walk_recursive($_POST, [Sanitize::class, 'addslashes_GPC']);
}

/**
 * Extract cookie user
 */
// $user = Users::extractUser();
 
/**
 * Extract cookie admin
 */
// $admin = Authors::extractAdmin();
 
/**
 * Cookies - analyse et purge - shiney 07-11-2010
 */
// if (!empty($_SERVER)) {
//     extract($_SERVER, EXTR_OVERWRITE);
// }
 
/**
 * Env
 */
// if (!empty($_ENV)) {
//     extract($_ENV, EXTR_OVERWRITE);
// }
 
/**
 * Files
 */
// if (!empty($_FILES)) {
//     foreach ($_FILES as $key => $value) {
//         $$key = $value['tmp_name'];
//     }
// }
 
/**
 * Controle de la connection admin 
 */
// require_once("auth.inc.php");

/**
 * Cookie de l(utilisateur)
 */
// $cookie = Users::cookieUser();

/**
 * Initilisation de la session
 */
// Session::session_manage();
 
/**
 * Metalang 
 */
//Metalang::charg_metalang();
