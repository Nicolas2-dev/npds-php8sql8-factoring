<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x and PhpBB integration source code               */
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
use npds\support\logs\logs;
use npds\support\auth\users;
use npds\support\cache\cache;
use npds\support\forum\forum;
use npds\support\routing\url;
use npds\support\theme\theme;
use npds\support\mail\mailler;
use npds\support\utility\code;
use npds\support\utility\spam;
use npds\system\config\Config;
use npds\support\security\hack;
use npds\support\utility\crypt;
use npds\system\support\facades\DB;
use npds\support\subscribe\subscribe;
use npds\system\support\facades\Request;


if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

vd(Request::all());

include('auth.php');

$forum = Request::input('forum');
$topic = Request::input('topic');

if (Request::input('cancel')) {
    header('Location: '. site_url('viewtopic.php?topic='. $topic .'&forum='. $forum));
}

$rowQ1 = cache::Q_Select3(DB::table('forums')
        ->select('forum_name', 'forum_moderator', 'forum_type', 'forum_pass', 'forum_access', 'arbre')
        ->where('forum_id', $forum)
        ->first(), 3600, crypt::encrypt('forums(forum_id'. $forum .')'));
if (!$rowQ1) {
    forum::forumerror('0001');
}

$forum_name     = $rowQ1['forum_name'];
$forum_access   = $rowQ1['forum_access'];
$forum_type     = $rowQ1['forum_type'];
$mod            = $rowQ1['forum_moderator'];

if (($forum_type == 1) and ($Forum_passwd != $rowQ1['forum_pass'])) {
    header('Location: '. site_url('forum.php'));
}

if ($forum_access == 9) {
    header('Location: '. site_url('forum.php'));
}

if (forum::is_locked($topic)) {
    forum::forumerror('0025');
}

if (!forum::does_exists($forum, "forum") || !forum::does_exists($topic, "topic")) {
    forum::forumerror('0026');
}

