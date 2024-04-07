<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x and PhpBB integration source code               */
/*                                                                      */
/* This version name NPDS Copyright (c) 2001-2020 by Philippe Brunier   */
/* Great mods by snipe                                                  */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\system\date\date;
use npds\system\cache\cache;
use npds\system\forum\forum;
use npds\system\routing\url;
use npds\system\utility\code;
use npds\system\config\Config;
use npds\system\security\hack;
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

include("auth.php");

global $NPDS_Prefix;

$rowQ1 = cache::Q_Select("SELECT forum_name, forum_moderator, forum_type, forum_pass, forum_access, arbre FROM " . $NPDS_Prefix . "forums WHERE forum_id = '$forum'", 3600);
if (!$rowQ1) {
    forum::forumerror('0001');
}

$myrow = $rowQ1[0];

$forum_type = $myrow['forum_type'];
$forum_access = $myrow['forum_access'];
$moderator = forum::get_moderator($myrow['forum_moderator']);

if (isset($user)) {
    $userX = base64_decode($user);
    $userdata = explode(':', $userX);
    $moderator = explode(' ', $moderator);
    $Mmod = false;

    for ($i = 0; $i < count($moderator); $i++) {
        if (($userdata[1] == $moderator[$i])) {
            $Mmod = true;
            break;
        }
    }
}

settype($submitS, 'string');

