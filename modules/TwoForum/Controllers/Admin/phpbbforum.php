<?php
/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on Parts of phpBB                                              */
/*                                                                      */
/* NPDS Copyright (c) 2002-2022 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\support\assets\js;
use npds\support\logs\logs;
use npds\support\assets\css;
use npds\support\cache\cache;
use npds\support\forum\forum;
use npds\support\str;
use npds\system\support\facades\DB;

if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'ForumAdmin';
$f_titre = __d('two_forum', 'Gestion des forums');

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

include("auth.php");

/**
 * [ForumAdmin description]
 *
 * @return  void
 */
function ForumAdmin(): void
{
    global $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('forumcat'));
    adminhead($f_meta_nom, $f_titre);

    echo '
    <hr />
    <h3 class="mb-3">'. __d('two_forum', 'Catégories de Forum') .'</h3>
    <table data-toggle="table" data-search="true" data-show-toggle="true" data-mobile-responsive="true" data-buttons-class="outline-secondary" data-icons="icons" data-icons-prefix="fa">
        <thead>
            <tr>
                <th class="n-t-col-xs-2" data-sortable="true" data-halign="center" data-align="right">'. __d('two_forum', 'Index') .'&nbsp;</th>
                <th class="n-t-col-xs-5" data-sortable="true" data-halign="center">'. __d('two_forum', 'Nom') .'&nbsp;</th>
                <th class="n-t-col-xs-3" data-halign="center" data-align="right">'. __d('two_forum', 'Nombre de Forum(s)') .'&nbsp;</th>
                <th class="n-t-col-xs-2" data-halign="center" data-align="center">'. __d('two_forum', 'Fonctions') .'&nbsp;</th>
            </tr>
        </thead>
        <tbody>';

    $categories = DB::table('catagories')->select('cat_id', 'cat_title')->orderBy('cat_id')->get();

    foreach($categories as $categ) {    
        
        //$numbers = DB::table('forums')->select(DB::raw('COUNT(*) AS total'))->where('cat_id', $categ['cat_id'])->get();
        $numbers = DB::table('forums')->select('total')->where('cat_id', $categ['cat_id'])->count();

        echo '
            <tr>
                <td>'. $categ['cat_id'] .'</td>
                <td>'. StripSlashes($categ['cat_title']) .'</td>
                <td>'. $numbers .' <a href="'. site_url('admin.php?op=ForumGo&amp;cat_id='. $categ['cat_id']) .'"><i class="fa fa-eye fa-lg align-middle" title="'. __d('two_forum', 'Voir les forums de cette catégorie') .': '. StripSlashes($categ['cat_title']) .'." data-bs-toggle="tooltip" data-bs-placement="right"></i></a></td>
                <td><a href="'. site_url('admin.php?op=ForumCatEdit&amp;cat_id='. $categ['cat_id']) .'"><i class="fa fa-edit fa-lg" title="'. __d('two_forum', 'Editer') .'" data-bs-toggle="tooltip"></i></a><a href="'. site_url('admin.php?op=ForumCatDel&amp;cat_id='. $categ['cat_id'] .'&amp;ok=0') .'"><i class="fas fa-trash fa-lg text-danger ms-3" title="'. __d('two_forum', 'Effacer') .'" data-bs-toggle="tooltip" ></i></a></td>
            </tr>';
    }

    echo '
        </tbody>
    </table>
    <h3 class="my-3">'. __d('two_forum', 'Ajouter une catégorie') .'</h3>
    <form id="forumaddcat" action="'. site_url('admin.php') .'" method="post">
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="catagories">'. __d('two_forum', 'Nom') .'</label>
            <div class="col-sm-8">
                <textarea class="form-control" name="catagories" id="catagories" rows="3" required="required"></textarea>
            </div>
        </div>
        <div class="mb-3 row">
            <div class="col-sm-8 ms-sm-auto">
                <input type="hidden" name="op" value="ForumCatAdd" />
                <button class="btn btn-primary col-12" type="submit"><i class="fa fa-plus-square fa-lg"></i>&nbsp;'. __d('two_forum', 'Ajouter une catégorie') .'</button>
            </div>
        </div>
    </form>';

    $arg1 = '
    var formulid = ["forumaddcat"];';

    css::adminfoot('fv', '', $arg1, '');
}

/**
 * [ForumGo description]
 *
 * @param   int|string   $cat_id  [$cat_id description]
 *
 * @return  void
 */
