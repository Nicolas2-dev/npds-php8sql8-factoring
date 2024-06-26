<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* This version name NPDS Copyright (c) 2001-2022 by Philippe Brunier   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\support\logs\logs;
use npds\support\assets\css;
use npds\support\routing\url;
use npds\support\str;
use npds\support\mail\mailler;
use npds\support\utility\spam;
use npds\support\security\hack;
use npds\support\language\language;
use npds\system\support\facades\DB;


if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

function display_score($score)
{
    $image = '<i class="fa fa-star"></i>';
    $halfimage = '<i class="fas fa-star-half-alt"></i>';
    $full = '<i class="fa fa-star"></i>';

    if ($score == 10) {
        for ($i = 0; $i < 5; $i++) {
            echo $full;
        }
    } else if ($score % 2) {
        $score -= 1;
        $score /= 2;
        for ($i = 0; $i < $score; $i++) {
            echo $image;
        }

        echo $halfimage;
    } else {
        $score /= 2;
        for ($i = 0; $i < $score; $i++) {
            echo $image;
        }
    }
}

function write_review()
{
    global $admin, $user, $cookie, $short_review, $NPDS_Prefix;

    include("themes/default/header.php");;

    echo '
    <h2>' . __d('two_reviews', 'Ecrire une critique') . '</h2>
    <hr />
    <form id="writereview" method="post" action="'. site_url('reviews.php') .'">
        <div class="form-floating mb-3">
            <textarea class="form-control" id="title_rev" name="title" required="required" maxlength="150" style="height:70px"></textarea>
            <label for="title_rev">' . __d('two_reviews', 'Objet') . '</label>
            <span class="help-block text-end" id="countcar_title_rev"></span>
        </div>
        <div class="form-floating mb-3">
            <textarea class="form-control" id="text_rev" name="text" required="required" style="height:120px"></textarea>
            <label for="text_rev">' . __d('two_reviews', 'Texte') . '</label>
            <span class="help-block">' . __d('two_reviews', 'Attention à votre expression écrite. Vous pouvez utiliser du code html si vous savez le faire') . '</span>
        </div>';

    if ($user) {

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $result = sql_query("SELECT uname, email FROM " . $NPDS_Prefix . "users WHERE uname='$cookie[1]'");
        list($uname, $email) = sql_fetch_row($result);

        echo '
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="reviewer_rev" name="reviewer" value="' . $uname . '" maxlength="25" required="required" />
            <label for="reviewer_rev">' . __d('two_reviews', 'Votre nom') . '</label>
        </div>
        <div class="form-floating mb-3">
            <input type="email" class="form-control" id="email_rev" name="email" value="' . $email . '" maxlength="254" required="required" />
            <label for="email_rev">' . __d('two_reviews', 'Votre adresse Email') . '</label>
            <span class="help-block text-end" id="countcar_email_rev"></span>
        </div>';
    } else
        echo '
        <div class="form-floating mb-3">
            <input class="form-control" type="text" id="reviewer_rev" name="reviewer" required="required" />
            <label for="reviewer_rev">' . __d('two_reviews', 'Votre nom') . '</label>
        </div>
        <div class="form-floating mb-3">
            <input type="email" class="form-control" id="email_rev" name="email" maxlength="254" required="required" />
            <label for="email_rev">' . __d('two_reviews', 'Votre adresse Email') . '</label>
            <span class="help-block text-end" id="countcar_email_rev"></span>
        </div>';

    echo '
        <div class="form-floating mb-3">
            <select class="form-select" id="score_rev" name="score">
                <option value="10">10</option>
                <option value="9">9</option>
                <option value="8">8</option>
                <option value="7">7</option>
                <option value="6">6</option>
                <option value="5">5</option>
                <option value="4">4</option>
                <option value="3">3</option>
                <option value="2">2</option>
                <option value="1">1</option>
            </select>
            <label for="score_rev">' . __d('two_reviews', 'Evaluation') . '</label>
            <span class="help-block">' . __d('two_reviews', 'Choisir entre 1 et 10 (1=nul 10=excellent)') . '</span>
        </div>';

    if (!$short_review) {
        echo '
        <div class="form-floating mb-3">
            <input type="url" class="form-control" id="url_rev" name="url" maxlength="320" />
            <label for="url_rev">' . __d('two_reviews', 'Lien relatif') . '</label>
            <span class="help-block">' . __d('two_reviews', 'Site web officiel. Veillez à ce que votre url commence bien par') . ' http(s)://<span class="float-end" id="countcar_url_rev"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="url_title_rev" name="url_title" maxlength="50" />
            <label for="url_title_rev">' . __d('two_reviews', 'Titre du lien') . '</label>
            <span class="help-block">' . __d('two_reviews', 'Obligatoire seulement si vous soumettez un lien relatif') . '<span class="float-end" id="countcar_url_title_rev"></span></span>
        </div>';

        if ($admin) {
            echo '
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="cover_rev" name="cover" maxlength="50" />
            <label for="cover_rev">' . __d('two_reviews', 'Nom de fichier de l\'image') . '</label>
            <span class="help-block">' . __d('two_reviews', 'Nom de l\'image principale non obligatoire, la mettre dans images/reviews/') . '<span class="float-end" id="countcar_cover_rev"></span></span>
        </div>';
        }
    }

    echo '
        <input type="hidden" name="op" value="preview_review" />
        <button type="submit" class="btn btn-primary my-3 me-2" >' . __d('two_reviews', 'Prévisualiser') . '</button>
        <button onclick="history.go(-1)" class="btn btn-secondary my-3">' . __d('two_reviews', 'Retour en arrière') . '</button>
        <p class="help-block">' . __d('two_reviews', 'Assurez-vous de l\'exactitude de votre information avant de la communiquer. N\'écrivez pas en majuscules, votre texte serait automatiquement rejeté') . '</p>
    </form>';

    $arg1 = '
        var formulid = ["writereview"];
        inpandfieldlen("title_rev",150);
        inpandfieldlen("email_rev",254);
        inpandfieldlen("url_rev",320);
        inpandfieldlen("url_title_rev",50);
        inpandfieldlen("cover_rev",100);';

    css::adminfoot('fv', '', $arg1, 'foo');
}

