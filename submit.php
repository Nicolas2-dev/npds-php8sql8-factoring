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

use npds\system\logs\logs;
use npds\system\auth\users;
use npds\system\routing\url;
use npds\system\support\str;
use npds\system\theme\theme;
use npds\system\mail\mailler;
use npds\system\pixels\image;
use npds\system\utility\code;
use npds\system\utility\spam;
use npds\system\config\Config;
use npds\system\security\hack;
use npds\system\support\editeur;
use npds\system\language\language;


if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

include("publication.php");

settype($user, 'string');

if (Config::get('app.mod_admin_news') > 0) {
    if ($admin == '' and $user == '') {
        Header("Location: index.php");
        exit;
    }

    if (Config::get('app.mod_admin_news') == 1) {
        if ($user != '' and $admin == '') {
            global $cookie;
            $result = sql_query("SELECT level FROM " . $NPDS_Prefix . "users_status WHERE uid='$cookie[0]'");
            
            if (sql_num_rows($result) == 1) {
                list($userlevel) = sql_fetch_row($result);
                
                if ($userlevel == 1) {
                    Header("Location: index.php");
                    exit;
                }
            }
        }
    }
}

function defaultDisplay()
{
    global $NPDS_Prefix;

    include("themes/default/header.php");

    global $user;
    if ($user) {
        $userinfo = users::getusrinfo($user);
    }

    echo '
    <h2>' . translate("Proposer un article") . '</h2>
    <hr />
    <form action="submit.php" method="post" name="adminForm">';
    echo '<p class="lead"><strong>' . translate("Votre nom") . '</strong> : ';

    if ($user) {
        echo '<a href="user.php">' . $userinfo['uname'] . '</a> [ <a href="user.php?op=logout">' . translate("Déconnexion") . '</a> ]</p>
        <input type="hidden" name="name" value="' . $userinfo['name'] . '" />';
    } else {
        echo Config::get('app.anonymous') . '[ <a href="user.php">' . translate("Nouveau membre") . '</a> ]</p>
        <input type="hidden" name="name" value="' . Config::get('app.anonymous') . '" />';
    }

    echo '
        <div class="mb-3 row">
            <label class="col-form-label col-sm-3" for="subject">' . translate("Titre") . '</label>
            <div class="col-sm-9">
                <input type="text" id="subject" name="subject" class="form-control" autofocus="autofocus">
                <p class="help-block">' . translate("Faites simple") . '! ' . translate("Mais ne titrez pas -un article-, ou -à lire-,...") . '</p>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-3" for="topic">' . translate("Sujet") . '</label>
            <div class="col-sm-9">
                <select class="form-select" name="topic">';

    $toplist = sql_query("SELECT topicid, topicname, topictext FROM " . $NPDS_Prefix . "topics ORDER BY topictext");

    echo '<option value="">' . translate("Sélectionner un sujet") . '</option>';

    settype($topic, 'string');
    settype($sel, 'string');

    while (list($topicid, $topiname, $topics) = sql_fetch_row($toplist)) {
        if ($topicid == $topic) {
            $sel = 'selected="selected" ';
        }

        echo '<option ' . $sel . ' value="' . $topicid . '">';

        if ($topics != '') { 
            echo language::aff_langue($topics);
        } else {
            echo $topiname;
        }

        echo '</option>';
        $sel = '';
    }

    echo '
                </select>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-12" for="story" >' . translate("Texte d'introduction") . '</label>
            <div class="col-sm-12">
                <textarea class=" form-control tin" rows="25" id="story" name="story"></textarea>
            </div>
        </div>';

    echo editeur::aff_editeur('story', '');

    echo '
        <div class="mb-3 row">
            <label class="col-form-label col-sm-12" for="bodytext">' . translate("Texte complet") . '</label>
            <div class="col-sm-12">
                <textarea class="form-control tin " rows="25" id="bodytext" name="bodytext"></textarea>
            </div>
        </div>';

    echo editeur::aff_editeur('bodytext', '');

    publication('', '', '', '', 0);

    echo '
        <div class="mb-3 row">
            <div class="col-sm-12">
                <span class="help-block">' . translate("Vous devez prévisualiser avant de pouvoir envoyer") . '</span>
                <input class="btn btn-outline-primary" type="submit" name="op" value="' . translate("Prévisualiser") . '" />
            </div>
        </div>
    </form>';

    include("themes/default/footer.php");;
}

