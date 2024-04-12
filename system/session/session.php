<?php

declare(strict_types=1);

namespace npds\system\session;

use npds\system\auth\users;
use npds\system\config\Config;
use npds\system\support\facades\DB;

class session
{

    /**
     * Mise à jour la table session
     *
     * @return  void    [return description]
     */
    public static function session_manage(): void
    {
        global $REQUEST_URI;

        $ip = getip();

        $cookie = users::cookieUser(1);

        $username = $cookie ? $cookie : $ip; // pas bon ...

        if ($username == $ip) {
            $guest = 1;
        } else {
            $guest = 0; 
        }

        //==> geoloc
        include("modules/geoloc/config/geoloc.conf");

        if ($geo_ip == 1) {
            include "modules/geoloc/geoloc_refip.php";
        }

        //<== geoloc
        $past = time() - 300;

        DB::table('session')->where('time', '<', $past)->delete();

        $session = DB::table('session')
                        ->select('time')
                        ->where('username', $username)
                        ->first();

        if ($session) {
            if ($session['time'] < (time() - 30)) {
                DB::table('session')->where('username', $username)->update(array(
                        'username'      => $username,
                        'time'          => time(),
                        'host_addr'     => $ip,
                        'guest'         => $guest,
                        'uri'           => $REQUEST_URI,
                        'agent'         => getenv("HTTP_USER_AGENT"),
                ));

                if ($guest == 0) {
                    DB::table('users')->where('uname', $username)->update(array(
                        'user_lastvisit'    => (time() + (int) Config::get('npds.gmt') * 3600),
                    ));
                }
            }
        } else {
            DB::table('session')->insert(array(
                'username'      => $username,
                'time'          => time(),
                'host_addr'     => $ip,
                'guest'         => $guest,
                'uri'           => $REQUEST_URI,
                'agent'         => getenv("HTTP_USER_AGENT"),
            ));
        }
    }
}