function preview_review($title, $text, $reviewer, $email, $score, $cover, $url, $url_title, $hits, ?int $id)
{
    global $admin;

    $title = stripslashes(strip_tags($title));
    $text = stripslashes(hack::removeHack(str::conv2br($text)));
    $reviewer = stripslashes(strip_tags($reviewer));
    $url_title = stripslashes(strip_tags($url_title));
    $error = '';

    include("themes/default/header.php");;

    echo '<h2 class="mb-4">';
    echo $id != 0 ? __d('two_reviews', 'Modification d\'une critique') : __d('two_reviews', 'Ecrire une critique');
    echo '
    </h2>
    <form id="prevreview" method="post" action="'. site_url('reviews.php') .'">';

    if ($title == '') {
        $error = 1;
        echo '<div class="alert alert-danger">' . __d('two_reviews', 'Titre non valide... Il ne peut pas être vide') . '</div>';
    }

    if ($text == '') {
        $error = 1;
        echo '<div class="alert alert-danger">' . __d('two_reviews', 'Texte de critique non valide... Il ne peut pas être vide') . '</div>';
    }

    if (($score < 1) || ($score > 10)) {
        $error = 1;
        echo '<div class="alert alert-danger">' . __d('two_reviews', 'Note non valide... Elle doit se situer entre 1 et 10') . '</div>';
    }

    if (($hits < 0) && ($id != 0)) {
        $error = 1;
        echo '<div class="alert alert-danger">' . __d('two_reviews', 'Le nombre de hits doit être un entier positif') . '</div>';
    }

    if ($reviewer == '' || $email == '') {
        $error = 1;
        echo '<div class="alert alert-danger">' . __d('two_reviews', 'Vous devez entrer votre nom et votre adresse Email') . '</div>';
    } else if ($reviewer != '' && $email != '') {
        if (!preg_match('#^[_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4}$#i', $email)) {
            $error = 1;
            echo '<div class="alert alert-danger">' . __d('two_reviews', 'Email non valide (ex.: prenom.nom@hotmail.com)') . '</div>';
        }

        if (mailler::checkdnsmail($email) === false) {
            $error = 1;
            echo '<div class="alert alert-danger">' . __d('two_reviews', 'Erreur : DNS ou serveur de mail incorrect') . '</div>';
        }
    }

    if ((($url_title != '' && $url == '') || ($url_title == "" && $url != "")) and (!$short_reviews)) {
        $error = 1;
        echo '<div class="alert alert-danger">' . __d('two_reviews', 'Vous devez entrer un titre de lien et une adresse relative, ou laisser les deux zones vides') . '</div>';
    } else if (($url != "") && (!preg_match('#^http(s)?://#i', $url))) {
        $error = 1;
        echo '<div class="alert alert-danger">' . __d('two_reviews', 'Site web officiel. Veillez à ce que votre url commence bien par') . ' http(s)://</div>';
    }

    if ($error == 1)
        echo '<button class="btn btn-secondary" type="button" onclick="history.go(-1)"><i class="fa fa-lg fa-undo"></i></button>';
    else {
        global $gmt;
        $fdate = date(str_replace('%', '', __d('two_reviews', 'linksdatestring')), time() + ((int)$gmt * 3600));

        echo __d('two_reviews', 'Critique');

        echo '
        <br />' . __d('two_reviews', 'Ajouté :') . ' ' . $fdate . '
        <hr />
        <h3>' . stripslashes($title) . '</h3>';

        if ($cover != '') {
            echo '<img class="img-fluid" src="assets/images/reviews/' . $cover . '" alt="img_" />';
        }

        echo $text;
        echo '
        <hr />
        <strong>' . __d('two_reviews', 'Le critique') . ' :</strong> <a href="mailto:' . $email . '" target="_blank">' . $reviewer . '</a><br />
        <strong>' . __d('two_reviews', 'Note') . '</strong>
        <span class="text-success">';

        display_score($score);
        echo '</span>';

        if ($url != '')
            echo '<br /><strong>' . __d('two_reviews', 'Lien relatif') . ' :</strong> <a href="' . $url . '" target="_blank">' . $url_title . '</a>';

        if ($id != 0) {
            echo '<br /><strong>' . __d('two_reviews', 'ID de la critique') . ' :</strong> ' . $id . '<br />
            <strong>' . __d('two_reviews', 'Hits') . ' :</strong> ' . $hits . '<br />';
        }

        $text = urlencode($text);
        echo '
                <input type="hidden" name="id" value="' . $id . '" />
                <input type="hidden" name="hits" value="' . $hits . '" />
                <input type="hidden" name="date" value="' . $fdate . '" />
                <input type="hidden" name="title" value="' . $title . '" />
                <input type="hidden" name="text" value="' . $text . '" />
                <input type="hidden" name="reviewer" value="' . $reviewer . '" />
                <input type="hidden" name="email" value="' . $email . '" />
                <input type="hidden" name="score" value="' . $score . '" />
                <input type="hidden" name="url" value="' . $url . '" />
                <input type="hidden" name="url_title" value="' . $url_title . '" />
                <input type="hidden" name="cover" value="' . $cover . '" />
                <input type="hidden" name="op" value="add_reviews" />
                <p class="my-3">' . __d('two_reviews', 'Cela semble-t-il correct ?') . '</p>';

        if (!$admin) {
            echo spam::Q_spambot();
        }

        // nimporte quoi ça !!!!
        $consent = __d('two_reviews', 'Pour conna&icirc;tre et exercer vos droits notamment de retrait de votre consentement &agrave; l\'utilisation des donn&eacute;es collect&eacute;es veuillez consulter notre <a href=" {0} ">politique de confidentialit&eacute;</a>.', site_url('static.php?op=politiqueconf.html&amp;npds=1&amp;metalang=1'));
        $accept = __d('two_reviews', 'En soumettant ce formulaire j\'accepte que les informations saisies soient exploit&#xE9;es dans le cadre de l\'utilisation et du fonctionnement de ce site.');
        
        echo '
        <div class="mb-3 row">
            <div class="col-sm-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="consent" name="consent" value="1" required="required"/>
                    <label class="form-check-label" for="consent">'
            . language::aff_langue($accept) . '
                        <span class="text-danger"> *</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="mb-3 row">
            <div class="col-sm-12">
                <input class="btn btn-primary" type="submit" value="' . __d('two_reviews', 'Oui') . '" />&nbsp;
                <input class="btn btn-secondary" type="button" onclick="history.go(-1)" value="' . __d('two_reviews', 'Non') . '" />
            </div>
        </div>
        <div class="mb-3 row">
            <div class="col small" >' . language::aff_langue($consent) . '
            </div>
        </div>';

        if ($id != 0) {
            $word = __d('two_reviews', 'modifié');
        } else {
            $word = __d('two_reviews', 'ajouté');
        }

        if ($admin) {
            echo '<div class="alert alert-success"><strong>' . __d('two_reviews', 'Note :') . '</strong> ' . __d('two_reviews', 'Actuellement connecté en administrateur... Cette critique sera') . ' ' . $word . ' ' . __d('two_reviews', 'immédiatement') . '.</div>';
        }
    }

    echo '
    </form>';

    $arg1 = '
        var formulid = ["prevreview"];';

    css::adminfoot('fv', '', $arg1, 'foo');
}

