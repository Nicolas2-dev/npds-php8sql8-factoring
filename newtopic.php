<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x and PhpBB integration source code               */
/*                                                                      */
/* This version name NPDS Copyright (c) 2001-2021 by Philippe Brunier   */
/* Great mods by snipe                                                  */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\system\logs\logs;
use npds\system\auth\users;
use npds\system\cache\cache;
use npds\system\forum\forum;
use npds\system\routing\url;
use npds\system\theme\theme;
use npds\system\utility\spam;
use npds\system\config\Config;
use npds\system\security\hack;
use npds\system\utility\crypt;
use npds\system\support\facades\DB;
use npds\system\subscribe\subscribe;
use npds\system\support\facades\Request;


if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

vd(
    
    Request::all(),

);

$forum = Request::query('forum');

if (Request::input('cancel')) {
    header('Location: '. site_url('viewforum.php?forum='. $forum));
}

include('auth.php');

if (!$myrow = cache::Q_Select3(DB::table('forums')
            ->select('forum_name', 'forum_moderator', 'forum_type', 'forum_pass', 'forum_access', 'arbre')
            ->where('forum_id', $forum = Request::input('forum'))
            ->first(), 3600, crypt::encrypt('forums(forum_name_forum_moderator)'))
) {
    forum::forumerror('0001');
}

$forum_name = $myrow['forum_name'];
$forum_access = $myrow['forum_access'];

$moderatorX = forum::get_moderator($myrow['forum_moderator']);
//$moderator = explode(' ', $moderator);

$user = users::getUser();

if (isset($user)) {
    // $userX = base64_decode($user);
    // $userdata = explode(':', $userX);
    $Mmod = false;
    
    $moderator = explode(' ', $moderatorX);
    for ($i = 0; $i < count($moderator); $i++) {
        if ((users::cookieUser(1) == $moderator[$i])) {
            $Mmod = true;
            break;
        }
    }

    $userdata = forum::get_userdata(users::cookieUser(1));
}

if (($myrow['forum_type'] == 1) and ($Forum_passwd != $myrow['forum_pass'])) {
    header('Location: '. site_url('forum.php'));
}

if ($forum_access == 9) {
    header('Location: '. site_url('forum.php'));
}

if (!forum::does_exists($forum, "forum")) {
    forum::forumerror('0030');
}

// Forum ARBRE
if ($myrow['arbre']) {
    $hrefX = "viewtopicH.php";
} else {
    $hrefX = "viewtopic.php";
}

settype($submitS, 'string');
settype($stop, 'integer');

