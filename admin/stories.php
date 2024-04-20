<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Admin DUNE Prototype                                                 */
/*                                                                      */
/* NPDS Copyright (c) 2002-2023 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\support\str;
use npds\support\editeur;
use npds\support\logs\logs;
use npds\support\news\news;
use npds\support\news\post;
use npds\support\assets\css;
use npds\support\auth\groupe;
use npds\support\routing\url;
use npds\support\theme\theme;
use npds\support\pixels\image;
use npds\system\config\Config;
use npds\support\language\language;
use npds\support\metalang\metalang;
use npds\system\support\facades\DB;
use npds\support\subscribe\subscribe;

if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'adminStory';

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

/**
 * [puthome description]
 *
 * @param   int   $ihome  [$ihome description]
 *
 * @return  void
 */
function puthome(int $ihome): void
{
    echo '
        <div class="mb-3 row">
            <label class="col-sm-4 col-form-label" for="ihome">'. adm_translate("Publier dans la racine ?") .'</label>';

    $sel1 = 'checked="checked"';
    $sel2 = '';
    if ($ihome == 1) {
        $sel1 = '';
        $sel2 = 'checked="checked"';
    }

    echo '
            <div class="col-sm-8 my-2">
                <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="ihome_y" name="ihome" value="0" '. $sel1 .' />
                <label class="form-check-label" for="ihome_y">'. adm_translate("Oui") .'</label>
                </div>
                <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="ihome_n" name="ihome" value="1" '. $sel2 .' />
                <label class="form-check-label" for="ihome_n">'. adm_translate("Non") .'</label>
                </div>
                <p class="help-block">'. adm_translate("Ne s'applique que si la catégorie : 'Articles' n'est pas sélectionnée.") .'</p>
            </div>
        </div>';

    $sel1 = '';
    $sel2 = 'checked="checked"';

    echo '
        <div class="mb-3 row">
            <label class="col-sm-4 col-form-label" >'. adm_translate("Seulement aux membres") .', '. adm_translate("Groupe") .'.</label>
            <div class="col-sm-8 my-2">
                <div class="form-check form-check-inline">';

    //?? à revoir comprends pas ...
    if ($ihome < 0) {
        $sel1 = 'checked="checked"';
        $sel2 = '';
    }

    if (($ihome > 1) and ($ihome <= 127)) {
        $Mmembers = $ihome;
        $sel1 = 'checked="checked"';
        $sel2 = '';
    }

    echo '
                <input class="form-check-input" type="radio" id="mem_y" name="members" value="1" '. $sel1 .' />
                <label class="form-check-label" for="mem_y">'. adm_translate("Oui") .'</label>
                </div>
                <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio"  id="mem_n" name="members" value="0" '. $sel2 .' />
                <label class="form-check-label" for="mem_n">'. adm_translate("Non") .'</label>
                </div>
            </div>
        </div>';

    // ---- Groupes
    $mX = groupe::liste_group();
    $tmp_groupe = '';

    settype($Mmembers, 'integer');

    foreach ($mX as $groupe_id => $groupe_name) {
        if ($groupe_id == '0') {
            $groupe_id = '';
        }

        if ($Mmembers == $groupe_id) {
            $sel3 = 'selected="selected"';
        } else {
            $sel3 = '';
        }

        $tmp_groupe .= '<option value="'. $groupe_id .'" '. $sel3 .'>'. $groupe_name .'</option>';
    }

    echo '
        <div class="mb-3 row" id="choixgroupe">
            <label class="col-sm-4 col-form-label" for="Mmembers">'. adm_translate("Groupe") .'</label>
            <div class="col-sm-8">
                <select class="form-select" id="Mmembers" name="Mmembers">'. $tmp_groupe .'</select>
            </div>
        </div>';
}

/**
 * [SelectCategory description]
 *
 * @param   int   $cat  [$cat description]
 *
 * @return  void
 */
function SelectCategory(int $cat): void
{
    $stories_cat = DB::table('stories_cat')->select('catid', 'title')->get();

    echo ' 
        <div class="mb-3 row">
            <label class="col-sm-4 col-form-label" for="catid">'. adm_translate("Catégorie") .'</label>
            <div class="col-sm-8">
                <select class="form-select" id="catid" name="catid">';

    if ($cat == 0) { 
        $sel = 'selected="selected"';
    } else {
        $sel = '';
    }

    echo '<option name="catid" value="0" '. $sel .'>'. adm_translate("Articles") .'</option>';
    
    foreach ($stories_cat as $categorie) {
        if ($categorie['catid'] == $cat) { 
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }

        echo '<option name="catid" value="'. $categorie['catid'] .'" '. $sel .'>'. language::aff_langue($categorie['title']) .'</option>';
    }

    echo '
                </select>
                <p class="help-block text-end">
                    <a href="'. site_url('admin.php?op=AddCategory') .'" class="btn btn-outline-primary btn-sm" title="'. adm_translate("Ajouter") .'" data-bs-toggle="tooltip" >
                        <i class="fa fa-plus-square fa-lg"></i>
                    </a>&nbsp;
                    <a class="btn btn-outline-primary btn-sm" href="'. site_url('admin.php?op=EditCategory') .'" title="'. adm_translate("Editer") .'" data-bs-toggle="tooltip" >
                        <i class="fa fa-edit fa-lg"></i>
                    </a>&nbsp;
                    <a class="btn btn-outline-danger btn-sm" href="'. site_url('admin.php?op=DelCategory') .'" title="'. adm_translate("Effacer") .'" data-bs-toggle="tooltip">
                        <i class="fas fa-trash fa-lg"></i>
                    </a>
                </p>
            </div>
        </div>';
}

// CATEGORIES

/**
 * [AddCategory description]
 *
 * @return  void
 */
function AddCategory(): void
{
    global $aid;

    $f_meta_nom = 'adminStory';
    $f_titre = adm_translate("Articles");

    //==> controle droit
    admindroits($aid, $f_meta_nom);
    //<== controle droit

    include("themes/default/header.php");

    GraphicAdmin('');
    adminhead($f_meta_nom, $f_titre);

    echo '
    <hr />
    <h3 class="mb-3">'. adm_translate("Ajouter une nouvelle Catégorie") .'</h3>
    <form id="storiesaddcat" action="'. site_url('admin.php') .'" method="post">
        <div class="mb-3 row">
            <label class="col-sm-12 col-form-label" for="title">'. adm_translate("Nom") .'</label>
            <div class="col-sm-12">
                <input class="form-control" type="text" id="title" name="title" maxlength="255" required="required" />
                <span class="help-block text-end" id="countcar_title"></span>
            </div>
        </div>
        <input type="hidden" name="op" value="SaveCategory" />
        <div class="mb-3 row">
            <div class="col-sm-12">
                <input class="btn btn-primary" type="submit" value="'. adm_translate("Sauver les modifications") .'" />
            </div>
        </div>
    </form>';

    $arg1 = '
    var formulid = ["storiesaddcat"];
    inpandfieldlen("title",255);';

    css::adminfoot('fv', '', $arg1, '');
}

/**
 * [SaveCategory description]
 *
 * @param   string  $title  [$title description]
 *
 * @return  void
 */
function SaveCategory(string $title): void
{
    global $aid, $f_meta_nom;

    $f_meta_nom = 'adminStory';
    $f_titre = adm_translate("Articles");

    //==> controle droit
    admindroits($aid, $f_meta_nom);
    //<== controle droit

    $title = preg_replace('#"#', '', $title);

    $check = DB::table('stories_cat')->select('catid')->where('title', $title)->first();

    if ($check['catid']) {
        $what1 = '<div class="alert alert-danger lead" role="alert">'. adm_translate("Cette Catégorie existe déjà !") .'<br /><a href="javascript:history.go(-1)" class="btn btn-secondary  mt-2">'. adm_translate("Retour en arrière, pour changer le Nom") .'</a></div>';
    } else {
        $what1 = '<div class="alert alert-success lead" role="alert">'. adm_translate("Nouvelle Catégorie ajoutée") .'</div>';
        DB::table('stories_cat')->insert(array(
            'title'       => $title,
            'counter'     => 0,
        ));
    }

    include("themes/default/header.php");

    GraphicAdmin('');
    adminhead($f_meta_nom, $f_titre);

    echo '
    <hr />
    <h3 class="mb-3">'. adm_translate("Ajouter une nouvelle Catégorie") .'</h3>
    '. $what1;

    css::adminfoot('', '', '', '');
}

/**
 * [EditCategory description]
 *
 * @param   int   $catid  [$catid description]
 *
 * @return  void
 */