function reversedate($myrow)
{
    if (substr($myrow, 2, 1) == '-') {
        $day = substr($myrow, 0, 2);
        $month = substr($myrow, 3, 2);
        $year = substr($myrow, 6, 4);
    } else {
        $day = substr($myrow, 8, 2);
        $month = substr($myrow, 5, 2);
        $year = substr($myrow, 0, 4);
    }

    return ($year . '-' . $month . '-' . $day);
}

function send_review($date, $title, $text, $reviewer, $email, $score, $cover, $url, $url_title, $hits, $id, $asb_question, $asb_reponse)
{
    global $admin, $user, $NPDS_Prefix;

    include("themes/default/header.php");;

    $date = reversedate($date);
    $title = stripslashes(str::FixQuotes(strip_tags($title)));
    $text = stripslashes(str::Fixquotes(urldecode(hack::removeHack($text))));

    if (!$user and !$admin) {
        //anti_spambot
        if (!spam::R_spambot($asb_question, $asb_reponse, $text)) {
            logs::Ecr_Log('security', 'Review Anti-Spam : title=' . $title, '');
            url::redirect_url("index.php");
            die();
        }
    }

    if ($id != 0) {
        echo '<h2>' . __d('two_reviews', 'Modification d\'une critique') . '</h2>';
    } else {
        echo '<h2>' . __d('two_reviews', 'Ecrire une critique') . '</h2>';
    }

    echo '
    <hr />
    <div class="alert alert-success">';

    if ($id != 0) {
        echo __d('two_reviews', 'Merci d\'avoir modifié cette critique') . '.';
    } else {
        echo __d('two_reviews', 'Merci d\'avoir posté cette critique') . ', ' . $reviewer;
        }

    echo '<br />';

    //DB::table('')->insert(array(
    //    ''       => ,
    //));

    if (($admin) && ($id == 0)) {
        sql_query("INSERT INTO " . $NPDS_Prefix . "reviews VALUES (NULL, '$date', '$title', '$text', '$reviewer', '$email', '$score', '$cover', '$url', '$url_title', '1')");
        echo __d('two_reviews', 'Dès maintenant disponible dans la base de données des critiques.');

    } else if (($admin) && ($id != 0)) {

        //DB::table('')->where('', )->update(array(
        //    ''       => ,
        //));

        sql_query("UPDATE " . $NPDS_Prefix . "reviews SET date='$date', title='$title', text='$text', reviewer='$reviewer', email='$email', score='$score', cover='$cover', url='$url', url_title='$url_title', hits='$hits' WHERE id='$id'");
        echo __d('two_reviews', 'Dès maintenant disponible dans la base de données des critiques.');

    } else {

        //DB::table('')->insert(array(
        //    ''       => ,
        //));

        sql_query("INSERT INTO " . $NPDS_Prefix . "reviews_add VALUES (NULL, '$date', '$title', '$text', '$reviewer', '$email', '$score', '$url', '$url_title')");
        echo __d('two_reviews', 'Nous allons vérifier votre contribution. Elle devrait bientôt être disponible !');
    }

    echo '
    </div>
    <a class="btn btn-secondary" href="'. site_url('reviews.php') .'" title="' . __d('two_reviews', 'Retour à l\'index des critiques') . '"><i class="fa fa-lg fa-undo"></i>  ' . __d('two_reviews', 'Retour à l\'index des critiques') . '</a>';
    
    include("themes/default/footer.php");;
}

