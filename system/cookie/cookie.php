<?php

declare(strict_types=1);

namespace npds\system\cookie;

use npds\system\config\Config;
use npds\system\support\facades\DB;

class cookie
{

    /**
     * Décode le cookie membre et vérifie certaines choses (password)
     *
     * @param   string  $user  [$user description]
     *
     * @return  array|bool
     */    
    public static function cookiedecode(string $user): array|bool
    {
        $stop = false;

        if (array_key_exists("user", $_GET)) {
            if ($_GET['user'] != '') {
                $stop = true;
                $user = "BAD-GET";
            }
        } else if (isset($HTTP_GET_VARS)) {
            if (array_key_exists("user", $HTTP_GET_VARS)) {
                $stop = true;
                $user = "BAD-GET";
            }
        }

        if ($user) {
            $cookie = explode(':', base64_decode($user));
            
            settype($cookie[0], "integer");
            
            if (trim($cookie[1]) != '') {

                $users = DB::table('users')
                        ->select('pass', 'user_langue')
                        ->where('uname', $cookie[1])
                        ->first();

                if ($users) {

                    if (($cookie[2] == md5($users['pass'])) and ($users['pass'] != '')) {
                        
                        if (Config::get('npds.language') != $users['user_langue']) {
                            DB::table('users')->where('uname', $cookie[1])->update(array(
                                'user_langue'       => Config::get('npds.language'),
                            ));
                        }

                        return $cookie;
                    } else {
                        $stop = true;
                    }
                } else {
                    $stop = true;
                }
            } else {
                $stop = true;
            }

            if ($stop) {
                setcookie('user', '', 0);
                unset($user);
                unset($cookie);
                header("Location: index.php");
                return true;
            }
        }
    }
}