if (Request::input('submitS')) {
    
    $message = Request::input('message');
    
    if ($message == '') {
        $stop = 1;
    } else {
        $stop = 0;
    }

    $user = users::getUser();

    if (!isset($user)) {
        if ($forum_access == 0) {
            $userdata = array("uid" => 1);
            $modo = '';

            include("themes/default/header.php");
        } else {
            if (($username == '') or ($password == ''))
                forum::forumerror('0027');
            else {

                $res_user = DB::table('users')->select('pass')->where('uname', $username)->first();

                if ((password_verify($password, $res['pass'])) and ($res['pass'] != '')) {
                    $userdata = forum::get_userdata($username);
                    
                    if ($userdata['uid'] == 1) {
                        forum::forumerror('0027');
                    } else {
                        include("themes/default/header.php");
                    }
                } else {
                    forum::forumerror('0028');
                }

                $modo = forum::user_is_moderator($username, $res['pass'], $forum_access);
                
                if ($forum_access == 2) {
                    if (!$modo) {
                        forum::forumerror('0027');
                    }
                }
            }
        }
    } else {

        $userX = base64_decode($user);
        $userdata = users::cookieUser();

        $modo = forum::user_is_moderator($userdata[0], $userdata[2], $forum_access);

        if ($forum_access == 2) {
            if (!$modo) {
                forum::forumerror('0027');
            }
        }

        $userdata = forum::get_userdata($userdata[1]);

        include("themes/default/header.php");
    }

    // Either valid user/pass, or valid session. continue with post.
    if ($stop != 1) {
        $poster_ip =  Request::getIp();

        $hostname = Config::get('npds.dns_verif') ? @gethostbyaddr($poster_ip) : '';
        
        // anti flood
        forum::anti_flood($modo, Config::get('forum.config.anti_flood'), $poster_ip, $userdata, Config::get('npds.gmt'));
        //anti_spambot
        
        if (!spam::R_spambot(Request::input('asb_question'), Request::input('asb_reponse'), $message)) {
            logs::Ecr_Log('security', 'Forum Anti-Spam : forum=' . $forum . ' / topic=' . $topic, '');
            
            url::redirect_url("index.php");
            die();
        }

        if (Config::get('forum.config.allow_html') == 0 || isset($html)) {
            $message = htmlspecialchars($message, ENT_COMPAT | ENT_HTML401, 'utf-8');
        }

        if (isset($sig) && $userdata['uid'] != 1) {
            $message .= ' [addsig]';
        }

        // if (($forum_type != '6') and ($forum_type != '5')) {
        //     $message = code::af_cod($message);
        //     $message =  str_replace(array("\r\n", "\r", "\n"), "<br />", $message);
        // }

        if ((Config::get('forum.config.allow_bbcode') == 1) and ($forum_type != '6') and ($forum_type != '5')) {
            $message = forum::smile($message);
        }

        if (($forum_type != '6') and ($forum_type != '5')) {
            $message = forum::make_clickable($message);
            $message = hack::removeHack($message);
        }

        $image_subject = hack::removeHack(Request::input('image_subject'));
        $message = addslashes($message);

        $time = date("Y-m-d H:i:s", time() + ((int) Config::get('npds.gmt') * 3600));

        $insertgetId = DB::table('posts')->insertgetId(array(
            'post_idH'      => 0,
            'topic_id'      => $topic,
            'image'         => $image_subject,
            'forum_id'      => $forum,
            'poster_id'     => $userdata['uid'],
            'post_text'     => $message,
            'post_time'     => $time,
            'poster_ip'     => $poster_ip,
            'poster_dns'    => $hostname,
        ));

        if (!$insertgetId) {
            forum::forumerror('0020');
        } else {
            $IdPost = $insertgetId;
        }

        $r = DB::table('forumtopics')->where('topic_id', $topic)->update(array(
            'topic_time'       => $time,
            'current_poster'   => $userdata['uid'],
        ));

        if (!$r) {
            forum::forumerror('0020');
        }

        //$t = DB::table('forum_read')->where('topicid', $topic)->where('uid', '<>', $userdata['uid'])->update(array(
        $t = DB::table('forum_read')->where('topicid', $topic)->where('uid', '=', $userdata['uid'])->update(array(
            'status'    => 0,
        ));

        if (!$t) {
            forum::forumerror('0001');
        }

        $r = DB::table('users_status')->where('uid', $userdata['uid'])->update(array(
            'posts' => DB::raw('posts+1'),
        ));

        if (!$r){
            forum::forumerror('0029');
        }

        $res_forumtopics = DB::table('forumtopics')
                                ->select('forumtopics.topic_notify', 'users.email', 'users.uname', 'users.uid', 'users.user_langue')
                                ->join('users', 'forumtopics.topic_poster', '=', 'users.uid')
                                ->where('forumtopics.topic_id', $topic)
                                ->first();

        if (!$m = $res_forumtopics) {
            forum::forumerror('0022');
        }

        $sauf = '';

        if (($m['topic_notify'] == 1) && ($m['uname'] != $userdata['uname'])) {

            include_once("language/multilangue.php");

            $res_forumtopics = DB::table('forumtopics')
                    ->select('topic_title')
                    ->where('topic_id', $topic)
                    ->first();

            $subject = strip_tags($forum_name) . "/" . $res_forumtopics['title_topic'] . " : " . html_entity_decode(translate_ml($m['user_langue'], "Une réponse à votre dernier Commentaire a été posté."), ENT_COMPAT | ENT_HTML401, 'utf-8');
            
            $message = $m['uname'] . "\n\n";
            $message .= translate_ml($m['user_langue'], "Vous recevez ce Mail car vous avez demandé à être informé lors de la publication d'une réponse.") . "\n";
            $message .= translate_ml($m['user_langue'], "Pour lire la réponse") . " : ";
            $message .= "<a href=\"". site_url('viewtopic.php?topic='. $topic .'&forum='. $forum .'&start=9999#lastpost') ."\">". site_url('viewtopic.php?topic='. $topic .'&forum='. $forum .'&start=9999') ."</a>\n\n";
            $message .= Config::get('signature.message');

            mailler::send_email($m['email'], $subject, $message, '', true, "html", '');

            $sauf = $m['uid'];
        }

        if (Config::get('npds.subscribe')) {
            if (subscribe::subscribe_query($userdata['uid'], "forum", $forum)) {
                $sauf = $userdata['uid'];
            }

            subscribe::subscribe_mail('forum', $topic, $forum, '', $sauf);
        }

        if (isset($upload)) {
            include("modules/upload/upload_forum.php");

            win_upload("forum_npds", $IdPost, $forum, $topic, "win");
            
            url::redirect_url('viewtopic.php?forum='. $forum .'&topic='. $topic .'&start=9999#lastpost');
            die();
        }

        url::redirect_url('viewforum.php?forum='. $forum);
    } else {
        echo '
        <h4 class="my-3">' . __d('two_forum', 'Poster une réponse dans le sujet') . '</h4>
        <p class="alert alert-danger">' . __d('two_forum', 'Vous devez taper un message à poster.') . '</p>
        <a class="btn btn-outline-primary" href="javascript:history.go(-1)" >' . __d('two_forum', 'Retour en arrière') . '</a>';
    }

} else {
    include('themes/default/header.php');

    if (Config::get('forum.config.allow_bbcode') == 1) {
        include("assets/formhelp.java.php");
    }

    $res_ftopic = DB::table('forumtopics')
            ->select('topic_title', 'topic_status')
            ->where('topic_id', $topic)
            ->first();

    $userdata = users::cookieUser();
    $posterdata = forum::get_userdata_from_id($userdata[0]);

    if (Config::get('npds.smilies')) {
        
        $user = users::getUser();

        if (isset($user)) {
            if ($posterdata['user_avatar'] != '') {
                
                if (stristr($posterdata['user_avatar'], "users_private")) {
                    $imgava = $posterdata['user_avatar'];
                } else {
                    $imgava = theme::theme_image_row('forum/avatar/'. $posterdata['user_avatar'], 'assets/images/forum/avatar/'. $posterdata['user_avatar']);
                }
            }
        } else {
            $imgava = theme::theme_image_row('forum/avatar/blank.gif', 'assets/images/forum/avatar/blank.gif');
        }
    }

    $moderator = forum::get_moderator($mod);
    $moderator = explode(' ', $moderator);
    $Mmod = false;

    echo '
    <p class="lead">
        <a href="'. site_url('forum.php') .'">' . __d('two_forum', 'Index du forum') . '</a>&nbsp;&raquo;&raquo;&nbsp;
        <a href="'. site_url('viewforum.php?forum=' . $forum) .'">' . stripslashes($forum_name) . '</a>&nbsp;&raquo;&raquo;&nbsp;' . $res_ftopic['topic_title'] . '
    </p>
    <div class="card">
        <div class="card-body p-1">
                ' . __d('two_forum', 'Modérateur(s)');

    for ($i = 0; $i < count($moderator); $i++) {
        $modera = forum::get_userdata($moderator[$i]);

        if ($modera['user_avatar'] != '') {
            if (stristr($modera['user_avatar'], "users_private")) {
                $imgtmp = $modera['user_avatar'];
            } else {
                $imgtmp = theme::theme_image_row('forum/avatar/'. $modera['user_avatar'], 'assets/images/forum/avatar/'. $modera['user_avatar']);
            }
        }

        echo '<a href="'. site_url('user.php?op=userinfo&amp;uname=' . $moderator[$i]) .'"><img width="48" height="48" class=" img-thumbnail img-fluid n-ava me-1" src="' . $imgtmp . '" alt="' . $modera['uname'] . '" title="' . $modera['uname'] . '" data-bs-toggle="tooltip" /></a>';
        
        $user = users::getUser();

        if (isset($user)) {
            if ((users::cookieUser(1) == $moderator[$i])) {
                $Mmod = true;
            }
        }
    }

    echo '
        </div>
    </div>
    <h4 class="d-none d-sm-block my-3"><img width="48" height="48" class=" rounded-circle me-3" src="' . $imgava . '" alt="" />' . __d('two_forum', 'Poster une réponse dans le sujet') . '</h4>
    <form action="'. site_url('reply.php') .'" method="post" name="coolsus">';

    echo '<blockquote class="blockquote d-none d-sm-block"><p>' . __d('two_forum', 'A propos des messages publiés :') . '<br />';

    if ($forum_access == 0) {
        echo __d('two_forum', 'Les utilisateurs anonymes peuvent poster de nouveaux sujets et des réponses dans ce forum.');
    } else if ($forum_access == 1) {
        echo __d('two_forum', 'Tous les utilisateurs enregistrés peuvent poster de nouveaux sujets et répondre dans ce forum.');
    } else if ($forum_access == 2) {
        echo __d('two_forum', 'Seuls les modérateurs peuvent poster de nouveaux sujets et répondre dans ce forum.');
    }

    echo '</blockquote>';

    $allow_to_reply = false;

    if ($forum_access == 0) {
        $allow_to_reply = true;
    } elseif ($forum_access == 1) {
        if (isset($user)) {
            $allow_to_reply = true;
        }
    } elseif ($forum_access == 2) {
        if (forum::user_is_moderator($userdata[0], $userdata[2], $forum_access)) {
            $allow_to_reply = true;
        }
    }

    if ($res_ftopic['topic_status'] != 0) {
        $allow_to_reply = false;
    }

    if ($allow_to_reply) {
        if (Request::input('submitP')) {
            $acc = 'reply';
            $message = stripslashes(Request::input('message'));

            include("preview.php");
        } else {
            $message = '';
        }

        if (Config::get('npds.smilies')) {

            $image_subject = Request::input('image_subject');

            echo '
        <div class="d-none d-sm-block mb-3 row">
            <label class="form-label">' . __d('two_forum', 'Icone du message') . '</label>
            <div class="col-sm-12">
                <div class="border rounded pt-3 px-2 n-fond_subject d-flex flex-row flex-wrap">
                ' . forum::emotion_add($image_subject) . '
                </div>
            </div>
        </div>';
        }

        echo '
        <div class="mb-3 row">
            <label class="form-label" for="message">' . __d('two_forum', 'Message') . '</label>
            <div class="col-sm-12">
                <div class="card">
                <div class="card-header">
                    <div class="float-start">';

        forum::putitems('ta_replypost');

        echo '
                    </div>';

        if (Config::get('forum.config.allow_html') == 1) {
            echo '<span class="text-success float-end mt-2" title="HTML ' . __d('two_forum', 'Activé') . '" data-bs-toggle="tooltip"><i class="fa fa-code fa-lg"></i></span>' . forum::HTML_Add();
        } else {
            echo '<span class="text-danger float-end mt-2" title="HTML ' . __d('two_forum', 'Désactivé') . '" data-bs-toggle="tooltip"><i class="fa fa-code fa-lg"></i></span>';
        }

        echo '
                </div>
                <div class="card-body">';

        $citation = Request::input('citation');

        if ($citation && !$submitP) {

            $res_post = DB::table('posts')
                    ->select('posts.post_text', 'posts.post_time', 'users.uname')
                    ->join('users', 'posts.poster_id', '=', 'users.uid')
                    ->where('post_id', $post)
                    ->xOrWhere('posts.poster_id', '=', 0)
                    ->get();

            if ($m = $res_post) {
                $text = $m['post_text'];
                
                if (($allow_bbcode) and ($forum_type != 6) and ($forum_type != 5)) {
                    $text = forum::smile($text);
                    $text = str_replace('<br />', "\n", $text);
                } else {
                    $text = htmlspecialchars($text, ENT_COMPAT | ENT_HTML401, 'utf-8');
                }

                $text = stripslashes($text);

                $reply = ($m['post_time'] != '' && $m['uname'] != '') ?
                    '<blockquote class="blockquote">' . __d('two_forum', 'Citation') . ' : <strong>' . $m['uname'] . '</strong><br />' . $text . '</blockquote>' :
                    $text . "\n";
                $reply = preg_replace("#\[hide\](.*?)\[\/hide\]#si", '', $reply);
            } else {
                $reply = __d('two_forum', 'Erreur de connexion à la base de données') . "\n";
            }
        }

        if (!isset($reply)) {
            $reply = $message;
        }

        if (Config::get('forum.config.allow_bbcode')) {
            $xJava = ' onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" onfocus="storeForm(this)"';
        }

        echo '
                    <textarea id="ta_replypost" class="form-control" ' . $xJava . ' name="message" rows="15">' . $reply . '</textarea>
                </div>
                <div class="card-footer p-0">
                    <span class="d-block">
                        <button class="btn btn-link" type="submit" value="' . __d('two_forum', 'Prévisualiser') . '" name="submitP" title="' . __d('two_forum', 'Prévisualiser') . '" data-bs-toggle="tooltip" ><i class="fa fa-eye fa-lg"></i></button>
                    </span>
                </div>
                </div>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="form-label">' . __d('two_forum', 'Options') . '</label>
            <div class="col-sm-12">';

        if ((Config::get('forum.config.allow_html') == 1) and ($forum_type != '6') and ($forum_type != '5')) {
            if (isset($html)) {
                $sethtml = 'checked';
            } else {
                $sethtml = '';
            }
            
            echo '
                <div class="checkbox my-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="html" name="html" ' . $sethtml . ' />
                    <label class="form-check-label" for="html">' . __d('two_forum', 'Désactiver le html pour cet envoi') . '</label>
                </div>
                </div>';
        }

        if ($user) {
            if (Config::get('forum.config.allow_sig') == 1 || $sig == 'on') {

                $res_status = DB::table('users_status')
                            ->select('attachsig')
                            ->where('uid', users::cookieUser(0))
                            ->first();

                if ($res_status['attachsig'] == 1) {
                    $s = 'checked="checked"';
                } else {
                    $s = '';
                }

                if (($forum_type != '6') and ($forum_type != '5')) {
                    echo '
                <div class="checkbox my-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="sig" name="sig" ' . $s . ' />
                    <label class="form-check-label" for="sig">' . __d('two_forum', 'Afficher la signature') . '</label>
                    <small class="help-block">' . __d('two_forum', 'Cela peut être retiré ou ajouté dans vos paramètres personnels') . '</small>
                </div>
                </div>';
                }
            }

            if (Config::get('forum.config.allow_upload_forum')) {
                if ($upload == 'on') {
                    $up = 'checked="checked"';
                }

                echo '
                <div class="checkbox my-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="upload" name="upload" ' . $up . ' />
                    <label class="form-check-label" for="upload">' . __d('two_forum', 'Charger un fichier une fois l\'envoi accepté') . '</label>
                </div>
                </div>';
            }
        }

        echo '
            </div>
        </div>
        ' . spam::Q_spambot() . '
        <div class="mb-3 row">
            <div class="col-sm-12">
                <input type="hidden" name="forum" value="' . $forum . '" />
                <input type="hidden" name="topic" value="' . $topic . '" />
                <button class="btn btn-primary" type="submit" value="' . __d('two_forum', 'Valider') . '" name="submitS" accesskey="s" title="' . __d('two_forum', 'Valider') . '" data-bs-toggle="tooltip" >' . __d('two_forum', 'Valider') . '</button>&nbsp;
                <button class="btn btn-danger" type="submit" value="' . __d('two_forum', 'Annuler la contribution') . '" name="cancel" title="' . __d('two_forum', 'Annuler la contribution') . '" data-bs-toggle="tooltip" >' . __d('two_forum', 'Annuler la contribution') . '</button>
            </div>
        </div>';
    } else {
        echo '
        <div class="alert alert-danger">' . __d('two_forum', 'Vous n\'êtes pas autorisé à participer à ce forum') . '</div>';
    }

    echo '
    </form>';

    if ($allow_to_reply) {
        echo '
        <h4 class="my-3">' . __d('two_forum', 'Aperçu des sujets :') . '</h4>';

        $query = DB::table('posts')
                    ->select('*')
                    ->where('topic_id', $topic)
                    ->where('forum_id', $forum);

        if($Mmod) {
            //
        } else {
            $query->where('post_aff', 1);
        }

        $result = $query->orderBy('post_id', 'desc')
                        ->limit(10)
                        ->offset(0)
                        ->get();

        if (!$myrow = $result) {
            forum::forumerror('0001');
        }

        $count = 0;

        foreach ($result as $myrow) {    
            echo '
        <div class="row">
            <div class="col-12">
                <div class="card mb-3">
                <div class="card-header">';

            $posterdata = forum::get_userdata_from_id($myrow['poster_id']);
            
            if ($myrow['poster_id'] !== '0') {
                $posts = $posterdata['posts'];

                $socialnetworks = array();
                $posterdata_extend = array();
                $res_id = array();

                $my_rs = '';

                if (!Config::get('npds.short_user')) {
                    $posterdata_extend = forum::get_userdata_extend_from_id($myrow['poster_id']);

                    include('modules/reseaux-sociaux/reseaux-sociaux.conf.php');

                    if ($user or users::autorisation(-127)) {
                        if (array_key_exists('M2', $posterdata_extend)) {
                            if ($posterdata_extend['M2'] != '') {
                                $socialnetworks = explode(';', $posterdata_extend['M2']);

                                foreach ($socialnetworks as $socialnetwork) {
                                    $res_id[] = explode('|', $socialnetwork);
                                }

                                sort($res_id);
                                sort($rs);

                                foreach ($rs as $v1) {
                                    foreach ($res_id as $y1) {
                                        $k = array_search($y1[0], $v1);
                                        
                                        if (false !== $k) {
                                            $my_rs .= '<a class="me-2" href="';

                                            if ($v1[2] == 'skype') {
                                                $my_rs .= $v1[1] . $y1[1] . '?chat';
                                            } else {
                                                $my_rs .= $v1[1] . $y1[1];
                                            }

                                            $my_rs .= '" target="_blank"><i class="fab fa-' . $v1[2] . ' fa-lg fa-fw mb-2"></i></a> ';
                                            break;
                                        } else {
                                            $my_rs .= '';
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                include('modules/geoloc/config/geoloc.conf');

                $useroutils = '';
                if ($posterdata['uid'] != 1 and $posterdata['uid'] != '') {
                    $useroutils .= '<hr />';
                }

                if ($user or users::autorisation(-127)) {
                    if ($posterdata['uid'] != 1 and $posterdata['uid'] != '') {
                        $useroutils .= '<a class="list-group-item text-primary text-center text-md-start" href="'. site_url('user.php?op=userinfo&amp;uname=' . $posterdata['uname']) .'" target="_blank" title="' . __d('two_forum', 'Profil') . '" data-bs-toggle="tooltip"><i class="fa fa-user fa-2x align-middle fa-fw"></i><span class="ms-3 d-none d-md-inline">' . __d('two_forum', 'Profil') . '</span></a>';
                    }

                    if ($posterdata['uid'] != 1) {
                        $useroutils .= '<a class="list-group-item text-primary text-center text-md-start" href="'. site_url('powerpack.php?op=instant_message&amp;to_userid=' . $posterdata["uname"]) .'" title="' . __d('two_forum', 'Envoyer un message interne') . '" data-bs-toggle="tooltip"><i class="far fa-envelope fa-2x align-middle fa-fw"></i><span class="ms-3 d-none d-md-inline">' . __d('two_forum', 'Message') . '</span></a>';
                    }

                    if ($posterdata['femail'] != '') {
                        $useroutils .= '<a class="list-group-item text-primary text-center text-md-start" href="mailto:' . spam::anti_spam($posterdata['femail'], 1) . '" target="_blank" title="' . __d('two_forum', 'Email') . '" data-bs-toggle="tooltip"><i class="fa fa-at fa-2x align-middle fa-fw"></i><span class="ms-3 d-none d-md-inline">' . __d('two_forum', 'Email') . '</span></a>';
                    }

                    if ($myrow['poster_id'] != 1 and array_key_exists($ch_lat, $posterdata_extend)) {
                        if ($posterdata_extend[$ch_lat] != '') {
                            $useroutils .= '<a class="list-group-item text-primary text-center text-md-start" href="'. site_url('modules.php?ModPath=geoloc&amp;ModStart=geoloc&amp;op=u' . $posterdata['uid']) .'" title="' . __d('two_forum', 'Localisation') . '" ><i class="fas fa-map-marker-alt fa-2x align-middle fa-fw">&nbsp;</i><span class="ms-3 d-none d-md-inline">' . __d('two_forum', 'Localisation') . '</span></a>';
                        }
                    }
                }

                if ($posterdata['url'] != '') {
                    $useroutils .= '<a class="list-group-item text-primary text-center text-md-start" href="' . $posterdata['url'] . '" target="_blank" title="' . __d('two_forum', 'Visiter ce site web') . '" data-bs-toggle="tooltip"><i class="fas fa-external-link-alt fa-2x align-middle fa-fw"></i><span class="ms-3 d-none d-md-inline">' . __d('two_forum', 'Visiter ce site web') . '</span></a>';
                }

                if ($posterdata['mns']) {
                    $useroutils .= '<a class="list-group-item text-primary text-center text-md-start" href="'. site_url('minisite.php?op=' . $posterdata['uname']) .'" target="_blank" target="_blank" title="' . __d('two_forum', 'Visitez le minisite') . '" data-bs-toggle="tooltip"><i class="fa fa-2x fa-desktop align-middle fa-fw"></i><span class="ms-3 d-none d-md-inline">' . __d('two_forum', 'Visitez le minisite') . '</span></a>';
                }
            }

            if (Config::get('npds.smilies')) {
                if ($myrow['poster_id'] !== '0') {
                    if ($posterdata['user_avatar'] != '') {
                        
                        if (stristr($posterdata['user_avatar'], "users_private")) {
                            $imgtmp = $posterdata['user_avatar'];
                        } else {
                            $imgtmp = theme::theme_image_row('forum/avatar/'. $posterdata['user_avatar'], 'assets/images/forum/avatar/'. $posterdata['user_avatar']);
                        }
                    }

                    echo '
                <a style="position:absolute; top:1rem;" tabindex="0" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-html="true" data-bs-title="' . $posterdata['uname'] . '" data-bs-content=\'<div class="my-2 border rounded p-2">' . forum::member_qualif($posterdata['uname'], $posts, $posterdata['rang']) . '</div><div class="list-group mb-3 text-center">' . $useroutils . '</div><div class="mx-auto text-center" style="max-width:170px;">' . $my_rs . '</div>\'><img class=" btn-outline-primary img-thumbnail img-fluid n-ava" src="' . $imgtmp . '" alt="' . $posterdata['uname'] . '" /></a>
                <span style="position:absolute; left:6em;" class="text-muted"><strong>' . $posterdata['uname'] . '</strong></span>';
                } else {
                    echo '
                <a style="position:absolute; top:1rem;" title="' . Config::get('npds.anonymous') . '" data-bs-toggle="tooltip"><img class=" btn-outline-primary img-thumbnail img-fluid n-ava" src="assets/images/forum/avatar/blank.gif" alt="' . Config::get('npds.anonymous') . '" /></a>
                <span style="position:absolute; left:6em;" class="text-muted"><strong>' . Config::get('npds.anonymous') . '</strong></span>';
                }
            } else {
                echo $myrow['poster_id'] !== '0' ?
                    '<span style="position:absolute; left:6em;" class="text-muted"><strong>' . $posterdata['uname'] . '</strong></span>' :
                    '<span class="text-muted"><strong>' . Config::get('npds.anonymous') . '</strong></span>';
            }

            echo '
                    <span class="float-end">';

            if ($myrow['image'] != '') {
                $imgtmp = theme::theme_image_row('forum/subject/'. $myrow['image'], 'assets/images/forum/subject/'. $myrow['image']);

                echo '<img class="n-smil" src="' . $imgtmp . '"  alt="" />';
            } else {
                $imgtmp = theme::theme_image_row('forum/subject/icons/posticon.gif', 'assets/images/forum/icons/posticon.gif');

                echo '<img class="n-smil" src="' . $imgtmp . '" alt="" />';
            }

            echo '
                    </span>
                </div>
                <div class="card-body">
                    <span class="text-muted float-end small" style="margin-top:-1rem;">' . __d('two_forum', 'Posté : ') . date::convertdate($myrow['post_time']) . '</span>
                    <div class="card-text pt-4">';

            $message = stripslashes($myrow['post_text']);

            if ((Config::get('forum.config.allow_bbcode')) and ($forum_type != 6) and ($forum_type != 5)) {
                $message = forum::smilie($message);
                $message = forum::aff_video_yt($message);
                $message = code::af_cod($message);
                $message = str_replace("\n", '<br />', $message);
            }

            if (($forum_type == '6') or ($forum_type == '5')) {
                highlight_string(stripslashes($myrow['post_text'])) . '<br /><br />';
            } else {
                if (array_key_exists('user_sig', $posterdata)) {
                    $message = str_replace('[addsig]', '<div class="n-signature">' . nl2br($posterdata['user_sig']) . '</div>', $message);
                }
                echo $message . '
                    </div>';
            }

            echo '
                    </div>
                    </div>
                </div>
            </div>';

            $count++;
        }
    }
}

include('themes/default/footer.php');