function reviews($field, $order)
{
    global $NPDS_Prefix;

    include("themes/default/header.php");

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $r_result = sql_query("SELECT title, description FROM " . $NPDS_Prefix . "reviews_main");
    list($r_title, $r_description) = sql_fetch_row($r_result);

    if ($order != "ASC" and $order != "DESC") $order = "ASC";

    switch ($field) {
        case 'reviewer':

            // = DB::table('')->select()->where('', )->orderBy('')->get();

            $result = sql_query("SELECT id, title, hits, reviewer, score, date FROM " . $NPDS_Prefix . "reviews ORDER BY reviewer $order");
            break;

        case 'score':

            // = DB::table('')->select()->where('', )->orderBy('')->get();

            $result = sql_query("SELECT id, title, hits, reviewer, score, date FROM " . $NPDS_Prefix . "reviews ORDER BY score $order");
            break;

        case 'hits':

            // = DB::table('')->select()->where('', )->orderBy('')->get();

            $result = sql_query("SELECT id, title, hits, reviewer, score, date FROM " . $NPDS_Prefix . "reviews ORDER BY hits $order");
            break;

        case 'date':

            // = DB::table('')->select()->where('', )->orderBy('')->get();

            $result = sql_query("SELECT id, title, hits, reviewer, score, date FROM " . $NPDS_Prefix . "reviews ORDER BY id $order");
            break;

        default:

            // = DB::table('')->select()->where('', )->orderBy('')->get();

            $result = sql_query("SELECT id, title, hits, reviewer, score, date FROM " . $NPDS_Prefix . "reviews ORDER BY title $order");
            break;
    }

    $numresults = sql_num_rows($result);

    echo '
    <h2>' . __d('two_reviews', 'Critiques') . '<span class="badge bg-secondary float-end" title="' . $numresults . ' ' . __d('two_reviews', 'Critique(s) trouvée(s).') . '" data-bs-toggle="tooltip">' . $numresults . '</span></h2>
    <hr />
    <h3>' . language::aff_langue($r_title) . '</h3>
    <p class="lead">' . language::aff_langue($r_description) . '</p>
    <h4><a href="'. site_url('reviews.php?op=write_review') .'"><i class="fa fa-edit"></i></a>&nbsp;' . __d('two_reviews', 'Ecrire une critique') . '</h4><br />
    ';

    echo '
    <div class="dropdown">
        <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-sort-amount-down me-2"></i>' . __d('two_reviews', 'Critiques') . '
        </a>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
            <a class="dropdown-item" href="'. site_url('reviews.php?op=sort&amp;field=date&amp;order=ASC') .'"><i class="fa fa-sort-amount-down me-2"></i>' . __d('two_reviews', 'Date') . '</a>
            <a class="dropdown-item" href="'. site_url('reviews.php?op=sort&amp;field=date&amp;order=DESC') .'"><i class="fa fa-sort-amount-up me-2"></i>' . __d('two_reviews', 'Date') . '</a>
            <a class="dropdown-item" href="'. site_url('reviews.php?op=sort&amp;field=title&amp;order=ASC') .'"><i class="fa fa-sort-amount-down me-2"></i>' . __d('two_reviews', 'Titre') . '</a>
            <a class="dropdown-item" href="'. site_url('reviews.php?op=sort&amp;field=title&amp;order=DESC') .'"><i class="fa fa-sort-amount-up me-2"></i>' . __d('two_reviews', 'Titre') . '</a>
            <a class="dropdown-item" href="'. site_url('reviews.php?op=sort&amp;field=reviewer&amp;order=ASC') .'"><i class="fa fa-sort-amount-down me-2"></i>' . __d('two_reviews', 'Posté par') . '</a>
            <a class="dropdown-item" href="'. site_url('reviews.php?op=sort&amp;field=reviewer&amp;order=DESC') .'"><i class="fa fa-sort-amount-up me-2"></i>' . __d('two_reviews', 'Posté par') . '</a>
            <a class="dropdown-item" href="'. site_url('reviews.php?op=sort&amp;field=score&amp;order=ASC') .'"><i class="fa fa-sort-amount-down me-2"></i>' . __d('two_reviews', 'Score') .'</a>
            <a class="dropdown-item" href="'. site_url('reviews.php?op=sort&amp;field=score&amp;order=DESC') .'"><i class="fa fa-sort-amount-up me-2"></i>' . __d('two_reviews', 'Score') .'</a>
            <a class="dropdown-item" href="'. site_url('reviews.php?op=sort&amp;field=hits&amp;order=ASC') .'"><i class="fa fa-sort-amount-down"></i>' . __d('two_reviews', 'Hits') .'</a>
            <a class="dropdown-item" href="'. site_url('reviews.php?op=sort&amp;field=hits&amp;order=DESC') .'"><i class="fa fa-sort-amount-up"></i>' . __d('two_reviews', 'Hits') .'</a>
        </div>
    </div>';

    if ($numresults > 0) {
        echo '
        <table data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-mobile-responsive="true" data-buttons-class="outline-secondary" data-icons-prefix="fa" data-icons="icons">
            <thead>
                <tr>
                <th data-align="center">
                    <a href="'. site_url('reviews.php?op=sort&amp;field=date&amp;order=ASC') .'"><i class="fa fa-sort-amount-down"></i></a> ' . __d('two_reviews', 'Date') . ' <a href="'. site_url('reviews.php?op=sort&amp;field=date&amp;order=DESC') .'"><i class="fa fa-sort-amount-up"></i></a>
                </th>
                <th data-align="left" data-halign="center" data-sortable="true" data-sorter="htmlSorter">
                    <a href="'. site_url('reviews.php?op=sort&amp;field=title&amp;order=ASC') .'"><i class="fa fa-sort-amount-down"></i></a> ' . __d('two_reviews', 'Titre') . ' <a href="'. site_url('reviews.php?op=sort&amp;field=title&amp;order=DESC') .'"><i class="fa fa-sort-amount-up"></i></a>
                </th>
                <th data-align="center" data-sortable="true">
                    <a href="'. site_url('reviews.php?op=sort&amp;field=reviewer&amp;order=ASC') .'"><i class="fa fa-sort-amount-down"></i></a> ' . __d('two_reviews', 'Posté par') . ' <a href="'. site_url('reviews.php?op=sort&amp;field=reviewer&amp;order=DESC') .'"><i class="fa fa-sort-amount-up"></i></a>
                </th>
                <th class="n-t-col-xs-2" data-align="center" data-sortable="true">
                    <a href="'. site_url('reviews.php?op=sort&amp;field=score&amp;order=ASC') .'"><i class="fa fa-sort-amount-down"></i></a> ' . __d('two_reviews', 'Score') .' <a href="'. site_url('reviews.php?op=sort&amp;field=score&amp;order=DESC') .'"><i class="fa fa-sort-amount-up"></i></a>
                </th>
                <th class="n-t-col-xs-2" data-align="right" data-sortable="true">
                    <a href="'. site_url('reviews.php?op=sort&amp;field=hits&amp;order=ASC') .'"><i class="fa fa-sort-amount-down"></i></a> ' . __d('two_reviews', 'Hits') .' <a href="'. site_url('reviews.php?op=sort&amp;field=hits&amp;order=DESC') .'"><i class="fa fa-sort-amount-up"></i></a>
                </th>
                </tr>
        </thead>
        <tbody>';

        while ($myrow = sql_fetch_assoc($result)) {
            $title = $myrow['title'];
            $id = $myrow['id'];
            $reviewer = $myrow['reviewer'];
            $score = $myrow['score'];
            $hits = $myrow['hits'];
            $date = $myrow['date'];

            echo '
                <tr>
                <td>' . f_date($date) . '</td>
                <td><a href="'. site_url('reviews.php?op=showcontent&amp;id='. $id) .'">' . ucfirst($title) . '</a></td>
                <td>';

            if ($reviewer != '') {
                echo $reviewer;
            }

            echo '</td>
                <td><span class="text-success">';

            display_score($score);

            echo '</span></td>
                <td>' . $hits . '</td>
                </tr>';
        }
        echo '
            </tbody>
        </table>';
    }

    sql_free_result($result);

    include("themes/default/footer.php");;
}

