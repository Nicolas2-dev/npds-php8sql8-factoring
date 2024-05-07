<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Module Bootstrap
|--------------------------------------------------------------------------
|
| Here is where you can register all of the Bootstrap for the Module.
*/

use Two\Support\Facades\DB;
use Two\Support\Facades\Config;
use Two\Support\Facades\Request;
use Modules\TwoUsers\Support\Facades\User;


// if (($aid = Request::input('aid')) and ($pwd = Request::input('pwd')) and (Request::input('op') == 'login')) {
    
//     if ($aid != '' and $pwd != '') {

//         $author_setinfo = DB::table('authors')
//                             ->select('pwd', 'hashkey')
//                             ->where('aid', $aid)
//                             ->first();

//         if ($author_setinfo) {

//             $dbpass = $author_setinfo['pwd'];
//             $scryptPass = false;

//             if (password_verify($pwd, $dbpass) or (strcmp($dbpass, $pwd) == 0)) {
                
//                 if (!$author_setinfo['hashkey']) {
//                     $AlgoCrypt = PASSWORD_BCRYPT;
//                     $min_ms = 100;
//                     $options = ['cost' => User::getOptimalBcryptCostParameter($pwd, $AlgoCrypt, $min_ms)];
//                     $hashpass = password_hash($pwd, $AlgoCrypt, $options);
//                     $pwd = crypt($pwd, $hashpass);
                    
//                     DB::table('authors')->where('aid', $aid)->update(array(
//                         'pwd'       => $pwd,
//                         'hashkey'   => 1,
//                     ));

//                     $_author_setinfo = DB::table('authors')
//                                             ->select('pwd', 'hashkey')
//                                             ->where('aid', $aid)
//                                             ->first();

//                     if ($_author_setinfo) {
//                         $dbpass = $_author_setinfo['pwd'];
//                         $scryptPass = crypt($dbpass, $hashpass);
//                     }
//                 }
//             }

//             if (password_verify($pwd, $dbpass)) {
//                 $CryptpPWD = $dbpass;
//             } elseif (password_verify($dbpass, $scryptPass) or strcmp($dbpass, $pwd) == 0) {
//                 $CryptpPWD = $pwd;
//             } else {
//                 Admin_Alert("Passwd not in DB#1 : $aid");
//             }

//             $admin = base64_encode("$aid:" . md5($CryptpPWD));

//             $admin_cook_duration = Config::get('npds.admin_cook_duration');

//             if ($admin_cook_duration <= 0) {
//                 $admin_cook_duration = 1;
//             }

//             $timeX = time() + (3600 * $admin_cook_duration);

//             setcookie('admin', $admin, $timeX);
//             setcookie('adm_exp', $timeX, $timeX);
//         }
//     }
// }

// #autodoc $admintest - $super_admintest : permet de savoir si un admin est connect&ecute; ($admintest=true) et s'il est SuperAdmin ($super_admintest=true)
// $admintest = false;
// $super_admintest = false;

// if (isset($admin) and ($admin != '')) {
    
//     global $aid;
    
//     $Xadmin = base64_decode($admin);
//     $Xadmin = explode(':', $Xadmin);
//     $aid = urlencode($Xadmin[0]);
//     $AIpwd = $Xadmin[1];

//     if ($aid == '' or $AIpwd == '') {
//         Admin_Alert('Null Aid or Passwd');
//     }

//     $res_author = DB::table('authors')
//         ->select('pwd', 'radminsuper')
//         ->where('aid', $aid)
//         ->first();

//     if (!$res_author) {
//         Admin_Alert("DB not ready #2 : $aid / $AIpwd");
//     } else {
        
//         if (md5($res_author['pwd']) == $AIpwd and $res_author['pwd'] != '') {
//             $admintest = true;
//             $super_admintest = $res_author['radminsuper'];
//         } else {
//             Admin_Alert("Password in Cookies not Good #1 : $aid / $AIpwd");
//         }
//     }
    
//     unset($AIpass);
//     unset($AIpwd);
//     unset($Xadmin);
//     unset($Xsuper_admintest);
// }
