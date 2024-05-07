<?php

namespace Modules\TwoCore\Core;

use Carbon\Carbon;
use Two\Http\Request;
use Two\Support\Facades\DB;
use Two\Support\Facades\Config;
use Modules\TwoCore\Core\BaseController;


abstract class FrontController extends BaseController
{

    //protected $guard = 'user';


    /**
     * Le thème actuellement utilisé.
     *
     * @var string
     */
    protected $theme = 'TwoFrontend';

    /**
     * La mise en page actuellement utilisée.
     *
     * @var string
     */
    protected $layout = 'Default';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        parent::initialize($request);
        
        // initialisation session
        $this->session_manage($request);
    }  

    /**
     * Mise à jour la table session
     *  
     * @return void
     */
    private function session_manage(Request $request)
    {
        $ip = getIp();

        $user = null; //Auth::guard('user')->user();

        $username = isset($user->username) ? $user->username : $ip;

        if ($username == $ip) {
            $guest = 1;
        } else {
            $guest = 0;
        }

        //==> geoloc
        // include("modules/geoloc/config/geoloc.conf");

        // if ($geo_ip == 1) {
        //     include "modules/geoloc/geoloc_refip.php";
        // }


        DB::table('session')->where('time', '<', (time() - 300))->delete();

        $Qsession = DB::table('session')
                        ->select('time')
                        ->where('username', $username)
                        ->first();

        if ($Qsession) {
            if ($Qsession->time < (time() - 30)) {
                DB::table('session')
                    ->where('username', $username)
                    ->update([
                        'username'  => $username,
                        'time'      => time(),
                        'host_addr' => $ip ,
                        'guest'     => $guest,
                        'uri'       => $request->getUri(),
                        'agent'     => $request->server('HTTP_USER_AGENT'),
                    ]);

                if ($guest == 0) {

                    // NOTE : a revoir la gestion des times !!!
                    $time_lastvisit = (time() + (int) Config::get('two_core::config.gmt') * 3600);
                    //$time_lastvisit = Carbon::now(); //->timezone(Config::get('app.timezone'))->locale(Config::get('two-core::config.locale'));

                    DB::table('users')
                            ->select('time')
                            ->where('username', $username)
                            ->update(['user_lastvisit' => $time_lastvisit]);

                }
            }
        } else {
            DB::table('session')
                ->where('username', $username)
                ->insert([
                    'username'  => $username,
                    'time'      => time(),
                    'host_addr' => $ip ,
                    'guest'     => $guest,
                    'uri'       => $request->getUri(),
                    'agent'     => $request->server('HTTP_USER_AGENT'),
                ]);
        }
    }
}