function ForumGo(int|string $cat_id): void 
{
    global $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('forumcat'));
    adminhead($f_meta_nom, $f_titre);

    $categorie = DB::table('catagories')->select('cat_title')->where('cat_id', $cat_id)->first();

    $categorie_title = StripSlashes($categorie['cat_title']);

    echo '
    <hr />
    <h3 class="mb-3">'. __d('two_forum', 'Forum classé en') .' '. $categorie_title .'</h3>
    <table data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-show-columns="true" data-mobile-responsive="true" data-buttons-class="outline-secondary" data-icons="icons" data-icons-prefix="fa">
        <thead>
            <tr>
                <th class="n-t-col-xs-1" data-sortable="true" data-halign="center" data-align="right">'. __d('two_forum', 'Index') .'&nbsp;</th>
                <th data-sortable="true" data-halign="center">'. __d('two_forum', 'Nom') .'&nbsp;</th>
                <th data-sortable="true" data-halign="center">'. __d('two_forum', 'Modérateur(s)') .'&nbsp;</th>
                <th data-sortable="true" data-halign="center">'. __d('two_forum', 'Accès') .'&nbsp;</th>
                <th data-sortable="true" data-halign="center">'. __d('two_forum', 'Type') .'&nbsp;</th>
                <th data-sortable="true" data-halign="center">'. __d('two_forum', 'Mode') .'&nbsp;</th>
                <th class="n-t-col-xs-1" data-sortable="true" data-halign="center" data-align="center"><img class="n-smil" src="assets/images/forum/subject/07.png" alt="icon_pieces jointes" title="'. __d('two_forum', 'Attachement') .'" data-bs-toggle="tooltip"></th>
                <th data-sortable="true" data-halign="center" data-align="center">'. __d('two_forum', 'Fonctions') .'&nbsp;</th>
            </tr>
        </thead>
        <tbody>';

    $forums = DB::table('forums')
        ->select('forum_id', 'forum_name', 'forum_access', 'forum_moderator', 'forum_type', 'arbre', 'attachement', 'forum_index')
        ->where('cat_id', $cat_id)
        ->orderBy('forum_index, forum_id')
        ->get();

    foreach ($forums as $forum) {
        $moderator = str_replace(' ', ', ', forum::get_moderator($forum['forum_moderator']));
        
        echo '
            <tr>
                <td>'. $forum['forum_index'] .'</td>
                <td>'. $forum['forum_name'] .'</td>
                <td><i class="fa fa-balance-scale fa-lg fa-fw me-1"></i>'. $moderator .'</td>';
        
        switch ($forum['forum_access']) {
            case (0):
                echo '
                <td>'. __d('two_forum', 'Publication Anonyme autorisée') .'</td>';
                break;

            case (1):
                echo '
                <td>'. __d('two_forum', 'Utilisateur enregistré') .'</td>';
                break;

            case (2):
                echo '
                <td>'. __d('two_forum', 'Modérateurs') .'</td>';
                break;

            case (9):
                echo '
                <td>Forum '. __d('two_forum', 'Fermé') .'</td>';
                break;
        }

        if ($forum['forum_type'] == 0) {
            echo '<td>'. __d('two_forum', 'Public') .'</td>';
        } elseif ($forum['forum_type'] == 1) {
            echo '<td>'. __d('two_forum', 'Privé') .'</td>';
        } elseif ($forum['forum_type'] == 5) {
            echo '<td>PHP + '. __d('two_forum', 'Groupe') .'</td>';
        } elseif ($forum['forum_type'] == 6) {
            echo '<td>PHP</td>';
        } elseif ($forum['forum_type'] == 7) {
            echo '<td>'. __d('two_forum', 'Groupe') .'</td>';
        } elseif ($forum['forum_type'] == 8) {
            echo '<td>'. __d('two_forum', 'Texte étendu') .'</td>';
        } else {
            echo '<td>'. __d('two_forum', 'Caché') .'</td>';
        }

        if ($forum['arbre']) {
            echo '<td>'. __d('two_forum', 'Arbre') .'</td>';
        } else {
            echo '<td>'. __d('two_forum', 'Standard') .'</td>';
        }

        if ($forum['attachement']) {
            echo '<td class="text-danger">'. __d('two_forum', 'Oui') .'</td>';
        } else {
            echo '<td>'. __d('two_forum', 'Non') .'</td>';
        }

        echo '
                <td><a href="'. site_url('admin.php?op=ForumGoEdit&amp;forum_id='. $forum['forum_id'] .'&amp;ctg='. urlencode($categorie_title)) .'"><i class="fa fa-edit fa-lg" title="'. __d('two_forum', 'Editer') .'" data-bs-toggle="tooltip"></i></a><a href="'. site_url('admin.php?op=ForumGoDel&amp;forum_id='. $forum['forum_id'] .'&amp;ok=0') .'"><i class="fas fa-trash fa-lg text-danger ms-3" title="'. __d('two_forum', 'Effacer') .'" data-bs-toggle="tooltip" ></i></a></td>
            </tr>';
    }

    echo '
        </tbody>
    </table>
    <h3 class="my-3">'. __d('two_forum', 'Ajouter plus de Forum pour') .' : <span class="text-muted">'. $categorie_title .'</span></h3>
    <form id="fadaddforu" action="'. site_url('admin.php') .'" method="post">
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="forum_index">'. __d('two_forum', 'Index') .'</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="forum_index" name="forum_index" max="9999" />
                <span class="help-block text-end" id="countcar_forum_index"></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="forum_name">'. __d('two_forum', 'Nom du forum') .'</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="forum_name" name="forum_name" maxlength="150" required="required" />
                <span class="help-block">'. __d('two_forum', '(Redirection sur un forum externe : <.a href...)') .'<span class="float-end" id="countcar_forum_name"></span></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="forum_desc">'. __d('two_forum', 'Description') .'</label>
            <div class="col-sm-8">
                <textarea class="form-control" id="forum_desc" name="forum_desc" rows="5"></textarea>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="forum_mod">'. __d('two_forum', 'Modérateur(s)') .'</label>
            <div class="col-sm-8">
                <input id="l_forum_mod" class="form-control" type="text" id="forum_mod" name="forum_mod" required="required" />
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="forum_access">'. __d('two_forum', 'Niveau d\'accès') .'</label>
            <div class="col-sm-8">
                <select class="form-select" id="forum_access" name="forum_access">
                <option value="0">'. __d('two_forum', 'Publication Anonyme autorisée') .'</option>
                <option value="1">'. __d('two_forum', 'Utilisateur enregistré uniquement') .'</option>
                <option value="2">'. __d('two_forum', 'Modérateurs uniquement') .'</option>
                <option value="9">'. __d('two_forum', 'Fermé') .'</option>
                </select>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="forum_type">'. __d('two_forum', 'Type') .'</label>
            <div class="col-sm-8">
                <select class="form-select" id="forum_type" name="forum_type" >
                <option value="0">'. __d('two_forum', 'Public') .'</option>
                <option value="1">'. __d('two_forum', 'Privé') .'</option>
                <option value="5">PHP Script + '. __d('two_forum', 'Groupe') .'</option>
                <option value="6">PHP Script</option>
                <option value="7">'. __d('two_forum', 'Groupe') .'</option>
                <option value="8">'. __d('two_forum', 'Texte étendu') .'</option>
                <option value="9">'. __d('two_forum', 'Caché') .'</option>
                </select>
            </div>
        </div>
        <div class="mb-3 row d-none" id="the_multi_input">
            <label id="labmulti" class="col-form-label col-sm-4" for="forum_pass"></label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="forum_pass" name="forum_pass" />
                <span id="help_forum_pass" class="help-block"><span class="float-end" id="countcar_forum_pass"></span></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="arbre">'. __d('two_forum', 'Mode') .'</label>
            <div class="col-sm-8">
                <select class="form-select" id="arbre" name="arbre">
                <option value="0">'. __d('two_forum', 'Standard') .'</option>
                <option value="1">'. __d('two_forum', 'Arbre') .'</option>
                </select>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="attachement">'. __d('two_forum', 'Attachement') .'</label>
            <div class="col-sm-8">
                <select class="form-select" id="attachement" name="attachement">
                <option value="0">'. __d('two_forum', 'Non') .'</option>
                <option value="1">'. __d('two_forum', 'Oui') .'</option>
                </select>
            </div>
        </div>
        <div class="mb-3 row">
            <div class="col-sm-8 ms-sm-auto">
                <input type="hidden" name="ctg" value="'. $categorie_title .'" />
                <input type="hidden" name="cat_id" value="'. $cat_id .'" />
                <input type="hidden" name="op" value="ForumGoAdd" />
                <button class="btn btn-primary col-12" type="submit"><i class="fa fa-plus-square fa-lg"></i>&nbsp;'. __d('two_forum', 'Ajouter') .' </button>
            </div>
        </div>
        </form>';

    echo js::auto_complete_multi('modera', 'uname', 'users', 'l_forum_mod', 'WHERE uid<>1');

    $arg1 = '
    var formulid=["fadaddforu"];
    inpandfieldlen("forum_index",4);
    inpandfieldlen("forum_name",150);';

    $fv_parametres = '
    forum_pass:{
        validators: {
            regexp: {
                enabled: false,
                regexp: /^([2-9]|[1-9][0-9]|[1][0-2][0-6])$/,
                message: "2...126",
            },
            stringLength: {
                min: 8,
                max:60,
                message: "> 8 < 60"
            },
            notEmpty: {
                enabled: true,
                message: "Required",
            }
        },
    },
    forum_index: {
        validators: {
            regexp: {
                regexp:/^([0-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-9][0-9][0-9][0-9])$/,
                message: "0-9999"
            }
        }
    },
    !###!
    var 
    inpOri = $("#the_multi_input"),
    helptext = $("#help_forum_pass"),
    oo = $("#forum_type").val(),
    labelo = $("#labmulti");
    const form  = document.getElementById("fadaddforu");
    const impu = document.getElementById("forum_pass");
    switch (oo){
        case "1":
            fvitem.enableValidator("forum_pass","notEmpty").disableValidator("forum_pass","regexp").enableValidator("forum_pass","stringLength")
        break;
        case "5": case "7":
            fvitem.enableValidator("forum_pass","notEmpty").enableValidator("forum_pass","regexp").disableValidator("forum_pass","stringLength");
        break;
        case "8":
            fvitem.enableValidator("forum_pass","notEmpty").disableValidator("forum_pass","regexp").disableValidator("forum_pass","stringLength");
        break;
        default:
            fvitem.disableValidator("forum_pass","notEmpty").disableValidator("forum_pass","regexp").disableValidator("forum_pass","stringLength");
        break;
    }

    form.querySelector(\'[name="forum_type"]\').addEventListener("change", function(e) {
        switch (e.target.value) {
            case "1":
                inpOri.removeClass("d-none").addClass("d-flex");
                $("#forum_pass").val("").attr({type:"password", maxlength:"60", required:"required"});
                helptext.html("<span class=\"float-end\" id=\"countcar_forum_pass\"></span>")
                labelo.html("'. __d('two_forum', 'Mot de Passe') .'");
                fvitem.enableValidator("forum_pass","notEmpty").disableValidator("forum_pass","regexp").enableValidator("forum_pass","stringLength")
            break;
            case "5": case "7":
                inpOri.removeClass("d-none").addClass("d-flex");
                $("#forum_pass").val("").attr({type:"text", maxlength:"3", required:"required"});
                helptext.html("2...126<span class=\"float-end\" id=\"countcar_forum_pass\"></span>");
                labelo.html("'. __d('two_forum', 'Groupe ID') .'");
                fvitem.enableValidator("forum_pass","notEmpty").enableValidator("forum_pass","regexp").disableValidator("forum_pass","stringLength");
            break;
            case "8":
                inpOri.removeClass("d-none").addClass("d-flex");
                $("#forum_pass").val("").attr({type:"text", maxlength:"60", required:"required"});
                helptext.html("=> modules/sform/forum<span class=\"float-end\" id=\"countcar_forum_pass\"></span>")
                labelo.html("'. __d('two_forum', 'Fichier de formulaire') .'");
                fvitem.enableValidator("forum_pass","notEmpty").disableValidator("forum_pass","regexp").disableValidator("forum_pass","stringLength");
            break;
            default:
                inpOri.removeClass("d-flex").addClass("d-none");
                $("#forum_pass").val("");
                fvitem.disableValidator("forum_pass","notEmpty").disableValidator("forum_pass","regexp").disableValidator("forum_pass","stringLength")
            break;
        }
    });
    impu.addEventListener("input", function(e) {fvitem.revalidateField("forum_pass");});';

    css::adminfoot('fv', $fv_parametres, $arg1, '');
}