function EditCategory(int $catid): void
{
    global $aid;

    $f_meta_nom = 'adminStory';
    $f_titre = adm_translate("Articles");

    //==> controle droit
    admindroits($aid, $f_meta_nom);
    //<== controle droit

    include("themes/default/header.php");

    GraphicAdmin('');
    adminhead($f_meta_nom, $f_titre);

    echo '
    <hr />
    <h3 class="mb-3">'. adm_translate("Edition des Catégories") .'</h3>';

    if (!$catid) {
        $selcat = sql_query("SELECT  FROM " . $NPDS_Prefix . "");
        
        $stories_cat = DB::table('stories_cat')->select('catid', 'title')->get();

        echo '
        <form action="'. site_url('admin.php') .'" method="post">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="catid">'. adm_translate("Sélectionner une Catégorie") .'</label>
                <div class="col-sm-12">
                    <select class="form-select" id="catid" name="catid">
                        <option name="catid" value="0">'. adm_translate("Articles") .'</option>';
        
        foreach ($stories_cat as $categ) {
            echo '
                <option name="catid" value="'. $categ['catid'] .'">'. language::aff_langue($categ['title']) .'</option>';
        }

        echo '
                </select>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-12">
                    <input type="hidden" name="op" value="EditCategory" />
                    <input class="btn btn-primary" type="submit" value="'. adm_translate("Editer") .'" />
                </div>
            </div>
        </form>';

        css::adminfoot('', '', '', '');
    } else {

        $stories_cat = DB::table('stories_cat')->select('title')->where('catid', $catid)->first();

        echo '
        <form id="storieseditcat" action="'. site_url('admin.php') .'" method="post">
            <div class="mb-3 row">
            <label class="col-form-label col-sm-12" for="title">'. adm_translate("Nom") .'</label>
                <div class="col-sm-12">
                    <input class="form-control" type="text" id="title" name="title" maxlength="255" value="'. $categorie_cat['title'] .'" required="required"/>
                    <span class="help-block text-end" id="countcar_title"></span>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-12">
                    <input type="hidden" name="catid" value="'. $catid .'" />
                    <input type="hidden" name="op" value="SaveEditCategory" />
                    <input class="btn btn-primary" type="submit" value="'. adm_translate("Sauver les modifications") .'" />
                </div>
            </div>
        </form>';

        $arg1 = '
        var formulid = ["storieseditcat"];
        inpandfieldlen("title",255);';

        css::adminfoot('fv', '', $arg1, '');
    }
}

/**
 * [SaveEditCategory description]
 *
 * @param   int     $catid  [$catid description]
 * @param   string  $title  [$title description]
 *
 * @return  void
 */
function SaveEditCategory(int $catid, string $title): void
{
    global $aid, $f_meta_nom;

    $f_titre = adm_translate("Articles");
    $title = preg_replace('#"#', '', $title);

    $check = DB::table('stories_cat')->select('catid')->where('title', $title)->first();

    if ($check) {
        $what1 = '<div class="alert alert-danger lead" role="alert">'. adm_translate("Cette Catégorie existe déjà !") .'<br /><a href="javascript:history.go(-2)" class="btn btn-secondary  mt-2">'. adm_translate("Retour en arrière, pour changer le Nom") .'</a></div>';
    } else {
        $what1 = '<div class="alert alert-success lead" role="alert">'. adm_translate("Catégorie sauvegardée") .'</div>';
        DB::table('stories_cat')->where('catid', $catid)->update(array(
            'title'       => $title,
        ));

        global $aid;
        logs::Ecr_Log("security", "SaveEditCategory($catid, $title) by AID : $aid", "");
    }

    include("themes/default/header.php");

    GraphicAdmin('');
    adminhead($f_meta_nom, $f_titre);

    echo '
    <hr />
    <h3 class="mb-3">'. adm_translate("Edition des Catégories") .'</h3>
    '. $what1;

    css::adminfoot('', '', '', '');
}

/**
 * [DelCategory description]
 *
 * @param   int   $cat  [$cat description]
 *
 * @return  void
 */
function DelCategory(int $cat): void
{
    global $aid;

    $f_meta_nom = 'adminStory';
    $f_titre = adm_translate("Articles");

    //==> controle droit
    admindroits($aid, $f_meta_nom);
    //<== controle droit

    include("themes/default/header.php");

    GraphicAdmin('');
    adminhead($f_meta_nom, $f_titre);

    echo '
    <hr />
    <h3 class="mb-3 text-danger">'. adm_translate("Supprimer une Catégorie") .'</h3>';

    if (!$cat) {
        $stories_cat = DB::table('stories_cat')->select('catid', 'title')->get();

        echo '
    <form action="'. site_url('admin.php') .'" method="post">
        <div class="mb-3 row">
        <label class="col-form-label col-sm-12" for="cat">'. adm_translate("Sélectionner une Catégorie à supprimer") .'</label>
            <div class="col-sm-12">
                <select class="form-select" id="cat" name="cat">';

        foreach ($stories_cat as $categ) {
            echo '<option name="cat" value="'. $categ['catid'] .'">'. language::aff_langue($categ['title']) .'</option>';
        }

        echo '
                </select>
            </div>
        </div>
        <div class="mb-3 row">
            <div class="col-sm-12">
                <input type="hidden" name="op" value="DelCategory" />
                <button class="btn btn-danger" type="submit">'. adm_translate("Effacer") .'</button>
            </div>
        </div>
    </form>';

    } else {
        $numrows = DB::table('stories')->select('*')->where('catid', $cat)->count();

        if ($numrows == 0) {
            DB::table('stories_cat')->where('catid', $cat)->delete();

            global $aid;
            logs::Ecr_Log('security', "DelCategory($cat) by AID : $aid", '');

            echo '
            <div class="alert alert-success" role="alert">'. adm_translate("Suppression effectuée") .'</div>';
        } else {

            $stories_cat = DB::table('stories_cat')->select('title')->where('catid', $cat)->first();
        
            echo '
            <div class="alert alert-danger lead" role="alert">
                <p class="noir"><strong>'. adm_translate("Attention : ") .'</strong> '. adm_translate("la Catégorie") .' <strong>'. $stories_cat['title'] .'</strong> '. adm_translate("a") .' <strong>'. $numrows .'</strong> '. adm_translate("Articles !") .'<br />';
            echo adm_translate("Vous pouvez supprimer la Catégorie, les Articles et Commentaires") .' ';
            echo adm_translate("ou les affecter à une autre Catégorie.") .'<br /></p>
                <p align="text-center"><strong>'. adm_translate("Que voulez-vous faire ?") .'</strong></p>
            </div>
            <a href="'. site_url('admin.php?op=YesDelCategory&amp;catid='. $cat) .'" class="btn btn-outline-danger">'. adm_translate("Tout supprimer") .'</a>
            <a href="'. site_url('admin.php?op=NoMoveCategory&amp;catid='. $cat) .'" class="btn btn-outline-primary">'. adm_translate("Affecter à une autre Catégorie") .'</a></p>';
        }
    }

    css::adminfoot('', '', '', '');
}

/**
 * [YesDelCategory description]
 *
 * @param   int   $catid  [$catid description]
 *
 * @return  void
 */
function YesDelCategory(int $catid): void
{
    DB::table('stories_cat')->where('catid', $catid)->delete();

    $stories = DB::table('stories')->select('sid')->where('catid', $catid)->get();

    foreach ($stories as $storie) {
        DB::table('stories')->where('catid', $catid)->delete();

        // pour article.conf
        $sid = $storie['sid'];

        // commentaires
        if (file_exists("modules/comments/config/article.conf.php")) {
            include("modules/comments/config/article.conf.php");

            DB::table('posts')->where('forum_id', $forum)->where('topic_id', $topic)->delete();
        }
    }

    global $aid;
    logs::Ecr_Log('security', "YesDelCategory($catid) by AID : $aid", '');

    Header('Location: '. site_url('admin.php'));
}

/**
 * [NoMoveCategory description]
 *
 * @param   int   $catid   [$catid description]
 * @param   int   $newcat  [$newcat description]
 *
 * @return  void
 */
