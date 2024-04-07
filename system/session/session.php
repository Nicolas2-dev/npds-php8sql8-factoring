<?php

declare(strict_types=1);

namespace npds\system\session;

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
        global $NPDS_Prefix, $cookie, $REQUEST_URI;

        $ip = getip();

        $username = isset($cookie[1]) ? $cookie[1] : $ip; // pas bon ...

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

        $result = sql_query("SELECT time FROM " . $NPDS_Prefix . "session WHERE username='$username'");
        
        if ($row = sql_fetch_assoc($result)) {
            if ($row['time'] < (time() - 30)) {
                sql_query("UPDATE " . $NPDS_Prefix . "session SET username='$username', time='" . time() . "', host_addr='$ip', guest='$guest', uri='$REQUEST_URI', agent='" . getenv("HTTP_USER_AGENT") . "' WHERE username='$username'");
                
                if ($guest == 0) {
                    global $gmt;
                    sql_query("UPDATE " . $NPDS_Prefix . "users SET user_lastvisit='" . (time() + (int) $gmt * 3600) . "' WHERE uname='$username'");
                }
            }
        } else {
            sql_query("INSERT INTO " . $NPDS_Prefix . "session (username, time, host_addr, guest, uri, agent) VALUES ('$username', '" . time() . "', '$ip', '$guest', '$REQUEST_URI', '" . getenv("HTTP_USER_AGENT") . "')");
        }
    }
}