/**
 * [ForumGoEdit description]
 *
 * @param   int      $forum_id  [$forum_id description]
 * @param   string   $ctg       [$ctg description]
 *
 * @return  void
 */
function ForumGoEdit(int $forum_id, string $ctg): void
{
    global $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('forumcat'));
    adminhead($f_meta_nom, $f_titre);

    $forum = DB::table('forums')
        ->select('forum_id', 'forum_name', 'forum_desc', 'forum_access', 'forum_moderator', 'cat_id', 'forum_type', 'forum_pass', 'arbre', 'attachement', 'forum_index')
        ->where('forum_id', $forum_id)
        ->first();

    settype($sel0, 'string');
    settype($sel1, 'string');
    settype($sel2, 'string');
    settype($sel5, 'string');
    settype($sel6, 'string');
    settype($sel7, 'string');
    settype($sel8, 'string');
    settype($sel9, 'string');

    echo '
    <hr />
    <h3 class="mb-3">'. __d('two_forum', 'Editer') .' : <span class="text-muted">'. $forum['forum_name'] .'</span></h3>
    <form id="fadeditforu" action="'. site_url('admin.php') .'" method="post">
    <input type="hidden" name="forum_id" value="'. $forum['forum_id'] .'" />
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="forum_index">'. __d('two_forum', 'Index') .'</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="forum_index" name="forum_index" value="'. $forum['forum_index'] .'" required="required" />
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="forum_name">'. __d('two_forum', 'Nom du forum') .'</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="forum_name" name="forum_name" value="'. $forum['forum_name'] .'" required="required" />
                <span class="help-block">'. __d('two_forum', '(Redirection sur un forum externe : <.a href...)') .'<span class="float-end" id="countcar_forum_name"></span></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="forum_desc">'. __d('two_forum', 'Description') .'</label>
            <div class="col-sm-8">
                <textarea class="form-control" id="forum_desc" name="forum_desc" rows="5">'. $forum['forum_desc'] .'</textarea>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="forum_mod">'. __d('two_forum', 'Modérateur(s)') .'</label>';

    $moderator = str_replace(' ', ',', forum::get_moderator($forum['forum_moderator']));

    echo '
            <div class="col-sm-8">
                <input id="forum_mod" class="form-control" type="text" id="forum_mod" name="forum_mod" value="'. $moderator .'," />
            </div>
        </div>';
    echo '
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="forum_access">'. __d('two_forum', 'Niveau d\'accès') .'</label>
            <div class="col-sm-8">
                <select class="form-select" id="forum_access" name="forum_access">';

    if ($forum['forum_access'] == 0) {
        $sel0 = ' selected="selected"';
    }

    if ($forum['forum_access'] == 1) {
        $sel1 = ' selected="selected"';
    }

    if ($forum['forum_access'] == 2) {
        $sel2 = ' selected="selected"';
    }

    if ($forum['forum_access'] == 9) {
        $sel9 = ' selected="selected"';
    }

    echo '
                <option value="0"'. $sel0 .'>'. __d('two_forum', 'Publication Anonyme autorisée') .'</option>
                <option value="1"'. $sel1 .'>'. __d('two_forum', 'Utilisateur enregistré uniquement') .'</option>
                <option value="2"'. $sel2 .'>'. __d('two_forum', 'Modérateurs uniquement') .'</option>
                <option value="9"'. $sel9 .'>'. __d('two_forum', 'Fermé') .'</option>
                </select>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="cat_id">'. __d('two_forum', 'Catégories') .' </label>
            <div class="col-sm-8">
                <select class="form-select" id="cat_id" name="cat_id">';

    $categories = DB::table('catagories')->select('cat_id', 'cat_title')->get();

    foreach ($categories as $categ) {
        if ($categ['cat_id'] == $forum['cat_id']) {
            echo '<option value="'. $categ['cat_id'] .'" selected="selected">'. StripSlashes($categ['cat_title']) .'</option>';
        } else {
            echo '<option value="'. $categ['cat_id'] .'">'. StripSlashes($categ['cat_title']) .'</option>';
        }
    }

    echo '
                </select>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="forum_type">'. __d('two_forum', 'Type') .'</label>
            <div class="col-sm-8">
                <select class="form-select" id="forum_type" name="forum_type">';

    if ($forum['forum_type'] == 0) {
        $sel0 = ' selected="selected"';
    } else {
        $sel0 = '';
    }

    if ($forum['forum_type'] == 1) {
        $sel1 = ' selected="selected"';
    } else {
        $sel1 = '';
    }

    if ($forum['forum_type'] == 5) {
        $sel5 = ' selected="selected"';
    } else {
        $sel5 = '';
    }

    if ($forum['forum_type'] == 6) {
        $sel6 = ' selected="selected"';
    } else {
        $sel6 = '';
    }

    if ($forum['forum_type'] == 7) {
        $sel7 = ' selected="selected"';
    } else {
        $sel7 = '';
    }

    if ($forum['forum_type'] == 8) {
        $sel8 = ' selected="selected"';
    } else {
        $sel8 = '';
    }

    if ($forum['forum_type'] == 9) {
        $sel9 = ' selected="selected"';
    } else {
        $sel9 = '';
    }

    $lana = '';
    $dinp = 'd-none';
    $attinp = 'type="text" ';
    $helpinp = '';

    switch ($forum['forum_type']) {
        case '1':
            $dinp = 'd-flex';
            $lana = 'Mot de Passe';
            $attinp = ' type="password" maxlength="60"';
            $helpinp = '';
            break;

        case '5':
            $dinp = 'd-flex';
            $lana = 'Groupe ID';
            $attinp = ' type="text" maxlength="3"';
            $helpinp = '';
            break;

        case '7':
            $dinp = 'd-flex';
            $lana = 'Groupe ID';
            $attinp = ' type="text" maxlength="3"';
            $helpinp = '';
            break;

        case '8':
            $dinp = 'd-flex';
            $lana = 'Fichier de formulaire';
            $attinp = 'type="text" maxlength="60"';
            $helpinp = '=> modules/sform/forum';
            break;
    }

    echo '
                <option value="0"'. $sel0 .'>'. __d('two_forum', 'Public') .'</option>
                <option value="1"'. $sel1 .'>'. __d('two_forum', 'Privé') .'</option>
                <option value="5"'. $sel5 .'>PHP Script + '. __d('two_forum', 'Groupe') .'</option>
                <option value="6"'. $sel6 .'>PHP Script</option>
                <option value="7"'. $sel7 .'>'. __d('two_forum', 'Groupe') .'</option>
                <option value="8"'. $sel8 .'>'. __d('two_forum', 'Texte étendu') .'</option>
                <option value="9"'. $sel9 .'>'. __d('two_forum', 'Caché') .'</option>
                </select>
            </div>
        </div>
        <div class="mb-3 row '. $dinp .'" id="the_multi_input">
            <label id="labmulti" class="col-form-label col-sm-4" for="forum_pass">'. __d('two_forum', '$lana') .'</label>
            <div class="col-sm-8">
                <input class="form-control" '. $attinp .' id="forum_pass" name="forum_pass" value="'. $forum['forum_pass'] .'" />
                <span id="help_forum_pass" class="help-block">'. $helpinp .'<span class="float-end" id="countcar_forum_pass"></span></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="arbre">'. __d('two_forum', 'Mode') .'</label>
            <div class="col-sm-8">
                <select class="form-select" id="arbre" name="arbre">';

    if ($forum['arbre']) {
        echo '
                <option value="0">'. __d('two_forum', 'Standard') .'</option>
                <option value="1" selected="selected">'. __d('two_forum', 'Arbre') .'</option>';
    } else {
        echo '
                <option value="0" selected="selected">'. __d('two_forum', 'Standard') .'</option>
                <option value="1">'. __d('two_forum', 'Arbre') .'</option>';
    }

    echo '
                </select>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="attachement">'. __d('two_forum', 'Attachement') .'</label>
            <div class="col-sm-8">
                <select class="form-select" id="attachement" name="attachement">';

    if ($forum['attachement']) {
        echo '
                <option value="0">'. __d('two_forum', 'Non') .'</option>
                <option value="1" selected="selected">'. __d('two_forum', 'Oui') .'</option>';
    } else {
        echo '
                <option value="0" selected="selected">'. __d('two_forum', 'Non') .'</option>
                <option value="1">'. __d('two_forum', 'Oui') .'</option>';
    }    

    echo '
                </select>
            </div>
        </div>
        <input type="hidden" name="ctg" value="'. StripSlashes($ctg) .'" />
        <input type="hidden" name="op" value="ForumGoSave" />
        <div class="mb-3 row">
            <div class="col-sm-8 ms-sm-auto">
                <button class="btn btn-primary" type="submit">'. __d('two_forum', 'Sauver les modifications') .'</button>
            </div>
        </div>
    </form>';

    echo js::auto_complete_multi('modera', 'uname', 'users', 'forum_mod', 'WHERE uid<>1');

    $arg1 = '
    var formulid=["fadeditforu"];
    inpandfieldlen("forum_name",150);';

    $fv_parametres = '
    forum_pass:{
        validators: {
            regexp: {
                enabled: false,
                regexp: /^([2-9]|[1-9][0-9]|[1][0-2][0-6])$/,
                message: "2...126",
            },
            stringLength: {
                enabled: false,
                min: 8,
                max:60,
                message: "> 8 < 60"
            },
            notEmpty: {
                enabled: true,
                message: "Required",
            }
        },
    },
    forum_index: {
        validators: {
            regexp: {
                regexp:/^([0-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-9][0-9][0-9][0-9])$/,
                message: "0-9999"
            }
        }
    },
    !###!
    var 
    inpOri = $("#the_multi_input"),
    helptext = $("#help_forum_pass"),
    oo = $("#forum_type").val(),
    labelo = $("#labmulti");
    const form  = document.getElementById("fadeditforu");
    const impu = document.getElementById("forum_pass");
    switch (oo){
        case "1":
            fvitem.enableValidator("forum_pass","notEmpty").disableValidator("forum_pass","regexp").enableValidator("forum_pass","stringLength")
        break;
        case "5": case "7":
            fvitem.enableValidator("forum_pass","notEmpty").enableValidator("forum_pass","regexp").disableValidator("forum_pass","stringLength");
        break;
        case "8":
            fvitem.enableValidator("forum_pass","notEmpty").disableValidator("forum_pass","regexp").disableValidator("forum_pass","stringLength");
        break;
        default:
            fvitem.disableValidator("forum_pass","notEmpty").disableValidator("forum_pass","regexp").disableValidator("forum_pass","stringLength");
        break;
    }
    form.querySelector(\'[name="forum_type"]\').addEventListener("change", function(e) {
        switch (e.target.value) {
            case "1":
                inpOri.removeClass("d-none").addClass("d-flex");
                $("#forum_pass").val("").attr({type:"password", maxlength:"60", required:"required"});
                helptext.html("<span class=\"float-end\" id=\"countcar_forum_pass\"></span>")
                labelo.html("'. __d('two_forum', 'Mot de Passe') .'");
                fvitem.enableValidator("forum_pass","notEmpty").disableValidator("forum_pass","regexp").enableValidator("forum_pass","stringLength");
            break;
            case "5": case "7":
                inpOri.removeClass("d-none").addClass("d-flex");
                $("#forum_pass").val("").attr({type:"text", maxlength:"3", required:"required"});
                helptext.html("2...126<span class=\"float-end\" id=\"countcar_forum_pass\"></span>");
                labelo.html("'. __d('two_forum', 'Groupe ID') .'");
                fvitem.enableValidator("forum_pass","notEmpty").enableValidator("forum_pass","regexp").disableValidator("forum_pass","stringLength");
            break;
            case "8":
                inpOri.removeClass("d-none").addClass("d-flex");
                $("#forum_pass").val("").attr({type:"text", maxlength:"60", required:"required"});
                helptext.html("=> modules/sform/forum<span class=\"float-end\" id=\"countcar_forum_pass\"></span>")
                labelo.html("'. __d('two_forum', 'Fichier de formulaire') .'");
                fvitem.enableValidator("forum_pass","notEmpty").disableValidator("forum_pass","regexp").disableValidator("forum_pass","stringLength");
            break;
            default:
                inpOri.removeClass("d-flex").addClass("d-none");
                $("#forum_pass").val("");
                fvitem.disableValidator("forum_pass","notEmpty").disableValidator("forum_pass","regexp").disableValidator("forum_pass","stringLength");
            break;
        }
    });
    impu.addEventListener("input", function(e) {fvitem.revalidateField("forum_pass");});';

    css::adminfoot('fv', $fv_parametres, $arg1, '');
}

