<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/*                                                                      */
/* NPDS Copyright (c) 2001-2020 by Philippe Brunier                     */
/* =========================                                            */
/*                                                                      */
/* Based on PhpNuke 4.x and PhpBB integration source code               */
/* Great mods by snipe                                                  */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\support\cache\cache;
use npds\support\forum\forum;
use npds\support\utility\spam;
use npds\system\cache\cacheManager;
use npds\system\support\facades\DB;
use npds\system\cache\SuperCacheEmpty;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

if ($SuperCache) {
    $cache_obj = new cacheManager();
} else {
    $cache_obj = new SuperCacheEmpty();
}

include('auth.php');
global $NPDS_Prefix, $adminforum, $admin;

//==> droits des admin sur les forums (superadmin et admin avec droit gestion forum)
$adminforum = false;
if ($admin) {
    $adminforum = 0;
    $adminX = base64_decode($admin);
    $adminR = explode(':', $adminX);

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $Q = sql_fetch_assoc(sql_query("SELECT * FROM " . $NPDS_Prefix . "authors WHERE aid='$adminR[0]' LIMIT 1"));

    if ($Q['radminsuper'] == 1) {
        $adminforum = 1;
    } else {

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $R = sql_query("SELECT fnom, fid, radminsuper FROM " . $NPDS_Prefix . "authors a LEFT JOIN " . $NPDS_Prefix . "droits d ON a.aid = d.d_aut_aid LEFT JOIN " . $NPDS_Prefix . "fonctions f ON d.d_fon_fid = f.fid WHERE a.aid='$adminR[0]' AND f.fid BETWEEN 13 AND 15");
        
        if (sql_num_rows($R) >= 1) {
            $adminforum = 1;
        }
    }
}
//<== droits des admin sur les forums (superadmin et admin avec droit gestion forum)

if (isset($arbre) and ($arbre == '1')) {
    $url_ret = "viewtopicH.php";
} else {
    $url_ret = "viewtopic.php";
}

//   if($mode!='viewip') {
$Mmod = false;

if (isset($user)) {
    $userX = base64_decode($user);
    $userdata = explode(':', $userX);

    settype($forum, 'integer');

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $rowQ1 = cache::Q_Select("SELECT forum_name, forum_moderator, forum_type, forum_pass, forum_access, arbre FROM " . $NPDS_Prefix . "forums WHERE forum_id = '$forum'", 3600);
    if (!$rowQ1) {
        forum::forumerror('0001');
    }

    $myrow = $rowQ1[0];
    $moderator = explode(' ', forum::get_moderator($myrow['forum_moderator']));

    for ($i = 0; $i < count($moderator); $i++) {
        if (($userdata[1] == $moderator[$i])) {
            if (forum::user_is_moderator($userdata[0], $userdata[2], $myrow['forum_access'])) {
                $Mmod = true;
            }
            break;
        }
    }
}

if ((!$Mmod) and ($adminforum == 0)) {
    forum::forumerror('0007');
}

//   }

