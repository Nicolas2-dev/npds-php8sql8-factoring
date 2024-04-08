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

use npds\system\config\Config;
use npds\system\language\language;
use npds\system\language\metalang;
use npds\system\support\facades\DB;
use npds\system\cache\SuperCacheEmpty;
use npds\system\support\facades\Cache as SuperCache;


if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

if (Config::get('cache.SuperCache')) {
    $cache_obj = SuperCache::getInstance();
} else {
    $cache_obj = new SuperCacheEmpty();
}

settype($op, 'string');
settype($Subforumid, 'array');

if ($op == "maj_subscribe") {
    if ($user) {
        settype($cookie[0], "integer");

        DB::table('subscribe')->where('uid', $cookie[0])->where('forumid', '!', 'NULL')->delete();

        $result = sql_query("SELECT forum_id FROM " . $NPDS_Prefix . "forums ORDER BY forum_index,forum_id");

        while (list($forumid) = sql_fetch_row($result)) {
            if (is_array($Subforumid)) {
                if (array_key_exists($forumid, $Subforumid)) {
                    $resultX = sql_query("INSERT INTO " . $NPDS_Prefix . "subscribe (forumid, uid) VALUES ('$forumid','$cookie[0]')");
                }
            }
        }
    }
}

include("themes/default/header.php");

// -- SuperCache
if ((Config::get('cache.SuperCache')) and (!$user)) {
    $cache_obj->startCachingPage();
}

if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!Config::get('cache.SuperCache')) or ($user)) {
    $inclusion = false;

    settype($catid, 'integer');

    if ($catid != '') {
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

// -- SuperCache
if ((Config::get('cache.SuperCache')) and (!$user)) {
    $cache_obj->endCachingPage();
}

include("themes/default/footer.php");
