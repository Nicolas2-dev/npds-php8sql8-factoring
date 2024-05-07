<?php

use Two\Support\Facades\DB;
use Two\Support\Facades\Config;

if (! function_exists('extratCookie'))
{
    /**
     * 
     *
     * @param   string  $name  [$name description]
     *
     * @return  string|bool
     */
    function extratCookie(string $name)
    {
        if (!empty($_COOKIE)) {
            extract($_COOKIE, EXTR_OVERWRITE);
        }

        if(isset($$name)) {
            return $$name;            
        }

        //return  false;
    }
}

if (! function_exists('cookiedecode'))
{
    /**
     * Décode le cookie membre et vérifie certaines choses (password)
     *
     * @param   string|bool  $user  [$user description]
     *
     * @return  array|bool
     */    
    function cookiedecode(string|bool $user)
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
            
            if (trim($cookie[1]) != '') {

                $users = DB::table('users')
                        ->select('pass', 'user_langue')
                        ->where('uname', $cookie[1])
                        ->first();

                if ($users) {

                    if (($cookie[2] == md5($users['pass'])) and ($users['pass'] != '')) {
                        
                        if (Config::get('two_core::config.language') != $users['user_langue']) {
                            DB::table('users')->where('uname', $cookie[1])->update(array(
                                'user_langue'       => Config::get('two_core::config.language'),
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

        return false;
    }
}