function f_date($xdate)
{
    $year = substr($xdate, 0, 4);
    $month = substr($xdate, 5, 2);
    $day = substr($xdate, 8, 2);

    $fdate = date(str_replace("%", '', __d('two_reviews', 'linksdatestring')), mktime(0, 0, 0, (int)$month, (int)$day, (int)$year));

    return $fdate;
}

function showcontent($id)
{
    global $admin, $NPDS_Prefix;

    include("themes/default/header.php");

    //settype($id,'integer');

    sql_query("UPDATE " . $NPDS_Prefix . "reviews SET hits=hits+1 WHERE id='$id'");


    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT * FROM " . $NPDS_Prefix . "reviews WHERE id='$id'");
    $myrow = sql_fetch_assoc($result);

    $id =  $myrow['id'];
    $fdate = f_date($myrow['date']);
    $title = $myrow['title'];
    $text = $myrow['text'];
    $cover = $myrow['cover'];
    $reviewer = $myrow['reviewer'];
    $email = $myrow['email'];
    $hits = $myrow['hits'];
    $url = $myrow['url'];
    $url_title = $myrow['url_title'];
    $score = $myrow['score'];

    echo '
    <h2>' . __d('two_reviews', 'Critiques') . '</h2>
    <hr />
    <a href="'. site_url('reviews.php') .'">' . __d('two_reviews', 'Retour à l\'index des critiques') . '</a>
    <div class="card card-body my-3">
        <div class="card-text text-muted text-end small">
    ' . __d('two_reviews', 'Ajouté :') . ' ' . $fdate . '<br />
        </div>
    <hr />
    <h3 class="mb-3">' . $title . '</h3><br />';

    if ($cover != '') {
        echo '<img class="img-fluid" src="assets/images/reviews/' . $cover . '" />';
        }

    echo $text;

    echo '
        <br /><br />
        <div class="card card-body mb-3">';

    if ($reviewer != '') {
        echo '<div class="mb-2"><strong>' . __d('two_reviews', 'Le critique') . ' :</strong> <a href="mailto:' . spam::anti_spam($email, 1) . '" >' . $reviewer . '</a></div>';
    }

    if ($score != '') {
        echo '<div class="mb-2"><strong>' . __d('two_reviews', 'Note') . ' : </strong>';
    }

    echo '<span class="text-success">';

    display_score($score);

    echo '</span>
    </div>';

    if ($url != '')
        echo '<div class="mb-2"><strong>' . __d('two_reviews', 'Lien relatif') . ' : </strong> <a href="' . $url . '" target="_blank">' . $url_title . '</a></div>';

    echo '<div><strong>' . __d('two_reviews', 'Hits : ') . '</strong><span class="badge bg-secondary">' . $hits . '</span></div>
        </div>';

    if ($admin)
        echo '
        <nav class="d-flex justify-content-center">
            <ul class="pagination pagination-sm">
                <li class="page-item disabled">
                <a class="page-link" href="#"><i class="fa fa-cogs fa-lg"></i><span class="ms-2 d-none d-lg-inline">' . __d('two_reviews', 'Outils administrateur') . '</span></a>
                </li>
                <li class="page-item">
                <a class="page-link" role="button" href="'. site_url('reviews.php?op=mod_review&amp;id='. $id) .'" title="' . __d('two_reviews', 'Editer') . '" data-bs-toggle="tooltip" ><i class="fa fa-lg fa-edit" ></i></a>
                </li>
                <li class="page-item">
                <a class="page-link text-danger" role="button" href="'. site_url('reviews.php?op=del_review&amp;id_del='. $id) .'" title="' . __d('two_reviews', 'Effacer') . '" data-bs-toggle="tooltip" ><i class="fas fa-trash fa-lg" ></i></a>
                </li>
            </ul>
        </nav>';

    echo '
    </div>';

    sql_free_result($result);

    global $user;
    if (file_exists("modules/comments/config/reviews.conf.php")) {
        include("modules/comments/config/reviews.conf.php");
        include("modules/comments/http/comments.php");
    }

    include("themes/default/footer.php");;
}

