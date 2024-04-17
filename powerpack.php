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

use npds\system\cache\cache;
use npds\system\support\str;
use npds\system\support\facades\DB;
use npds\system\messenger\messenger;


if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}


// note a revoir function a tranferer et eradiquer powerpack.php !!!!

global $powerpack;
$powerpack = true;

settype($op, 'string');

switch ($op) {
    
    // Instant Members Message
    case 'instant_message':
        messenger::Form_instant_message($to_userid);
        break;

    case 'write_instant_message':
        settype($copie, 'string');
        settype($messages, 'string');

        if (isset($user)) {
            $rowQ1 = cache::Q_Select("SELECT uid FROM " . $NPDS_Prefix . "users WHERE uname='$cookie[1]'", 3600);
            $uid = $rowQ1[0];
            $from_userid = $uid['uid'];

            if (($subject != '') or ($message != '')) {
                $subject = str::FixQuotes($subject) . '';
                $messages = str::FixQuotes($messages) . '';
                
                messenger::writeDB_private_message($to_userid, '', $subject, $from_userid, $message, $copie);
            }
        }

        Header('Location: '. site_url('index.php'));
        break;

    // Instant Members Message
    // Purge Chat Box
    case 'admin_chatbox_write':
        if ($admin) {
            $adminX = base64_decode($admin);
            $adminR = explode(':', $adminX);

            $Q = sql_fetch_assoc(sql_query("SELECT * FROM " . $NPDS_Prefix . "authors WHERE aid='$adminR[0]' LIMIT 1"));
            if ($Q['radminsuper'] == 1) {
                if ($chatbox_clearDB == 'OK') {
                    DB::table('chatbox')->where('date', '<=', (time() - (60 * 5)))->delete();
                }
            }
        }
        
        Header('Location: '. site_url('index.php'));
        break;
        // Purge Chat Box
}