/**
 * [ForumCatEdit description]
 *
 * @param   int   $cat_id  [$cat_id description]
 *
 * @return  void
 */
function ForumCatEdit(int $cat_id): void
{
    global $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('forumcat'));
    adminhead($f_meta_nom, $f_titre);

    $categorie = DB::table('catagories')->select('cat_id', 'cat_title')->where('cat_id', $cat_id)->first();

    echo '
    <hr />
    <h3 class="mb-3">'. __d('two_forum', 'Editer la catégorie') .'</h3>
    <form id="phpbbforumedcat" action="'. site_url('admin.php') .'" method="post">
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="cat_id">ID</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" name="cat_id" id="cat_id" value="'. $categorie['cat_id'] .'" required="required" />
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="cat_title">'. __d('two_forum', 'Catégorie') .'</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="cat_title" name="cat_title" value="'. StripSlashes($categorie['cat_title']) .'" required="required" />
            </div>
        </div>
        <div class="mb-3 row">
            <input type="hidden" name="old_cat_id" value="'. $categorie['cat_id'] .'" />
            <input type="hidden" name="op" value="ForumCatSave" />
            <div class="col-sm-8 ms-sm-auto">
                <button class="btn btn-primary col-sm-12" type="submit"><i class="fa fa-check-square fa-lg"></i>&nbsp;'. __d('two_forum', 'Sauver les modifications') .'</button>
            </div>
        </div>
    </form>';

    $fv_parametres = '
    cat_id: {
        validators: {
            regexp: {
                regexp:/^(-|[1-9])(\d{0,10})$/,
                message: "0-9"
            },
            between: {
                min: -2147483648,
                max: 2147483647,
                message: "-2147483648 ... 2147483647"
            }
        }
    },';

    $arg1 = '
    var formulid=["phpbbforumedcat"];';

    css::adminfoot('fv', $fv_parametres, $arg1, '');
}