function NoMoveCategory(int $catid, int $newcat): void
{
    global $f_meta_nom, $f_titre, $aid;

    $f_meta_nom = 'adminStory';
    $f_titre = adm_translate("Articles");

    //==> controle droit
    admindroits($aid, $f_meta_nom);
    //<== controle droit

    include("themes/default/header.php");

    GraphicAdmin('');
    adminhead($f_meta_nom, $f_titre);

    $stories_cat = DB::table('stories_cat')->select('title')->where('catid', $catid)->first();

    echo '
    <hr />
    <h3 class="mb-3">'. adm_translate("Affectation d'Articles vers une nouvelle Catégorie") .'</h3>';

    if (!$newcat) {
        echo '<label>'. adm_translate("Tous les Articles dans") .' <strong>'. language::aff_langue($stories_cat['title']) .'</strong> '. adm_translate("seront affectés à") .'</label>';
        
        $selcat = sql_query("SELECT  FROM " . $NPDS_Prefix . "");
        
        $stories_cat = DB::table('stories_cat')->select('catid', 'title')->get();

        echo '
        <form action="'. site_url('admin.php') .'" method="post">
            <div class="mb-3 row">
                <label class="col-form-label visually-hidden" for="newcat">'. adm_translate("Sélectionner la nouvelle Catégorie : ") .'</label>
                <div class="col-sm-12">
                    <select class="form-select" id="newcat" name="newcat">
                    <option name="newcat" value="0">'. adm_translate("Articles") .'</option>';

        foreach ($stories_cat as $cat) {
            echo '<option name="newcat" value="'. $cat['newcat'] .'">'. language::aff_langue($cat['title']) .'</option>';
        }

        echo '
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-12">
                    <input type="hidden" name="catid" value="'. $catid .'" />
                    <input type="hidden" name="op" value="NoMoveCategory" />
                    <input class="btn btn-primary" type="submit" value="'. adm_translate("Affectation") .'" />
                </div>
            </div>
        </form>';

    } else {
        $stories = DB::table('stories')->select('sid')->where('catid', $catid)->get();

        foreach ($stories as $storie) {
            DB::table('stories')->where('sid', $storie['sid'])->update(array(
                'catid'       => $newcat,
            ));
        }

        DB::table('stories_cat')->where('catid', $catid)->delete();

        global $aid;
        logs::Ecr_Log("security", "NoMoveCategory($catid, $newcat) by AID : $aid", "");

        echo '<div class="alert alert-success"><strong>'. adm_translate("La ré-affectation est terminée !") .'</strong></div>';
    }

    css::adminfoot('', '', '', '');
}

// NEWS

/**
 * [displayStory description]
 *
 * @param   int   $qid  [$qid description]
 *
 * @return  void
 */
function displayStory(int $qid): void
{
    global $aid, $radminsuper;

    $f_meta_nom = 'adminStory';
    $f_titre = adm_translate("Articles");

    $queue = DB::table('queue')->select('qid', 'uid', 'uname', 'subject', 'story', 'bodytext', 'topic', 'date_debval', 'date_finval',' auto_epur')->where('qid', $qid)->get();

    if ($queue['topic'] < 1) {
        $queue['topic'] = 1;
    }

    $affiche = false;

    $topic = DB::table('topics')->select('topictext', 'topicimage', 'topicadmin')->where('topicid', $queue['topic'])->first();

    if ($radminsuper) {
        $affiche = true;
    } else {
        $topicadminX = explode(',', $topic['topicadmin']);
        for ($i = 0; $i < count($topicadminX); $i++) {
            if (trim($topicadminX[$i]) == $aid) $affiche = true;
        }
    }

    if (!$affiche) {
        header('location: '. site_url('admin.php?op=submissions'));
    }

    $topiclogo = '<span class="badge bg-secondary float-end"><strong>'. language::aff_langue($topic['topictext']) .'</strong></span>';

    include("themes/default/header.php");

    GraphicAdmin(manuel('newarticle'));
    adminhead($f_meta_nom, $f_titre);

    echo '
    <hr />
    <h3>'. adm_translate("Prévisualiser l'Article") .'</h3>
    <form action="'. site_url('admin.php') .'" method="post" name="adminForm" id="adminForm">
        <label class="col-form-label">'. adm_translate("Langue de Prévisualisation") .'</label>
        '. language::aff_localzone_langue("local_user_language") .'
        <div class="card card-body mb-3">';

    if ($topic['topicimage'] !== '') {
        if (!$imgtmp = theme::theme_image('topics/'. $topic['topicimage'])) {
            $imgtmp = config::get('npds.tipath') . $topic['topicimage'];
        }

        if (file_exists($imgtmp)) {
            $topiclogo = '<img class="img-fluid n-sujetsize" src="'. $imgtmp .'" align="right" alt="" />';
        }
    }

    
    $subject = stripslashes($queue['subject']);

    post::code_aff('<h4>'. $subject . $topiclogo .'</h4>', '<div class="text-muted">'. metalang::meta_lang($story) .'</div>', metalang::meta_lang($bodytext), "");

    echo '
            </div>
        <div class="mb-3 row">
            <label class="col-sm-4 col-form-label" for="author">'. userpopover($queue['uname'], 40, '') . adm_translate("Utilisateur") .'</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="author" name="author" value="'. $queue['uname'] .'" />
                <a href="'. site_url('replypmsg.php?send='. urlencode($queue['uname'])) .'" target="_blank" title="'. adm_translate("Diffusion d'un Message Interne") .'" data-bs-toggle="tooltip"><i class="far fa-envelope fa-lg"></i></a>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-sm-4 col-form-label" for="subject">'. adm_translate("Titre") .'</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="subject" name="subject" value="'. $subject .'" required="required" />
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-sm-4 col-form-label" for="topic">'. adm_translate("Sujet") .'</label>
            <div class="col-sm-8">
                <select class="form-select" id="topic" name="topic">';

    if ($radminsuper) {
        echo '<option value="">'. adm_translate("Tous les Sujets") .'</option>';
    }

    $toplis = DB::table('topics')->select('topicid', 'topictext', 'topicadmin')->orderBy('topictext')->get();

    foreach ($toplist as $list) {
        $affiche = false;
        
        if ($radminsuper) {
            $affiche = true;
        } else {
            $topicadminX = explode(',', $list['topicadmin']);
            for ($i = 0; $i < count($topicadminX); $i++) {
                if (trim($topicadminX[$i]) == $aid) { 
                    $affiche = true;
                }
            }
        }

        if ($affiche) {
            if ($topicid == $topic) {
                $sel = 'selected="selected" ';
            }

            echo '<option '. $sel .' value="'. $list['topicid'] .'">'. language::aff_langue($list['topics']) .'</option>';
            $sel = '';
        }
    }

    echo '
                </select>
            </div>
        </div>';

    settype($cat, 'string');

    SelectCategory($cat);

    settype($ihome, 'integer');

    puthome($ihome);

    $story = stripslashes($queue['story']);
    $bodytext = stripslashes($queue['bodytext']);

    echo '
    <div class="mb-3 row">
        <label class="col-form-label col-12" for="hometext">'. adm_translate("Texte d'introduction") .'</label>
        <div class="col-12">
            <textarea class="tin form-control" rows="25" id="hometext" name="hometext">'. $story .'</textarea>
        </div>
    </div>';

    echo editeur::aff_editeur('hometext', '');

    echo '
    <div class="mb-3 row">
        <label class="col-form-label col-12" for="bodytext">'. adm_translate("Texte étendu") .'</label>
        <div class="col-12">
            <textarea class="tin form-control" rows="25" id="bodytext" name="bodytext" >'. $bodytext .'</textarea>
        </div>
    </div>';

    echo editeur::aff_editeur('bodytext', '');

    echo '
    <div class="mb-3 row">
        <label class="col-form-label col-12" for="notes">'. adm_translate("Notes") .'</label>
        <div class="col-12">
            <textarea class="tin form-control" rows="7" id="notes" name="notes"></textarea>
        </div>
    </div>';

    echo editeur::aff_editeur('notes', '');

    $dd_pub = substr($queue['date_debval'], 0, 10);
    $fd_pub = substr($queue['date_finval'], 0, 10);
    $dh_pub = substr($queue['date_debval'], 11, 5);
    $fh_pub = substr($queue['date_finval'], 11, 5);

    post::publication($dd_pub, $fd_pub, $dh_pub, $fh_pub, $epur);

    echo '
        <input type="hidden" name="qid" value="'. $queue['qid'] .'" />
        <input type="hidden" name="uid" value="'. $queue['uid'] .'" />
        <div class="mb-3">
            <select class="form-select" name="op">
                <option value="DeleteStory">'. adm_translate("Effacer l'Article") .'</option>
                <option value="PreviewAgain" selected="selected">'. adm_translate("Re-prévisualiser") .'</option>
                <option value="PostStory">'. adm_translate("Poster un Article ") .'</option>
            </select>
        </div>
        <input class="btn btn-primary" type="submit" value="'. adm_translate("Ok") .'" />
    </form>';

    $arg1 = '
    var formulid = ["adminForm"];';

    css::adminfoot('fv', '', $arg1, '');
}

