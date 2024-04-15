<?php
/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* NPDS Copyright (c) 2002-2020 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\system\cache\cache;
use npds\system\config\Config;
use npds\system\support\facades\DB;
use npds\system\support\facades\Request;

if (!function_exists("Mysql_Connexion")) {
    header('location: ' . site_url('index.php'));
}

if ($rowQ1 = cache::Q_Select3(
    DB::table('config')
        ->select('*')
        ->get(), 3600, 'tbl_config(*)')) 
{
    foreach ($rowQ1[0] as $key => $value) {
       // $$key = $value;
        
        if ($key == 'upload_table') {
            Config::set('forum.config.upload_table', DB::getTablePrefix() . $value);
        } else {
            Config::set('forum.config.'.$key, $value);            
        }       
    }
}

//vd(Config::get('forum.config'));

$forum = Request::query('forum');

if (Config::get('forum.config.allow_upload_forum')) {
    if ($rowQ1 = cache::Q_Select3(
        DB::table('forums')
            ->select('attachement')
            ->where('forum_id', Request::query('forum'))
            ->get(), 3600, 'tbl_forum(attachement)')) 
    {
        foreach ($rowQ1[0] as $value) {
            //$allow_upload_forum = $value;
            Config::set('forum.config.allow_upload_forum', $value);
        }
    }
}

if ($rowQ1 = cache::Q_Select3(
    DB::table('forums')
        ->select('forum_pass')
        ->where('forum_id', $forum_id = Request::query('forum'))
        ->where('forum_type', 1)
        ->get(), 3600, 'tbl_forum(forum_pass)')) 
{
    // forum_priv cookie
    if (isset($Forum_Priv[$forum_id])) {
        $Xpasswd = base64_decode($Forum_Priv[$forum_id]);
        
        foreach ($rowQ1[0] as $value) {
            $forum_xpass = $value;
        }

        if (md5($forum_xpass) == $Xpasswd) {
            $Forum_passwd = $forum_xpass;
        } else {
            setcookie("Forum_Priv[$forum_id]", '', 0);
        }

    } else {
        if (isset($Forum_passwd)) {
            foreach ($rowQ1[0] as $value) {
                if ($value == $Forum_passwd) {
                    setcookie("Forum_Priv[$forum_id]", base64_encode(md5($Forum_passwd)), time() + 900);
                }
            }
        }
    }
}