/**
 * [ForumCatSave description]
 *
 * @param   int     $old_catid  [$old_catid description]
 * @param   int     $cat_id     [$cat_id description]
 * @param   string  $cat_title  [$cat_title description]
 *
 * @return  void
 */
function ForumCatSave(int $old_catid, int $cat_id, string $cat_title): void 
{
    $r = DB::table('catagories')->where('cat_id', $old_catid)->update(array(
        'cat_id'       => $cat_id,
        'cat_title'    => AddSlashes($cat_title),
    ));
    
    if ($r) {
        DB::table('forums')->where('cat_id', $old_catid)->update(array(
            'cat_id'       => $cat_id,
        ));       
    }

    cache::Q_Clean();
    
    global $aid;
    logs::Ecr_Log("security", "UpdateForumCat($old_catid, $cat_id, $cat_title) by AID : $aid", '');

    Header('Location: '. site_url('admin.php?op=ForumAdmin'));
}

/**
 * [ForumGoSave description]
 *
 * @param   int     $forum_id      [$forum_id description]
 * @param   string  $forum_name    [$forum_name description]
 * @param   string  $forum_desc    [$forum_desc description]
 * @param   int     $forum_access  [$forum_access description]
 * @param   string  $forum_mod     [$forum_mod description]
 * @param   int     $cat_id        [$cat_id description]
 * @param   int     $forum_type    [$forum_type description]
 * @param   string  $forum_pass    [$forum_pass description]
 * @param   int     $arbre         [$arbre description]
 * @param   int     $attachement   [$attachement description]
 * @param   int     $forum_index   [$forum_index description]
 * @param   string  $ctg           [$ctg description]
 *
 * @return  void
 */
