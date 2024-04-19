<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* NPDS Copyright (c) 2002-2023 by Philippe Brunier                     */
/* Great mods by snipe                                                  */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\support\date\date;
use npds\support\assets\css;
use npds\support\auth\groupe;
use npds\support\cache\cache;
use npds\support\forum\forum;
use npds\support\str;
use npds\support\theme\theme;
use npds\system\config\Config;

if (!function_exists("Mysql_Connexion")) { 
    include('boot/bootstrap.php');
}

global $NPDS_Prefix;

include('auth.php');

// = DB::table('')->select()->where('', )->orderBy('')->get();

$rowQ1 = cache::Q_Select("SELECT forum_id FROM " . $NPDS_Prefix . "forumtopics WHERE topic_id='$topic'", 3600);
if (!$rowQ1) {
    forum::forumerror('0001');
}

$myrow = $rowQ1[0];
$forum = $myrow['forum_id'];

// = DB::table('')->select()->where('', )->orderBy('')->get();

$rowQ1 = cache::Q_Select("SELECT forum_name, forum_moderator, forum_type, forum_pass, forum_access, arbre FROM " . $NPDS_Prefix . "forums WHERE forum_id = '$forum'", 3600);
if (!$rowQ1) {
    forum::forumerror('0001');
}

$myrow = $rowQ1[0];
$forum_name = $myrow['forum_name'];
$mod = $myrow['forum_moderator'];
$forum_type = $myrow['forum_type'];
$forum_access = $myrow['forum_access'];

if (($forum_type == 1) and ($Forum_passwd != $myrow['forum_pass'])) {
    header('Location: '. site_url('forum.php'));
}

if (($forum_type == 5) or ($forum_type == 7)) {
    $ok_affiche = false;
    $tab_groupe = groupe::valid_group($user);
    $ok_affiche = groupe::groupe_forum($myrow['forum_pass'], $tab_groupe);

    if (!$ok_affiche) {
        header('location: '. site_url('forum.php'));
    }
}

if (($forum_type == 9) and (!$user)) {
    header('location: '. site_url('forum.php'));
}

// Moderator
if (isset($user)) {
    $userX = base64_decode($user);
    $userdata = explode(':', $userX);
}

$moderator = forum::get_moderator($mod);
$moderator = explode(' ', $moderator);
$Mmod = false;

if (isset($user)) {
    for ($i = 0; $i < count($moderator); $i++) {
        if (($userdata[1] == $moderator[$i])) {
            $Mmod = true;
            break;
        }
    }
}

// = DB::table('')->select()->where('', )->orderBy('')->get();

$sql = "SELECT topic_title, topic_status FROM " . $NPDS_Prefix . "forumtopics WHERE topic_id = '$topic'";
if (!$result = sql_query($sql)) {
    forum::forumerror('0001');
}

$myrow = sql_fetch_assoc($result);
$topic_subject = stripslashes($myrow['topic_title']);
$lock_state = $myrow['topic_status'];

if (isset($user)) {
    if ($cookie[9] == '') {
        $cookie[9] = Config::get('npds.Default_Theme');
    }

    if (isset($theme)) {
        $cookie[9] = $theme;
    }

    $tmp_theme = $cookie[9];
    
    if (!$file = @opendir("themes/$cookie[9]")) {
        $tmp_theme = Config::get('npds.Default_Theme');
    }
} else{
    $tmp_theme = Config::get('npds.Default_Theme');
}

// = DB::table('')->select()->where('', )->orderBy('')->get();


$post_aff = $Mmod ? ' ' : " AND post_aff='1' ";

$sql = "SELECT * FROM " . $NPDS_Prefix . "posts WHERE topic_id='$topic' AND post_id='$post_id'" . $post_aff;
if (!$result = sql_query($sql)) {
    forum::forumerror('0001');
}

$myrow = sql_fetch_assoc($result);

if ($allow_upload_forum) {

// = DB::table('')->select()->where('', )->orderBy('')->get();

    $visible = !$Mmod ? ' AND visible = 1' : '';

    $sql = "SELECT att_id FROM $upload_table WHERE apli='forum_npds' && topic_id = '$topic' $visible";
    $att = sql_num_rows(sql_query($sql));

    if ($att > 0) {
        include("modules/upload/include_forum/upload.func.forum.php");
    }
}

if ($myrow['poster_id'] != 0) {
    $posterdata = forum::get_userdata_from_id($myrow['poster_id']);
    $posts = $posterdata['posts'];
}

