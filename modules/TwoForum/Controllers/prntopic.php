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

use npds\support\str;
use npds\support\date\date;
use npds\support\assets\css;
use npds\support\auth\users;
use npds\support\auth\groupe;
use npds\support\cache\cache;
use npds\support\forum\forum;
use npds\support\theme\theme;
use npds\system\config\Config;
use npds\support\utility\crypt;
use npds\system\support\facades\DB;
use npds\system\support\facades\Request;

if (!function_exists("Mysql_Connexion")) { 
    include('boot/bootstrap.php');
}

include('auth.php');

if (!$rowQ1 = cache::Q_Select3(DB::table('forumtopics')
    ->select('forum_id')
    ->where('topic_id', $topic = Request::query('topic'))
    ->where('forum_id', Request::query('forum'))
    ->first(), 3600, crypt::encrypt('forumtopics('. $topic .')'))) 
{
    forum::forumerror('0001');
}

if (!$rowQ2 = cache::Q_Select3(DB::table('forums')
        ->select('forum_name', 'forum_moderator', 'forum_type', 'forum_pass', 'forum_access', 'arbre')
        ->where('forum_id', $rowQ1['forum_id'])
        ->first(), 3600, crypt::encrypt('forum_id('. $rowQ1['forum_id'] .')'))) 
{
    forum::forumerror('0001');
}

$forum_name   = $rowQ2['forum_name'];
$mod          = $rowQ2['forum_moderator'];
$forum_type   = $rowQ2['forum_type'];
$forum_access = $rowQ2['forum_access'];

if (($forum_type == 1) and ($Forum_passwd != $rowQ2['forum_pass'])) {
    header('Location: '. site_url('forum.php'));
}

$user = users::getUser();

if (($forum_type == 5) or ($forum_type == 7)) {
    $ok_affiche = false;
    $tab_groupe = groupe::valid_group($user);
    $ok_affiche = groupe::groupe_forum($rowQ2['forum_pass'], $tab_groupe);

    if (!$ok_affiche) {
        header('location: '. site_url('forum.php'));
    }
}

if (($forum_type == 9) and (!$user)) {
    header('location: '. site_url('forum.php'));
}

// Moderator
$moderator = forum::get_moderator($mod);
$moderator = explode(' ', $moderator);
$Mmod = false;

if (isset($user)) {
    for ($i = 0; $i < count($moderator); $i++) {
        if ((users::cookieUser(1) == $moderator[$i])) {
            $Mmod = true;
            break;
        }
    }
}

if (!$myrow = DB::table('forumtopics')
            ->select('topic_title', 'topic_status')
            ->where('topic_id', $topic)
            ->first()) 
{
    forum::forumerror('0001');
}

$topic_subject = stripslashes($myrow['topic_title']);
$lock_state = $myrow['topic_status'];

$query = DB::table('posts')
            ->select('*')
            ->where('topic_id', $topic)
            ->where('post_id', $post_id = Request::query('post_id'));

if(!$Mmod)
{
    $query->where('post_aff', 1);
}

if (!$myrow = $query->first()) {
    forum::forumerror('0001');
}

if (Config::get('forum.config.allow_upload_forum')) {

    $query = DB::table(Config::get('forum.config.upload_table'))
                ->select('att_id')
                ->where('apli', 'forum_npds')
                ->where('topic_id', $topic);

    if (!$Mmod) {
        $query->were('visible', 1);
    }

    $att = $query->count();

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
    ' . css::import_css(theme::getTheme(), Config::get('npds.language'), '', '', '') . '
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
            <strong>' . __d('two_forum', 'Auteur') . '</strong><br />';

if (Config::get('npds.smilies')) {
    if ($myrow['poster_id'] != 0) {
        if ($posterdata['user_avatar'] != '') {
            
            if (stristr($posterdata['user_avatar'], "users_private")) {
                $imgtmp = $posterdata['user_avatar'];
            } else {
                $imgtmp = theme::theme_image_row('forum/avatar/'. $posterdata['user_avatar'], 'assets/images/forum/avatar/'. $posterdata['user_avatar']);
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
            <p class="">' . __d('two_forum', 'Forum') . '&nbsp;&raquo;&nbsp;&raquo;&nbsp;' . stripslashes($forum_name) . '&nbsp;&raquo;&nbsp;&raquo;&nbsp;<strong>' . $topic_subject . '</strong></p>
            <hr />
            <p class="text-end">
            <small>' . __d('two_forum', 'Posté : ') . date::convertdate($myrow['post_time']) . '</small> ';

if ($myrow['image'] != '') {
    $imgtmp = theme::theme_image_row('forum/subject/' . $myrow['image'], 'assets/images/forum/subject/' . $myrow['image']);

    echo '<img class="n-smil" src="' . $imgtmp . '" alt="icone du post" />';
} else {
    echo '<img class="n-smil" src="assets/images/forum/subject/00.png" alt="icone du post" />';
}

echo '</p>';

$message = stripslashes($myrow['post_text']);

if (Config::get('forum.config.allow_bbcode')) {
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

if (Config::get('forum.config.allow_upload_forum') and ($att > 0)) {
    $post_id = $myrow['post_id'];
    echo display_upload("forum_npds", $post_id, $Mmod);
}

echo '
                <hr />
                <p class="text-center">' . __d('two_forum', 'Cet article provient de') . ' ' . Config::get('npds.sitename') . '<br />
                <a href="'. site_url('viewtopic.php?topic=' . $topic . '&amp;forum=' . $forum . '&amp;post_id=' . $post_id) .'">'. site_url('viewtopic.php?topic=' . $topic . '&amp;forum=' . $forum) .'</a></p>
            </div>
        </div>
    </body>
    </html>';