function ForumGoSave(int $forum_id, string $forum_name, string $forum_desc, int $forum_access, string $forum_mod, int $cat_id, int $forum_type, string $forum_pass, int $arbre, int $attachement, int $forum_index, string $ctg): void
{
    // il faut supprimer le dernier , à cause de l'auto-complete
    $forum_mod = rtrim(chop($forum_mod), ',');
    $moderator = explode(',', $forum_mod);

    $forum_mod = '';
    $error_mod = '';

    for ($i = 0; $i < count($moderator); $i++) {

        $forum_moderator = DB::table('users')->select('uid')->where('uname', trim($moderator[$i]))->first();

        if ($forum_moderator['uid'] != '') {
            $forum_mod .= $forum_moderator['uid'] .' ';

            DB::table('users_status')->where('uid', $forum_moderator['uid'])->update(array(
                'level'       => 2,
            ));
        } else {
            $error_mod .= $moderator[$i] .' ';
        }
    }

    if ($error_mod != '') {
        include("themes/default/header.php");
        
        GraphicAdmin(manuel('forumcat'));
        
        echo "<div><p align=\"center\">" . __d('two_forum', 'Le Modérateur sélectionné n\'existe pas.') . " : $error_mod<br />";
        echo "[ <a href=\"javascript:history.go(-1)\" >" . __d('two_forum', 'Retour en arrière') . "</a> ]</p></div>";
        
        include("themes/default/footer.php");
    } else {
        $forum_mod = str_replace(' ', ',', chop($forum_mod));
        
        if ($arbre > 1) {
            $arbre = 1;
        }
        
        if ($forum_pass) {
            if (($forum_type == 7) and ($forum_access == 0)) {
                $forum_access = 1;
            }

            DB::table('forums')->where('forum_id', $forum_id)->update(array(
                'forum_name'        => $forum_name,
                'forum_desc'        => stripslashes($forum_desc),
                'forum_access'      => $forum_access,
                'forum_moderator'   => $forum_mod,
                'cat_id'            => $cat_id,
                'forum_type'        => $forum_type,
                'forum_pass'        => $forum_pass,
                'arbre'             => $arbre,
                'attachement'       => $attachement,
                'forum_index'       => $forum_index,
            ));
        } else {
            DB::table('forums')->where('forum_id', $forum_id)->update(array(
                'forum_name'        => $forum_name,
                'forum_desc'        => stripslashes($forum_desc),
                'forum_access'      => $forum_access,
                'forum_moderator'   => $forum_mod,
                'cat_id'            => $cat_id,
                'forum_type'        => $forum_type,
                'forum_pass'        => '',
                'arbre'             => $arbre,
                'attachement'       => $attachement,
                'forum_index'       => $forum_index,
            ));
        }

        cache::Q_Clean();

        global $aid;
        logs::Ecr_Log("security", "UpdateForum($forum_id, $forum_name) by AID : $aid", '');

        Header('Location: '. site_url('admin.php?op=ForumGo&cat_id='. $cat_id));
    }
}

