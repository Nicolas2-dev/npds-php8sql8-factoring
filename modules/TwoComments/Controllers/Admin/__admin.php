<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/* Based on Parts of phpBB                                              */
/*                                                                      */
/* NPDS Copyright (c) 2002-2023 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

use npds\support\forum\forum;
use npds\support\routing\url;
use npds\system\config\Config;


if (!function_exists("Mysql_Connexion"))
    die();

include('auth.php');
include('modules/geoloc/http/geoloc_locip.php');

filtre_module($file_name);
if (file_exists("modules/comments/config/$file_name.conf.php"))
    include("modules/comments/config/$file_name.conf.php");
else
    die();

settype($forum, 'integer');
if ($forum >= 0)
    die();

// gestion des params du 'forum' : type, accès, modérateur ...
$forum_name = 'comments';
$forum_type = 0;
$allow_to_post = false;

if (Config::get('npds.anonpost'))
    $forum_access = 0;
else
    $forum_access = 1;

global $NPDS_Prefix;

$moderate = Config::get('npds.moderate');

if (($moderate == 1) and $admin)
    $Mmod = true;
elseif ($moderate == 2) {
    $userX = base64_decode($user);
    $userdata = explode(':', $userX);

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT level FROM " . $NPDS_Prefix . "users_status WHERE uid='" . $userdata[0] . "'");
    list($level) = sql_fetch_row($result);
    if ($level >= 2)
        $Mmod = true;
} else
    $Mmod = false;
// gestion des params du 'forum' : type, accès, modérateur ...

if ($Mmod) {
    switch ($mode) {
        case 'del':

            // DB::table('')->where('', )->delete();

            $sql = "DELETE FROM " . $NPDS_Prefix . "posts WHERE forum_id='$forum' AND topic_id = '$topic'";
            if (!$result = sql_query($sql))
                forum::forumerror('0009');
            // ordre de mise à jour d'un champ externe ?
            if ($comments_req_raz != '')

                //DB::table('')->where('', )->update(array(
                //    ''       => ,
                //));

                sql_query("UPDATE " . $NPDS_Prefix . $comments_req_raz);
            url::redirect_url("$url_ret");
            break;
        case 'viewip':
            include("themes/default/header.php");

            // = DB::table('')->select()->where('', )->orderBy('')->get();

            $sql = "SELECT u.uname, p.poster_ip, p.poster_dns FROM " . $NPDS_Prefix . "users u, " . $NPDS_Prefix . "posts p WHERE p.post_id = '$post' AND u.uid = p.poster_id";
            if (!$r = sql_query($sql))
                forum::forumerror('0013');
            if (!$m = sql_fetch_assoc($r))
                forum::forumerror('0014');
            echo '
        <h2 class="mb-3">' . __d('two_comments', 'Commentaire') . '</h2>
        <div class="card mb-3">
            <div class="card-body">
                <h3 class="card-title mb-3">' . __d('two_comments', 'Adresses IP et informations sur les utilisateurs') . '</h3>
                <div class="row">
                <div class="col mb-3">
                    <span class="text-muted">' . __d('two_comments', 'Identifiant : ') . '</span> ' . $m['uname'] . '<br />
                    <span class="text-muted">' . __d('two_comments', 'Adresse IP de l\'utilisateur : ') . '</span> ' . $m['poster_ip'] . '<br />
                    <span class="text-muted">' . __d('two_comments', 'Adresse DNS de l\'utilisateur : ') . '</span> ' . $m['poster_dns'] . '<br />
                </div>';
            echo localiser_ip($iptoshow = $m['poster_ip']);
            echo '
                </div>
            </div>';
            include('modules/geoloc/config/geoloc.conf');
            if ($geo_ip == 1)
                echo '
            <div class="card-footer text-end">
                <a href="modules.php?ModPath=geoloc&amp;ModStart=geoloc&amp;op=allip"><span><i class=" fa fa-globe fa-lg me-1"></i><i class=" fa fa-tv fa-lg me-2"></i></span><span class="d-none d-sm-inline">Carte des IP</span></a>
            </div>';
            echo '
        </div>
        <p><a href="' . rawurldecode($url_ret) . '" class="btn btn-secondary">' . __d('two_comments', 'Retour en arrière') . '</a></p>';
            include("themes/default/footer.php");
            break;
        case 'aff':

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            $sql = "UPDATE " . $NPDS_Prefix . "posts SET post_aff = '$ordre' WHERE post_id = '$post'";
            sql_query($sql);

            // ordre de mise à jour d'un champ externe ?
            if ($ordre) {
                if ($comments_req_add != '')

                    //DB::table('')->where('', )->update(array(
                    //    ''       => ,
                    //));

                    sql_query("UPDATE " . $NPDS_Prefix . $comments_req_add);
            } else {
                if ($comments_req_del != '')

                    //DB::table('')->where('', )->update(array(
                    //    ''       => ,
                    //));

                    sql_query("UPDATE " . $NPDS_Prefix . $comments_req_del);
            }
            url::redirect_url("$url_ret");
            break;
    }
} else {
    include("themes/default/header.php");
    echo '
        <p class="text-center">' . __d('two_comments', 'Vous n\'êtes pas identifié comme modérateur de ce forum. Opération interdite.') . '<br /><br />
        <a href="javascript:history.go(-1)" class="btn btn-secondary">' . __d('two_comments', 'Retour en arrière') . '</a></p>';
    include("themes/default/footer.php");
}