if (Request::input('submitS')) {
    if ($message == '') {
        $stop = 1;
    }

    if ($subject == '') {
        $stop = 1;
    }

    if (!isset($user)) {
        if ($forum_access == 0) {
            $userdata = array("uid" => 1);
            $modo = '';

            include('themes/default/header.php');
        } else {
            if (($username == '') or ($password == '')) {
                forum::forumerror('0027');
            } else {
                $modo = '';
                
                $res_user = DB::table('users')
                                ->select('pass')
                                ->where('uname', $username)
                                ->first(); 

                if ((password_verify($password, $res_user['pass'])) and ($res_user['pass'] != '')) {
                    $userdata = forum::get_userdata($username);

                    include('themes/default/header.php');
                } else {
                    forum::forumerror('0028');
                }
            }
        }
    } else {
        $modo = forum::user_is_moderator($userdata['uid'], $userdata['uname'], $forum_access);
        include('themes/default/header.php');
    }

    // Either valid user/pass, or valid session. continue with post.
    if ($stop != 1) {
        $poster_ip = Request::getip();
        
        if (Config::get('npds.dns_verif')) {
            $hostname = @gethostbyaddr($poster_ip);
        } else {
            $hostname = '';
        }

        // anti flood
        forum::anti_flood($modo, Config::get('forum.config.anti_flood'), $poster_ip, $userdata, Config::get('npds.gmt'));

        //anti_spambot
        if (!spam::R_spambot($asb_question, $asb_reponse, $message)) {
            logs::Ecr_Log('security', 'Forum Anti-Spam : forum=' . $forum . ' / topic_title=' . $subject, '');
            url::redirect_url("index.php");
            die();
        }

        if ($myrow['forum_type'] == 8) {
            $formulaire = $myrow['forum_pass'];
            include("modules/sform/forum/forum_extender.php");
        }

        // if (Config::get('forum.config.allow_html') == 0 || isset($html)) {
        //     $message = htmlspecialchars($message, ENT_COMPAT|ENT_HTML401, 'utf-8');
        // }

        $sig = Request::input('sig');

        if (isset($sig) && $userdata['uid'] != 1 && $myrow['forum_type'] != 6 && $myrow['forum_type'] != 5) {
            $message .= " [addsig]";
        }

        // if (($myrow['forum_type'] != 6) and ($myrow['forum_type'] != 5)) {
        //     $message = code::af_cod($message);
        // }

        if ((Config::get('forum.config.allow_bbcode')) and ($myrow['forum_type'] != 6) and ($myrow['forum_type'] != 5)) {
            $message = forum::smile($message);
        }

        if (($myrow['forum_type'] != 6) and ($myrow['forum_type'] != 5)) {
            $message = forum::make_clickable($message);
            $message = hack::removeHack($message);
        }

        $message = addslashes($message);

        if (!isset($Mmod)) {
            $subject = hack::removeHack(strip_tags($subject));
        }

        //$Msubject = $subject;

        $time = date("Y-m-d H:i", time() + ((int) Config::get('npds.gmt') * 3600));

        if (!$insertGetIdTopicId = DB::table('forumtopics')->insertGetId(array(
                'topic_title'       => $subject,
                'topic_poster'      => $userdata['uid'],
                'current_poster'    => $userdata['uid'],
                'forum_id'          => $forum,
                'topic_time'        => $time,
                'topic_notify'      => ((isset($notify2) && $userdata['uid'] != 1) ? ", '1'" :  ", '0'")
            ))
        ) {
            forum::forumerror('0020');
        }

        $topic_id = $insertGetIdTopicId;

        $insertGetId = DB::table('posts')->insertGetId(array(
            'topic_id'      => $topic_id,
            'image'         => $image_subject,
            'forum_id'      => $forum,
            'poster_id'     => $userdata['uid'],
            'post_text'     => $message,
            'post_time'     => $time,
            'poster_ip'     => $poster_ip,
            'poster_dns'    => $hostname,
        ));

        if (!$insertGetId) {
            forum::forumerror('0020');
        } else {
            $IdPost = $insertGetId;
        }

        if (!DB::table('users_status')->where('uid', $userdata['uid'])
            ->update(array(
                'posts' => DB::raw('posts+1'),
            ))
        ) {
            forum::forumerror('0029');
        }

        //$topic = $topic_id;

        if (Config::get('npds.subscribe')) {
            //subscribe::subscribe_mail("forum", $topic, stripslashes($forum), stripslashes($Msubject), $userdata['uid']);
            subscribe::subscribe_mail("forum", $topic_id, stripslashes($forum), stripslashes($subject), $userdata['uid']);
        }

        if (isset($upload)) {
            include("modules/upload/upload_forum.php");
            //win_upload("forum_npds", $IdPost, $forum, $topic, "win");
            win_upload("forum_npds", $IdPost, $forum, $topic_id, "win");
        }

        //url::redirect_url($hrefX . '?forum='. $forum .'&topic='. $topic);
        url::redirect_url($hrefX . '?forum='. $forum .'&topic='. $topic_id);
    } else {
        echo '
        <div class="alert alert-danger lead" role="alert">
            <i class="fa fa-exclamation-triangle fa-lg"></i>&nbsp;
            ' . translate("Vous devez choisir un titre et un message pour poster votre sujet.") . '
        </div>';
    }
} else {
    include('themes/default/header.php');

    if (Config::get('forum.config.allow_bbcode')) {
        include("assets/formhelp.java.php");
    }

    // $userX = base64_decode($user);
    // $userdata = explode(':', $userX);
    // $posterdata = forum::get_userdata_from_id($userdata[0]);

    if (Config::get('npds.smilies')) {
        if (isset($user)) {

            $posterdata = forum::get_userdata_from_id(users::cookieUser(0));

            if ($posterdata['user_avatar'] != '') {
                if (stristr($posterdata['user_avatar'], "users_private")) {
                    $imgava = $posterdata['user_avatar'];
                } else {
                    // if ($ibid = theme::theme_image("forum/avatar/" . $posterdata['user_avatar'])) {
                    //     $imgava = $ibid;
                    // } else {
                    //     $imgava = "assets/images/forum/avatar/" . $posterdata['user_avatar'];
                    // }

                    $imgava = theme::theme_image_row('forum/avatar/'. $posterdata['user_avatar'], 'assets/images/forum/avatar/'. $posterdata['user_avatar']);
                }
            }
        } else {
            // if ($ibid = theme::theme_image("forum/avatar/blank.gif")) {
            //     $imgava = $ibid;
            // } else {
            //     $imgava = "assets/images/forum/avatar/blank.gif";
            // }

            $imgava = theme::theme_image_row('forum/avatar/blank.gif', 'assets/images/forum/avatar/blank.gif');
        }
    }

    echo '
    <p class="lead">
        <a href="'. site_url('forum.php') .'" >
            ' . translate("Index du forum") . '
        </a>
        &nbsp;&raquo;&raquo;&nbsp;
        <a href="'. site_url('viewforum.php?forum=' . $forum) .'">
            ' . stripslashes($forum_name) . '
        </a>
    </p>
        <div class="card">
            <div class="card-block-small">
            ' . translate("Modéré par : ");

    //$moderatorX = forum::get_moderator($myrow['forum_moderator']);
    //$moderator_data = explode(' ', $moderatorX);
    $moderator_data = explode(' ', $moderatorX);

    for ($i = 0; $i < count($moderator_data); $i++) {
        $modera = forum::get_userdata($moderator_data[$i]);
        
        if ($modera['user_avatar'] != '') {
            if (stristr($modera['user_avatar'], "users_private")) {
                $imgtmp = $modera['user_avatar'];
            } else {
                // if ($ibid = theme::theme_image("forum/avatar/" . $modera['user_avatar'])) {
                //     $imgtmp = $ibid;
                // } else {
                //     $imgtmp = "assets/images/forum/avatar/" . $modera['user_avatar'];
                // }

                $imgtmp = theme::theme_image_row('forum/avatar/' . $modera['user_avatar'], 'assets/images/forum/avatar/' . $modera['user_avatar']);
            }
        }

        echo '<a href="'. site_url('user.php?op=userinfo&amp;uname=' . $moderator_data[$i]) .'">
                <img width="48" height="48" class=" img-thumbnail img-fluid n-ava me-1 mx-1" src="' . $imgtmp . '" alt="' . $modera['uname'] . '" title="' . $modera['uname'] . '" data-bs-toggle="tooltip" />
            </a>';
    }

    echo '
            </div>
        </div>
        <h4 class="my-3">
            <img width="48" height="48" class=" rounded-circle me-3" src="' . $imgava . '" alt="" />
                ' . translate("Poster un nouveau sujet dans :") . ' ' . stripslashes($forum_name) . '<span class="text-muted">&nbsp;#' . $forum . '</span>
        </h4>
            <blockquote class="blockquote">' . translate("A propos des messages publiés :") . '<br />';
    
    if ($forum_access == 0) {
        echo translate("Les utilisateurs anonymes peuvent poster de nouveaux sujets et des réponses dans ce forum.");
    } elseif ($forum_access == 1) {
        echo translate("Tous les utilisateurs enregistrés peuvent poster de nouveaux sujets et répondre dans ce forum.");
    } elseif ($forum_access == 2) {
        echo translate("Seuls les modérateurs peuvent poster de nouveaux sujets et répondre dans ce forum.");
    }

    echo '
        </blockquote>
        <form id="new_top" action="'. site_url('newtopic.php') .'" method="post" name="coolsus">';

    echo '<br />';

    if ($forum_access == 1) {
        if (!isset($user)) {
            echo '
            <fieldset>
                <div class="mb-3 row">
                <label class="control-label col-sm-2" for="username">' . translate("Identifiant : ") . '</label>
                <div class="col-sm-8 col-md-4">
                    <input class="form-control" type="text" id="username" name="username" placeholder="' . translate("Identifiant") . '" required="required" />
                </div>
                </div>
                <div class="mb-3 row">
                <label class="control-label col-sm-2" for="password">' . translate("Mot de passe : ") . '</label>
                <div class="col-sm-8">
                    <input class="form-control" type="password" id="password" name="password" placeholder="' . translate("Mot de passe") . '" required="required" />
                </div>
                </div>
            </fieldset>';
            $allow_to_post = 1;
        } else {
            $allow_to_post = 1;
        }

    } elseif ($forum_access == 2) {
        if (forum::user_is_moderator(users::cookieUser(0), users::cookieUser(2), $forum_access)) {
            
            echo '<strong>' . translate("Auteur") . ' :</strong>';
            echo users::cookieUser(1);
            
            $allow_to_post = 1;
        }
    } elseif ($forum_access == 0) {
        $allow_to_post = 1;
    }

    //settype($submitP, 'string');

    if ($allow_to_post) {
        
        if (Request::input('submitP')) {
            $acc = 'newtopic';
            $subject = stripslashes($subject);
            $message = stripslashes($message);

            if (isset($username)) {
                $username = stripslashes($username);
            } else {
                $username = '';
            }

            if (isset($password)) {
                $password = stripslashes($password);
            } else {
                $password = '';
            }

            include("preview.php");
        } else {
            $username = '';
            $password = '';
            $subject = '';
            $message = '';
        }

        if ($myrow['forum_type'] == 8) {
            $formulaire = $myrow['forum_pass'];

            include("modules/sform/forum/forum_extender.php");
        } else {
            echo ' 
            <div class="mb-3 row">
                <label class="form-label" for="subject">' . translate("Sujet") . '</label>
                <div class="col-sm-12">
                    <input class="form-control" type="text" id="subject" name="subject" placeholder="' . translate("Sujet") . '" required="required" value="' . $subject . '" />
                </div>
            </div>';

            if (Config::get('npds.smilies')) {
                settype($image_subject, 'string');

                echo '
                <div class="d-none d-sm-block mb-3 row">
                    <label class="form-label">' . translate("Icone du message") . '</label>
                    <div class="col-sm-12">
                        <div class="border rounded pt-3 px-2 n-fond_subject d-flex flex-row flex-wrap">
                        ' . forum::emotion_add($image_subject) . '
                        </div>
                    </div>
                </div>';
            }

            echo ' 
            <div class="mb-3 row">
                <label class="form-label" for="message">' . translate("Message") . '</label>';

            if (Config::get('forum.config.allow_bbcode')) {
                $xJava = 'name="message" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" onfocus="storeForm(this)"';
            }

            echo '
            <div class="col-sm-12">
                <div class="card">
                <div class="card-header">
                    <div class="float-start">';

            forum::putitems('ta_newtopic');

            echo '</div>';

            if (Config::get('forum.config.allow_html') == 1) {
                echo '<span class="text-success float-end mt-2" title="HTML ' . translate("On") . '" data-bs-toggle="tooltip"><i class="fa fa-code fa-lg"></i></span>' . forum::HTML_Add();
            } else {
                echo '<span class="text-danger float-end mt-2" title="HTML ' . translate("Off") . '" data-bs-toggle="tooltip"><i class="fa fa-code fa-lg"></i></span>';
            }

            echo '
                </div>
                <div class="card-body">
                    <textarea id="ta_newtopic" class="form-control" ' . $xJava . ' name="message" rows="12">' . $message . '</textarea>
                </div>
                <div class="card-footer p-0">
                    <span class="d-block">
                        <button class="btn btn-link" type="submit" value="' . translate("Prévisualiser") . '" name="submitP" title="' . translate("Prévisualiser") . '" data-bs-toggle="tooltip" ><i class="fa fa-eye fa-lg"></i></button>
                    </span>
                </div>
                </div>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="form-label">' . translate("Options") . '</label>
            <div class="col-sm-12">
                <div class="custom-controls-stacked">';

            if ((Config::get('forum.config.allow_html') == 1) and ($myrow['forum_type'] != 6) and ($myrow['forum_type'] != 5)) {
                
                //vd(isset($html));
                $html = Request::input('html');

                if (isset($html)) {
                    $sethtml = 'checked="checked"';
                } else {
                    $sethtml = '';
                }

                echo '
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="html" name="html" ' . $sethtml . ' />
                    <label class="form-check-label" for="html">' . translate("Désactiver le html pour cet envoi") . '</label>
                </div>';
            }

            if ($user) {

                $res_status = DB::table('users_status')
                    ->select('attachsig')
                    ->where('uid', users::cookieUser(0))
                    ->first(); 

                //if (Config::get('forum.config.allow_sig') == 1 || Request::input('sig') == 'on') {
                if (Config::get('forum.config.allow_sig') == 1 && $res_status['attachsig'] == 1) {

                    // $res_status = DB::table('users_status')
                    //                 ->select('attachsig')
                    //                 ->where('uid', users::cookieUser(0))
                    //                 ->first(); 

                    //if ($res_status['attachsig'] == 1) { 
                    if ($res_status['attachsig'] == 1 && !is_null(Request::input('sig'))) { 
                        $s = 'checked="checked"';
                    } else {
                        $s = '';
                    }
                    
                    if (($myrow['forum_type'] != 6) and ($myrow['forum_type'] != 5)) {
                        echo '
                        <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="sig" name="sig" ' . $s . ' />
                        <label class="form-check-label" for="sig">' . translate("Afficher la signature") . '</label>
                        </div>';
                    }
                }

                settype($up, 'string');
                settype($upload, 'string');

                if (Config::get('forum.config.allow_upload_forum')) {
                    if ($upload == "on") {
                        $up = 'checked="checked"';
                    }

                    echo '
                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="upload" name="upload" ' . $up . ' />
                    <label class="form-check-label" for="upload">' . translate("Charger un fichier une fois l'envoi accepté") . '</label>
                    </div>';
                }

                if (isset($notify2)) {
                    $selnot = 'checked="checked"';
                } else {
                    $selnot = '';
                }

                echo '
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="notify2" name="notify2" ' . $selnot . ' />
                    <label class="form-check-label" for="notify2">' . translate("Prévenir par Email quand de nouvelles réponses sont postées") . '</label>
                </div>';
            }

            echo '
                </div>
            </div>
        </div>
        ' . spam::Q_spambot() . '
        <div class="mb-3 row">
            <div class="col-sm-12">
                <input type="hidden" name="forum" value="' . $forum . '" />
                <input class="btn btn-primary" type="submit" name="submitS" value="' . translate("Valider") . '" accesskey="s" />
                <input class="btn btn-danger" type="submit" name="cancel" value="' . translate("Annuler la contribution") . '" />
            </div>
        </div>';
        }
    }

    echo '
        </form>';
}

include('themes/default/footer.php');