if ((isset($submit)) and ($mode == 'move')) {

    //DB::table('')->where('', )->update(array(
    //    ''       => ,
    //));

    $sql = "UPDATE " . $NPDS_Prefix . "forumtopics SET forum_id='$newforum' WHERE topic_id='$topic'";
    if (!$r = sql_query($sql)) {
        forum::forumerror('0010');
    }

    //DB::table('')->where('', )->update(array(
    //    ''       => ,
    //));

    $sql = "UPDATE " . $NPDS_Prefix . "posts SET forum_id='$newforum' WHERE topic_id='$topic' AND forum_id='$forum'";
    if (!$r = sql_query($sql)) {
        forum::forumerror('0010');
    }

    $r = DB::table('forum_read')->where('topicid', $topic)->delete();
    if (!$r) {
        forum::forumerror('0001');
    }

    //DB::table('')->where('', )->update(array(
    //    ''       => ,
    //));

    $sql = "UPDATE $upload_table SET forum_id='$newforum' WHERE apli='forum_npds' AND topic_id='$topic' AND forum_id='$forum'";
    sql_query($sql);

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $sql = "SELECT arbre FROM " . $NPDS_Prefix . "forums WHERE forum_id='$newforum'";
    $arbre = sql_fetch_assoc(sql_query($sql));

    if ($arbre['arbre']) {
        $url_ret = "viewtopicH.php";
    } else {
        $url_ret = "viewtopic.php";
    }

    include("themes/default/header.php");

    echo '
        <div class="alert alert-success">
        <h4 class="alert-heading">' . __d('two_forum', 'Le sujet a été déplacé.') . '</h4>
        <hr /><a href="' . $url_ret . '?topic=' . $topic . '&amp;forum=' . $newforum . '" class="alert-link">' . __d('two_forum', 'Cliquez ici pour voir le nouveau sujet.') . '</a><br /><a href="'. site_url('forum.php') .'" class="alert-link">' . __d('two_forum', 'Cliquez ici pour revenir à l\'index des Forums.') . '</a>
        </div>';

    cache::Q_Clean();

    include("themes/default/footer.php");
} else {
    if ((isset($Mmod) and $Mmod === true) or ($adminforum == 1)) {

        switch ($mode) {
            case 'move':

                include("themes/default/header.php");

                echo '
        <h2>' . __d('two_forum', 'Forum') . '</h2>
        <form action="'. site_url('topicadmin.php') .'" method="post">
            <div class="mb-3 row">
                <label class="form-label" for="newforum">' . __d('two_forum', 'Déplacer le sujet vers : ') . '</label>
                <div class="col-sm-12">
                <select class="form-select" name="newforum">';

                // = DB::table('')->select()->where('', )->orderBy('')->get();

                $sql = "SELECT forum_id, forum_name FROM " . $NPDS_Prefix . "forums WHERE forum_id!='$forum' ORDER BY cat_id,forum_index,forum_id";
                if ($result = sql_query($sql)) {
                    if ($myrow = sql_fetch_assoc($result)) {
                        
                        do {
                            echo '
                        <option value="' . $myrow['forum_id'] . '">' . $myrow['forum_name'] . '</option>';
                        } while ($myrow = sql_fetch_assoc($result));

                    } else {
                        echo '
                        <option value="-1">' . __d('two_forum', 'Plus de forum') . '</option>';
                    }
                } else {
                    echo '
                        <option value="-1">Database Error</option>';
                }

                echo '
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-12">
                <input type="hidden" name="mode" value="move" />
                <input type="hidden" name="topic" value="' . $topic . '" />
                <input type="hidden" name="forum" value="' . $forum . '" />
                <input type="hidden" name="arbre" value="' . $arbre . '" />
                <input class="btn btn-primary" type="submit" name="submit" value="' . __d('two_forum', 'Déplacer le sujet') . '" />
                </div>
            </div>
        </form>';

                include("themes/default/footer.php");

                break;
            case 'del':
                $r = DB::table('posts')->where('topic_id', $topic)->where('forum_id', $forum)->delete();
                if (!$r) {
                    forum::forumerror('0009');
                }

                $r = DB::table('forumtopics')->where('topic_id', $topic)->delete();
                if (!$r) {
                    forum::forumerror('0010');
                }

                $r = DB::table('forum_read')->where('topicid', $topic)->delete();
                if (!$r = sql_query($sql)) {
                    forum::forumerror('0001');
                }

                forum::control_efface_post("forum_npds", "", $topic, "");

                header('location: '. site_url('viewforum.php?forum='. $forum));
                break;

            case 'lock':

                //DB::table('')->where('', )->update(array(
                //    ''       => ,
                //));

                $sql = "UPDATE " . $NPDS_Prefix . "forumtopics SET topic_status=1 WHERE topic_id='$topic'";
                if (!$r = sql_query($sql)) {
                    forum::forumerror('0011');
                }

                header("location: $url_ret?topic=$topic&forum=$forum");
                break;

            case 'unlock':
                $topic_title = '';

                // = DB::table('')->select()->where('', )->orderBy('')->get();

                $sql = "SELECT topic_title FROM " . $NPDS_Prefix . "forumtopics WHERE topic_id = '$topic'";
                $r = sql_fetch_assoc(sql_query($sql));

                $topic_title = str_replace("[" . __d('two_forum', 'Résolu') . "] - ", "", $r['topic_title']);

                //DB::table('')->where('', )->update(array(
                //    ''       => ,
                //));

                $sql = "UPDATE " . $NPDS_Prefix . "forumtopics SET topic_status = '0', topic_first='1', topic_title='" . addslashes($topic_title) . "' WHERE topic_id = '$topic'";
                if (!$r = sql_query($sql)) {
                    forum::forumerror('0012');
                }

                header('location: '. site_url($url_ret .'?topic='. $topic .'&forum='. $forum));
                break;

            case 'first':

                //DB::table('')->where('', )->update(array(
                //    ''       => ,
                //));

                $sql = "UPDATE " . $NPDS_Prefix . "forumtopics SET topic_status = '1', topic_first='0' WHERE topic_id = '$topic'";
                if (!$r = sql_query($sql)) {
                    forum::forumerror('0011');
                }

                header('location: '. site_url($url_ret .'?topic='. $topic .'&forum='. $forum));
                break;

            case 'viewip':
                include("themes/default/header.php");
                include('modules/geoloc/geoloc_locip.php');

                // = DB::table('')->select()->where('', )->orderBy('')->get();
                
                $sql = "SELECT u.uname, p.poster_ip, p.poster_dns FROM " . $NPDS_Prefix . "users u, " . $NPDS_Prefix . "posts p WHERE p.post_id = '$post' AND u.uid = p.poster_id";
                
                if (!$r = sql_query($sql)) {
                    forum::forumerror('0013');
                }

                if (!$m = sql_fetch_assoc($r)) {
                    forum::forumerror('0014');
                }

                echo '
        <h2 class="mb-3">' . __d('two_forum', 'Forum') . '</h2>
        <div class="card card-body mb-3">
            <h3 class="card-title mb-3" >' . __d('two_forum', 'Adresses IP et informations sur les utilisateurs') . '</h3>
            <div class="row">
                <div class="col mb-3">
                <span class="text-muted">' . __d('two_forum', 'Identifiant : ') . '</span><span class="">' . $m['uname'] . '</span><br />
                <span class="text-muted">' . __d('two_forum', 'Adresse IP de l\'utilisateur : ') . '</span><span class="">' . $m['poster_ip'] . ' => <a class="text-danger" href="'. site_url('topicadmin.php?mode=banip&topic=' . $topic . '&post=' . $post . '&forum=' . $forum . '&arbre=' . $arbre) .'" >' . __d('two_forum', 'Bannir cette @Ip') . '</a></span><br />
                <span class="text-muted">' . __d('two_forum', 'Adresse DNS de l\'utilisateur : ') . '</span><span class="">' . $m['poster_dns'] . '</span><br />
                <span class="text-muted">GeoTool : </span><span class=""><a href="http://www.ip-tracker.org/?ip=' . $m['poster_ip'] . '" target="_blank" >IP tracker</a><br />
                </div>';

                echo localiser_ip($iptoshow = $m['poster_ip']);

                echo '
            </div>
        </div>
        <a href="' . $url_ret . '?topic=' . $topic . '&amp;forum=' . $forum . '" class="btn btn-secondary">' . __d('two_forum', 'Retour en arrière') . '</a>';

                include("themes/default/footer.php");
                break;

            case 'banip':

                // = DB::table('')->select()->where('', )->orderBy('')->get();

                $sql = "SELECT p.poster_ip FROM " . $NPDS_Prefix . "users u, " . $NPDS_Prefix . "posts p WHERE p.post_id = '$post' AND u.uid = p.poster_id";

                if (!$r = sql_query($sql)) {
                    forum::forumerror('0013');
                }

                if (!$m = sql_fetch_assoc($r)) {
                    forum::forumerror('0014');
                }

                spam::L_spambot($m['poster_ip'], "ban");

                header("location: $url_ret?topic=$topic&forum=$forum");
                break;

            case 'aff':

                //DB::table('')->where('', )->update(array(
                //    ''       => ,
                //));

                $sql = "UPDATE " . $NPDS_Prefix . "posts SET post_aff = '$ordre' WHERE post_id = '$post'";
                sql_query($sql);

                header("location: $url_ret?topic=$topic&forum=$forum");
                break;
        }
    } else {
        include("themes/default/header.php");

        echo '
            <div class="alert alert-danger">' . __d('two_forum', 'Vous n\'êtes pas identifié comme modérateur de ce forum. Opération interdite.') . '<br />
                <a class="btn btn-secondary" href="javascript:history.go(-1)" >' . __d('two_forum', 'Go Back') . '</a>
            </div>';
            
        include("themes/default/footer.php");
    }
}
