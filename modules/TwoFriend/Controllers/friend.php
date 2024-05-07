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

use npds\support\logs\logs;
use npds\support\assets\css;
use npds\support\auth\users;
use npds\support\routing\url;
use npds\support\mail\mailler;
use npds\support\utility\spam;
use npds\system\config\Config;
use npds\support\security\hack;
use npds\support\language\language;
use npds\system\support\facades\DB;
use npds\system\support\facades\Request;


if (!function_exists("Mysql_Connexion"))  {
    include('boot/bootstrap.php');
}

/**
 * [FriendSend description]
 *
 * @param   int   $sid      [$sid description]
 * @param   int   $archive  [$archive description]
 *
 * @return  void
 */
function FriendSend(): void
{
    $res = DB::table('stories')
                ->select('title', 'aid')
                ->where('sid', $sid = Request::query('sid'))
                ->first();

    if (!$res['aid']) {
        header('Location: '. site_url('index.php'));
    }

    include("themes/default/header.php");

    echo '
    <div class="card card-body">
    <h2><i class="fa fa-at fa-lg text-muted"></i>&nbsp;' . translate("Envoi de l'article à un ami") . '</h2>
    <hr />
    <p class="lead">' . translate("Vous allez envoyer cet article") . ' : <strong>' . language::aff_langue($res['title']) . '</strong></p>
    <form id="friendsendstory" action="'. site_url('friend.php') .'" method="post">
        <input type="hidden" name="sid" value="' . $sid . '" />';

    if ($user = users::getUser()) {
        $res_user = DB::table('users')
                ->select('name', 'email')
                ->where('uname', users::cookieUser(1))
                ->first();
    }

    echo '
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="fname" name="fname" maxlength="100" required="required" />
            <label for="fname">' . translate("Nom du destinataire") . '</label>
            <span class="help-block text-end"><span class="muted" id="countcar_fname"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input type="email" class="form-control" id="fmail" name="fmail" maxlength="254" required="required" />
            <label for="fmail">' . translate("Email du destinataire") . '</label>
            <span class="help-block text-end"><span class="muted" id="countcar_fmail"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="yname" name="yname" value="' . ($user ? $res_user['name'] : '') . '" maxlength="100" required="required" />
            <label for="yname">' . translate("Votre nom") . '</label>
            <span class="help-block text-end"><span class="muted" id="countcar_yname"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input type="email" class="form-control" id="ymail" name="ymail" value="' . ($user ? $res_user['email'] : '') . '" maxlength="254" required="required" />
            <label for="ymail">' . translate("Votre Email") . '</label>
            <span class="help-block text-end"><span class="muted" id="countcar_ymail"></span></span>
        </div>';

    echo '' . spam::Q_spambot();

    echo '
        <input type="hidden" name="archive" value="' . Request::query('archive') . '" />
        <input type="hidden" name="op" value="SendStory" />
        <button type="submit" class="btn btn-primary" title="' . translate("Envoyer") . '"><i class="fa fa-lg fa-at"></i>&nbsp;' . translate("Envoyer") . '</button>
    </form>';

    $arg1 = '
    var formulid = ["friendsendstory"];
    inpandfieldlen("yname",100);
    inpandfieldlen("ymail",254);
    inpandfieldlen("fname",100);
    inpandfieldlen("fmail",254);';

    css::adminfoot('fv', '', $arg1, '');
}

/**
 * [SendStory description]
 *
 * @param   int     $sid           [$sid description]
 * @param   string  $yname         [$yname description]
 * @param   string  $ymail         [$ymail description]
 * @param   string  $fname         [$fname description]
 * @param   string  $fmail         [$fmail description]
 * @param   int     $archive       [$archive description]
 * @param   string  $asb_question  [$asb_question description]
 * @param   string  $asb_reponse   [$asb_reponse description]
 *
 * @return  void
 */
