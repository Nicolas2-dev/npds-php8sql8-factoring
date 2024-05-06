<?php

namespace App\Controllers\Core;

use App\Support\Auth\Users;
use App\Controllers\Core\BaseController;

use Npds\Http\Request;
use Npds\Support\Facades\DB;
use Npds\Support\Facades\Config;


class FrontController extends BaseController
{

    protected function initialize(Request $request)
    {
        parent::initialize($request);
        
        //
        $this->session_manage($request);
    }

    /**
     * Mise à jour la table session
     *
     * @return  void    [return description]
     */
    public static function session_manage(Request $request): void
    {
        $ip = getip();

        $cookie = Users::cookieUser(1);

        $username = $cookie ? $cookie : $ip; // pas bon ...

        if ($username == $ip) {
            $guest = 1;
        } else {
            $guest = 0; 
        }

        //==> geoloc
        //include("modules/geoloc/config/geoloc.conf");

        // if ($geo_ip == 1) {
        //     include "modules/geoloc/geoloc_refip.php";
        // }

        //<== geoloc
        $past = time() - 300;

        DB::table('session')->where('time', '<', $past)->delete();

        $session = DB::table('session')
                        ->select('time')
                        ->where('username', $username)
                        ->first();

        if ($session) {
            if ($session->time < (time() - 30)) {
                DB::table('session')->where('username', $username)->update(array(
                        'username'      => $username,
                        'time'          => time(),
                        'host_addr'     => $ip,
                        'guest'         => $guest,
                        'uri'           => $request->getUri(),
                        'agent'         => getenv("HTTP_USER_AGENT"),
                ));

                if ($guest == 0) {
                    DB::table('users')->where('uname', $username)->update(array(
                        'user_lastvisit'    => (time() + (int) Config::get('gmt') * 3600),
                    ));
                }
            }
        } else {
            DB::table('session')->insert(array(
                'username'      => $username,
                'time'          => time(),
                'host_addr'     => $ip,
                'guest'         => $guest,
                'uri'           => $request->getUri(),
                'agent'         => getenv("HTTP_USER_AGENT"),
            ));
        }
    }


}
