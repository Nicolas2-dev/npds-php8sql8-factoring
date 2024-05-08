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

use npds\support\forum\forum;
use npds\support\theme\theme;
use npds\support\utility\code;
use npds\system\config\Config;
use npds\support\security\hack;
use npds\system\support\facades\DB;
use npds\system\support\facades\Request;


$userdatat = $userdata;

switch ($acc) {
    case "newtopic":
        $forum_type = $myrow['forum_type'];

        if ($forum_type == 8) {
            $formulaire = $myrow['forum_pass'];

            include("modules/sform/forum/forum_extender.php");
        }

        $messageP = Request::input('message');
        $html     = Request::input('html');

        // if (Config::get('forum.config.allow_html') == 0 || isset($html)) {
        //     $messageP = htmlspecialchars($messageP, ENT_COMPAT|ENT_HTML401, 'utf-8');
        // }

        $sig = Request::input('sig');

        if (isset($sig) && $userdata['0'] != 1 && $myrow['forum_type'] != 6 && $myrow['forum_type'] != 5) {
            $messageP .= ' [addsig]';
        }
        
        if (($forum_type != 6) and ($forum_type != 5)) {
            $messageP = code::af_cod($messageP);
            $messageP = str_replace("\n", '<br />', $messageP);
        }

        if ((Config::get('forum.config.allow_bbcode')) and ($forum_type != 6) and ($forum_type != 5)) {
            $messageP = forum::smile($messageP);
        }

        if (($forum_type != 6) and ($forum_type != 5)) {
            $messageP = forum::make_clickable($messageP);
            $messageP = hack::removeHack($messageP);

            if (Config::get('forum.config.allow_bbcode')) {
                $messageP = forum::aff_video_yt($messageP);
            }
        }

        $subject = Request::input('subject');

        if (!isset($Mmod)) {
            $subject = hack::removeHack(strip_tags($subject));
        }

        $subject = htmlspecialchars($subject, ENT_COMPAT | ENT_HTML401, 'utf-8');
        break;

    case 'reply':
        if (array_key_exists(1, $userdata)) {
            $userdata = forum::get_userdata($userdata[1]);
        }

        $messageP = Request::input('message');
        $html     = Request::input('html');

        if (Config::get('forum.config.allow_html') == 0 || isset($html)) {
            $messageP = htmlspecialchars($messageP, ENT_COMPAT | ENT_HTML401, 'utf-8');
        }

        $sig = Request::input('sig');

        if (isset($sig) && $userdata['uid'] != 1) {
            $messageP .= " [addsig]";
        }

        if (($forum_type != '6') and ($forum_type != '5')) {
            $messageP = code::af_cod($messageP);
            $messageP = str_replace("\n", '<br />', $messageP);
        }

        if ((Config::get('forum.config.allow_bbcode')) and ($forum_type != '6') and ($forum_type != '5')) {
            $messageP = forum::smile($messageP);
        }

        if (($forum_type != 6) and ($forum_type != 5)) {
            $messageP = forum::make_clickable($messageP);
            $messageP = hack::removeHack($messageP);

            if (Config::get('forum.config.allow_bbcode')) {
                $messageP = forum::aff_video_yt($messageP);
            }
        }
        $messageP = addslashes($messageP);
        break;

    case 'editpost':

        $userdata = forum::get_userdata($userdata[1]);

        $post_first = DB::table('posts')
                        ->select('poster_id', 'topic_id')
                        ->where('post_id', Request::input('post_id'))
                        ->first();

        if (!$post_first) {
            forum::forumerror('0022');
        }

        $userdata['uid'] = $post_first['poster_id'];

        $messageP = Request::input('message');
        $html     = Request::input('html');

        if (Config::get('forum.config.allow_html') == 0 || isset($html)) {
            $messageP = htmlspecialchars($messageP, ENT_COMPAT | ENT_HTML401, 'utf-8');
        }

        $forum_first = DB::table('forums')
                        ->select('forum_type')
                        ->where('forum_id', Request::input('forum'))
                        ->first();

        if ((Config::get('forum.config.allow_bbcode')) and ($forum_first['forum_type'] != 6) and ($forum_first['forum_type'] != 5)) {
            $messageP = forum::smile($messageP);
        }

        if (($forum_type != 6) and ($forum_type != 5)) {
            $messageP = code::af_cod($messageP);
            $messageP = str_replace("\n", '<br />', hack::removeHack($messageP));
            $messageP .= '<br /><div class=" text-muted text-end small"><i class="fa fa-edit"></i> ' . __d('two_forum', 'Message édité par') . ' : ' . $userdata['uname'] . '</div';

            if (Config::get('forum.config.allow_bbcode')) {
                $messageP = forum::aff_video_yt($messageP);
            }
        } else{
            $messageP .= "\n\n" . __d('two_forum', 'Message édité par') . ' : ' . $userdata['uname'];
        }

        $messageP = addslashes($messageP);
        break;
}