function mod_review($id)
{
    global $admin, $NPDS_Prefix;

    include("themes/default/header.php");;

    settype($id, 'integer');

    if (($id != 0) && ($admin)) {

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $result = sql_query("SELECT * FROM " . $NPDS_Prefix . "reviews WHERE id = '$id'");
        $myrow =  sql_fetch_assoc($result);

        $id =  $myrow['id'];
        $date = $myrow['date'];
        $title = $myrow['title'];
        $text = str_replace('<br />', '\r\n', $myrow['text']);
        $cover = $myrow['cover'];
        $reviewer = $myrow['reviewer'];
        $email = $myrow['email'];
        $hits = $myrow['hits'];
        $url = $myrow['url'];
        $url_title = $myrow['url_title'];
        $score = $myrow['score'];

        echo '
    <h2 class="mb-4">' . __d('two_reviews', 'Modification d\'une critique') . '</h2>
    <hr />
    <form id="modreview" method="post" action="'. site_url('reviews.php?op=preview_review') .'">
        <input type="hidden" name="id" value="' . $id . '">
        <div class="form-floating mb-3">
            <input type="text" class="form-control w-100" id="date_modrev" name="date" value="' . $date . '" />
            <label for="date_modrev">' . __d('two_reviews', 'Date') . '</label>
        </div>
        <div class="form-floating mb-3">
            <textarea class="form-control" id="title_modrev" name="title" required="required" maxlength="150" style="height:70px;">' . $title . '</textarea>
            <label for="title_modrev">' . __d('two_reviews', 'Titre') . '</label>
            <span class="help-block text-end" id="countcar_title_modrev"></span>
        </div>
        <div class="form-floating mb-3">
            <textarea class="form-control" id="text_modrev" name="text" required="required" style="height:70px;">' . $text . '</textarea>
            <label for="text_modrev">' . __d('two_reviews', 'Texte') . '</label>
        </div>
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="reviewer_modrev" name="reviewer" value="' . $reviewer . '" required="required" maxlength="25"/>
            <label for="reviewer_modrev">' . __d('two_reviews', 'Le critique') . '</label>
            <span class="help-block text-end" id="countcar_reviewer_modrev"></span>
        </div>
        <div class="form-floating mb-3">
            <input type="email" class="form-control" id="email_modrev" name="email" value="' . $email . '" maxlength="254" required="required"/>
            <label for="email_modrev">' . __d('two_reviews', 'Email') . '</label>
            <span class="help-block text-end" id="countcar_email_modrev"></span>
        </div>
        <div class="form-floating mb-3">
            <select class="form-select" id="score_modrev" name="score">';

        $i = 1;
        $sel = '';

        do {
            if ($i == $score) { 
                $sel = 'selected="selected" ';
            } else {
                $sel = '';
                }
            
            echo '<option value="' . $i . '" ' . $sel . '>' . $i . '</option>';

            $i++;
        } while ($i <= 10);

        echo '
            </select>
            <label for="score_modrev">' . __d('two_reviews', 'Evaluation') . '</label>
            <span class="help-block">' . __d('two_reviews', 'Choisir entre 1 et 10 (1=nul 10=excellent)') . '</span>
        </div>
        <div class="form-floating mb-3">
            <input type="url" class="form-control" id="url_modrev" name="url" maxlength="320" value="' . $url . '" />
            <label for="url_modrev">' . __d('two_reviews', 'Lien') . '</label>
            <span class="help-block">' . __d('two_reviews', 'Site web officiel. Veillez à ce que votre url commence bien par') . ' http(s)://<span class="float-end" id="countcar_url_modrev"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="url_title_modrev" name="url_title" value="' . $url_title . '"  maxlength="50" />
            <label for="url_title_modrev">' . __d('two_reviews', 'Titre du lien') . '</label>
            <span class="help-block">' . __d('two_reviews', 'Obligatoire seulement si vous soumettez un lien relatif') . '<span class="float-end" id="countcar_url_title_modrev"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="cover_modrev" name="cover" value="' . $cover . '" maxlength="100"/>
            <label for="cover_modrev">' . __d('two_reviews', 'Image de garde') . '</label>
            <span class="help-block">' . __d('two_reviews', 'Nom de l\'image principale non obligatoire, la mettre dans images/reviews/') . '<span class="float-end" id="countcar_cover_modrev"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="hits_modrev" name="hits" value="' . $hits . '" maxlength="9" />
            <label for="hits_modrev">' . __d('two_reviews', 'Hits') . '</label>
        </div>
        <input type="hidden" name="op" value="preview_review" />
        <input class="btn btn-primary my-3 me-2" type="submit" value="' . __d('two_reviews', 'Prévisualiser les modifications') . '" />
        <input class="btn btn-secondary my-3" type="button" onclick="history.go(-1)" value="' . __d('two_reviews', 'Annuler') . '" />
        </form>
        <script type="text/javascript" src="assets/shared/flatpickr/dist/flatpickr.min.js"></script>
        <script type="text/javascript" src="assets/shared/flatpickr/dist/l10n/' . language::language_iso(1, '', '') . '.js"></script>
        <script type="text/javascript">
        //<![CDATA[
            $(document).ready(function() {
                $("<link>").appendTo("head").attr({type: "text/css", rel: "stylesheet",href: "assets/shared/flatpickr/dist/themes/npds.css"});
            })
            
        //]]>
        </script>';

        $fv_parametres = '
        date:{},
        hits: {
            validators: {
                regexp: {
                regexp:/^\d{1,9}$/,
                message: "0-9"
                },
                between: {
                min: 1,
                max: 999999999,
                message: "1 ... 999999999"
                }
            }
        },
        !###!
        flatpickr("#date_modrev", {
            altInput: true,
            altFormat: "l j F Y",
            dateFormat:"Y-m-d",
            "locale": "' . language::language_iso(1, '', '') . '",
            onChange: function() {
                fvitem.revalidateField(\'date\');
            }
        });
        ';

        $arg1 = '
        var formulid = ["modreview"];
        inpandfieldlen("title_modrev",150);
        inpandfieldlen("reviewer_modrev",25);
        inpandfieldlen("email_modrev",254);
        inpandfieldlen("url_modrev",320);
        inpandfieldlen("url_title_modrev",50);
        inpandfieldlen("cover_modrev",100);';

        sql_free_result($result);
    }

    css::adminfoot('fv', $fv_parametres, $arg1, 'foo');
}

