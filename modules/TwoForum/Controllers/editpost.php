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

use npds\support\date\date;
use npds\support\auth\users;
use npds\support\cache\cache;
use npds\support\forum\forum;
use npds\support\routing\url;
use npds\support\utility\code;
use npds\system\config\Config;
use npds\support\security\hack;
use npds\support\utility\crypt;
use npds\system\support\facades\DB;
use npds\system\support\facades\Request;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

include("auth.php");

if (!$rowQ1 = cache::Q_Select3(DB::table('forums')
        ->select('forum_name', 'forum_moderator', 'forum_type', 'forum_pass', 'forum_access', 'arbre')
        ->where('forum_id', $forum = Request::input('forum'))
        ->first(), 3600, crypt::encrypt('forum(forum_id)')
)) {
    forum::forumerror('0001');
}

$forum_type     = $rowQ1['forum_type'];
$forum_access   = $rowQ1['forum_access'];
$moderator      = forum::get_moderator($rowQ1['forum_moderator']);

$user = users::getUser();

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

if (Request::input('submitS')) {

    include("themes/default/header.php");

    if (!$result_post = DB::table('posts')
        ->select('poster_id', 'topic_id')
        ->where('post_id', $post_id = Request::input('post_id'))
        ->first()
    ) {
        forum::forumerror('0022');
    }

    if ($userdata[0] == $result_post['poster_id']) {
        $ok_maj = true;
    } else {
        if (!$Mmod) {
            forum::forumerror('0035');
        }

        if ((forum::user_is_moderator($userdata[0],$userdata[2], $forum_access) < 2)) {
            forum::forumerror('0036');
        }
    }

    $userdata = forum::get_userdata($userdata[1]);

    $html       = Request::input('html');
    $message    = Request::input('message');

    if (Config::get('forum.config.allow_html') == 0 || isset($html)) {
        $message = htmlspecialchars($message, ENT_COMPAT | ENT_HTML401, 'utf-8');
    }

    if ((Config::get('forum.config.allow_bbcode') == 1) and ($forum_type != '6') and ($forum_type != '5'))  {
        $message = forum::smile($message);
    }

    if (($forum_type != 6) and ($forum_type != 5)) {
        $message = forum::make_clickable($message);
        $message = code::af_cod($message);
        $message = str_replace("\n", "<br />", hack::removeHack($message));
        $message .= "<div class='text-muted text-end small'><i class='fa fa-edit'></i>&nbsp;" . __d('two_forum', 'Message édité par') . " : " . $userdata['uname'] . " / " . date::post_convertdate(time() + ((int) Config::get('npds.gmt') * 3600)) . '</div>';             
    } else {
        $message .= "\n\n" . __d('two_forum', 'Message édité par') . " : " . $userdata['uname'] . " / " . date::post_convertdate(time() + ((int) Config::get('npds.gmt') * 3600));
    }

    $message = addslashes($message);

    $subject = Request::input('subject');

    if (!$subject) {
        $subject = __d('two_forum', 'Sans titre');
    }

    // Forum ARBRE
    if (Request::input('arbre')) {
        $hrefX = 'viewtopicH.php';
    } else {
        $hrefX = 'viewtopic.php';
    }

    if (!Request::input('delete')) {

        if (!DB::table('posts')->where('post_id', $post_id)
            ->update(array(
                'post_text'     => stripslashes($message),
                'image'         => Request::input('image_subject'),
            ))
        ) {
            forum::forumerror('0001');
        }

        if (!DB::table('forum_read')->where('topicid', $result_post['topic_id'])
            ->update(array(
                'status'       => 0,
            ))
        ) {
            forum::forumerror('0001');
        }

        if (!DB::table('forumtopics')->where('topic_id', $result_post['topic_id'])
            ->update(array(
                'topic_title'      => $subject,
                'topic_time'       => date("Y-m-d H:i:s", time() + ((int) Config::get('npds.gmt') * 3600)),
                'current_poster'   => users::cookieUser(0),
            ))
        ) { 
            forum::forumerror('0020');
        }

        url::redirect_url($hrefX .'?topic=' . $result_post['topic_id'] . '&forum='. $forum);
    } else {

        if (!DB::table('posts')
                    ->select('post_id')
                    ->where('post_idH', $post_id)
                    ->count()
        ) {
       
            if (!DB::table('posts')
                    ->where('post_id', $post_id)
                    ->delete()
            ) {
                forum::forumerror('0001');
            }

            forum::control_efface_post("forum_npds", $post_id, "", "");

            if (forum::get_total_posts($forum, $result_post['topic_id'], "topic", $Mmod) == 0) {
                
                if (!DB::table('forumtopics')
                        ->where('topic_id', $result_post['topic_id'])
                        ->delete()
                ) {
                    forum::forumerror('0001');
                }

                DB::table('forum_read')->where('topicid', $row['topic_id'])->delete();

                url::redirect_url('viewforum.php?forum='. $forum);
                die();
            } else {
                $rowX = DB::table('posts')
                        ->select('post_time', 'poster_id')
                        ->where('topic_id', $result_post['topic_id'])
                        ->orderBy('post_id', 'desc')
                        ->limit(1)
                        ->offset(0)
                        ->first();
      
                if (!DB::table('forumtopics')->where('topic_id', $result_post['topic_id'])
                    ->update(array(
                        'topic_time'        => $rowX['post_time'],
                        'current_poster'    => $rowX['poster_id'],
                    ))
                ) {
                    forum::forumerror('0001');
                }
            }

            url::redirect_url($hrefX .'?topic=' . $result_post['topic_id'] . '&forum='. $forum);
        } else {
            echo '<div class="alert alert-danger">' . __d('two_forum', 'Votre contribution n\'a pas été supprimée car au moins un post est encore rattaché (forum arbre).') . '</div>';
        }
    }
} else {
    include("themes/default/header.php");

    if (Config::get('forum.config.allow_bbcode') == 1) {
        include("assets/formhelp.java.php");
    }
    
    if (!$res_post = DB::table('posts')
            ->select('posts.*', 'users.uname', 'users.uid', 'users.user_sig')
            ->join('users', 'posts.poster_id', '=', 'users.uid')
            ->where('posts.post_id', $post_id)
            ->xOrWhere('posts.poster_id', '=', '0')
            ->first()
    ) {
        forum::forumerror('0001');
    }

    if ((!$Mmod) and ($userdata[0] != $res_post['uid'])) {
        forum::forumerror('0035');
    }

    if (!$res_forumtopic = DB::table('forumtopics')
                ->select('topic_title', 'topic_status')
                ->where('topic_id', $res_post['topic_id'])
                ->first()
    ) {
        forum::forumerror('0001');
    } else {
        if (($res_forumtopic['topic_status'] != 0) and !$Mmod) {
            forum::forumerror('0025');
        }
    }

    if (Request::input('submitP')) {
        $acc = 'editpost';
        $title = stripslashes($subject);
        $message = stripslashes(forum::make_clickable($message));

        include("preview.php");
    } else {
        $image_subject = $res_post['image'];
        $title = stripslashes($res_forumtopic['topic_title']);
        $message = $res_post['post_text'];

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

    if ((($Mmod) or ($userdata[0] == $res_post['uid'])) and ($forum_access != 9)) {
        $qui = $res_post['poster_id'] == 0 ? Config::get('npds.anonymous') : $res_post['uname'];

        echo '
        <div>
        <h3>' . __d('two_forum', 'Edition de la soumission') . ' de <span class="text-muted">' . $qui . '</span></h3>
        <hr />
        <form action="'. site_url('editpost.php') .'" method="post" name="coolsus">';
        
        if ($Mmod) {
            echo '
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="subject">' . __d('two_forum', 'Titre') . '</label>
                <div class="col-sm-12">
                <input class="form-control" type="text" id="subject" name="subject" maxlength="100" value="' . htmlspecialchars($title, ENT_COMPAT | ENT_HTML401, 'utf-8') . '" />
                </div>
            </div>';
        } else {
            echo '<strong>' . __d('two_forum', 'Edition de la soumission') . '</strong> : ' . $title;
            echo '<input type="hidden" name="subject" value="' . htmlspecialchars($title, ENT_COMPAT | ENT_HTML401, 'utf-8') . '" />';
        }
    } else {
        forum::forumerror('0036');
    }

    if (Config::get('npds.smilies')) {
        echo '
        <div class="d-none d-sm-block mb-3 row">
            <label class="col-form-label col-sm-12">' . __d('two_forum', 'Icone du message') . '</label>
            <div class="col-sm-12">
                <div class="border rounded pt-2 px-2 n-fond_subject">
                ' . forum::emotion_add($image_subject) . '
                </div>
            </div>
        </div>';
    }

    echo '
        <div class="mb-3 row">
            <label class="col-form-label col-sm-12" for="message">' . __d('two_forum', 'Message') . '</label>';

    if (Config::get('forum.config.allow_bbcode')) {
        $xJava = ' onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" onfocus="storeForm(this)"';
    }

    echo '
            <div class="col-sm-12">
                <div class="card">
                <div class="card-header">
                    <div class="float-start">';

    forum::putitems('ta_edipost');

    echo '          </div>';

    if (Config::get('forum.config.allow_html') == 1) {
        echo '<span class="text-success float-end mt-2" title="HTML ' . __d('two_forum', 'Activé') . '" data-bs-toggle="tooltip"><i class="fa fa-code fa-lg"></i></span>' . forum::HTML_Add();
    } else {
        echo '<span class="text-danger float-end mt-2" title="HTML ' . __d('two_forum', 'Désactivé') . '" data-bs-toggle="tooltip"><i class="fa fa-code fa-lg"></i></span>';
    }

    echo '
                </div>
                <div class="card-body">
                    <textarea id="ta_edipost" class="form-control" ' . $xJava . ' name="message" rows="10" cols="60">' . $message . '</textarea>
                </div>
                <div class="card-footer p-0">
                    <span class="d-block">
                        <button class="btn btn-link" type="submit" value="' . __d('two_forum', 'Prévisualiser') . '" name="submitP" title="' . __d('two_forum', 'Prévisualiser') . '" data-bs-toggle="tooltip" ><i class="fa fa-eye fa-lg"></i></button>
                    </span>
                </div>
                </div>
            </div>
        </div>';

    if ((Config::get('forum.config.allow_html') == 1) and ($forum_type != 6)) {
        echo '
        <div class="mb-3 row">
            <label class="col-form-label col-sm-12">' . __d('two_forum', 'Options') . '</label>
            <div class="col-sm-12">
                <div class="checkbox">
                    <div class="form-check text-danger">
                    <input class="form-check-input" type="checkbox" id="delete_p" name="delete" />
                    <label class="form-check-label" for="delete_p">' . __d('two_forum', 'Supprimer ce message') . '</label>
                    </div>
                </div>
                <div class="checkbox">
                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="html" name="html" ' . (isset($html) ? 'checked="checked"' : '') . ' />
                    <label class="form-check-label" for="html">' . __d('two_forum', 'Désactiver le html pour cet envoi') . '</label>
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
                <button class="btn btn-primary" type="submit" name="submitS" value="' . __d('two_forum', 'Valider') . '" >' . __d('two_forum', 'Valider') . '</button>&nbsp;
            </div>
        </div>
    </form>
    </div>';
}

include("themes/default/footer.php");