echo '<div class="mb-3">
        <h4 class="mb-3">' . __d('two_forum', 'Prévisualiser') . '</h4>
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">';

$theposterdata = forum::get_userdata_from_id($userdatat[0]);

if (Config::get('npds.smilies')) {
    if (array_key_exists('user_avatar', $theposterdata)) {
        if ($theposterdata['user_avatar'] != '') {
            
            if (stristr($theposterdata['user_avatar'], "users_private")) {
                $imgtmp = $theposterdata['user_avatar'];
            } else {
                $imgtmp = theme::theme_image_row('forum/avatar/'. $theposterdata['user_avatar'], 'assets/images/forum/avatar/'. $theposterdata['user_avatar']);
            }

            echo '<a style="position:absolute; top:1rem;" tabindex="0" data-bs-toggle="popover" data-bs-html="true" data-bs-title="' . $theposterdata['uname'] . '" data-bs-content=\'' . forum::member_qualif($theposterdata['uname'], $theposterdata['posts'], $theposterdata['rang']) . '\'>
                <img class=" btn-secondary img-thumbnail img-fluid n-ava" src="' . $imgtmp . '" alt="' . $theposterdata['uname'] . '" />
            </a>';
        }
    } else {
        echo '<a style="position:absolute; top:1rem;" tabindex="0" data-bs-toggle="popover" data-bs-html="true" data-bs-title="' . Config::get('npds.anonymous') . '" data-bs-content=\'' . Config::get('npds.anonymous') . '\'>
            <img class=" btn-secondary img-thumbnail img-fluid n-ava" src="assets/images/forum/avatar/blank.gif" alt="icone ' . Config::get('npds.anonymous') . '" />
        </a>';
    }
}

$postername = array_key_exists('1', $userdatat) ? $userdatat[1] : Config::get('npds.anonymous');

echo '&nbsp;<span style="position:absolute; left:6rem;" class="text-muted"><strong>' . $postername . '</strong></span>
<span class="float-end">';

$image_subject = Request::input('image_subject');

if (isset($image_subject)) {
    $imgtmp = theme::theme_image_row('forum/subject/'. $image_subject, 'assets/images/forum/subject/'. $image_subject);
    echo '<img class="n-smil" src="' . $imgtmp . '" alt="icone du post" />';
} else {
    $imgtmp = theme::theme_image_row('forum/icons/posticon.gif', 'assets/images/forum/icons/posticon.gif');

    echo '<img class="n-smil" src="' . $imgtmpP . '" alt="icone du post" />';
}

$time = date(__d('two_forum', 'dateinternal'), time() + ((int) Config::get('npds.gmt') * 3600));

echo '</span>
                    </div>
                    <div class="card-body">
                        <span class="text-muted float-end small" style="margin-top:-1rem;">' . __d('two_forum', 'Commentaires postés : ') . $time . '</span>
                        <div id="post_preview" class="card-text pt-3">';

$messageP = stripslashes($messageP);

if (($forum_type == '6') or ($forum_type == '5')) {
    highlight_string(stripslashes($messageP));
} else {
    if (Config::get('forum.config.allow_bbcode')) {
        $messageP = forum::smilie($messageP);
    }

    if (array_key_exists('user_sig', $theposterdata)) {
        $messageP = str_replace('[addsig]', '<div class="n-signature">' . nl2br($theposterdata['user_sig']) . '</div>', $messageP);
    }

    echo $messageP . '
                        </div>
                    </div>';
}

echo '
                </div>
                </div>
            </div>
        </div>';

if ($acc == 'reply' || $acc == 'editpost') {
    $userdata = $userdatat;
}