/**
 * [previewStory description]
 *
 * @param   int     $qid       [$qid description]
 * @param   int     $uid       [$uid description]
 * @param   string  $author    [$author description]
 * @param   string  $subject   [$subject description]
 * @param   string  $hometext  [$hometext description]
 * @param   string  $bodytext  [$bodytext description]
 * @param   int     $topic     [$topic description]
 * @param   string  $notes     [$notes description]
 * @param   int     $catid     [$catid description]
 * @param   int     $ihome     [$ihome description]
 * @param   string  $members   [$members description]
 * @param   int     $Mmembers  [$Mmembers description]
 * @param   int     $dd_pub    [$dd_pub description]
 * @param   int     $fd_pub    [$fd_pub description]
 * @param   int     $dh_pub    [$dh_pub description]
 * @param   int     $fh_pub    [$fh_pub description]
 * @param   int     $epur      [$epur description]
 *
 * @return  void
 */
function previewStory(int $qid, int $uid, string $author, string $subject, string $hometext, string $bodytext, int $topic, string $notes, int $catid, int $ihome, string $members, int $Mmembers, int $dd_pub, int $fd_pub, int $dh_pub, int $fh_pub, int $epur): void
{
    global $aid, $radminsuper;

    $f_meta_nom = 'adminStory';
    $f_titre = adm_translate("Articles");

    $subject = stripslashes(str_replace('"', '&quot;', $subject));
    $hometext = stripslashes(image::dataimagetofileurl($hometext, 'storage/cache/ai'));
    $bodytext = stripslashes(image::dataimagetofileurl($bodytext, 'storage/cache/ac'));
    $notes = stripslashes(image::dataimagetofileurl($notes, 'storage/cache/an'));

    if ($topic < 1) {
        $topic = 1;
    }

    $affiche = false;

    $topic = DB::table('topics')->select('topictext', 'topicimage', 'topicadmin')->where('topicid', $topic)->first();

    if ($radminsuper) {
        $affiche = true;
    } else {
        $topicadminX = explode(',', $topic['topicadmin']);
        for ($i = 0; $i < count($topicadminX); $i++) {
            if (trim($topicadminX[$i]) == $aid) {
                $affiche = true;
            }
        }
    }

    if (!$affiche) {
        header('location: '. site_url('admin.php?op=submissions'));
    }

    $topiclogo = '<span class="badge bg-secondary float-end"><strong>'. language::aff_langue($topic['topictext']) .'</strong></span>';

    include("themes/default/header.php");

    GraphicAdmin(manuel('newarticle'));
    adminhead($f_meta_nom, $f_titre);

    echo '
    <hr />
    <h3>'. adm_translate("Prévisualiser l'Article") .'</h3>
    <form action="'. site_url('admin.php') .'" method="post" name="adminForm">
        <label class="col-form-label">'. adm_translate("Langue de Prévisualisation") .'</label>
        '. language::aff_localzone_langue("local_user_language") .'
        <div class="card card-body mb-3">';

    if ($topic['topicimage'] !== '') {
        if (!$imgtmp = theme::theme_image('topics/'. $topic['topicimage'])) {
            $imgtmp = Config::get('npds.tipath') . $topic['topicimage'];
        }

        $timage = $imgtmp;

        if (file_exists($imgtmp)) {
            $topiclogo = '<img class="img-fluid n-sujetsize" src="'. $timage .'" align="right" alt="" />';
        }
    }

    post::code_aff('<h3>'. $subject . $topiclogo .'</h3>', '<div class="text-muted">'. metalang::meta_lang($hometext) .'</div>', metalang::meta_lang($bodytext), metalang::meta_lang($notes));

    echo '
            </div>
        <div class="mb-3 row">
            <label class="col-sm-4 col-form-label" for="author">'. adm_translate("Utilisateur") .'</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="author" name="author" value="'. $author .'" />
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-sm-4 col-form-label" for="subject">'. adm_translate("Titre") .'</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="subject" name="subject" value="'. $subject .'" />
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="topic">'. adm_translate("Sujet") .'</label>
            <div class="col-sm-8">
                <select class="form-select" id="topic" name="topic">';

    $toplist = DB::table('topics')->select('topicid', 'topictext', 'topicadmin')->orderBy('topictext')->get();

    if ($radminsuper) {
        echo '<option value="">'. adm_translate("Tous les Sujets") .'</option>';
    }

    foreach ($toplist as $list) {
        $affiche = false;

        if ($radminsuper) {
            $affiche = true;
        } else {
            $topicadminX = explode(',', $list['topicadmin']);
            for ($i = 0; $i < count($topicadminX); $i++) {
                if (trim($topicadminX[$i]) == $aid) { 
                    $affiche = true;
                }
            }
        }

        if ($affiche) {
            if ($topicid == $topic) {
                $sel = 'selected="selected" ';
            }

            echo '<option '. $sel .' value="'. $list['topicid'] .'">'. language::aff_langue($list['topics']) .'</option>';
            $sel = '';
        }
    }

    echo '
                </select>
            </div>
        </div>';

    SelectCategory($catid);

    if (($members == 1) and ($Mmembers == '')) {
        $ihome = '-127';
    }

    if (($members == 1) and (($Mmembers > 1) and ($Mmembers <= 127))) {
        $ihome = $Mmembers;
    }

    puthome($ihome);

    echo '
        <div class="mb-3 row">
        <label class="col-form-label col-12" for="hometext">'. adm_translate("Texte d'introduction") .'</label>
        <div class="col-12">
            <textarea class="tin form-control" cols="70" rows="25" id="hometext" name="hometext" >'. $hometext .'</textarea>
        </div>
    </div>';

    echo editeur::aff_editeur('hometext', '');

    echo '
    <div class="mb-3 row">
        <label class="col-form-label col-12" for="bodytext">'. adm_translate("Texte étendu") .'</label>
        <div class="col-12">
            <textarea class="tin form-control" cols="70" rows="25" id="bodytext" name="bodytext" >'. $bodytext .'</textarea>
        </div>
    </div>';

    echo editeur::aff_editeur('bodytext', '');

    echo '
    <div class="mb-3 row">
        <label class="col-form-label col-12" for="notes">'. adm_translate("Notes") .'</label>
        <div class="col-12">
            <textarea class="tin form-control" cols="70" rows="7" id="notes" name="notes" >'. $notes .'</textarea>
        </div>
    </div>';

    echo editeur::aff_editeur('notes', '');

    post::publication($dd_pub, $fd_pub, $dh_pub, $fh_pub, $epur);

    echo '
        <input type="hidden" name="qid" value="'. $qid .'" />
        <input type="hidden" name="uid" value="'. $uid .'" />
        <select class="form-select" name="op">
            <option value="DeleteStory">'. adm_translate("Effacer l'Article") .'</option>
            <option value="PreviewAgain" selected="selected">'. adm_translate("Re-prévisualiser") .'</option>
            <option value="PostStory">'. adm_translate("Poster un Article ") .'</option>
        </select>
        <input class="btn btn-primary my-2" type="submit" value="'. adm_translate("Ok") .'" />
    </form>';

    css::adminfoot('', '', '', '');
}

/**
 * [postStory description]
 *
 * @param   string  $type_pub     [$type_pub description]
 * @param   int     $qid          [$qid description]
 * @param   int     $uid          [$uid description]
 * @param   string  $author       [$author description]
 * @param   string  $subject      [$subject description]
 * @param   string  $hometext     [$hometext description]
 * @param   string  $bodytext     [$bodytext description]
 * @param   int     $topic        [$topic description]
 * @param   string  $notes        [$notes description]
 * @param   int     $catid        [$catid description]
 * @param   int     $ihome        [$ihome description]
 * @param   string  $members      [$members description]
 * @param   int     $Mmembers     [$Mmembers description]
 * @param   int     $date_debval  [$date_debval description]
 * @param   int     $date_finval  [$date_finval description]
 * @param   int     $epur         [$epur description]
 *
 * @return  void
 */
