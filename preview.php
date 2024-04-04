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

use npds\system\forum\forum;
use npds\system\theme\theme;
use npds\system\utility\code;
use npds\system\config\Config;
use npds\system\security\hack;

$userdatat = $userdata;
$messageP = $message;

$time = date(translate("dateinternal"), time() + ((int)$gmt * 3600));

switch ($acc) {
    case "newtopic":
        $forum_type = $myrow['forum_type'];

        if ($forum_type == 8) {
            $formulaire = $myrow['forum_pass'];

            include("modules/sform/forum/forum_extender.php");
        }

        // if ($allow_html == 0 || isset($html)) {
        //     $messageP = htmlspecialchars($messageP, ENT_COMPAT|ENT_HTML401, 'utf-8');
        // }

        if (isset($sig) && $userdata['0'] != 1 && $myrow['forum_type'] != 6 && $myrow['forum_type'] != 5) {
            $messageP .= ' [addsig]';
        }

        if (($forum_type != 6) and ($forum_type != 5)) {
            $messageP = code::af_cod($messageP);
            $messageP = str_replace("\n", '<br />', $messageP);
        }

        if (($allow_bbcode) and ($forum_type != 6) and ($forum_type != 5)) {
            $messageP = forum::smile($messageP);
        }

        if (($forum_type != 6) and ($forum_type != 5)) {
            $messageP = forum::make_clickable($messageP);
            $messageP = hack::removeHack($messageP);

            if ($allow_bbcode) {
                $messageP = forum::aff_video_yt($messageP);
            }
        }

        if (!isset($Mmod)) {
            $subject = hack::removeHack(strip_tags($subject));
        }

        $subject = htmlspecialchars($subject, ENT_COMPAT | ENT_HTML401, 'utf-8');
        break;

    case 'reply':
        if (array_key_exists(1, $userdata)) {
            $userdata = forum::get_userdata($userdata[1]);
        }

        if ($allow_html == 0 || isset($html)) {
            $messageP = htmlspecialchars($messageP, ENT_COMPAT | ENT_HTML401, 'utf-8');
        }

        if (isset($sig) && $userdata['uid'] != 1) {
            $messageP .= " [addsig]";
        }

        if (($forum_type != '6') and ($forum_type != '5')) {
            $messageP = code::af_cod($messageP);
            $messageP = str_replace("\n", '<br />', $messageP);
        }

        if (($allow_bbcode) and ($forum_type != '6') and ($forum_type != '5')) {
            $messageP = forum::smile($messageP);
        }

        if (($forum_type != 6) and ($forum_type != 5)) {
            $messageP = forum::make_clickable($messageP);
            $messageP = hack::removeHack($messageP);

            if ($allow_bbcode) {
                $messageP = forum::aff_video_yt($messageP);
            }
        }
        $messageP = addslashes($messageP);
        break;

    case 'editpost':
        $userdata = forum::get_userdata($userdata[1]);

        settype($post_id, "integer");

        $sql = "SELECT poster_id, topic_id FROM " . $NPDS_Prefix . "posts WHERE (post_id = '$post_id')";
        $result = sql_query($sql);

        if (!$result) {
            forum::forumerror('0022');
        }

        $row2 = sql_fetch_assoc($result);

        $userdata['uid'] = $row2['poster_id'];

        // IF we made it this far we are allowed to edit this message
        settype($forum, "integer");

        $myrow2 = sql_fetch_assoc(sql_query("SELECT forum_type FROM " . $NPDS_Prefix . "forums WHERE (forum_id = '$forum')"));
        $forum_type = $myrow2['forum_type'];

        if ($allow_html == 0 || isset($html)) {
            $messageP = htmlspecialchars($messageP, ENT_COMPAT | ENT_HTML401, 'utf-8');
        }

        if (($allow_bbcode) and ($forum_type != 6) and ($forum_type != 5)) {
            $messageP = forum::smile($messageP);
        }

        if (($forum_type != 6) and ($forum_type != 5)) {
            $messageP = code::af_cod($messageP);
            $messageP = str_replace("\n", '<br />', hack::removeHack($messageP));
            $messageP .= '<br /><div class=" text-muted text-end small"><i class="fa fa-edit"></i> ' . translate("Message édité par") . ' : ' . $userdata['uname'] . '</div';

            if ($allow_bbcode) {
                $messageP = forum::aff_video_yt($messageP);
            }
        } else{
            $messageP .= "\n\n" . translate("Message édité par") . ' : ' . $userdata['uname'];
        }

        $messageP = addslashes($messageP);
        break;
}