include("storage/meta/meta.php");

echo '
    <link rel="stylesheet" href="assets/shared/bootstrap/dist/css/bootstrap.min.css" />
    ' . css::import_css($tmp_theme, Config::get('npds.language'), '', '', '') . '
    </head>
    <body>
        <div max-width="640" class="container p-3 n-hyphenate">
            <div>';

$site_logo = Config::get('npds.site_logo');

$pos = strpos($site_logo, '/');

if ($pos) {
    echo '<img class="img-fluid d-block mx-auto" src="' . $site_logo . '" alt="website logo" />';
} else {
    echo '<img class="img-fluid d-block mx-auto" src="assets/images/' . $site_logo . '" alt="website logo" />';
}

echo '
    <div class="row mt-4">
        <div class="col-md-2 text-sm-center">
            <strong>' . translate("Auteur") . '</strong><br />';

if ($smilies) {
    if ($myrow['poster_id'] != 0) {
        if ($posterdata['user_avatar'] != '') {
            
            if (stristr($posterdata['user_avatar'], "users_private")) {
                $imgtmp = $posterdata['user_avatar'];
            } else {
                if ($ibid = theme::theme_image("forum/avatar/" . $posterdata['user_avatar'])) {
                    $imgtmp = $ibid;
                } else {
                    $imgtmp = "assets/images/forum/avatar/" . $posterdata['user_avatar'];
                }
            }

            echo '<img class="n-ava-48 border my-2" src="' . $imgtmp . '" alt="avatar" /><br />';
        }
    } else {
        echo '<img class="n-ava-48 border my-2" src="assets/images/forum/avatar/blank.gif" alt="avatar" /><br />';
    }
}

echo $myrow['poster_id'] != 0 ? $posterdata['uname'] : Config::get('npds.anonymous');

echo '
        </div>
        <div class="col-md-10">
        <hr />
            <p class="">' . translate("Forum") . '&nbsp;&raquo;&nbsp;&raquo;&nbsp;' . stripslashes($forum_name) . '&nbsp;&raquo;&nbsp;&raquo;&nbsp;<strong>' . $topic_subject . '</strong></p>
            <hr />
            <p class="text-end">
            <small>' . translate("Posté : ") . date::convertdate($myrow['post_time']) . '</small> ';

if ($myrow['image'] != '') {
    if ($ibid = theme::theme_image("forum/subject/" . $myrow['image'])) {
        $imgtmp = $ibid;
    } else {
        $imgtmp = "assets/images/forum/subject/" . $myrow['image'];
    }

    echo '<img class="n-smil" src="' . $imgtmp . '" alt="icone du post" />';
} else {
    echo '<img class="n-smil" src="assets/images/forum/subject/00.png" alt="icone du post" />';
}

echo '</p>';

$message = stripslashes($myrow['post_text']);

if ($allow_bbcode) {
    $message = forum::smilie($message);
    $message = str_replace('[video_yt]', 'https://www.youtube.com/watch?v=', $message);
    $message = str_replace('[/video_yt]', '', $message);
}

if (stristr($message, '<a href')) {
    $message = preg_replace('#_blank(")#i', '_blank\1 class=\1\1', $message);
}

$message = str::split_string_without_space($message, 80);

if (($forum_type == '6') or ($forum_type == '5')) {
    highlight_string(stripslashes($myrow['post_text'])) . '<br /><br />';
} else {
    if ($myrow['poster_id'] != 0) {
        if (array_key_exists('user_sig', $posterdata)) {
            $message = str_replace('[addsig]', '<div class="n-signature">' . nl2br($posterdata['user_sig']) . '</div>', $message);
        }
    }
}

echo $message;

if ($allow_upload_forum and ($att > 0)) {
    $post_id = $myrow['post_id'];
    echo display_upload("forum_npds", $post_id, $Mmod);
}

$nuke_url = Config::get('npds.nuke_url');

echo '
                <hr />
                <p class="text-center">' . translate("Cet article provient de") . ' ' . Config::get('npds.sitename') . '<br />
                <a href="'. site_url('viewtopic.php?topic=' . $topic . '&amp;forum=' . $forum . '&amp;post_id=' . $post_id) .'">'. site_url('viewtopic.php?topic=' . $topic . '&amp;forum=' . $forum) .'</a></p>
            </div>
        </div>
    </body>
    </html>';