function postStory(string $type_pub, int $qid, int $uid, string $author, string $subject, string $hometext, string $bodytext, int $topic, string $notes, int $catid, int $ihome, string $members, int $Mmembers, int $date_debval, int $date_finval, int $epur): void
{
    global $aid;

    if ($uid == 1) {
        $author = '';
    }

    if ($hometext == $bodytext) {
        $bodytext = '';
    }

    $artcomplet = array('hometext' => $hometext, 'bodytext' => $bodytext, 'notes' => $notes);
    $rechcacheimage = '#storage/cache/(a[i|c|n]\d+_\d+_\d+.[a-z]{3,4})\\\"#m';

    foreach ($artcomplet as $k => $artpartie) {
        preg_match_all($rechcacheimage, $artpartie, $cacheimages);

        foreach ($cacheimages[1] as $imagecache) {
            rename("storage/cache/" . $imagecache, "modules/upload/upload/" . $imagecache);
            $$k = preg_replace($rechcacheimage, 'modules/upload/upload/\1"', $artpartie, 1);
        }
    }

    $subject = stripslashes(str::FixQuotes(str_replace('"', '&quot;', $subject)));

    $hometext = image::dataimagetofileurl($hometext, 'modules/upload/upload/ai');
    $bodytext = image::dataimagetofileurl($bodytext, 'modules/upload/upload/ac');
    $notes = image::dataimagetofileurl($notes, 'modules/upload/upload/an');

    $hometext = stripslashes(str::FixQuotes($hometext));
    $bodytext = stripslashes(str::FixQuotes($bodytext));
    $notes = stripslashes(str::FixQuotes($notes));

    if (($members == 1) and ($Mmembers == '')) {
        $ihome = '-127';
    }

    if (($members == 1) and (($Mmembers > 1) and ($Mmembers <= 127))) {
        $ihome = $Mmembers;
    }

    if ($type_pub == 'pub_immediate') {
        DB::table('stories')->insert(array(
            'catid'         => $catid,
            'aid'           => $subject,
            'title'         => 'now()',
            'time'          => $hometext,
            'hometext'      => $bodytext,
            'bodytext'      => 0,
            'comments'      => 0,
            'counter'       => $topic,
            'topic'         => $author,
            'informant'     => $notes,
            'notes'         => $ihome,
            'ihome'         => 0,
            'date_finval'   => $date_finval,
            'auto_epur'     => $epur,
        ));

        logs::Ecr_Log("security", "postStory (pub_immediate, $subject) by AID : $aid", "");
    } else {
        DB::table('autonews')->insert(array(
            'catid'         => $catid,
            'aid'           => $aid,
            'title'         => $subject,
            'time'          => 'now()',
            'hometext'      => $hometext,
            'bodytext'      => $bodytext,
            'topic'         => $topic,
            'informant'     => $author,
            'notes'         => $notes,
            'ihome'         => $ihome,
            'date_debval'   => $date_debval,
            'date_finval'   => $date_finval,
            'auto_epur'     => $epur,
        ));

        logs::Ecr_Log("security", "postStory (autonews, $subject) by AID : $aid", "");
    }

    if (($uid != 1) and ($uid != '')) {
        DB::table('users')->where('uid', $uid)->update(array(
            'counter'       => DB::raw('counter+1'),
        ));
    }

    DB::table('authors')->where('aid', $aid)->update(array(
        'counter'       => DB::raw('counter+1'),
    ));

    if (Config::get('npds.ultramode')) {
        news::ultramode();
    }

    deleteStory($qid);

    if ($type_pub == 'pub_immediate') {
        global $subscribe;
        if ($subscribe) {
            subscribe::subscribe_mail("topic", $topic, '', $subject, '');
        }

        // Cluster Paradise
        if (file_exists("modules/cluster-paradise/config/cluster-activate.php")) {
            include("modules/cluster-paradise/config/cluster-activate.php");
        }

        if (file_exists("modules/cluster-paradise/http/cluster-M.php")) { 
            include("modules/cluster-paradise/http/cluster-M.php");
        }
        // Cluster Paradise

        // Réseaux sociaux
        if (file_exists('modules/npds_twi/http/npds_to_twi.php')) {
            include('modules/npds_twi/http/npds_to_twi.php');
        }

        if (file_exists('modules/npds_fbk/http/npds_to_fbk.php')) {
            include('modules/npds_twi/http/npds_to_fbk.php');
        }
        // Réseaux sociaux
    }

    url::redirect_url("admin.php");
}

/**
 * [editStory description]
 *
 * @param   int   $sid  [$sid description]
 *
 * @return  void
 */
function editStory(int $sid): void
{
    global $aid, $radminsuper;

    $f_meta_nom = 'adminStory';
    $f_titre = adm_translate("Editer un Article");

    //==> controle droit
    admindroits($aid, $f_meta_nom);
    //<== controle droit

    if (($sid == '') or ($sid == '0')) {
        header('location: '. site_url('admin.php'));
    }

    $storie = DB::table('stories')->select('catid', 'title', 'hometext', 'bodytext', 'topic', 'notes', 'ihome', 'date_finval', 'auto_epur')->where('sid', $sid)->first();

    $subject = stripslashes($storie['subject']);
    $hometext = stripslashes($storie['hometext']);
    $hometext = str_replace('<i class="fa fa-thumb-tack fa-2x me-2 text-muted"></i>', '', $hometext);
    $bodytext = stripslashes($storie['bodytext']);
    $notes = stripslashes($storie['notes']);

    $affiche = false;

    $topic = DB::table('topics')->select('topictext', 'topicname', 'topicimage', 'topicadmin')->where('topicid', $storie['topic'])->get();

    if ($radminsuper) {
        $affiche = true;
    } else {
        $topicadminX = explode(',', $topic['topicadmin']);
        for ($i = 0; $i < count($topicadminX); $i++) {
            if (trim($topicadminX[$i]) == $aid) {
                    $affiche = true;
                }
        }
    }

    if (!$affiche) {
        header('location: '. site_url('admin.php'));
    }

    $topiclogo = '<span class="badge bg-secondary float-end"><strong>'. language::aff_langue($topic['topicname']) .'</strong></span>';

    include("themes/default/header.php");

    GraphicAdmin(manuel('newarticle'));
    adminhead($f_meta_nom, $f_titre);

    $topic = DB::table('topics')->select('topictext', 'topicimage')->where('topicid', $topic)->first();

    echo '<hr />'. language::aff_local_langue('', 'local_user_language', '<label class="col-form-label">'. adm_translate("Langue de Prévisualisation") .'</label>');
    
    if ($topic['topicimage'] !== '') {
        if (!$imgtmp = theme::theme_image('topics/'. $topic['topicimage'])) {
            $imgtmp = Config::get('npds.tipath') . $topic['topicimage'];
        }

        if (file_exists($imgtmp)) {
            $topiclogo = '<img class="img-fluid " src="'. $imgtmp .'" align="right" alt="" />';
        }
    }

    echo '
    <div id="art_preview" class="card card-body mb-3">';

    echo post::code_aff('<h3>'. $subject . $topiclogo .'</h3>', '<div class="text-muted">'. $hometext .'</div>', $bodytext, $notes);

    echo '
    </div>';
    echo '
    <form id="editstory" action="'. site_url('admin.php') .'" method="post" name="adminForm">
        <div class="mb-3 row">
            <label class="col-sm-4 col-form-label" for="subject">'. adm_translate("Titre") .'</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="subject" name="subject" value="'. $subject .'" maxlength="255" required="required" />
                <span class="help-block text-end" id="countcar_subject"></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-sm-4 col-form-label" for="topic">'. adm_translate("Sujet") .'</label>
            <div class="col-sm-8">
                <select class="form-select" id="topic" name="topic">';

    if ($radminsuper) {
        echo '<option value="">'. adm_translate("Tous les Sujets") .'</option>';
    }

    $toplist = DB::table('topics')->select('topicid', 'topictext', 'topicadmin')->orderBy('topictext')->get();

    foreach ($toplist as $list) {
        $affiche = false;

        if ($radminsuper) {
            $affiche = true;
        } else {
            $topicadminX = explode(',', $list['topicadmin']);
            
            for ($i = 0; $i < count($topicadminX); $i++) {
                if (trim($topicadminX[$i]) == $aid) {
                    $affiche = true;
                }
            }
        }

        if ($affiche) {
            $sel = $list['topicid'] == $list['topic'] ? 'selected="selected"' : '';
            echo '<option value="'. $topic['topicid'] .'" '. $sel .'>'. language::aff_langue($topics) .'</option>';
        }
    }

    echo '
                </select>
            </div>
        </div>';

    SelectCategory($catid);

    puthome($storie['ihome']);

    echo '
        <div class="mb-3 row">
            <label class="col-form-label col-12" for="hometext">'. adm_translate("Texte d'introduction") .'</label>
            <div class="col-12">
                <textarea class="tin form-control" rows="25" id="hometext" name="hometext" >'. $hometext .'</textarea>
            </div>
        </div>';

    echo editeur::aff_editeur("hometext", "true");

    echo '
        <div class="mb-3 row">
            <label class="col-form-label col-12" for="bodytext">'. adm_translate("Texte complet") .'</label>
            <div class="col-12">
                <textarea class="tin form-control" rows="25" id="bodytext" name="bodytext" >'. $bodytext .'</textarea>
            </div>
        </div>';

    echo editeur::aff_editeur("bodytext", "true");

    echo '
        <div class="mb-3 row">
            <label class="col-form-label col-12" for="notes">'. adm_translate("Notes") .'</label>
            <div class="col-12">
                <textarea class="tin form-control" rows="7" id="notes" name="notes" >'. $notes .'</textarea>
            </div>
        </div>';

    echo editeur::aff_editeur('notes', '');

    echo '
        <div class="mb-3 row">
            <label class="col-form-label col-sm-6" for="Cdate">'. adm_translate("Changer la date") .'?</label>
            <div class="col-sm-6 my-2">
                <div class="form-check">
                <input class="form-check-input" type="checkbox" id="Cdate" name="Cdate" value="true" />
                <label class="form-check-label" for="Cdate">'. adm_translate("Oui") .'</label>
                </div>
                <span class="small help-block">'. translate(date("l")) . date(" " . translate("dateinternal"), time() + ((int) Config::get('npds.gmt') * 3600)) .'</span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-6" for="Csid">'. adm_translate("Remettre cet article en première position ? : ") .'</label>
            <div class="col-sm-6 my-2">
                <div class="form-check">
                <input class="form-check-input" type="checkbox" id="Csid" name="Csid" value="true" />
                <label class="form-check-label" for="Csid">'. adm_translate("Oui") .'</label>
                </div>
            </div>
        </div>';

    if ($storie['date_finval'] != '') {
        $fd_pub = substr($storie['date_finval'], 0, 10);
        $fh_pub = substr($storie['date_finval'], 11, 5);
    } else {
        $fd_pub = (date("Y") + 99) .'-01-01';
        $fh_pub = '00:00';
    }

    post::publication(-1, $fd_pub, -1, $fh_pub, $epur);

    global $theme;
    echo '
        <input type="hidden" name="sid" value="'. $sid .'" />
        <input type="hidden" name="op" value="ChangeStory" />
        <input type="hidden" name="theme" value="'. $theme .'" />
        <div class="mb-3 row">
            <div class="col-12">
                <input class="btn btn-primary" type="submit" value="'. adm_translate("Modifier l'Article") .'" />
            </div>
        </div>
    </form>';

    $fv_parametres = '

    !###!
    mem_y.addEventListener("change", function (e) {
        if(e.target.checked) {
            choixgroupe.style.display="flex";
        }
    });
    mem_n.addEventListener("change", function (e) {
        if(e.target.checked) {
            choixgroupe.style.display="none";
        }
    });
    ';

    $arg1 = '
    var formulid = ["editstory"];
    inpandfieldlen("subject",255);
    const choixgroupe = document.getElementById("choixgroupe");
    const mem_y = document.querySelector("#mem_y");
    const mem_n = document.querySelector("#mem_n");
    mem_y.checked ? "" : choixgroupe.style.display="none" ;
    ';

    css::adminfoot('fv', $fv_parametres, $arg1, '');
}