$theposterdata = forum::get_userdata_from_id($userdatat[0]);

echo '
        <div class="mb-3">
            <h4 class="mb-3">' . translate("Prévisualiser") . '</h4>
            <div class="row mb-3">
                <div class="col-12">
                <div class="card">
                    <div class="card-header">';

if ($smilies) {
    if (array_key_exists('user_avatar', $theposterdata)) {
        if ($theposterdata['user_avatar'] != '') {
            
            if (stristr($theposterdata['user_avatar'], "users_private")) {
                $imgtmp = $theposterdata['user_avatar'];
            } else {
                if ($ibid = theme::theme_image("forum/avatar/" . $theposterdata['user_avatar'])) {
                    $imgtmp = $ibid;
                } else {
                    $imgtmp = "assets/images/forum/avatar/" . $theposterdata['user_avatar'];
                }
            }

            echo '<a style="position:absolute; top:1rem;" tabindex="0" data-bs-toggle="popover" data-bs-html="true" data-bs-title="' . $theposterdata['uname'] . '" data-bs-content=\'' . forum::member_qualif($theposterdata['uname'], $theposterdata['posts'], $theposterdata['rang']) . '\'><img class=" btn-secondary img-thumbnail img-fluid n-ava" src="' . $imgtmp . '" alt="' . $theposterdata['uname'] . '" /></a>';
        }
    } else {
        echo '<a style="position:absolute; top:1rem;" tabindex="0" data-bs-toggle="popover" data-bs-html="true" data-bs-title="' . Config::get('app.anonymous') . '" data-bs-content=\'' . Config::get('app.anonymous') . '\'><img class=" btn-secondary img-thumbnail img-fluid n-ava" src="assets/images/forum/avatar/blank.gif" alt="icone ' . Config::get('app.anonymous') . '" /></a>';
    }
}

$postername = array_key_exists('1', $userdatat) ? $userdatat[1] : Config::get('app.anonymous');

echo '&nbsp;<span style="position:absolute; left:6rem;" class="text-muted"><strong>' . $postername . '</strong></span>
<span class="float-end">';

if (isset($image_subject)) {
    if ($ibid = theme::theme_image("forum/subject/$image_subject")) {
        $imgtmp = $ibid;
    } else {
        $imgtmp = "assets/images/forum/subject/$image_subject";
    }

    echo '<img class="n-smil" src="' . $imgtmp . '" alt="icone du post" />';
} else {
    if ($ibid = theme::theme_image("forum/icons/posticon.gif")) {
        $imgtmpP = $ibid;
    } else {
        $imgtmpP = "assets/images/forum/icons/posticon.gif";
    }

    echo '<img class="n-smil" src="' . $imgtmpP . '" alt="icone du post" />';
}

echo '</span>
                    </div>
                    <div class="card-body">
                        <span class="text-muted float-end small" style="margin-top:-1rem;">' . translate("Commentaires postés : ") . $time . '</span>
                        <div id="post_preview" class="card-text pt-3">';

$messageP = stripslashes($messageP);

if (($forum_type == '6') or ($forum_type == '5')) {
    highlight_string(stripslashes($messageP));
} else {
    if ($allow_bbcode) {
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