function PreviewStory($name, $subject, $story, $bodytext, $topic, $dd_pub, $fd_pub, $dh_pub, $fh_pub, $epur)
{
    global $tipath, $NPDS_Prefix, $topicimage;

    // bug $topictext toujour null pas a la bonne place puisque tu ne recupere jjamais le topictext en provenance de la function defaultDisplay()
    //bug aff_langue strpos sur valeur null
    //$topiclogo = '<span class="badge bg-secondary float-end"><strong>'.language::aff_langue($topictext).'</strong></span>';

    include("themes/default/header.php");;

    $story = stripslashes(image::dataimagetofileurl($story, 'storage/cache/ai'));
    $bodytext = stripslashes(image::dataimagetofileurl($bodytext, 'storage/cache/ac'));
    $subject = stripslashes(str_replace('"', '&quot;', (strip_tags($subject))));

    echo '
    <h2>' . translate("Proposer un article") . '</h2>
    <hr />
    <form action="submit.php" method="post" name="adminForm">
        <p class="lead"><strong>' . translate("Votre nom") . '</strong> : ' . $name . '</p>
        <input type="hidden" name="name" value="' . $name . '" />
        <div class="card card-body mb-4">';

    if ($topic == '') {
        $topicimage = 'all-topics.gif';
        $warning = '<div class="alert alert-danger"><strong>' . translate("Sélectionner un sujet") . '</strong></div>';
    } else {
        $warning = '';
        $result = sql_query("SELECT topictext, topicimage FROM " . $NPDS_Prefix . "topics WHERE topicid='$topic'");
        list($topictext, $topicimage) = sql_fetch_row($result);
    }

    if ($topicimage !== '') {
        if (!$imgtmp = theme::theme_image('topics/' . $topicimage)) {
            $imgtmp = $tipath . $topicimage;
        }

        $timage = $imgtmp;
        if (file_exists($imgtmp)) {
            $topiclogo = '<img class="img-fluid n-sujetsize" src="' . $timage . '" align="right" alt="" />';
        }

        // correction du bug plus haut
        // ajout du else et ajout de la ligne pour le topictext qui et recuperer plus haut
    } else {
        $topiclogo = '<span class="badge bg-secondary float-end"><strong>' . language::aff_langue($topictext) . '</strong></span>';
    }

    $storyX = code::aff_code($story);
    $bodytextX = code::aff_code($bodytext);

    theme::themepreview('<h3>' . $subject . $topiclogo . '</h3>', '<div class="text-muted">' . $storyX . '</div>', $bodytextX, '');

    //    if ($no_img) {
    //       echo '<strong>'.language::aff_langue($topictext).'</strong>';
    //    }

    echo '
    </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-3" for="subject">' . translate("Titre") . '</label>
            <div class="col-sm-9">
                <input type="text" name="subject" class="form-control" value="' . $subject . '" />
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-3" for="topic">' . translate("Sujet") . '</label>
            <div class="col-sm-9">
                <select class="form-select" name="topic">';

    $toplist = sql_query("SELECT topicid, topictext FROM " . $NPDS_Prefix . "topics ORDER BY topictext");

    echo '<option value="">' . translate("Sélectionner un sujet") . '</option>';

    while (list($topicid, $topics) = sql_fetch_row($toplist)) {
        if ($topicid == $topic) {
            $sel = 'selected="selected" ';
        }

        echo '<option ' . $sel . ' value="' . $topicid . '">' . language::aff_langue($topics) . '</option>';
        $sel = '';
    }

    echo '
                </select>
                <span class="help-block text-danger">' . $warning . '</span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-12" for="story">' . translate("Texte d'introduction") . '</label>
            <div class="col-sm-12">
                <span class="help-block">' . translate("Les spécialistes peuvent utiliser du HTML, mais attention aux erreurs") . '</span>
                <textarea class="tin form-control" rows="25" name="story">' . $story . '</textarea>';

    echo editeur::aff_editeur('story', '');

    echo '</div>
        </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12">' . translate("Texte complet") . '</label>
                <div class="col-sm-12">
                <textarea class="tin form-control" rows="25" name="bodytext">' . $bodytext . '</textarea>
                </div>
            </div>';

    echo editeur::aff_editeur('bodytext', '');

    publication($dd_pub, $fd_pub, $dh_pub, $fh_pub, $epur);

    echo spam::Q_spambot();

    echo '
            <div class="mb-3 row">
                <div class="col-sm-12">
                <input class="btn btn-secondary" type="submit" name="op" value="' . translate("Prévisualiser") . '" />&nbsp;
                <input class="btn btn-primary" type="submit" name="op" value="Ok" />
                </div>
            </div>
    </form>';

    include("themes/default/footer.php");;
}