/**
 * [deleteStory description]
 *
 * @param   int   $qid  [$qid description]
 *
 * @return  void
 */
function deleteStory(int $qid): void 
{
    $queue = DB::table('queue')->select('story', 'bodytext')->where('qid', $qid)->first();

    $artcomplet = $queue['story'] . $queue['bodytext'];
    $rechcacheimage = '#storage/cache/a[i|c|]\d+_\d+_\d+.[a-z]{3,4}#m';

    preg_match_all($rechcacheimage, $artcomplet, $cacheimages);

    foreach ($cacheimages[0] as $imagetodelete) {
        unlink($imagetodelete);
    }

    DB::table('queue')->where('qid', $qid)->delete();

    global $aid;
    logs::Ecr_Log("security", "deleteStoryfromQueue($qid) by AID : $aid", "");
}

/**
 * [removeStory description]
 *
 * @param   int   $sid  [$sid description]
 * @param   int   $ok   [$ok description]
 *
 * @return  void
 */
function removeStory(int $sid, int $ok = 0): void
{
    global $aid, $radminsuper;  

    if (($sid == '') or ($sid == '0')) {
        header('location: '. site_url('admin.php'));
    }

    $storie = DB::table('stories')->select('topic')->where('sid', $sid)->first();

    $affiche = false;

    $topic = DB::table('topics')->select('topicadmin', 'topicname')->where('topicid', $storie['topic'])->first();

    if ($radminsuper) {
        $affiche = true;
    } else {
        $topicadminX = explode(',', $topic['topicadmin']);
        
        for ($i = 0; $i < count($topicadminX); $i++) {
            if (trim($topicadminX[$i]) == $aid) {
                $affiche = true;
            }
        }
    }

    if (!$affiche) {
        header('location: '. site_url('admin.php'));
    }

    if ($ok) {

        $storie = DB::table('stories')->select('hometext', 'bodytext', 'notes')->where('sid', $sid)->first();

        $artcomplet = $storie['hometext'] . $storie['bodytext'] . $storie['notes'];
        $rechuploadimage = '#modules/upload/upload/a[i|c|]\d+_\d+_\d+.[a-z]{3,4}#m';

        preg_match_all($rechuploadimage, $artcomplet, $uploadimages);
        
        foreach ($uploadimages[0] as $imagetodelete) {
            unlink($imagetodelete);
        }

        DB::table('stories')->where('sid', $sid)->delete();

        // commentaires
        if (file_exists("modules/comments/config/article.conf.php")) {
            include("modules/comments/config/article.conf.php");

            DB::table('posts')->where('forum_id', $forum)->where('topic_id', $topic)->delete();
        }

        global $aid;
        logs::Ecr_Log('security', "removeStory ($sid, $ok) by AID : $aid", '');

        if (Config::get('npds.ultramode')) {
            news::ultramode();
        }

        Header('Location: '. site_url('admin.php'));
    } else {
        include("themes/default/header.php");;

        GraphicAdmin(manuel('newarticle'));

        echo '
        <div class="alert alert-danger">'. adm_translate("Etes-vous sûr de vouloir effacer l'Article N°") .' '. $sid .' '. adm_translate("et tous ses Commentaires ?") .'</div>
        <p class="">
            <a href="'. site_url('admin.php?op=RemoveStory&amp;sid='. $sid .'&amp;ok=1') .'" class="btn btn-danger" >
                '. adm_translate("Oui") .'
            </a>&nbsp;
            <a href="'. site_url('admin.php') .'" class="btn btn-secondary" >
                '. adm_translate("Non") .'
            </a>
        </p>';
        
        include("themes/default/footer.php");
    }
}

/**
 * [changeStory description]
 *
 * @param   int     $sid          [$sid description]
 * @param   string  $subject      [$subject description]
 * @param   string  $hometext     [$hometext description]
 * @param   string  $bodytext     [$bodytext description]
 * @param   int     $topic        [$topic description]
 * @param   string  $notes        [$notes description]
 * @param   int     $catid        [$catid description]
 * @param   int     $ihome        [$ihome description]
 * @param   string  $members      [$members description]
 * @param   int     $Mmembers     [$Mmembers description]
 * @param   int     $Cdate        [$Cdate description]
 * @param   int     $Csid         [$Csid description]
 * @param   string  $date_finval  [$date_finval description]
 * @param   int     $epur         [$epur description]
 * @param   int     $theme        [$theme description]
 * @param   int     $dd_pub       [$dd_pub description]
 * @param   int     $fd_pub       [$fd_pub description]
 * @param   int     $dh_pub       [$dh_pub description]
 * @param   int     $fh_pub       [$fh_pub description]
 *
 * @return  void
 */