function SendStory(): void
{
    $yname = Request::input('yname');
    $ymail = Request::input('ymail');

    if (!users::getUser()) {
        //anti_spambot
        if (!spam::R_spambot(Request::input('asb_question'), Request::input('asb_reponse'), '')) {
            logs::Ecr_Log('security', "Send-Story Anti-Spam : name=" . $yname . " / mail=" . $ymail, '');
            
            url::redirect_url("index.php");
            die();
        }
    }

    $sid = Request::input('sid');

    $res_storie = DB::table('stories')
                    ->select('title', 'time', 'topic')
                    ->where('sid', $sid)
                    ->first();

    $res_topic = DB::table('topics')
                    ->select('topictext')
                    ->where('topicid', $res_storie['topic'])
                    ->first();

    $subject = html_entity_decode(translate("Article intéressant sur"), ENT_COMPAT | ENT_HTML401, 'utf-8') . Config::get('npds.sitename');

    $fname = hack::removeHack(Request::input('fname'));

    $archive = Request::input('archive');

    $message = translate("Bonjour") . " $fname :\n\n" . translate("Votre ami") . " $yname " . translate("a trouvé cet article intéressant et a souhaité vous l'envoyer.") . "\n\n"
         . language::aff_langue($res_storie['title']) . "\n" . translate("Date :") . " ". $res_storie['time'] ."\n" . translate("Sujet : ") . " " . language::aff_langue($res_topic['topictext']) . "\n\n"
         . translate("L'article") . " : <a href=\"". site_url('article.php?sid='. $sid .'&amp;archive='. $archive) ."\">"
         . site_url('article.php?sid='. $sid .'&amp;archive='. $archive) ."</a>\n\n";
    
    $message .= Config::get('signature.message');

    $fmail = hack::removeHack(Request::input('fmail'));
    $subject = hack::removeHack($subject);
    $message = hack::removeHack($message);
    $yname = hack::removeHack($yname);
    $ymail = hack::removeHack($ymail);

    $stop = false;

    if ((!$fmail) || ($fmail == "") || (!preg_match('#^[_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4}$#i', $fmail))) {
        $stop = true;
    }
    
    if ((!$ymail) || ($ymail == "") || (!preg_match('#^[_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4}$#i', $ymail))) {
        $stop = true;
    }
    
    if (!$stop){
        mailler::send_email($fmail, $subject, $message, $ymail, false, 'html', '');
    }else {
        $res_storie['title'] = '';
        $fname = '';
    }

    $title = urlencode(language::aff_langue($res_storie['title']));
    $fname = urlencode($fname);

    Header('Location: '. site_url('friend.php?op=StorySent&title='. $title .'&fname='. $fname));
}

/**
 * [StorySent description]
 *
 * @param   string  $title  [$title description]
 * @param   string  $fname  [$fname description]
 *
 * @return  void
 */
function StorySent(): void
{
    include("themes/default/header.php");

    $title = urldecode(Request::query('title'));
    $fname = urldecode(Request::query('fname'));

    if ($fname == '') {
        echo '<div class="alert alert-danger">' . translate("Erreur : Email invalide") . '</div>';
    } else {
        echo '<div class="alert alert-success">' . translate("L'article") . ' <strong>' . stripslashes($title) . '</strong> ' . translate("a été envoyé à") . '&nbsp;' . $fname . '<br />' . translate("Merci") . '</div>';
    }

    include("themes/default/footer.php");;
}

/**
 * [RecommendSite description]
 *
 * @return  void
 */
function RecommendSite(): void
{
    if ($user = users::getUser()) {
        $res_user = DB::table('users')
                        ->select('name', 'email')
                        ->where('uname', users::cookieUser(1))
                        ->first();
    }

    include("themes/default/header.php");

    echo '
    <div class="card card-body">
    <h2>' . translate("Recommander ce site à un ami") . '</h2>
    <hr />
    <form id="friendrecomsite" action="'. site_url('friend.php') .'" method="post">
        <input type="hidden" name="op" value="SendSite" />
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="yname" name="yname" value="' . ($user ? $res_user['name'] : '') . '" required="required" maxlength="100" />
            <label for="yname">' . translate("Votre nom") . '</label>
            <span class="help-block text-end"><span class="muted" id="countcar_yname"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input type="email" class="form-control" id="ymail" name="ymail" value="' . ($user ? $res_user['email'] : '') . '" required="required" maxlength="100" />
            <label for="ymail">' . translate("Votre Email") . '</label>
        </div>
        <span class="help-block text-end"><span class="muted" id="countcar_ymail"></span></span>
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="fname" name="fname" required="required" maxlength="100" />
            <label for="fname">' . translate("Nom du destinataire") . '</label>
            <span class="help-block text-end"><span class="muted" id="countcar_fname"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input type="email" class="form-control" id="fmail" name="fmail" required="required" maxlength="100" />
            <label for="fmail">' . translate("Email du destinataire") . '</label>
            <span class="help-block text-end"><span class="muted" id="countcar_fmail"></span></span>
        </div>
        ' . spam::Q_spambot() . '
        <div class="mb-3 row">
            <div class="col-sm-8 ms-sm-auto">
                <button type="submit" class="btn btn-primary"><i class="fa fa-lg fa-at"></i>&nbsp;' . translate("Envoyer") . '</button>
            </div>
        </div>
    </form>';

    $arg1 = '
    var formulid = ["friendrecomsite"];
    inpandfieldlen("yname",100);
    inpandfieldlen("ymail",100);
    inpandfieldlen("fname",100);
    inpandfieldlen("fmail",100);';

    css::adminfoot('fv', '', $arg1, '');
}

