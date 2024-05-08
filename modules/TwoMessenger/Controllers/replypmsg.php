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

use npds\support\assets\js;
use npds\support\assets\css;
use npds\support\auth\users;
use npds\support\assets\java;
use npds\support\forum\forum;
use npds\support\mail\mailler;
use npds\support\utility\code;
use npds\system\config\Config;
use npds\support\security\hack;
use npds\system\support\facades\DB;
use npds\system\support\facades\Request;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

include('auth.php');

vd(Request::all());

if (Request::input('cancel')) {
    if ($full_interface != 'short') {
        header('Location: '. site_url('viewpmsg.php'));
    } else {
        header('Location: '. site_url('readpmsg_imm.php?op=new_msg'));
    }
    die();
}

$user = users::getUser();

if (isset($user)) {

    $userdata = forum::get_userdata(users::cookieUser(1));

    $usermore = forum::get_userdata_from_id(users::cookieUser(0));

    if (Request::input('submitS')) {
        if ($subject == '') {
            forum::forumerror('0017');
        }

        $subject = hack::removeHack($subject);

        if (Config::get('npds.smilies')) {
            if ($image_subject == '') {
                forum::forumerror('0018');
            }
        }

        if ($message == '') {
            forum::forumerror('0019');
        }

        if (Config::get('forum.config.allow_html') == 0 || isset($html)) {
            $message = htmlspecialchars($message, ENT_COMPAT | ENT_HTML401, 'utf-8');
        }

        $sig = Request::input('sig');

        if ($sig) {
            $message .= '<br /><br />' . $userdata['user_sig'];
        }

        $message = code::aff_code($message);
        $message = str_replace("\n", '<br />', $message);

        if (Config::get('forum.config.allow_bbcode')) {
            $message = forum::smile($message);
        }

        $message = forum::make_clickable($message);
        $message = hack::removeHack(addslashes($message));
        $time = date(__d('two_messenger', 'dateinternal'), time() + ((int) Config::get('npds.gmt') * 3600));

        include_once("language/multilangue.php");

        if (strstr($to_user, ',')) {
            $tempo = explode(',', $to_user);

            foreach ($tempo as $to_user) {

                $res_user = DB::table('users')
                                ->select('uid', 'user_langue')
                                ->where('uname', $to_user)
                                ->first();

                $to_userid   = $res_user['uid'];
                $user_langue = $res_user['user_langue'];

                if (($to_userid != '') and ($to_userid != 1)) {

                    $r = DB::table('priv_msgs')->insert(array(
                       'msg_image'     => $image_subject,
                       'subject'       => $subject,
                       'from_userid'   => $userdata['uid'],
                       'to_userid'     => $to_userid,
                       'msg_time'      => $time,
                       'msg_text'      => $message,
                    ));

                    if (!$r) {
                        forum::forumerror('0020');
                    }

                    $copie = Request::input('copie');

                    if ($copie) {

                        $r = DB::table('priv_msgs')->insert(array(
                           'msg_image'      => $image_subject,
                           'subject'        => $subject,
                           'from_userid'    => $userdata['uid'],
                           'to_userid'      => $to_userid,
                           'msg_time'       => $time,
                           'msg_text'       => $message,
                           'type_msg'       => 1,
                           'read_msg'       => 1,
                        ));

                        if (!$r) {
                            forum::forumerror('0020');
                        }
                    }

                    if (Config::get('npds.subscribe')) {
                        $old_message = $message; // what this
                        
                        $sujet = translate_ml($user_langue, "Notification message privé.") . '[' . $usermore['uname'] . '] / ' . Config::get('npds.sitename');
                        
                        $message = translate_ml($user_langue, "Bonjour") . '<br />' . translate_ml($user_langue, "Vous avez un nouveau message.") . '<br />' . $time . '<br /><br /><b>' . $subject . '</b><br /><br /><a href="'. site_url('viewpmsg.php') .'">' . translate_ml($user_langue, "Cliquez ici pour lire votre nouveau message.") . '</a><br /><br />';
                        $message .= Config::get('signature.message');

                        mailler::copy_to_email($to_userid, $sujet, stripslashes($message));
                        $message = $old_message; // what this
                    }
                }
            }
        } else {

            $res_user = DB::table('users')
                            ->select('uid', 'user_langue')
                            ->where('uname', $to_user)
                            ->first();

            $to_userid   = $res_user['uid'];
            $user_langue = $res_user['user_langue'];

            if (($to_userid == '') or ($to_userid == 1)) {
                forum::forumerror('0016');
            } else {

                $r = DB::table('priv_msgs')->insert(array(
                   'msg_image'       => $image_subject,
                   'subject'         => $subject,
                   'from_userid'     => $userdata['uid'],
                   'to_userid'       => $to_userid,
                   'msg_time'        => $time,
                   'msg_text'        => $message,
                ));

                if (!$r) {
                    forum::forumerror('0020');
                }
                
                $copie = Request::input('copie');

                if ($copie) {

                    $r = DB::table('priv_msgs')->insert(array(
                       'msg_image'       => $image_subject,
                       'subject'         => $subject,
                       'from_userid'     => $userdata['uid'],
                       'to_userid'       => $to_userid,
                       'msg_time'        => $time,
                       'msg_text'        => $message,
                       'type_msg'        => 1,
                       'read_msg'        => 1,
                    ));

                    if (!$r) {
                        forum::forumerror('0020');
                    }
                }

                if (Config::get('npds.subscribe')) {
                    $sujet = translate_ml($user_langue, "Notification message privé.") . '[' . $usermore['uname'] . '] / ' . Config::get('npds.sitename');

                    $message = translate_ml($user_langue, "Bonjour") . '<br />' . translate_ml($user_langue, "Vous avez un nouveau message.") . '<br />' . $time . '<br /><br /><b>' . $subject . '</b><br /><br /><a href="'. site_url('viewpmsg.php') .'">' . translate_ml($user_langue, "Cliquez ici pour lire votre nouveau message.") . '</a><br /><br />';
                    $message .= Config::get('signature.message');

                    mailler::copy_to_email($to_userid, $sujet, stripslashes($message));
                }
            }
        }

        unset($message);
        unset($sujet);

        if ($full_interface != 'short') {
            header('Location: '. site_url('viewpmsg.php'));
        } else {
            header('Location: '. site_url('readpmsg_imm.php?op=new_msg'));
        }
    }

    $delete_messages = Request::input('delete_messages');

    if ($delete_messages and $msg_id) {

        foreach ($msg_id as $v) {
            if ($type == 'outbox') {
                $r = DB::table('priv_msgs')
                    ->where('msg_id', $v)
                    ->where('from_userid', $userdata['uid'])
                    ->where('type_msg', 1)
                    ->delete();
            } else {
                $r = DB::table('priv_msgs')
                    ->where('msg_id', $v)
                    ->where('to_userid', $userdata['uid'])
                    ->delete();
            }

            if (!$r) {
                forum::forumerror('0021');
            } else {
                $status = 1;
            }
        }

        if ($status == 1) {
            header('Location: '. site_url('viewpmsg.php'));
        }

    } elseif ($delete_messages = '' and !$msg_id) {
        header('Location: '. site_url('viewpmsg.php'));
    }

    if (isset($delete)) {
        if (isset($type) and $type == 'outbox') {
            $r = DB::table('priv_msgs')
                ->where('msg_id', $msg_id)
                ->where('from_userid', $userdata['uid'])
                ->where('type_msg', 1)
                ->delete();
        } else {
            $r = DB::table('priv_msgs')
                ->where('msg_id', $msg_id)
                ->where('to_userid', $userdata['uid'])
                ->delete();
        }

        if (!$r) {
            forum::forumerror('0021');
        } else {
            header('Location: '. site_url('viewpmsg.php'));
        }
    }

    $classement = Request::input('classement');

    if ($classement) {
        if ($nouveau_dossier != '') {
            $dossier = $nouveau_dossier;
        }

        $dossier = strip_tags($dossier);

        $r = DB::table('priv_msgs')->where('msg_id', $msg_id)->where('to_userid', $userdata['uid'])->update(array(
           'dossier' => $dossier,
        ));

        if (!$r) {
            forum::forumerror('0005');
        }

        header('Location: '. site_url('viewpmsg.php'));
    }

    // Interface
    $full_interface = Request::input('full_interface');

    if ($full_interface == 'short') {
        if ($userdataX[9] != '') {
            if (!$file = @opendir("themes/$userdataX[9]")) {
                $tmp_theme = Config::get('npds.Default_Theme');
            } else {
                $tmp_theme = $userdataX[9];
            }
        } else{
            $tmp_theme = Config::get('npds.Default_Theme');
        }

        include("themes/$tmp_theme/theme.php");
        include("storage/meta/meta.php");
        include("themes/default/view/include/header_before.inc");
        include("themes/default/view/include/header_head.inc");

        echo css::import_css($tmp_theme, Config::get('npds.language'), '', '', '');

        echo '
    </head>
    <body class="my-4 mx-4">';

    } else {
        include('themes/default/header.php');
    }

    if ($reply || $send || $to_user) {
        if (config::get('forum.config.allow_bbcode')) {
            include("assets/formhelp.java.php");
        }

        if ($reply) {

            $row = DB::table('priv_msgs')
                    ->select('msg_image', 'subject', 'from_userid', 'to_userid')
                    ->where('to_userid', $userdata['uid'])
                    ->where('msg_id', $msg_id)
                    ->where('type_msg', 0)
                    ->first();

            if (!$row) { 
                forum::forumerror('0022');
            }

            if (!$row) {
                forum::forumerror('0023');
            }

            $fromuserdata = forum::get_userdata_from_id($row['from_userid']);
            
            if (array_key_exists(0, $fromuserdata)) {
                if ($fromuserdata[0] == 1) {
                    forum::forumerror('0101');
                }
            }

            $touserdata = forum::get_userdata_from_id($row['to_userid']);
            if (($user) and ($userdata['uid'] != $touserdata['uid'])) {
                forum::forumerror('0024');
            }
        }

        echo '
        <h2><a href="'. site_url('viewpmsg.php') .'"><i class="me-2 fa fa-inbox"></i></a>' . __d('two_messenger', 'Message personnel') . '</h2>
        <hr />
        <blockquote class="blockquote">' . __d('two_messenger', 'A propos des messages publiés :') . '<br />' .
            __d('two_messenger', 'Tous les utilisateurs enregistrés peuvent poster des messages privés.') . '</blockquote>';

        $submitP = Request::input('submitP');

        if ($submitP) {
            echo '
            <hr />
            <h3>' . __d('two_messenger', 'Prévisualiser') . '</h3>
            <p class="lead">' . StripSlashes($subject) . '</p>';

            $Xmessage = $message = StripSlashes($message);

            if (Config::get('forum.config.allow_html') == 0 || isset($html)) {
                $Xmessage = htmlspecialchars($Xmessage, ENT_COMPAT | ENT_HTML401, 'utf-8');
            }

            $sig = Request::input('sig');

            if ($sig == 'on') {
                $Xmessage .= '<div class="n-signature">' . nl2br($userdata['user_sig']) . '</div>';
            }

            $Xmessage = code::aff_code($Xmessage);
            $Xmessage = str_replace("\n", '<br />', $Xmessage);

            if (Config::get('forum.config.allow_bbcode')) {
                $Xmessage = forum::smilie($Xmessage);
                $Xmessage = forum::aff_video_yt($Xmessage);
            }

            $Xmessage = forum::make_clickable($Xmessage);
            echo $Xmessage;
            echo '<hr />';
        }

        echo '
            <form id="pmessage" action="'. site_url('replypmsg.php') .'" method="post" name="coolsus">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-3" for="to_user">' . __d('two_messenger', 'Destinataire') . '</label>
                <div class="col-sm-9">';

        if ($reply) {
            echo userpopover($fromuserdata['uname'], 48, 2) . '
                <input class="form-control-plaintext d-inline-block w-75" type="text" id="to_user" name="to_user" value="' . $fromuserdata['uname'] . '" readonly="readonly" />';
        } else {
            if ($send != 1) {
                $Xto_user = $send;
            }

            if ($to_user) {
                $Xto_user = $to_user;
            }

            echo '
                <input class="form-control" type="text" id="to_user" name="to_user" value="' . $Xto_user . '" maxlength="100" required="required"/>';
        }

        if (!$reply) {
            $carnet = java::JavaPopUp(site_url('carnet.php'), "CARNET", 300, 350);
            $carnet = '<a href="javascript:void(0);" onclick="window.open(' . $carnet . '); ">';

            echo $carnet . '<span class="small">' . __d('two_messenger', 'Carnet d\'adresses') . '</span></a>';
        }

        echo '
                </div>
            </div>';

        $copie = Request::input('copie');

        if ($copie) {
            $checked = 'checked="checked"';
        } else {
            $checked = '';
        }

        echo '
            <div class="mb-3 row">
                <div class="col-sm-9 ms-auto">
                <div class="form-check">
                <input class="form-check-input" type="checkbox" id="copie" name="copie" ' . $checked . ' />
                <label class="form-check-label" for="copie"> ' . __d('two_messenger', 'Conserver une copie') . '</label>
                </div>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="subject">' . __d('two_messenger', 'Sujet') . '</label>
                <div class="col-sm-12">';

        $subject = Request::input('subject');

        if ($subject) {
            $tmp = StripSlashes($subject);
        } else {
            if ($reply) {
                $tmp = "Re: " . StripSlashes($row['subject']);
            } else {
                $tmp = '';
            }
        }

        echo '
                <input class="form-control" type="text" id="subject" name="subject" value="' . $tmp . '" maxlength="100" required="required" />
                <span class="help-block" id ="countcar_subject"></span>
                </div>
            </div>';

        if (Config::get('npds.smilies')) {

            $image_subject = Request::input('image_subject');

            echo '
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12">' . __d('two_messenger', 'Icone du message') . '</label>
                <div class="col-sm-12">
                <div class="border rounded pt-3 px-2 n-fond_subject d-flex flex-row flex-wrap">
                ' . forum::emotion_add($image_subject) . '
                </div>
                </div>
            </div>';
        }

        echo '
        <div class="mb-3 row">
            <label class="col-form-label col-sm-12" for="message">' . __d('two_messenger', 'Message') . '</label>
            <div class="col-sm-12">
                <div class="card">
                <div class="card-header">';

        if (Config::get('forum.config.allow_html') == 1) { 
            echo '<span class="text-success float-end" title="HTML ' . __d('two_messenger', 'Activé') . '" data-bs-toggle="tooltip"><i class="fa fa-code fa-lg"></i></span>' . forum::HTML_Add();
        } else {
            echo '<span class="text-danger float-end" title="HTML ' . __d('two_messenger', 'Désactivé') . '" data-bs-toggle="tooltip"><i class="fa fa-code fa-lg"></i></span>';
        }

        echo '
                </div>
                <div class="card-body">';

        $message = Request::input('message');

        if ($reply and $message == '') {

            $row = DB::table('priv_msgs')
                    ->select('priv_msgs.msg_text', 'priv_msgs.msg_time', 'users.uname')
                    ->join('users', 'priv_msgs.msg_id', '=', $msg_id)
                    ->where('priv_msgs.from_userid', 'users.uid')
                    ->where('priv_msgs.type_msg', 0)
                    ->get();

            if ($row) {
  
                $text = forum::smile($row['msg_text']);
                $text = str_replace("<br />", "\n", $text);
                $text = str_replace("<BR />", "\n", $text);
                $text = str_replace("<BR>", "\n", $text);
                $text = stripslashes($text);

                if ($row['msg_time'] != '' && $row['uname'] != '') {
                    $Xreply = $row['msg_time'] . ', ' . $row['uname'] . ' ' . __d('two_messenger', 'a écrit :') . "\n$text\n";
                } else {
                    $Xreply = $text;
                }

                $Xreply = '
                <div class="blockquote">
                ' . $Xreply . '
                </div>';
            } else {
                $Xreply = __d('two_messenger', 'Pas de connexion à la base forums.') . "\n";
            }

        } elseif ($message != '') {
            $Xreply = $message;
        }

        if (Config::get('forum.config.allow_bbcode')) {
            $xJava = 'name="message" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" onfocus="storeForm(this)"';
        }

        echo ' <textarea id="ta_replypm" class="form-control" ' . $xJava . ' name="message" rows="15">';

        if ($Xreply) {
            echo $Xreply;
        }
        
        echo '
            </textarea>
            <span class="help-block text-end">
                <button class="btn btn-outline-danger btn-sm" type="reset" value="' . __d('two_messenger', 'Annuler') . '" title="' . __d('two_messenger', 'Annuler') . '" data-bs-toggle="tooltip" ><i class="fas fa-times " ></i></button>
                <button class="btn btn-outline-primary btn-sm" type="submit" value="' . __d('two_messenger', 'Prévisualiser') . '" name="submitP" title="' . __d('two_messenger', 'Prévisualiser') . '" data-bs-toggle="tooltip" ><i class="fa fa-eye "></i></button>
            </span>
                </div>
                <div class="card-footer text-muted">';

        if (Config::get('forum.config.allow_bbcode')) {
            forum::putitems('ta_replypm');
        }

        echo '
                </div>
                </div>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-3">' . __d('two_messenger', 'Options') . '</label>';

        if (Config::get('forum.config.allow_html') == 1) {

            $html = Request::input('html');

            if ($html == 'on') {
                $checked = 'checked="checked"';
            } else {
                $checked = '';
            }

            echo '
            <div class="col-sm-9 my-2">
                <div class="form-check">
                <input class="form-check-input" type="checkbox" id="html" name="html" ' . $checked . ' />
                <label class="form-check-label" for="html">' . __d('two_messenger', 'Désactiver le html pour cet envoi') . '</label>
                </div>';
        }

        if (Config::get('forum.config.allow_sig') == 1 && $usermore['attachsig'] == '1') {

            if ($submitP) {
                if ($sig == 'on') { 
                    $checked = 'checked="checked"';
                } else {
                    $checked = '';
                }
            }

            echo '
                <div class="form-check">
                <input class="form-check-input" type="checkbox" id="sig" name="sig" ' . $checked . ' />
                <label class="form-check-label" for="sig">' . __d('two_messenger', 'Afficher la signature') . '</label>
                </div>
                <small class="help-block">' . __d('two_messenger', 'Cela peut être retiré ou ajouté dans vos paramètres personnels') . '</small>';
        }

        echo '
            </div>
        </div>
        <div class="mb-3 row">
            <div class="col-sm-12">
                <input type="hidden" name="msg_id" value="' . $msg_id . '" />
                <input type="hidden" name="full_interface" value="' . $full_interface . '" />';

        $send = Request::input('send');

        if ($send == 1) {
            echo '<input type="hidden" name="send" value="1" />';
        }

        if ($reply == 1) {
            echo '<input type="hidden" name="reply" value="1" />';
        }

        echo '<input class="btn btn-primary" type="submit" name="submitS" value="' . __d('two_messenger', 'Valider') . '" />&nbsp;';

        if ($reply) {
            echo '
                <input class="btn btn-danger ms-2" type="submit" name="cancel" value="' . __d('two_messenger', 'Annuler la réponse') . '" />';
        } else {
            echo '
                <input class="btn btn-danger ms-2" type="submit" name="cancel" value="' . __d('two_messenger', 'Annuler l\'envoi') . '" />';

            echo js::auto_complete('membre', 'uname', 'users', 'to_user', 86400);
        }

        echo '
            </div>
        </div>
    </form>';
    
        $arg1 = '
            var formulid=["pmessage"]
            inpandfieldlen("subject",100);';

        css::adminfoot('', '', $arg1, 'foo');
    }
} else {
    Header('Location: '. site_url('user.php'));
}