function changeStory(int $sid, string $subject, string $hometext, string $bodytext, int $topic, string $notes, int $catid, int $ihome, string $members, int $Mmembers, int $Cdate, int $Csid, string $date_finval, int $epur, int $theme, int $dd_pub, int $fd_pub, int $dh_pub, int $fh_pub): void
{
    global $aid;

    $subject = stripslashes(str::FixQuotes(str_replace('"', '&quot;', $subject)));
    $hometext = stripslashes(str::FixQuotes($hometext));
    $bodytext = stripslashes(str::FixQuotes($bodytext));
    $notes = stripslashes(str::FixQuotes($notes));

    if (($members == 1) and ($Mmembers == '')) {
        $ihome = '-127';
    }

    if (($members == 1) and (($Mmembers > 1) and ($Mmembers <= 127))) {
        $ihome = $Mmembers;
    }

    if ($Cdate) {
        DB::table('stories')->where('sid', $sid)->update(array(
            'catid'         => $catid,
            'title'         => $subject,
            'hometext'      => $hometext,
            'bodytext'      => $bodytext,
            'topic'         => $topic,
            'notes'         => $notes,
            'ihome'         => $ihome,
            'time'          => 'now()',
            'date_finval'   => $date_finval,
            'auto_epur'     => $epur,
            'archive'       => 0,
        ));
    } else {
        DB::table('stories')->where('sid', $sid)->update(array(
            'catid'         => $catid,
            'title'         => $subject,
            'hometext'      => $hometext,
            'bodytext'      => $bodytext,
            'topic'         => $topic,
            'notes'         => $notes,
            'ihome'         => $ihome,
            'date_finval'   => $date_finval,
            'auto_epur'     => $epur,
        ));
    }

    if ($Csid) {
        DB::table('stories')->where('sid', $sid)->update(array(
            'hometext'       => '<i class=\"fa fa-thumb-tack fa-2x me-2 text-muted\"></i>'. $hometext,
        ));

        $storie = DB::table('stories')->select('sid')->orderBy('sid', 'desc')->first();

        $storie['sid']++;
        DB::table('stories')->where('sid', $sid)->update(array(
            'sid'       => $storie['sid'],
        ));

        // commentaires
        if (file_exists("modules/comments/config/article.conf.php")) {
            include("modules/comments/config/article.conf.php");

            DB::table('posts')->where('forum_id', $forum)->where('topic_id', $topic)->update(array(
                'topic_id'       => $storie['sid'],
            ));
        }

        $sid = $storie['sid'];
    }

    global $aid;
    logs::Ecr_Log('security', "changeStory($sid, $subject, hometext..., bodytext..., $topic, notes..., $catid, $ihome, $members, $Mmembers, $Cdate, $Csid, $date_finval,$epur,$theme) by AID : $aid", '');
    
    if (Config::get('npds.ultramode')) {
        news::ultramode();
    }

    // Cluster Paradise
    if (file_exists("modules/cluster-paradise/config/cluster-activate.php")) {
        include("modules/cluster-paradise/config/cluster-activate.php");
    }

    if (file_exists("modules/cluster-paradise/http/cluster-M.php")) {
        include("modules/cluster-paradise/http/cluster-M.php");
    }
    // Cluster Paradise

    // Réseaux sociaux
    if (file_exists('modules/npds_twi/http/npds_to_twi.php')) {
        include('modules/npds_twi/http/npds_to_twi.php');
    }

    if (file_exists('modules/npds_fbk/http/npds_to_fbk.php')) {
        include('modules/npds_twi/http/npds_to_fbk.php');
    }
    // Réseaux sociaux

    url::redirect_url("admin.php?op=EditStory&sid=$sid");
}

/**
 * [adminStory description]
 *
 * @return  void
 */
function adminStory(): void
{
    global $aid, $radminsuper;

    $f_meta_nom = 'adminStory';
    $f_titre = adm_translate("Nouvel Article");

    //==> controle droit
    admindroits($aid, $f_meta_nom);
    //<== controle droit

    include("themes/default/header.php");

    GraphicAdmin(manuel('newarticle'));
    adminhead($f_meta_nom, $f_titre);

    settype($hometext, 'string');
    settype($bodytext, 'string');
    settype($dd_pub, 'string');
    settype($fd_pub, 'string');
    settype($dh_pub, 'string');
    settype($fh_pub, 'string');
    settype($epur, 'integer');
    settype($ihome, 'integer');
    settype($sel, 'string');
    settype($topic, 'string');

    echo '
    <hr />
    <form id="storiesnewart" action="'. site_url('admin.php') .'" method="post" name="adminForm">
        <div class="mb-3 row">
            <label class="col-sm-4 col-form-label" for="subject">'. adm_translate("Titre") .'</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" name="subject" id="subject" value="" maxlength="255" required="required" />
                <span class="help-block text-end" id="countcar_subject"></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-sm-4 col-form-label" for="topic">'. adm_translate("Sujet") .'</label>
            <div class="col-sm-8">
            <select class="form-select" id="topic" name="topic">';

    //probablement ici aussi mettre les droits pour les gestionnaires de topics ??
    if ($radminsuper) {
        echo '<option value="">'. adm_translate("Sélectionner un Sujet") .'</option>';
    }

    $topics = DB::table('topics')->select('topicid', 'topictext', 'topicadmin')->orderBy('topictext')->get();

    foreach ($topics as $topic) {
        $affiche = false;
        
        if ($radminsuper) {
            $affiche = true;
        } else {
            $topicadminX = explode(',', $topic['topicadmin']);
            for ($i = 0; $i < count($topicadminX); $i++) {
                if (trim($topicadminX[$i]) == $aid) {
                    $affiche = true;
                }
            }
        }

        if ($affiche) {
            if ($topic['topicid'] == $topic) {
                $sel = 'selected="selected"';
            }

            echo '<option '. $sel .' value="'. $topic['topicid'] .'">'. language::aff_langue($topic['topics']) .'</option>';
            $sel = '';
        }
    }

    echo '
                </select>
            </div>
        </div>';

    $cat = 0;
    SelectCategory($cat);

    puthome($ihome);

    echo '
        <div class="mb-3 row">
            <label class="col-form-label col-12" for="hometext">'. adm_translate("Texte d'introduction") .'</label>
            <div class="col-12">
                <textarea class="tin form-control" rows="25" id="hometext" name="hometext">'. $hometext .'</textarea>
            </div>
        </div>';

    echo editeur::aff_editeur('hometext', '');

    echo '
        <div class="mb-3 row">
            <label class="col-form-label col-12" for="bodytext">'. adm_translate("Texte étendu") .'</label>
            <div class="col-12">
                <textarea class="tin form-control" rows="25" id="bodytext" name="bodytext" >'. $bodytext .'</textarea>
            </div>
        </div>';

    echo editeur::aff_editeur('bodytext', '');

    post::publication($dd_pub, $fd_pub, $dh_pub, $fh_pub, $epur);

    echo '
        <input type="hidden" name="author" value="'. $aid .'" />
        <input type="hidden" name="op" value="PreviewAdminStory" />
        <div class="mb-3 row">
            <div class="col-sm-12">
                <input class="btn btn-primary" type="submit" name="preview" value="'. adm_translate("Prévisualiser") .'" />
            </div>
        </div>
    </form>';

    $fv_parametres = '

    !###!
    mem_y.addEventListener("change", function (e) {
        if(e.target.checked) {
            choixgroupe.style.display="flex";
        }
    });
    mem_n.addEventListener("change", function (e) {
        if(e.target.checked) {
            choixgroupe.style.display="none";
        }
    });
    ';

    $arg1 = '
    var formulid = ["storiesnewart"];
    inpandfieldlen("subject",255);
    const choixgroupe = document.getElementById("choixgroupe");
    const mem_y = document.querySelector("#mem_y");
    const mem_n = document.querySelector("#mem_n");
    mem_y.checked ? "" : choixgroupe.style.display="none" ;
    ';

    css::adminfoot('fv', $fv_parametres, $arg1, '');
}

/**
 * [previewAdminStory description]
 *
 * @param   string  $subject   [$subject description]
 * @param   string  $hometext  [$hometext description]
 * @param   string  $bodytext  [$bodytext description]
 * @param   int     $topic     [$topic description]
 * @param   int     $catid     [$catid description]
 * @param   int     $ihome     [$ihome description]
 * @param   string  $members   [$members description]
 * @param   int     $Mmembers  [$Mmembers description]
 * @param   int     $dd_pub    [$dd_pub description]
 * @param   int     $fd_pub    [$fd_pub description]
 * @param   int     $dh_pub    [$dh_pub description]
 * @param   int     $fh_pub    [$fh_pub description]
 * @param   int     $epur      [$epur description]
 *
 * @return  void
 */