/**
 * [SendSite description]
 *
 * @param   string  $yname         [$yname description]
 * @param   string  $ymail         [$ymail description]
 * @param   string  $fname         [$fname description]
 * @param   string  $fmail         [$fmail description]
 * @param   string  $asb_question  [$asb_question description]
 * @param   string  $asb_reponse   [$asb_reponse description]
 *
 * @return  void
 */
function SendSite(): void
{
    $yname = Request::input('yname');
    $ymail = Request::input('ymail');

    if (!users::getUser()) {
        //anti_spambot
        if (!spam::R_spambot(Request::input('asb_question'), Request::input('asb_reponse'), '')) {
            logs::Ecr_Log('security', "Friend Anti-Spam : name=" . $yname . " / mail=" . $ymail, '');
            
            url::redirect_url("index.php");
            die();
        }
    }

    $nuke_url = Config::get('npds.nuke_url');
    $sitename = Config::get('npds.sitename');

    $subject = html_entity_decode(translate("Site à découvrir : "), ENT_COMPAT | ENT_HTML401, 'utf-8') . " $sitename";

    $fname = hack::removeHack(Request::input('fname'));

    $message = translate("Bonjour") . " $fname :\n\n" . translate("Votre ami") . " $yname " . translate("a trouvé notre site") . " $sitename " . translate("intéressant et a voulu vous le faire connaître.") . "\n\n$sitename : <a href=\"$nuke_url\">$nuke_url</a>\n\n";
    $message .= Config::get('sinature.message');

    $fmail = hack::removeHack(Request::input('fmail'));
    $subject = hack::removeHack($subject);
    $message = hack::removeHack($message);
    $yname = hack::removeHack($yname);
    $ymail = hack::removeHack($ymail);

    $stop = false;

    if ((!$fmail) || ($fmail == '') || (!preg_match('#^[_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4}$#i', $fmail))) {
        $stop = true;
    }

    if ((!$ymail) || ($ymail == '') || (!preg_match('#^[_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4}$#i', $ymail))) {
        $stop = true;
    }

    if (!$stop) {
        mailler::send_email($fmail, $subject, $message, $ymail, false, 'html', '');
    } else {
        $fname = '';
    }

    Header('Location: '. site_url('friend.php?op=SiteSent&fname='. $fname));
}

/**
 * [SiteSent description]
 *
 * @param   string  $fname  [$fname description]
 *
 * @return  void
 */
function SiteSent(): void
{
    include("themes/default/header.php");

    if ($fname = Request::query('fname')) {
        echo '
            <div class="alert alert-danger lead" role="alert">
                <i class="fa fa-exclamation-triangle fa-lg"></i>&nbsp;
                ' . translate("Erreur : Email invalide") . '
            </div>';
    } else {
        echo '
        <div class="alert alert-success lead" role="alert">
            <i class="fa fa-exclamation-triangle fa-lg"></i>&nbsp;
            ' . translate("Nos références ont été envoyées à ") . ' ' . $fname . ', <br />
            <strong>' . translate("Merci de nous avoir recommandé") . '</strong>
        </div>';
    }

    include("themes/default/footer.php");
}

switch (Request::input('op')) {
    case 'FriendSend':
        FriendSend();
        break;

    case 'SendStory':
        SendStory();
        break;

    case 'StorySent':
        StorySent();
        break;

    case 'SendSite':
        SendSite();
        break;

    case 'SiteSent':
        SiteSent();
        break;
        
    default:
        RecommendSite();
        break;
}