function submitStory($subject, $story, $bodytext, $topic, $date_debval, $date_finval, $epur, $asb_question, $asb_reponse)
{
    global $user, $EditedMessage, $notify, $NPDS_Prefix;

    if ($user != '') {
        global $cookie;
        $uid = $cookie[0];
        $name = $cookie[1];
    } else {
        $uid = -1;
        $name = Config::get('app.anonymous');

        //anti_spambot
        if (!spam::R_spambot($asb_question, $asb_reponse, '')) {
            logs::Ecr_Log('security', "Submit Anti-Spam : name=" . $name . " / uid=" . $uid, '');
            url::redirect_url("index.php");
            die();
        }
    }

    $story = image::dataimagetofileurl($story, 'storage/cache/ai');
    $bodytext = image::dataimagetofileurl($bodytext, 'storage/cache/ac');
    $subject = hack::removeHack(stripslashes(str::FixQuotes(str_replace("\"", "&quot;", (strip_tags($subject))))));
    $story = hack::removeHack(stripslashes(str::FixQuotes($story)));
    $bodytext = hack::removeHack(stripslashes(str::FixQuotes($bodytext)));

    $result = sql_query("INSERT INTO " . $NPDS_Prefix . "queue VALUES (NULL, '$uid', '$name', '$subject', '$story', '$bodytext', now(), '$topic','$date_debval','$date_finval','$epur')");
    if (sql_last_id()) {
        if ($notify) {
            global $notify_email, $notify_subject, $notify_message, $notify_from;
            mailler::send_email($notify_email, $notify_subject, $notify_message, $notify_from, false, "html", '');
        }

        include("themes/default/header.php");

        echo '
        <h2>' . translate("Proposer un article") . '</h2>
        <hr />
        <div class="alert alert-success lead">' . translate("Merci pour votre contribution.") . '</div>';

        include("themes/default/footer.php");
    } else {
        include("themes/default/header.php");
        echo sql_error();
        include("themes/default/footer.php");
    }
}

settype($op, 'string');

switch ($op) {
    case 'Prévisualiser':
    case translate("Prévisualiser"):
        if ($user) {
            $userinfo = users::getusrinfo($user);
            $name = $userinfo['uname'];
        } else {
            $name = Config::get('app.anonymous');
        }

        PreviewStory($name, $subject, $story, $bodytext, $topic, $dd_pub, $fd_pub, $dh_pub, $fh_pub, $epur);

        break;

    case 'Ok':
        settype($date_debval, 'string');
        if (!$date_debval) {
            $date_debval = $dd_pub . ' ' . $dh_pub . ':01';
        }

        settype($date_finval, 'string');

        if (!$date_finval) {
            $date_finval = $fd_pub . ' ' . $fh_pub . ':01';
        }

        if ($date_finval < $date_debval) {
            $date_finval = $date_debval;
        }

        SubmitStory($subject, $story, $bodytext, $topic, $date_debval, $date_finval, $epur, $asb_question, $asb_reponse);
        break;
        
    default:
        defaultDisplay();
        break;
}
