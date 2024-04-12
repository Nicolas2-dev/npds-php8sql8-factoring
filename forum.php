<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/* Based on Parts of phpBB                                              */
/*                                                                      */
/* NPDS Copyright (c) 2002-2019 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\system\auth\users;
use npds\system\cache\cache;
use npds\system\theme\theme;
use npds\system\language\language;
use npds\system\language\metalang;
use npds\system\support\facades\DB;
use npds\system\support\facades\Request;


if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

if (Request::query('op') == "maj_subscribe") { 
    if (users::getUser()) {

        DB::table('subscribe')
            ->where('uid', users::cookieUser(0))
            ->where('forumid', '!', null)
            ->delete();

        foreach (DB::table('forums')
            ->select('forum_id')
            ->orderBy('forum_index, forum_id')
            ->get() as $forum) 
        {    
            $Subforumid = Request::query('Subforumid');

            if (is_array($Subforumid)) {
                if (array_key_exists($forum['forum_id'], $Subforumid)) {
                    DB::table('subscribe')->insert(array(
                        'forumid'   => $forum['forum_id'],
                        'uid'       => users::cookieUser(0),
                    ));

                }
            }
        }
    }
}

include("themes/default/header.php");

// -- SuperCache
    // start Caching page
if (cache::cacheManagerStart2()) {
    $inclusion = false;

    $theme = theme::getTheme();
    
    if ($catid = Request::query('catid')) {
        if (file_exists("themes/$theme/view/forum-cat$catid.html")) {
            $inclusion = "themes/$theme/view/forum-cat$catid.html";
        } elseif (file_exists("themes/default/view/forum-cat$catid.html")) {
            $inclusion = "themes/default/view/forum-cat$catid.html";
        }
    }

    if ($inclusion == false) {
        if (file_exists("themes/$theme/view/forum-adv.html")) {
            $inclusion = "themes/$theme/view/forum-adv.html";
        } elseif (file_exists("themes/$theme/view/forum.html")) {
            $inclusion = "themes/$theme/view/forum.html";
        } elseif (file_exists("themes/default/view/forum.html")) {
            $inclusion = "themes/default/view/forum.html";
        } else {
            echo "html/forum.html / not find !<br />";
        }
    }

    if ($inclusion) {
        $Xcontent = join('', file($inclusion));

        echo metalang::meta_lang(language::aff_langue($Xcontent));
    }
}

// end Caching page
cache::cacheManagerEnd();

include("themes/default/footer.php");
