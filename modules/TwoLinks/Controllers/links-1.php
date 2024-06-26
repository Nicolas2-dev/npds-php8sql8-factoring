<?php

/************************************************************************/                                                                                                                                  /* DUNE by NPDS                                                         */
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* This version name NPDS Copyright (c) 2001-2021 by Philippe Brunier   */
/*                                                                      */
/* New Links.php Module with SFROM extentions                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

use npds\support\logs\logs;
use npds\support\assets\css;
use npds\support\auth\users;
use npds\support\routing\url;
use npds\support\pixels\image;
use npds\support\utility\spam;
use npds\system\config\Config;
use npds\support\security\hack;
use npds\support\editeur;
use npds\support\language\language;

if (!stristr($_SERVER['PHP_SELF'], "modules.php")) die();
function error_head($class)
{
    global $ModPath, $ModStart;
    include("themes/default/header.php");
    $mainlink = 'ad_l';
    menu($mainlink);
    SearchForm();
    echo '
    <div class="alert ' . $class . '" role="alert" align="center">';
}
function error_foot()
{
    echo '
    </div>';
    include("themes/default/footer.php");
}

function AddLink()
{
    global $ModPath, $ModStart, $links_DB, $NPDS_Prefix, $links_anonaddlinklock, $op;
    include("themes/default/header.php");
    global $user, $ad_l;
    mainheader();
    if (users::autorisation($links_anonaddlinklock)) {
        echo '
    <div class="card card-body mb-3">
        <h3 class="mb-3">Proposer un lien</h3>
        <div class="card card-outline-secondary mb-3">
            <div class="card-body">
                <span class="help-block">' . __d('two_links', 'Proposer un seul lien.') . '<br />' . __d('two_links', 'Tous les liens proposés sont vérifiés avant insertion.') . '<br />' . __d('two_links', 'Merci de ne pas abuser, le nom d\'utilisateur et l\'adresse IP sont enregistrés.') . '</span>
            </div>
        </div>
        <form id="addlink" method="post" action="modules.php" name="adminForm">
            <input type="hidden" name="ModPath" value="' . $ModPath . '" />
            <input type="hidden" name="ModStart" value="' . $ModStart . '" />
            <div class="mb-3 row">
                <label class="col-form-label col-sm-3" for="title">' . __d('two_links', 'Titre') . '</label>
                <div class="col-sm-9">
                <input class="form-control" type="text" id="title" name="title" maxlength="100" required="required" />
                <span class="help-block text-end" id="countcar_title"></span>
            </div>
            </div>';
        global $links_url;
        if (($links_url) or ($links_url == -1))
            echo '
            <div class="mb-3 row">
                <label class="col-form-label col-sm-3" for="url">URL</label>
                <div class="col-sm-9">
                <input class="form-control" type="url" id="url" name="url" maxlength="320" value="http://" required="required" />
                <span class="help-block text-end" id="countcar_url"></span>
            </div>
            </div>';

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $result = sql_query("SELECT cid, title FROM " . $links_DB . "links_categories ORDER BY title");
        echo '
            <div class="mb-3 row">
                <label class="col-form-label col-sm-3" for="cat">' . __d('two_links', 'Catégorie') . '</label>
                <div class="col-sm-9">
                <select class="form-select" id="cat" name="cat">';
        while (list($cid, $title) = sql_fetch_row($result)) {
            echo '
                    <option value="' . $cid . '">' . language::aff_langue($title) . '</option>';
            $result2 = sql_query("select sid, title from " . $links_DB . "links_subcategories WHERE cid='$cid' ORDER BY title");
            while (list($sid, $stitle) = sql_fetch_row($result2)) {
                echo '
                    <option value="' . $cid . '-' . $sid . '">' . language::aff_langue($title . '/' . $stitle) . '</option>';
            }
        }
        echo '
                </select>
            </div>
            </div>';
        global $links_topic;
        if ($links_topic) {
            echo '
            <div class="mb-3 row">
                <label class="col-form-label col-sm-3" for="topicL">' . __d('two_links', 'Sujets') . '</label>
                <div class="col-sm-9">
                <select class="form-select" id="topicL" name="topicL">';

            // = DB::table('')->select()->where('', )->orderBy('')->get();

            $toplist = sql_query("SELECT topicid, topictext FROM " . $NPDS_Prefix . "topics ORDER BY topictext");
            echo '
                    <option value="">' . __d('two_links', 'Tous les sujets') . '</option>';
            while (list($topicid, $topics) = sql_fetch_row($toplist)) {
                echo '
                    <option value="' . $topicid . '">' . $topics . '</option>';
            }
            echo '
                </select>
                </div>
            </div>';
        }
        echo '
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="xtext">' . __d('two_links', 'Description') . '</label>
                <div class="col-sm-12">
                <textarea class="tin form-control" name="xtext" id="xtext" rows="10"></textarea>
                </div>
            </div>';
        echo editeur::aff_editeur('xtext', '');
        global $cookie;
        $nom = isset($cookie) ? $cookie[1] : '';
        echo '
            <div class="mb-3 row">
                <label class="col-form-label col-sm-3" for="name">' . __d('two_links', 'Votre nom') . '</label>
                <div class="col-sm-9">
                <input type="text" class="form-control" id="name" name="name" maxlength="60" value="' . $nom . '" required="required" />
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-3" for="email">' . __d('two_links', 'Votre Email') . '</label>
                <div class="col-sm-9">
                <input type="email" class="form-control" id="email" name="email" maxlength="254" required="required" />
                <span class="help-block text-end" id="countcar_email"></span>
                </div>
            </div>';
        echo spam::Q_spambot();
        echo '
            <div class="mb-3 row">
                <input type="hidden" name="op" value="Add" />
                <div class="col-sm-9 ms-sm-auto">
                <input type="submit" class="btn btn-primary" value="' . __d('two_links', 'Ajouter une url') . '" />
                </div>
            </div>
        </form>
        </div>
    <div>
    </div>';
        $arg1 = '
        var formulid = ["addlink"];
        inpandfieldlen("title",100);
        inpandfieldlen("url",320);
        inpandfieldlen("email",254);
        ';
        SearchForm();
        css::adminfoot('fv', '', $arg1, '1');
        include("themes/default/footer.php");
    } else {
        echo '
            <div class="alert alert-warning">' . __d('two_links', 'Vous n\'êtes pas (encore) enregistré ou vous n\'êtes pas (encore) connecté.') . '<br />
            ' . __d('two_links', 'Si vous étiez enregistré, vous pourriez proposer des liens.') . '</div>';
        SearchForm();
        include("themes/default/footer.php");
    }
}

function Add($title, $url, $name, $cat, $description, $email, $topicL, $asb_question, $asb_reponse)
{
    global $ModPath, $ModStart, $links_DB, $user, $admin;
    if (!$user and !$admin) {
        //anti_spambot
        if (!spam::R_spambot($asb_question, $asb_reponse, '')) {
            logs::Ecr_Log('security', 'Links Anti-Spam : url=' . $url, '');
            url::redirect_url("index.php");
            die();
        }
    }

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT lid FROM " . $links_DB . "links_newlink");
    $numrows = sql_num_rows($result);
    if ($numrows >= Config::get('npds.troll_limit')) {
        error_head("alert-danger");
        echo __d('two_links', 'Erreur : cette url est déjà présente dans la base de données') . '<br />';
        error_foot();
        exit();
    }
    global $user;
    if (isset($user)) {
        global $cookie;
        $submitter = $cookie[1];
    } else
        $submitter = Config::get('npds.anonymous');
    if ($title == '') {
        error_head('alert-danger');
        echo __d('two_links', 'Erreur : vous devez saisir un titre pour votre lien') . '<br />';
        error_foot();
        exit();
    }
    if ($email == '') {
        error_head('alert-danger');
        echo __d('two_links', 'Erreur : Email invalide') . '<br />';
        error_foot();
        exit();
    }
    global $links_url;
    if (($url == '') and ($links_url == 1)) {
        error_head('alert-danger');
        echo __d('two_links', 'Erreur : vous devez saisir une url pour votre lien') . '<br />';
        error_foot();
        exit();
    }
    if ($description == '') {
        error_head('alert-danger');
        echo __d('two_links', 'Erreur : vous devez saisir une description pour votre lien') . '<br />';
        error_foot();
        exit();
    }
    $cat = explode('-', $cat);
    if (!array_key_exists(1, $cat))
        $cat[1] = 0;

    $title = hack::removeHack(stripslashes(FixQuotes($title)));
    $url = hack::removeHack(stripslashes(FixQuotes($url)));
    $description = image::dataimagetofileurl($description, 'modules/upload/upload/lindes');
    $description = hack::removeHack(stripslashes(FixQuotes($description)));
    $name = hack::removeHack(stripslashes(FixQuotes($name)));
    $email = hack::removeHack(stripslashes(FixQuotes($email)));

    //DB::table('')->insert(array(
    //    ''       => ,
    //));

    sql_query("INSERT INTO " . $links_DB . "links_newlink VALUES (NULL, '$cat[0]', '$cat[1]', '$title', '$url', '$description', '$name', '$email', '$submitter', '$topicL')");
    error_head('alert-success');
    echo __d('two_links', 'Nous avons bien reçu votre demande de lien, merci') . '<br />';
    echo __d('two_links', 'Vous recevrez un mèl quand elle sera approuvée.') . '<br />';
    error_foot();
}

function links_search($query, $topicL, $min, $max, $offset)
{
    global $ModPath, $ModStart, $links_DB;
    include("themes/default/header.php");
    mainheader();
    $filen = "modules/$ModPath/links.ban_02.php";
    if (file_exists($filen)) {
        include($filen);
    }
    $query = hack::removeHack(stripslashes(htmlspecialchars($query, ENT_QUOTES, 'utf-8'))); // Romano et NoSP

    if ($topicL != '')

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $result = sql_query("SELECT lid, url, title, description, date, hits, topicid_card, cid, sid FROM " . $links_DB . "links_links WHERE topicid_card='$topicL' AND (title LIKE '%$query%' OR description LIKE '%$query%') ORDER BY lid ASC LIMIT $min,$offset");
    else

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $result = sql_query("SELECT lid, url, title, description, date, hits, topicid_card, cid, sid FROM " . $links_DB . "links_links WHERE title LIKE '%$query%' OR description LIKE '%$query%' ORDER BY lid ASC LIMIT $min,$offset");
    if ($result) {
        $link_fiche_detail = '';
        include_once("modules/$ModPath/links-view.php");
        $prev = $min - $offset;
        if ($prev >= 0) {
            echo "$min <a href=\"modules.php?ModPath=$ModPath&amp;ModStart=$ModStart&amp;op=search&min=$prev&amp;query=$query&amp;topicL=$topicL\" class=\"noir\">";
            echo __d('two_links', 'réponses précédentes') . "</a>&nbsp;&nbsp;";
        }
        if ($x >= ($offset - 1)) {
            echo "<a href=\"modules.php?ModPath=$ModPath&amp;ModStart=$ModStart&amp;op=search&amp;min=$max&amp;query=$query&amp;topicL=$topicL\" class=\"noir\">";
            echo __d('two_links', 'réponses suivantes') . "</a>";
        }
    }
    include("themes/default/footer.php");
}