if ($submitS) {
    include("themes/default/header.php");

    $sql = "SELECT poster_id, topic_id FROM " . $NPDS_Prefix . "posts WHERE post_id = '$post_id'";
    $result = sql_query($sql);

    if (!$result) {
        forum::forumerror('0022');
    }

    $row = sql_fetch_assoc($result);

    if ($userdata[0] == $row['poster_id']) {
        $ok_maj = true;
    } else {
        if (!$Mmod) {
            forum::forumerror('0035');
        }

        if ((forum::user_is_moderator($userdata[0], $userdata[2], $forum_access) < 2)) {
            forum::forumerror('0036');
        }
    }

    $userdata = forum::get_userdata($userdata[1]);

    if ($allow_html == 0 || isset($html)) {
        $message = htmlspecialchars($message, ENT_COMPAT | ENT_HTML401, 'utf-8');
    }

    if (($allow_bbcode == 1) and ($forum_type != '6') and ($forum_type != '5'))  {
        $message = forum::smile($message);
    }

    if (($forum_type != 6) and ($forum_type != 5)) {
        $message = forum::make_clickable($message);
        $message = code::af_cod($message);
        $message = str_replace("\n", "<br />", hack::removeHack($message));
        $message .= '<div class="text-muted text-end small"><i class="fa fa-edit"></i>&nbsp;' . translate("Message édité par") . " : " . $userdata['uname'] . " / " . date::post_convertdate(time() + ((int)$gmt * 3600)) . '</div>';
    } else {
        $message .= "\n\n" . translate("Message édité par") . " : " . $userdata['uname'] . " / " . date::post_convertdate(time() + ((int)$gmt * 3600));
    }

    $message = addslashes($message);

    if ($subject == '') {
        $subject = translate("Sans titre");
    }

    // Forum ARBRE
    if ($arbre) {
        $hrefX = 'viewtopicH.php';
    } else {
        $hrefX = 'viewtopic.php';
    }

    if (!isset($delete)) {
        $sql = "UPDATE " . $NPDS_Prefix . "posts SET post_text = '$message', image='$image_subject' WHERE (post_id = '$post_id')";
        
        if (!$result = sql_query($sql)) {
            forum::forumerror('0001');
        }
        
        $sql = "UPDATE " . $NPDS_Prefix . "forum_read SET status='0' WHERE topicid = '" . $row['topic_id'] . "'";
        if (!$r = sql_query($sql)) {
            forum::forumerror('0001');
        }

        $sql = "UPDATE " . $NPDS_Prefix . "forumtopics SET topic_title = '$subject', topic_time = '" . date("Y-m-d H:i:s", time() + ((int)$gmt * 3600)) . "', current_poster='" . $userdata['uid'] . "' WHERE topic_id = '" . $row['topic_id'] . "'";
        if (!$result = sql_query($sql)) { 
            forum::forumerror('0020');
        }

        url::redirect_url("$hrefX?topic=" . $row['topic_id'] . "&forum=$forum");
    } else {
        $indice = sql_num_rows(sql_query("SELECT post_id FROM " . $NPDS_Prefix . "posts WHERE post_idH='$post_id'"));

        if (!$indice) {
            $r DB::table('posts')->where('post_id', $post_id)->delete();

            if (!$r) {
                forum::forumerror('0001');
            }

            forum::control_efface_post("forum_npds", $post_id, "", "");

            if (forum::get_total_posts($forum, $row['topic_id'], "topic", $Mmod) == 0) {
                $r = DB::table('forumtopics')->where('topic_id', $row['topic_id'])->delete();

                if (!$r) {
                    forum::forumerror('0001');
                }

                DB::table('forum_read')->where('topicid', $row['topic_id'])->delete();

                url::redirect_url("viewforum.php?forum=$forum");
                die();
            } else {
                $result = sql_query("SELECT post_time, poster_id FROM " . $NPDS_Prefix . "posts WHERE topic_id='" . $row['topic_id'] . "' ORDER BY post_id DESC LIMIT 0,1");
                $rowX = sql_fetch_row($result);

                $sql = "UPDATE " . $NPDS_Prefix . "forumtopics SET topic_time = '$rowX[0]', current_poster='$rowX[1]' WHERE topic_id = '" . $row['topic_id'] . "'";
                
                if (!$r = sql_query($sql)) {
                    forum::forumerror('0001');
                }
            }

            url::redirect_url("$hrefX?topic=" . $row['topic_id'] . "&forum=$forum");
        } else {
            echo '<div class="alert alert-danger">' . translate("Votre contribution n'a pas été supprimée car au moins un post est encore rattaché (forum arbre).") . '</div>';
        }
    }
} else {
    include("themes/default/header.php");

    if ($allow_bbcode == 1) {
        include("assets/formhelp.java.php");
    }

    $sql = "SELECT p.*, u.uname, u.uid, u.user_sig FROM " . $NPDS_Prefix . "posts p, " . $NPDS_Prefix . "users u WHERE (p.post_id = '$post_id') AND ((p.poster_id = u.uid) XOR (p.poster_id=0))";
    if (!$result = sql_query($sql)) {
        forum::forumerror('0001');
    }

    $myrow = sql_fetch_assoc($result);

    if ((!$Mmod) and ($userdata[0] != $myrow['uid'])) {
        forum::forumerror('0035');
    }

    if (!$result = sql_query("SELECT topic_title, topic_status FROM " . $NPDS_Prefix . "forumtopics WHERE topic_id='" . $myrow['topic_id'] . "'")) {
        forum::forumerror('0001');
    } else {
        list($title, $topic_status) = sql_fetch_row($result);
        
        if (($topic_status != 0) and !$Mmod) {
            forum::forumerror('0025');
        }
    }

    settype($submitP, 'string');

    if ($submitP) {
        $acc = 'editpost';
        $title = stripslashes($subject);
        $message = stripslashes(forum::make_clickable($message));

        include("preview.php");
    } else {
        $image_subject = $myrow['image'];
        $title = stripslashes($title);
        $message = $myrow['post_text'];

        if (($forum_type != 6) and ($forum_type != 5)) {
            $message = str_replace("<br />", "\n", $message);
            $message = forum::smile($message);
            $message = code::desaf_cod($message);
            $message = forum::undo_htmlspecialchars($message, ENT_COMPAT | ENT_HTML401, 'utf-8');
        } else {
            $message = htmlspecialchars($message, ENT_COMPAT | ENT_HTML401, 'utf-8');
        }

        $message = stripslashes($message);
    }

    if ((($Mmod) or ($userdata[0] == $myrow['uid'])) and ($forum_access != 9)) {
        $qui = $myrow['poster_id'] == 0 ? Config::get('npds.anonymous') : $myrow['uname'];

        echo '
        <div>
        <h3>' . translate("Edition de la soumission") . ' de <span class="text-muted">' . $qui . '</span></h3>
        <hr />
        <form action="editpost.php" method="post" name="coolsus">';
        
        if ($Mmod)
            echo '
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="subject">' . translate("Titre") . '</label>
                <div class="col-sm-12">
                <input class="form-control" type="text" id="subject" name="subject" maxlength="100" value="' . htmlspecialchars($title, ENT_COMPAT | ENT_HTML401, 'utf-8') . '" />
                </div>
            </div>';
        else {
            echo '<strong>' . translate("Edition de la soumission") . '</strong> : ' . $title;
            echo "<input type=\"hidden\" name=\"subject\" value=\"" . htmlspecialchars($title, ENT_COMPAT | ENT_HTML401, 'utf-8') . "\" />";
        }
    } else {
        forum::forumerror('0036');
    }

    if ($smilies) {
        echo '
        <div class="d-none d-sm-block mb-3 row">
            <label class="col-form-label col-sm-12">' . translate("Icone du message") . '</label>
            <div class="col-sm-12">
                <div class="border rounded pt-2 px-2 n-fond_subject">
                ' . forum::emotion_add($image_subject) . '
                </div>
            </div>
        </div>';
    }

    echo '
        <div class="mb-3 row">
            <label class="col-form-label col-sm-12" for="message">' . translate("Message") . '</label>';

    if ($allow_bbcode) {
        $xJava = ' onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" onfocus="storeForm(this)"';
    }

    echo '
            <div class="col-sm-12">
                <div class="card">
                <div class="card-header">
                    <div class="float-start">';

    forum::putitems('ta_edipost');

    echo '          </div>';

    if ($allow_html == 1) {
        echo '<span class="text-success float-end mt-2" title="HTML ' . translate("Activé") . '" data-bs-toggle="tooltip"><i class="fa fa-code fa-lg"></i></span>' . forum::HTML_Add();
    } else {
        echo '<span class="text-danger float-end mt-2" title="HTML ' . translate("Désactivé") . '" data-bs-toggle="tooltip"><i class="fa fa-code fa-lg"></i></span>';
    }

    echo '
                </div>
                <div class="card-body">
                    <textarea id="ta_edipost" class="form-control" ' . $xJava . ' name="message" rows="10" cols="60">' . $message . '</textarea>
                </div>
                <div class="card-footer p-0">
                    <span class="d-block">
                        <button class="btn btn-link" type="submit" value="' . translate("Prévisualiser") . '" name="submitP" title="' . translate("Prévisualiser") . '" data-bs-toggle="tooltip" ><i class="fa fa-eye fa-lg"></i></button>
                    </span>
                </div>
                </div>
            </div>
        </div>';

    if (($allow_html == 1) and ($forum_type != 6)) {
        if (isset($html)) {
            $sethtml = 'checked="checked"';
        } else {
            $sethtml = '';
        }

        echo '
        <div class="mb-3 row">
            <label class="col-form-label col-sm-12">' . translate("Options") . '</label>
            <div class="col-sm-12">
                <div class="checkbox">
                    <div class="form-check text-danger">
                    <input class="form-check-input" type="checkbox" id="delete_p" name="delete" />
                    <label class="form-check-label" for="delete_p">' . translate("Supprimer ce message") . '</label>
                    </div>
                </div>
                <div class="checkbox">
                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="html" name="html" ' . $sethtml . ' />
                    <label class="form-check-label" for="html">' . translate("Désactiver le html pour cet envoi") . '</label>
                    </div>
                </div>
            </div>
        </div>';
    }

    echo '
        <input type="hidden" name="post_id" value="' . $post_id . '" />
        <input type="hidden" name="forum" value="' . $forum . '" />
        <input type="hidden" name="topic_id" value="' . $topic . '" />
        <input type="hidden" name="topic" value="' . $topic . '" />
        <input type="hidden" name="arbre" value="' . $arbre . '" />
        <div class="mb-3 row">
            <div class="col-sm-12 ms-sm-auto ">
                <button class="btn btn-primary" type="submit" name="submitS" value="' . translate("Valider") . '" >' . translate("Valider") . '</button>&nbsp;
            </div>
        </div>
    </form>
    </div>';
}

include("themes/default/footer.php");