function del_review($id_del)
{
    global $admin;

    settype($id_del, "integer");

    if ($admin) {
        DB::table('reviews')->where('id', $id_del)->delete();

        // commentaires
        if (file_exists("modules/comments/config/reviews.conf.php")) {
            include("modules/comments/config/reviews.conf.php");
 
            DB::table('posts')->where('forum_id', $forum)->where('topic_id', $id_del)->delete();
        }
    }

    url::redirect_url("reviews.php");
}

settype($op, 'string');
settype($hits, 'integer');
settype($id,'integer');
settype($cover, 'string');
settype($asb_question, 'string');
settype($asb_reponse, 'string');

switch ($op) {
    case 'showcontent':
        showcontent($id);
        break;

    case 'write_review':
        write_review();
        break;

    case 'preview_review':
        preview_review($title, $text, $reviewer, $email, $score, $cover, $url, $url_title, $hits, $id);
        break;

    case 'add_reviews':
        send_review($date, $title, $text, $reviewer, $email, $score, $cover, $url, $url_title, $hits, $id, $asb_question, $asb_reponse);
        break;

    case 'del_review':
        del_review($id_del);
        break;

    case 'mod_review':
        mod_review($id);
        break;

    case 'sort':
        reviews($field, $order);
        break;
        
    default:
        reviews('date', 'DESC');
        break;
}