/**
 * [ForumCatAdd description]
 *
 * @param   string  $catagories  [$catagories description]
 *
 * @return  void
 */
function ForumCatAdd(string $catagories): void
{
    DB::table('catagories')->insert(array(
        'cat_title'       => $catagories,
    ));

    global $aid;
    logs::Ecr_Log('security', "AddForumCat($catagories) by AID : $aid", '');

    Header('Location: '. site_url('admin.php?op=ForumAdmin'));
}

/**
 * [ForumGoAdd description]
 *
 * @param   string  $forum_name    [$forum_name description]
 * @param   string  $forum_desc    [$forum_desc description]
 * @param   int     $forum_access  [$forum_access description]
 * @param   string  $forum_mod     [$forum_mod description]
 * @param   int     $cat_id        [$cat_id description]
 * @param   int     $forum_type    [$forum_type description]
 * @param   string  $forum_pass    [$forum_pass description]
 * @param   int     $arbre         [$arbre description]
 * @param   int     $attachement   [$attachement description]
 * @param   int     $forum_index   [$forum_index description]
 * @param   string  $ctg           [$ctg description]
 *
 * @return  void
 */
function ForumGoAdd(string $forum_name, string $forum_desc, int $forum_access, string $forum_mod, int $cat_id, int $forum_type, string $forum_pass, int $arbre, int $attachement, int $forum_index, string $ctg): void
{
    // il faut supprimer le dernier , à cause de l'auto-complete
    $forum_mod = rtrim(chop($forum_mod), ',');
    $moderator = explode(",", $forum_mod);

    $forum_mod = '';
    $error_mod = '';

    for ($i = 0; $i < count($moderator); $i++) {

        $forum_moderator = DB::table('users')->select('uid')->where('uname', trim($moderator[$i]))->first();

        if ($forum_moderator['uid'] != '') {
            $forum_mod .= $forum_moderator['uid'] .' ';

            DB::table('users_status')->where('uid', $forum_moderator['uid'])->update(array(
                'level'       => 2,
            ));
        } else {
            $error_mod .= $moderator[$i] .' ';
        }
    }

    if ($error_mod != '') {
        include("themes/default/header.php");

        GraphicAdmin(manuel('forumcat'));

        echo '
        <div class="alert alert-danger">
            <p>'. __d('two_forum', 'Le Modérateur sélectionné n\'existe pas.') .' : '. $error_mod .'</p>
            <a href="javascript:history.go(-1)" class="btn btn-secondary">'. __d('two_forum', 'Retour en arrière') .'</a>
        </div>';

        include("themes/default/footer.php");
    } else {
        if ($arbre > 1) {
            $arbre = 1;
        }

        DB::table('forums')->insert(array(
            'forum_name'        => $forum_name,
            'forum_desc'        => $forum_desc,
            'forum_access'      => $forum_access,
            'forum_moderator'   => str_replace(' ', ',', chop($forum_mod)),
            'cat_id'            => $cat_id,
            'forum_type'        => $forum_type,
            'forum_pass'        => $forum_pass,
            'arbre'             => $arbre,
            'attachement'       => $attachement,
            'forum_index'       => $forum_index,
        ));

        cache::Q_Clean();

        global $aid;
        logs::Ecr_Log("security", "AddForum($forum_name) by AID : $aid", "");

        Header('Location: '. site_url('admin.php?op=ForumGo&cat_id='.$cat_id));
    }
}