function previewAdminStory(string $subject, string $hometext, string $bodytext, int $topic, int $catid, int $ihome, string $members, int $Mmembers, int $dd_pub, int $fd_pub, int $dh_pub, int $fh_pub, int $epur): void
{
    global $aid, $radminsuper;

    $subject = stripslashes(str_replace('"', '&quot;', $subject));
    $hometext = stripslashes(image::dataimagetofileurl($hometext, 'storage/cache/ai'));
    $bodytext = stripslashes(image::dataimagetofileurl($bodytext, 'storage/cache/ac'));

    settype($sel, 'string');

    if ($topic < 1) {
        $topic = 1;
    }

    $affiche = false;

    $topic = DB::table('topics')->select('topictext', 'topicimage', 'topicadmin')->where('topicid', $topic)->first();

    if ($radminsuper) {
        $affiche = true;
    } else {
        $topicadminX = explode(',', $topic['topicadmin']);
        
        for ($i = 0; $i < count($topicadminX); $i++) {
            if (trim($topicadminX[$i]) == $aid) {
                $affiche = true;
            }
        }
    }

    if (!$affiche) {
        header('location: '. site_url('admin.php'));
    }

    $f_meta_nom = 'adminStory';
    $f_titre = adm_translate("Nouvel Article");

    //==> controle droit
    //   admindroits($aid,$f_meta_nom); // à voir l'intégration avec les droits sur les topics ...
    //<== controle droit

    $topiclogo = '<span class="badge bg-secondary float-end"><strong>'. language::aff_langue($topic['topictext']) .'</strong></span>';

    include("themes/default/header.php");;

    GraphicAdmin(manuel('newarticle'));
    adminhead($f_meta_nom, $f_titre); 

    echo '
    <hr />
    <h3>'. adm_translate("Prévisualiser l'Article") .'</h3>
    <form id="storiespreviswart" action="'. site_url('admin.php') .'" method="post" name="adminForm">
        <label class="col-form-label">'. adm_translate("Langue de Prévisualisation") .'</label> 
        '. language::aff_localzone_langue("local_user_language") .'
        <div class="card card-body mb-3">';

    if ($topic['topicimage'] !== '') {
        if (!$imgtmp = theme::theme_image('topics/'. $topic['topicimage'])) {
            $imgtmp = Config::get('npds.tipath') . $topic['topicimage'];
        }

        $timage = $imgtmp;

        if (file_exists($imgtmp)) {
            $topiclogo = '<img class="img-fluid " src="'. $timage .'" align="right" alt="" />';
        }
    }

    post::code_aff('<h3>'. $subject . $topiclogo .'</h3>', '<div class="text-muted">'. $hometext .'</div>', $bodytext, '');

    echo '
        </div>
            <div class="mb-3 row">
                <label class="col-sm-4 col-form-label" for="subject">'. adm_translate("Titre") .'</label>
                <div class="col-sm-8">
                <input class="form-control" type="text" name="subject" id="subject" value="'. $subject .'" maxlength="255" required="required" />
                <span class="help-block text-end" id="countcar_subject"></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-4 col-form-label" for="topic">'. adm_translate("Sujet") .'</label>
                <div class="col-sm-8">
                <select class="form-select" id="topic" name="topic">';

    if ($radminsuper) {
        echo '<option value="">'. adm_translate("Tous les Sujets") .'</option>';
    }

    $topics = DB::table('topics')->select('topicid', 'topictext', 'topicadmin')->orderBy('topictext')->get();

    foreach ($topics as $topic) {
        $affiche = false;
        
        if ($radminsuper) {
            $affiche = true;
        } else {
            $topicadminX = explode(',', $topic['topicadmin']);
            
            for ($i = 0; $i < count($topicadminX); $i++) {
                if (trim($topicadminX[$i]) == $aid) {
                    $affiche = true;
                }
            }
        }

        if ($affiche) {
            if ($topicid == $topic) {
                $sel = 'selected="selected"';
            }

            echo '<option '. $sel .' value="'. $topic['topicid'] .'">'. language::aff_langue($topic['topics']) .'</option>';
            $sel = '';
        }
    }

    echo '
                </select>
                </div>
            </div>';

    $cat = $catid; // ?????
    SelectCategory($catid);

    if (($members == 1) and ($Mmembers == '')) { 
        $ihome = '-127';
    }

    if (($members == 1) and (($Mmembers > 1) and ($Mmembers <= 127))) {
        $ihome = $Mmembers;
    }

    puthome($ihome);

    echo '
            <div class="mb-3 row">
                <label class="col-form-label col-12" for="hometext">'. adm_translate("Texte d'introduction") .'</label>
                <div class="col-12">
                <textarea class="tin form-control" rows="25" id="hometext" name="hometext">'. $hometext .'</textarea>
                </div>
            </div>';

    echo editeur::aff_editeur("hometext", "true");

    echo '
            <div class="mb-3 row">
                <label class="col-form-label col-12" for="bodytext">'. adm_translate("Texte étendu") .'</label>
                <div class="col-12">
                <textarea class="tin form-control" rows="25" id="bodytext" name="bodytext" >'. $bodytext .'</textarea>
                </div>
            </div>';

    echo editeur::aff_editeur('bodytext', '');

    post::publication($dd_pub, $fd_pub, $dh_pub, $fh_pub, $epur);

    echo '
        <div class="mb-3 row">
            <input type="hidden" name="author" value="'. $aid .'" />
            <div class="col-7">
                <select class="form-select" name="op">
                <option value="PreviewAdminStory" selected>'. adm_translate("Prévisualiser") .'</option>
                <option value="PostStory">'. adm_translate("Poster un Article Admin") .'</option>
                </select>
            </div>
            <div class="col-5">
                <input class="btn btn-primary" type="submit" value="'. adm_translate("Ok") .'" />
            </div>
        </div>
    </form>';

    $fv_parametres = '

    !###!
    mem_y.addEventListener("change", function (e) {
        if(e.target.checked) {
            choixgroupe.style.display="flex";
        }
    });
    mem_n.addEventListener("change", function (e) {
        if(e.target.checked) {
            choixgroupe.style.display="none";
        }
    });
    ';

    $arg1 = '
    var formulid = ["storiespreviswart"];
    inpandfieldlen("subject",255);
    const choixgroupe = document.getElementById("choixgroupe");
    const mem_y = document.querySelector("#mem_y");
    const mem_n = document.querySelector("#mem_n");
    mem_y.checked ? "" : choixgroupe.style.display="none" ;
    ';

    css::adminfoot('fv', $fv_parametres, $arg1, '');
}

settype($catid, 'integer');

switch ($op) {
    case 'EditCategory':
        EditCategory($catid);
        break;

    case 'DelCategory':
        DelCategory($cat);
        break;

    case 'YesDelCategory':
        YesDelCategory($catid);
        break;

    case 'NoMoveCategory':
        NoMoveCategory($catid, $newcat);
        break;

    case 'SaveEditCategory':
        SaveEditCategory($catid, $title);
        break;

    case 'AddCategory':
        AddCategory();
        break;

    case 'SaveCategory':
        SaveCategory($title);
        break;

    case 'DisplayStory':
        displayStory($qid);
        break;

    case 'PreviewAgain':
        previewStory($qid, $uid, $author, $subject, $hometext, $bodytext, $topic, $notes, $catid, $ihome, $members, $Mmembers, $dd_pub, $fd_pub, $dh_pub, $fh_pub, $epur);
        break;

    case 'PostStory':
        settype($notes, 'string');
        settype($date_debval, 'string');
        settype($date_finval, 'string');
        settype($qid, 'integer');
        settype($uid, 'string'); //

        if (!$date_debval) {
            $date_debval = $dd_pub .' '. $dh_pub .':01';
        }

        if (!$date_finval) {
            $date_finval = $fd_pub .' '. $fh_pub .':01';
        }

        if ($date_finval < $date_debval) {
            $date_finval = $date_debval;
        }

        $temp_new = mktime((int) substr($date_debval, 11, 2), (int) substr($date_debval, 14, 2), 0, (int) substr($date_debval, 5, 2), (int) substr($date_debval, 8, 2), (int) substr($date_debval, 0, 4));
        $temp = time();
        
        if ($temp > $temp_new) {
            postStory("pub_immediate", $qid, $uid, $author, $subject, $hometext, $bodytext, $topic, $notes, $catid, $ihome, $members, $Mmembers, $date_debval, $date_finval, $epur);
        } else {
            postStory("pub_automated", $qid, $uid, $author, $subject, $hometext, $bodytext, $topic, $notes, $catid, $ihome, $members, $Mmembers, $date_debval, $date_finval, $epur);
        }
        break;

    case 'DeleteStory':
        deleteStory($qid);
        Header('Location: '. site_url('admin.php?op=submissions'));
        break;

    case 'EditStory':
        editStory($sid);
        break;

    case 'ChangeStory':
        settype($fd_pub, 'string');
        settype($fh_pub, 'string');
        settype($dd_pub, 'string');
        settype($dh_pub, 'string');
        settype($Cdate, 'string');
        settype($Csid, 'boolean');

        $date_finval = "$fd_pub $fh_pub:00";
        changeStory($sid, $subject, $hometext, $bodytext, $topic, $notes, $catid, $ihome, $members, $Mmembers, $Cdate, $Csid, $date_finval, $epur, $theme, $dd_pub, $fd_pub, $dh_pub, $fh_pub);
        break;

    case 'RemoveStory':
        settype($ok, 'string');
        removeStory($sid, $ok);
        break;

    case 'adminStory':
        adminStory();
        break;
        
    case 'PreviewAdminStory':
        previewAdminStory($subject, $hometext, $bodytext, $topic, $catid, $ihome, $members, $Mmembers, $dd_pub, $fd_pub, $dh_pub, $fh_pub, $epur);
        break;
}
