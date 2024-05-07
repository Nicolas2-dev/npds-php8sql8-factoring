<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* NPDS Copyright (c) 2002-2023 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\support\str;
use npds\support\auth\users;
use npds\support\cache\cache;
use npds\support\auth\authors;
use npds\support\utility\crypt;
use npds\system\support\facades\DB;
use npds\support\messenger\messenger;
use npds\system\support\facades\Request;


if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}


// note a revoir function a tranferer et eradiquer powerpack.php !!!!
global $powerpack;
$powerpack = true;

switch (Request::input('op')) {
    
    // Instant Members Message
    case 'instant_message':
        messenger::Form_instant_message(Request::query('to_userid'));
        break;

    case 'write_instant_message':

        $user = users::getUser();

        if (isset($user)) {

            $from_userid = cache::Q_Select3(DB::table('users')
                    ->select('uid')
                    ->where('uname', users::cookieUser(1))
                    ->first(), 3600, crypt::encrypt('user_messenger_white'));

            $subject = Request::input('subject');
            $messages = Request::input('messages');

            if (($subject != '') or ($message != '')) {
                $subject = str::FixQuotes($subject) . '';
                $messages = str::FixQuotes($messages) . '';
                
                messenger::writeDB_private_message(Request::input('to_userid'), '', $subject, $from_userid['uid'], $message, Request::input('copie'));
            }
        }

        Header('Location: '. site_url('index.php'));
        break;


}