/**
 * [ForumCatDel description]
 *
 * @param   int   $cat_id  [$cat_id description]
 * @param   int   $ok      [$ok description]
 *
 * @return  void
 */
function ForumCatDel(int $cat_id, int $ok = 0): void
{
    global $f_meta_nom, $f_titre;

    if ($ok == 1) {
        $forums = DB::table('forums')->select('forum_id')->where('cat_id', $cat_id)->get();

        foreach ($forums as $forum) {
            DB::table('forumtopics')->where('forum_id', $forum['forum_id'])->delete();
            DB::table('forum_read')->where('forum_id', $forum['forum_id'])->delete();

            forum::control_efface_post("forum_npds", "", "", $forum['forum_id']);

            DB::table('posts')->where('forum_id', $forum['forum_id'])->delete();
        }

        DB::table('forums')->where('cat_id', $cat_id)->delete();
        DB::table('catagories')->where('cat_id', $cat_id)->delete();

        cache::Q_Clean();

        global $aid;
        logs::Ecr_Log("security", "DeleteForumCat($cat_id) by AID : $aid", "");

        Header('Location: '. site_url('admin.php?op=ForumAdmin'));
    } else {
        include("themes/default/header.php");

        GraphicAdmin(manuel('forumcat'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <div class="alert alert-danger">
            <p>'. __d('two_forum', 'ATTENTION :  êtes-vous sûr de vouloir supprimer cette Catégorie, ses Forums et tous ses Sujets ?') .'</p>
            <a href="'. site_url('admin.php?op=ForumCatDel&amp;cat_id='. $cat_id .'&amp;ok=1') .'" class="btn btn-danger me-2">'. __d('two_forum', 'Oui') .'</a>
            <a href="'. site_url('admin.php?op=ForumAdmin" class="btn btn-secondary') .'">'. __d('two_forum', 'Non') .'</a>
        </div>';

        css::adminfoot('', '', '', '');
    }
}

/**
 * [ForumGoDel description]
 *
 * @param   int   $forum_id  [$forum_id description]
 * @param   int   $ok        [$ok description]
 *
 * @return  void
 */
function ForumGoDel(int $forum_id, int $ok = 0): void
{
    global $f_meta_nom, $f_titre;

    if ($ok == 1) {
        DB::table('forumtopics')->where('forum_id', $forum_id)->delete();
        DB::table('forum_read')->where('forum_id', $forum_id)->delete();

        forum::control_efface_post('forum_npds', '', '', $forum_id);

        DB::table('forums')->where('forum_id', $forum_id)->delete();
        DB::table('posts')->where('forum_id', $forum_id)->delete();

        cache::Q_Clean();

        global $aid;
        logs::Ecr_Log('security', "DeleteForum($forum_id) by AID : $aid", '');

        Header('Location: '. site_url('admin.php?op=ForumAdmin'));
    } else {
        include('themes/default/header.php');

        GraphicAdmin(manuel('forumcat'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <div class="alert alert-danger">
            <p>'. __d('two_forum', 'ATTENTION :  êtes-vous certain de vouloir effacer ce Forum et tous ses Sujets ?') .'</p>
            <a class="btn btn-danger me-2" href="'. site_url('admin.php?op=ForumGoDel&amp;forum_id='. $forum_id .'&amp;ok=1') .'">'. __d('two_forum', 'Oui') .'</a>
            <a class="btn btn-secondary" href="'. site_url('admin.php?op=ForumAdmin') .'" >'. __d('two_forum', 'Non') .'</a>
        </div>';
        
        css::adminfoot('', '', '', '');
    }
}
