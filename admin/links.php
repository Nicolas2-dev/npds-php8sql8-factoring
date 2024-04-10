<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* This version name NPDS Copyright (c) 2001-2023 by Philippe Brunier   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\system\date\date;
use npds\system\logs\logs;
use npds\system\assets\css;
use npds\system\support\str;
use npds\system\mail\mailler;
use npds\system\config\Config;
use npds\system\support\editeur;
use npds\system\language\language;
use npds\system\support\facades\DB;

if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'links';
$f_titre = 'Liens';

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

/**
 * [links description]
 *
 * @return  void
 */
function links(): void
{
    global $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('weblinks'));
    adminhead($f_meta_nom, $f_titre);

    $numrows = DB::table('links_links')->select('lid')->count();

    $totalbrokenlinks = DB::table('links_modrequest')->select('lid')->where('brokenlink', 1)->count();

    $totalmodrequests = DB::table('links_modrequest')->select('lid')->where('brokenlink', 0)->count();

    echo '
    <hr />
    <h3>'. adm_translate("Liens")  .' <span class="">'. $numrows  .'</span></h3>';
    echo '[ <a href="admin.php?op=LinksListBrokenLinks">'. adm_translate("Soumission de Liens brisés")  .' ('. $totalbrokenlinks  .')</a> -
    <a href="admin.php?op=LinksListModRequests">'. adm_translate("Proposition de modifications de Liens")  .' ('. $totalmodrequests  .')</a> ]';

    $links_newlink = DB::table('links_newlink')
                ->select('lid', 'cid', 'sid', 'title', 'url', 'description', 'name', 'email', 'submitter')
                ->orderBy('lid', 'ASK')
                ->limit(1)
                ->offset(0)
                ->first();

    $adminform = '';

    if ($links_newlink > 0) {
        $adminform = 'adminForm';

        echo '
        <hr />
        <h3>'. adm_translate("Liens en attente de validation")  .'</h3>
        <form action="admin.php" method="post" name="'. $adminform  .'" id="linksattente">';
        echo
        adm_translate("Lien N° : ")  .'<b>'. $links_newlink['lid']  .'</b> - '. adm_translate("Auteur")  .' : '. $links_newlink['submitter']  .' <br /><br />
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4 " for="titleenattente">'. adm_translate("Titre de la page")  .'</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="titleenattente" name="title" value="'. $links_newlink['title']  .'" maxlength="100" required="required"/>
                <span class="help-block text-end"><span id="countcar_titleenattente"></span></span>

            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4 " for="urlenattente">'. adm_translate("URL de la Page")  .'</label>
            <div class="col-sm-8">
                <div class="input-group">
                <span class="input-group-text">
                    <a href="'. $links_newlink['url']  .'" target="_blank"><i class="fas fa-external-link-alt fa-lg"></i></a>
                </span>
                <input class="form-control" id="urlenattente" type="url" name="url" value="'. $links_newlink['url']  .'" maxlength="255" required="required" />
                </div>
                <span class="help-block text-end"><span id="countcar_urlenattente"></span></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-12 " for="xtextenattente">'. adm_translate("Description")  .'</label>
            <div class="col-sm-12">
                <textarea class="tin form-control" id="xtextenattente" name="xtext" rows="10">'. $links_newlink['description']  .'</textarea>
            </div>
        </div>
        '. editeur::aff_editeur('xtext', '')  .'
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4 " for="nameenattente">'. adm_translate("Nom")  .'</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="nameenattente" name="name" maxlength="100" value="'. $links_newlink['name']  .'" />
                <span class="help-block text-end"><span id="countcar_nameenattente"></span></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4 " for="emailenattente">'. adm_translate("E-mail")  .'</label>
            <div class="col-sm-8">
                <input class="form-control" type="email" id="emailenattente" name="email" maxlength="100" value="'. $links_newlink['email']  .'">
                <span class="help-block text-end"><span id="countcar_emailenattente"></span></span>
            </div>
        </div>
        <input type="hidden" name="new" value="1">
        <input type="hidden" name="lid" value="'. $links_newlink['lid']  .'">
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4 " for="catenattente">'. adm_translate("Catégorie")  .'</label>
            <div class="col-sm-8">
                <select class="form-select" id="catenattente" name="cat">';

        $links_categories = DB::table('links_categories')
                                ->select('cid', 'title')
                                ->orderBy('title')
                                ->get();

        foreach($links_categories as $categorie) {

            $sel = (($links_newlink['cid'] == $categorie['cid'] and $links_newlink['sid'] == 0) ? 'selected="selected" ' : '');

            echo '<option value="'. $categorie['cid']  .'" '. $sel  .'>'. language::aff_langue($categorie['title'])  .'</option>';

            $links_subcategories = DB::table('links_subcategories')
                                    ->select('sid', 'title')
                                    ->where('cid', $categorie['cid'])
                                    ->orderBy('title')
                                    ->get();

            foreach($links_subcategories as $subcategories) {

                $sel = (($links_newlink['sid'] == $subcategories['sid']) ? 'selected="selected" ' : '');

                echo '<option value="'. $categorie['cid']  .'-'. $subcategories['sid']  .'" '. $sel  .'>'. language::aff_langue($categorie['title'])  .' / '. language::aff_langue($subcategories['title'])  .'</option>';
            }
        }

        echo '
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-8 ms-sm-auto">
                    <input type="hidden" name="submitter" value="'. $links_newlink['submitter']  .'">
                    <input type="hidden" name="op" value="LinksAddLink">
                    <input class="btn btn-primary" type="submit" value="'. adm_translate("Ajouter")  .'" />&nbsp;
                    <a class="btn btn-danger" href="admin.php?op=LinksDelNew&amp;lid='. $links_newlink['lid']  .'" >'. adm_translate("Effacer")  .'</a>
                </div>
            </div>
        </form>
            <script type="text/javascript">
            //<![CDATA[
                $(document).ready(function() {
                    inpandfieldlen("titleenattente",100);
                    inpandfieldlen("urlenattente",255);
                    inpandfieldlen("nameenattente",100);
                    inpandfieldlen("emailenattente",100);
                });
            //]]>
        </script>';
        // Fin de List
    }

    // Add a Link to Database
    $count_categorie = DB::table('links_categories')
                            ->select('cid', 'title')
                            ->get();

    if ($count_categorie > 0) {
        echo '
        <div class="card card-body mb-3">
        <h3>'. adm_translate("Ajouter un lien")  .'</h3>';

        if ($adminform == '') {
            echo '<form method="post" action="admin.php" id="addlink" name="adminForm">';
        } else {
            echo '<form method="post" action="admin.php" id="addlink">';
        }

        echo '
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4 " for="title">'. adm_translate("Titre de la page")  .'</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" name="title" id="title" maxlength="100" required="required" />
                <span class="help-block text-end"><span id="countcar_title"></span></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4 " for="url">'. adm_translate("URL de la Page")  .'</label>
            <div class="col-sm-8">
                <input class="form-control" type="url" name="url" id="url" maxlength="255" placeholder="http(s)://" required="required" />
                <span class="help-block text-end"><span id="countcar_url"></span></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="cat">'. adm_translate("Catégorie")  .'</label>
            <div class="col-sm-8">
                <select class="form-select" id="cat" name="cat" id="cat">';

        $links_categories = DB::table('links_categories')
                            ->select('cid', 'title')
                            ->orderBy('title')
                            ->get();

        foreach($links_categories as $categorie) {
            echo '<option value="'. $categorie['cid']  .'">'. language::aff_langue($categorie['title'])  .'</option>';

            $links_subcategories = DB::table('links_subcategories')
                                    ->select('sid', 'title')
                                    ->orderBy('title')
                                    ->get();

            foreach ($links_subcategories as $subcategories) {
                echo '<option value="'. $categorie['cid']  .'-'. $subcategories['sid']  .'">'. language::aff_langue($categorie['title'])  .' / '. language::aff_langue($subcategories['title'])  .'</option>';
            }
        }

        echo '
                </select>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="xtext">'. adm_translate("Description")  .'</label>
            <div class="col-sm-8">
                <textarea class="tin form-control" id="xtext" name="xtext" rows="6"></textarea>
            </div>
        </div>';

        if ($adminform == '') {
            echo editeur::aff_editeur('xtext', '');
        }

        echo '
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="name">'. adm_translate("Nom")  .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" name="name" id="name" maxlength="60" />
                    <span class="help-block text-end"><span id="countcar_name"></span></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4 " for="email">'. adm_translate("E-mail")  .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="email" name="email" id="email" maxlength="60" />
                    <span class="help-block text-end"><span id="countcar_email"></span></span>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-8 ms-sm-auto">
                    <input type="hidden" name="op" value="LinksAddLink">
                    <input type="hidden" name="new" value="0">
                    <input type="hidden" name="lid" value="0">
                    <button class="btn btn-primary col-12" type="submit"><i class="fa fa-plus-square fa-lg"></i>&nbsp;'. adm_translate("Ajouter une URL")  .'</button>
                </div>
            </div>
        </form>
        </div>';
    }

    // Add a Main category
    echo '
    <div class="card card-body mb-3">
        <h3>'. adm_translate("Ajouter une catégorie")  .'</h3>
        <form action="admin.php" method="post" id="linksaddcat">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4 " for="cattitle" >'. adm_translate("Nom")  .'</label>
                <div class="col-sm-8">
                <input class="form-control" type="text" id="cattitle" name="title" maxlength="100" required="required"/>
                <span class="help-block text-end"><span id="countcar_cattitle"></span></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4 " for="cdescription">'. adm_translate("Description")  .'</label>
                <div class="col-sm-8">
                <textarea class="form-control" id="cdescription" name="cdescription" rows="7"></textarea>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-8 ms-sm-auto">
                <input type="hidden" name="op" value="LinksAddCat">
                <button class="btn btn-primary col-12" type="submit"><i class="fa fa-plus-square fa-lg"></i>&nbsp;'. adm_translate("Ajouter une catégorie")  .'</button>
                </div>
            </div>
        </form>
    </div>';

    // Add a New Sub-Category
    $count_categorie = DB::table('links_categories')
                            ->select('*')
                            ->get();

    if ($count_categorie > 0) {
        echo '
        <div class="card card-body mb-3">
            <h3 class="mb-3">'. adm_translate("Ajouter une Sous-catégorie")  .'</h3>
            <form method="post" action="admin.php" id="linksaddsubcat">
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-4" for="subcattitle">'. adm_translate("Nom")  .'</label>
                    <div class="col-sm-8">
                    <input class="form-control" type="text" id="subcattitle" name="title" maxlength="100" required="required">
                    <span class="help-block text-end"><span id="countcar_subcattitle"></span></span>
                    </div>
                </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="cid">'. adm_translate("Catégorie")  .'</label>
                <div class="col-sm-8">
                <select class="form-select" name="cid">';

        $links_categories = DB::table('links_categories')
                            ->select('cid', 'title')
                            ->orderBy('title')
                            ->get();

        foreach ($links_categories as $categories) {
            echo '<option value="'. $categories['cid']  .'">'. language::aff_langue($categories['title'])  .'</option>';
        }

        echo '
                    </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-8 ms-sm-auto">
                    <input type="hidden" name="op" value="LinksAddSubCat">
                    <button class="btn btn-primary col-12" type="submit"><i class="fa fa-plus-square fa-lg"></i>&nbsp;'. adm_translate("Ajouter une Sous-catégorie")  .'</button>
                    </div>
                </div>
            </form>
        </div>';
    }

    // Modify Category
    $count_categorie = DB::table('links_categories')
                            ->select('*')
                            ->get();

    if ($count_categorie > 0) {

        echo '
        <div class="card card-body">
            <h3 class="mb-3">'. adm_translate("Modifier la Catégorie")  .'</h3>
            <form method="post" action="admin.php">
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-4" for="cat">'. adm_translate("Catégorie")  .'</label>
                    <div class="col-sm-8">
                    <select class="form-select" name="cat">';

        $links_categorie = DB::table('links_categories')
                            ->select('cid', 'title')
                            ->orderBy('title')
                            ->get();

        foreach($links_categorie as $categorie) {
            echo '<option value="'. $categorie['cid']  .'">'. language::aff_langue($categorie['title'])  .'</option>';

            $links_subcategories = DB::table('links_subcategories')
                                    ->select('sid', 'title')
                                    ->where('cid', $categorie['cid'])
                                    ->orderBy('title')
                                    ->get();

            foreach ($links_subcategories as $subcategories) {
                echo '<option value="'. $categorie['cid']  .'-'. $subcategories['sid']  .'">'. language::aff_langue($categorie['title'])  .' / '. language::aff_langue($subcategories['title'])  .'</option>';
            }
        }

        echo '
                        </select>
                    </div>
                    </div>
                <div class="mb-3 row">
                    <div class="col-sm-8 ms-sm-auto">
                    <input type="hidden" name="op" value="LinksModCat">
                    <button class="btn btn-primary col-12" type="submit"><i class="fa fa-edit fa-lg"></i>&nbsp;'. adm_translate("Editer une catégorie")  .'</button>
                    </div>
                </div>
            </form>
        </div>';
    }

    // Modify Links
    $count_links = DB::table('links_links')->select('lid')->count();

    echo '
    <hr />
    <h3 class="mb-3">'. adm_translate("Liste des liens")  .' <span class="badge bg-secondary float-end">'. $count_links  .'</span></h3>
    <table id="tad_link" data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-mobile-responsive="true" data-buttons-class="outline-secondary" data-icons="icons" data-icons-prefix="fa">
        <thead>
            <tr>
                <th class="n-t-col-xs-1" data-sortable="true" data-halign="center" data-align="right">ID</th>
                <th data-sortable="true" data-halign="center" >'. adm_translate('Titre')  .'</th>
                <th data-sortable="true" data-sorter="htmlSorter" data-halign="center" >URL</th>
                <th class="n-t-col-xs-2" data-halign="center" data-align="center" data-align="right">'. adm_translate('Fonctions')  .'</th>
            </tr>
        </thead>
        <tbody>';

    
    global $deja_affiches;
    settype($deja_affiches, "integer");

    // ne sert strictemen a rien !!!! not used ???????
    // if ($deja_affiches < 0) {
    //     $sens = -1;
    // } else {
    //     $sens = +1;
    // }

    $deja_affiches = abs($deja_affiches);
    
    $links_links = DB::table('links_links')
                            ->select('lid', 'title', 'url')
                            ->orderBy('lid', 'asc')
                            ->limit(Config::get('link.rupture'))
                            ->offset($deja_affiches)
                            ->get();

    foreach($links_links as $link) {
        echo '
            <tr>
                <td>'. $link['lid']  .'</td>
                <td>'. $link['title']  .'</td>
                <td><a href="'. $link['url']  .'" target="_blank">'. $link['url']  .'</a></td>
                <td>
                <a href="admin.php?op=LinksModLink&amp;lid='. $link['lid']  .'" ><i class="fas fa-edit fa-lg"></i></a>
                <a href="admin.php?op=LinksDelLink&amp;lid='. $link['lid']  .'" class="text-danger"><i class="fas fa-trash fa-lg ms-3"></i></a>
                </td>
            </tr>';
    }

    echo '
        </tbody>
    </table>';

    $deja_affiches_plus = $deja_affiches + Config::get('link.rupture');
    $deja_affiches_moin = $deja_affiches - Config::get('link.rupture');

    echo '
    <ul class="pagination pagination-sm mt-3">
        <li class="page-item disabled"><a class="page-link" href="#">'. $count_links  .'</a></li>';

    if ($deja_affiches >= Config::get('link.rupture')) {
        echo '<li class="page-item"><a class="page-link" href="admin.php?op=suite_links&amp;deja_affiches=-'. $deja_affiches_moin  .'" >'. adm_translate("Précédent")  .'</a></li>';
    }

    if ($deja_affiches_plus < $count_links) {
        echo '<li class="page-item"><a class="page-link" href="admin.php?op=suite_links&amp;deja_affiches='. $deja_affiches_plus  .'" >'. adm_translate("Suivant")  .'</a></li>';
    }

    echo '</ul>';

    $arg1 = '
        var formulid = ["addlink","linksaddcat","linksaddsubcat"];
        inpandfieldlen("title",100);
        inpandfieldlen("url",255);
        inpandfieldlen("name",100);
        inpandfieldlen("email",100);
        inpandfieldlen("cattitle",100);
        inpandfieldlen("subcattitle",100);';

    css::adminfoot('fv', '', $arg1, '');
}

/**
 * [LinksModLink description]
 *
 * @param   int   $lid  [$lid description]
 *
 * @return  void
 */
function LinksModLink(int $lid): void
{
    global $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('weblinks'));
    adminhead($f_meta_nom, $f_titre);  

    $link = DB::table('links_links')->select('cid', 'sid', 'title', 'url', 'description', 'name', 'email', 'hits')->where('lid', $lid)->first();

    echo '
    <hr />
    <h3 class="mb-3">'. adm_translate("Modifier le lien")  .' - '. $lid  .'</h3>';

    $title = stripslashes($link['title']);
    $description = stripslashes($link['description']);

    echo '
    <form action="admin.php" method="post" name="adminForm" id="linksmodlink">
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4 " for="title">'. adm_translate("Titre de la page")  .'</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" name="title" id="title" value="'. $title  .'" maxlength="100" required="required" />
                <span class="help-block text-end"><span id="countcar_title"></span></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4 " for="url">'. adm_translate("URL de la Page")  .'</label>
            <div class="col-sm-8">
                <div class="input-group">
                <span class="input-group-text">
                    <a href="'. $link['url']  .'" target="_blank"><i class="fas fa-external-link-alt fa-lg"></i></a>
                </span>
                <input class="form-control" type="url" name="url" id="url" value="'. $link['url']  .'" maxlength="255" required="required" />
                </div>
                <span class="help-block text-end"><span id="countcar_url"></span></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4 " for="xtext">'. adm_translate("Description")  .'</label>
            <div class="col-sm-8">
                <textarea class="form-control tin" id="xtext" name="xtext" rows="10">'. $description  .'</textarea>
            </div>
        </div>';

    echo editeur::aff_editeur('xtext', '');

    echo '
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4 " for="name">'. adm_translate("Nom")  .'</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" name="name" id="name" maxlength="100" value="'. $link['name']  .'" />
                <span class="help-block text-end"><span id="countcar_name"></span></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4 " for="email">'. adm_translate("E-mail")  .'</label>
            <div class="col-sm-8">
                <input class="form-control" type="email" name="email" id="email" maxlength="100" value="'. $link['email']  .'" />
                <span class="help-block text-end"><span id="countcar_email"></span></span>
            </div>
        </div>
        <div class="mb-3">
            <div class="row">
                <label class="col-form-label col-sm-4 " for="hits">'. adm_translate("Nombre de Hits")  .'</label>
                <div class="col-sm-8">
                <input class="form-control" type="number" id="hits" name="hits" value="'. $link['hits']  .'" min="0" max="99999999999" />
                </div>
            </div>
        </div>
        <div class="mb-3 row">
            <input type="hidden" name="lid" value="'. $lid  .'" />
            <label class="col-form-label col-sm-4 " for="cat">'. adm_translate("Catégorie")  .'</label>
            <div class="col-sm-8">
                <select class="form-select" id="cat" name="cat">';

    $links_categories = DB::table('links_categories')->select('cid', 'title')->orderBy('title')->get();

    foreach($links_categories as $categorie) {

        $sel = (($link['cid'] == $categorie['cid'] and $link['sid'] == 0) ? "selected" : '');

        echo '<option value="'. $categorie['cid']  .'" '. $sel  .'>'. language::aff_langue($categorie['title'])  .'</option>';

        $links_subcategories = DB::table('links_subcategories')->select('sid', 'title')->where('cid', $categorie['cid'])->orderBy('title')->get();

        foreach($links_subcategories as $subcategories) {

            $sel = (($link['sid'] == $subcategories['sid']) ? 'selected' : '');

            echo '<option value="'. $categorie['cid']  .'-'. $subcategories['sid']  .'" $sel>'. language::aff_langue($categorie['title'])  .' / '. language::aff_langue($subcategories['title'])  .'</option>';
        }
    }

    echo '
                </select>
            </div>
        </div>
        <div class="mb-3 row">
            <div class="col-sm-8 ms-sm-auto">
                <input type="hidden" name="op" value="LinksModLinkS" />
                <button type="submit" class="btn btn-primary" ><i class="fa fa-check fa-lg"></i>&nbsp;'. adm_translate("Modifier")  .' </button>
                <a href="admin.php?op=LinksDelLink&amp;lid='. $lid  .'" class="btn btn-danger"><i class="fas fa-trash fa-lg"></i>&nbsp;'. adm_translate("Effacer")  .'</a>
            </div>
        </div>
    </form>';

    //Modify or Add Editorial
    $links_editorials = DB::table('links_editorials')->select('adminid', 'editorialtimestamp', 'editorialtext', 'editorialtitle')->where('linkid', $lid)->get();

    if (empty($links_editorials)) {
        echo '
        <h3 class="mb-3">'. adm_translate("Ajouter un Editorial")  .'</h3>
        <form action="admin.php" method="post" id="linksaddeditorial">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4 " for="editorialtitle">'. adm_translate("Titre")  .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" name="editorialtitle" id="editorialtitle" maxlength="100" />
                    <span class="help-block text-end"><span id="countcar_editorialtitle"></span></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4 " for="editorialtext">'. adm_translate("Texte complet")  .'</label>
                <div class="col-sm-8">
                    <textarea class="form-control" id="editorialtext" name="editorialtext" rows="10"></textarea>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-8 ms-sm-auto">
                    <input type="hidden" name="linkid" value="'. $lid  .'" />
                    <input type="hidden" name="op" value="LinksAddEditorial" />
                    <button class="btn btn-primary col-12" type="submit"><i class="fa fa-plus-square fa-lg"></i>&nbsp;'. adm_translate("Ajouter un Editorial")  .'</button>
                </div>
            </div>
        </form>';
    } else {
        foreach($links_editorials as $editorials) { 

            $editorialtitle = stripslashes($editorials['editorialtitle']);
            $editorialtext = stripslashes($editorials['editorialtext']);

            echo '<h3 class="mb-3">'. adm_translate("Modifier l'Editorial")  .'</h3> - '. adm_translate("Auteur")  .' : '. $editorials['adminid']  .' : '. date::formatTimeStamp($editorials['editorialtimestamp']);
            echo '<form action="admin.php" method="post" id="linksediteditorial">
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-4 " for="editorialtitle">'. adm_translate("Titre")  .'</label>
                    <div class="col-sm-8">
                        <input class="form-control" type="text" name="editorialtitle" id="editorialtitle" value="'. $editorialtitle  .'" maxlength="100" />
                        <span class="help-block text-end"><span id="countcar_editorialtitle"></span></span>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-4 " for="editorialtext">'. adm_translate("Texte complet")  .'</label>
                    <div class="col-sm-8">
                        <textarea class="form-control" id="editorialtext" name="editorialtext" rows="10">'. $editorialtext  .'</textarea>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-8 ms-sm-auto">
                        <input type="hidden" name="linkid" value="'. $lid  .'" />
                        <input type="hidden" name="op" value="LinksModEditorial" />
                        <button class="btn btn-primary" type="submit"><i class="fa fa-check fa-lg"></i>&nbsp;'. adm_translate("Modifier")  .'</button>
                        <a href="admin.php?op=LinksDelEditorial&amp;linkid='. $lid  .'" class="btn btn-danger"><i class="fas fa-trash fa-lg"></i>&nbsp;'. adm_translate("Effacer")  .'</a>
                        </div>
                </div>
            </form>';
        }
    }

    $fv_parametres = '  
        email: {
            validators: {
                emailAddress: {
                    message: "The value is not a valid email address"
                }
            }
        },
        title: {
            validators: {
                notEmpty: {
                    message: "pas vide."
                }
            }
        },
        url: {
            validators: {
                uri: {
                    allowLocal: false,
                    message: "valid url please.",
                    allowEmptyProtocol:false,
                }
            }
        }';

    $arg1 = '
        var formulid = ["linksmodlink","linkseditorial"];
        inpandfieldlen("title",100);
        inpandfieldlen("url",255);
        inpandfieldlen("editorialtitle",100);';

    css::adminfoot('fv', $fv_parametres, $arg1, '');
}

/**
 * [LinksListBrokenLinks description]
 *
 * @return  void
 */
function LinksListBrokenLinks(): void
{
    global $f_meta_nom, $f_titre;

    $totalbrokenlinks = DB::table('links_modrequest')->select('lid', 'modifysubmitter')->where('brokenlink', 1)->orderBy('requestid')->get();

    if ($totalbrokenlinks == 0) {
        header("location: admin.php?op=links");
    }

    include("themes/default/header.php");

    GraphicAdmin(manuel('weblinks'));
    adminhead($f_meta_nom, $f_titre);

    echo '
    <hr />
    <h3>'. adm_translate("Liens cassés rapportés par un ou plusieurs Utilisateurs")  .' <span class="badge bg-danger float-end">'. $totalbrokenlinks  .'</span></h3>
    <div class="blockquote">
        <i class="fas fa-trash fa-lg text-primary me-2"></i>'. adm_translate("Ignorer (Efface toutes les demandes pour un Lien donné)")  .'<br />
        <i class="fas fa-trash fa-lg text-danger me-2"></i>'. adm_translate("Effacer (Efface les Liens cassés et les avis pour un Lien donné)")  .'
    </div>';

    if ($totalbrokenlinks == 0) {
        echo '<div class="alert alert-success lead">'. adm_translate("Aucun lien brisé rapporté.")  .'</div>';
    } else {
        echo '
        <table id="tad_linkbrok" data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-mobile-responsive="true" data-buttons-class="outline-secondary" data-icons="icons" data-icons-prefix="fa">
        <thead>
            <tr>
                <th class="n-t-col-xs-4" data-sortable="true" data-halign="center" >'. adm_translate("Liens")  .'</th>
                <th data-sortable="true" data-halign="center" >'. adm_translate("Auteur")  .'</th>
                <th data-sortable="true" data-halign="center" >'. adm_translate("Propriétaire")  .'</th>
                <th class="n-t-col-xs-1" data-halign="center" data-align="center" >'. adm_translate("Ignorer")  .'</th>
                <th class="n-t-col-xs-1" data-halign="center" data-align="center" >'. adm_translate("Effacer")  .'</th>
            </tr>
        </thead>
        <tbody>';

        foreach($totalbrokenlinks as $brokenlinks) {

            $link = DB::table('links_links')->select('title', 'url', 'submitter')->where('lid', $lid)->first();

            if ($brokenlinks['modifysubmitter'] != Config::get('npds.anonymous')) {
                $user = DB::table('users')->select('email')->where('uname', $brokenlinks['modifysubmitter'])->first();
            }

            $user_owner = DB::table('users')->select('email')->where('uname', $link['owner'])->first();

            echo '
            <tr>
                <td><div>'. $link['title']  .'&nbsp;<span class="float-end"><a href="'. $link['url']  .'" target="_blank" ><i class="fas fa-external-link-alt fa-lg"></i></a></span></div></td>';
            
            if ($email == '') {
                echo '
                <td>'. $brokenlinks['modifysubmitter'];
            } else {
                echo '
                <td><div>'. $brokenlinks['modifysubmitter']  .'&nbsp;<span class="float-end"><a href="mailto:'. $user['email']  .'" ><i class="fa fa-at fa-lg"></i></a></span></div>';
            }

            echo '</td>';
            
            if ($user_owner['email'] == '') {
                echo '
                <td>'. $link['owner'];
            } else {
                echo '
                <td><div>'. $link['owner']  .'&nbsp;<span class="float-end"><a href="mailto:'. $user_owner['email']  .'"><i class="fa fa-at fa-lg"></i></a></span></div>';
            }

            echo '
                </td>
                <td><a href="admin.php?op=LinksIgnoreBrokenLinks&amp;lid='. $brokenlinks['lid']  .'" ><i class="fas fa-trash fa-lg" title="'. adm_translate("Ignorer (Efface toutes les demandes pour un Lien donné)")  .'" data-bs-toggle="tooltip" data-bs-placement="left"></i></a></td>
                <td><a href=admin.php?op=LinksDelBrokenLinks&amp;lid='. $brokenlinks['lid']  .'" ><i class="fas fa-trash text-danger fa-lg" title="'. adm_translate("Effacer (Efface les Liens cassés et les avis pour un Lien donné)")  .'" data-bs-toggle="tooltip" data-bs-placement="left"></i></a></td>
            </tr>';
        }
    }

    echo '
        </tbody>
    </table>';

    css::adminfoot('', '', '', '');
}

/**
 * [LinksDelBrokenLinks description]
 *
 * @param   int   $lid  [$lid description]
 *
 * @return  void
 */
function LinksDelBrokenLinks(int $lid): void 
{
    DB::table('links_modrequest')->where('lid', $lid)->delete();
    DB::table('links_links')->where('lid', $lid)->delete();

    global $aid;
    logs::Ecr_Log('security', "DeleteBrokensLinks($lid) by AID : $aid", '');

    Header("Location: admin.php?op=LinksListBrokenLinks");
}

/**
 * [LinksIgnoreBrokenLinks description]
 *
 * @param   int   $lid  [$lid description]
 *
 * @return  void
 */
function LinksIgnoreBrokenLinks(int $lid): void
{
    DB::table('links_modrequest')->where('lid', $lid)->where('brokenlink', 1)->delete();

    Header("Location: admin.php?op=LinksListBrokenLinks");
}

/**
 * [LinksListModRequests description]
 *
 * @return  void
 */
function LinksListModRequests(): void
{
    $links_modrequest = DB::table('links_modrequest')
                            ->select('requestid', 'lid', 'cid', 'sid', 'title', 'url', 'description', 'modifysubmitter')
                            ->where('brokenlink', 0)
                            ->orderBy('requestid')
                            ->get();

    if ($links_modrequest == 0) {
        header("location: admin.php?op=links");
    }

    include("themes/default/header.php");

    $x_mod = '';
    $x_ori = '';

    function clformodif($x_ori, $x_mod)
    {
        if ($x_ori != $x_mod) return ' class="text-danger" ';
    }

    GraphicAdmin(manuel('weblinks'));

    echo '<h3 class="my-3">'. adm_translate("Requête de modification d'un Lien Utilisateur")  .'<span class="badge bg-danger float-end">'. count($links_modrequest)  .'</span></h3>';
    
    foreach($links_modrequest as $modrequest ) {

        $link = DB::table('links_links')
            ->select('cid', 'sid', 'title', 'url', 'description', 'submitter')
            ->where('lid', )
            ->orderBy($modrequest['lid'])
            ->first();

        $categories = DB::table('links_categories')
            ->select('title')
            ->where('cid', $modrequest['cid'])
            ->first();

        $link_subcategorie = DB::table('links_subcategories')
            ->select('title')
            ->where('cid', $modrequest['cid'])
            ->where('sid', $modrequest['sid'])
            ->first();

        $link_categorie = DB::table('links_categories')
            ->select('title')
            ->where('cid', $link['cid'])
            ->first();

        $subcategories = DB::table('links_subcategories')
            ->select('title')
            ->where('cid', $link['cid'])
            ->where('sid', $link['sid'])
            ->first();

        $user = DB::table('users')
            ->select('email')
            ->where('uname', $modrequest['modifysubmitter'])
            ->first();

        $user_owner = DB::table('users')
            ->select('email')
            ->where('uname', $link['submitter'])
            ->first();

        $title = stripslashes($modrequest['title']);
        $description = stripslashes($modrequest['description']);

        if ($link['submitter'] == '') {
            $link['submitter'] = 'administration';
        }

        if ($sub_categories['title'] == '') {
            $sub_categories['title'] = '-----';
        }

        if ($link_subcategorie['title'] == '') {
            $link_subcategorie['title'] = '-----';
        }

        echo '
        <div class="card-deck-wrapper mt-3">
            <div class="card-deck">
            <div class="card card-body">
                <h4 class="card-title">'. adm_translate("Original")  .'</h4>
                <div class="card-text">
                    <strong>'. adm_translate("Description:")  .'</strong> <div>'. $link['description']  .'</div>
                    <strong>'. adm_translate("Titre :")  .'</strong> '. $link['title']  .'<br />
                    <strong>'. adm_translate("URL : ")  .'</strong> <a href="'. $link['url']  .'" target="_blank" >'. $link['url']  .'</a><br />';

        global $links_topic;
        if ($links_topic) {
            echo '<strong>'. adm_translate("Topic")  .' :</strong> '. $oritopic  .'<br />';
        }

        echo '<strong>'. adm_translate("Catégorie :")  .'</strong> '. $link_categorie['title']  .'<br />
                <strong>'. adm_translate("Sous-catégorie :")  .'</strong> '. $sub_categories['title']  .'<br />';

        if ($owneremail == '') {
            echo '<strong>'. adm_translate("Propriétaire")  .':</strong> <span'. clformodif($sub_categories['title'], $link_subcategorie['title'])  .'>'. $link['submitter']  .'</span><br />';
        }  else {
            echo '<strong>'. adm_translate("Propriétaire")  .':</strong> <a href="mailto:'. $user_owner['email']  .'">'. $link['submitter']  .'</a></span><br />';
        }

        echo '
            </div>
        </div>
        <div class="card card-body border-danger">
            <h4 class="card-title">'. adm_translate("Proposé")  .'</h4>
            <div class="card-text">
                <strong>'. adm_translate("Description:")  .'</strong><div'. clformodif($link['description'], $description)  .'>'. $description  .'</div>
                <strong>'. adm_translate("Titre :")  .'</strong> <span'. clformodif($link['title'], $title)  .'>'. $title  .'</span><br />
                <strong>'. adm_translate("URL : ")  .'</strong> <span'. clformodif($link['url'], $modrequest['url'])  .'><a href="'. $modrequest['url']  .'" target="_blank" >'. $modrequest['url']  .'</a></span><br />';
        
        global $links_topic;
        if ($links_topic) {
            echo '<strong>'. adm_translate("Topic")  .' :</strong> '. $oritopic  .'<br />';
        }
        
        echo '<strong>'. adm_translate("Catégorie :")  .'</strong> <span'. clformodif($link_categorie['title'], $categories['title'])  .'>'. $categories['title']  .'</span><br />
                <strong>'. adm_translate("Sous-catégorie :")  .'</strong> <span'. clformodif($sub_categories['title'], $link_subcategorie['title'])  .'>'. $link_subcategorie['title']  .'</span><br />';
        
        if ($user_owner['email'] == '') {
            echo '<strong>'. adm_translate("Propriétaire")  .' : </strong> <span>'. $link['submitter']  .'</span><br />';
        } else {
            echo '<strong>'. adm_translate("Propriétaire")  .' :  </strong> <span><a href="mailto:'. $user_owner['email']  .'" >'. $link['submitter']  .'</span><br />';
        }

        echo '
                </div>
            </div>
        </div>';

        if ($user['email'] == '') {
            echo adm_translate("Auteur")  .' : '. $modrequest['modifysubmitter'];
        } else {
            echo adm_translate("Auteur")  .' : <a href="mailto:'. $user['email']  .'">'. $modrequest['modifysubmitter']  .'</a>';
        }

        echo '
            <div class="mb-3">
                <a href="admin.php?op=LinksChangeModRequests&amp;requestid='. $modrequest['requestid']  .'" class="btn btn-primary btn-sm">'. adm_translate("Accepter")  .'</a>
                <a href="admin.php?op=LinksChangeIgnoreRequests&amp;requestid='. $modrequest['requestid']  .'" class="btn btn-secondary btn-sm">'. adm_translate("Ignorer")  .'</a>
            </div>
        </div>';
    }

    include("themes/default/footer.php");;
}

/**
 * [LinksChangeModRequests description]
 *
 * @param   int   $Xrequestid  [$Xrequestid description]
 *
 * @return  void
 */
function LinksChangeModRequests(int $Xrequestid): void 
{
    $links_modrequest = DB::table('links_modrequest')->select('requestid', 'lid', 'cid', 'sid', 'title', 'url', 'description')->where('requestid', $Xrequestid)->get();

    foreach($links_modrequest as $modrequest) {   

        DB::table('links_links')->where('lid', $modrequest['lid'])->update(array(
            'cid'           => $modrequest['cid'],
            'sid'           => $modrequest['sid'],
            'title'         => stripslashes($modrequest['title']),
            'url'           => $modrequest['url'],
            'description'   => stripslashes($modrequest['description']),
        ));
    }

    DB::table('links_modrequest')->where('requestid', $Xrequestid)->delete();

    global $aid;
    logs::Ecr_Log('security', "UpdateModRequestLinks($Xrequestid) by AID : $aid", '');

    Header("Location: admin.php?op=LinksListModRequests");
}

/**
 * [LinksChangeIgnoreRequests description]
 *
 * @param   int   $requestid  [$requestid description]
 *
 * @return  void
 */
function LinksChangeIgnoreRequests(int $requestid): void
{
    DB::table('links_modrequest')->where('requestid', $requestid)->delete();

    Header("Location: admin.php?op=LinksListModRequests");
}

/**
 * [LinksModLinkS description]
 *
 * @param   int     $lid    [$lid description]
 * @param   string  $title  [$title description]
 * @param   string  $url    [$url description]
 * @param   string  $xtext  [$xtext description]
 * @param   string  $name   [$name description]
 * @param   string  $email  [$email description]
 * @param   int     $hits   [$hits description]
 * @param   string  $cat    [$cat description]
 *
 * @return  void
 */
function LinksModLinkS(int $lid, string $title, string $url, string $xtext, string $name, string $email, int $hits, string $cat): void
{
    $cat = explode('-', $cat);

    if (!array_key_exists(1, $cat)) {
        $cat[1] = 0;
    }

    DB::table('links_links')->where('lid', $lid)->update(array(
        'cid'           => $cat[0],
        'sid'           => $cat[1],
        'title'         => stripslashes(str::FixQuotes($title)),
        'url'           => stripslashes(str::FixQuotes($url)),
        'description'   => stripslashes(str::FixQuotes($xtext)),
        'name'          => stripslashes(str::FixQuotes($name)),
        'email'         => stripslashes(str::FixQuotes($email)),
        'hits'          => $hits,
    ));

    global $aid;
    logs::Ecr_Log('security', "UpdateLinks($lid, $title) by AID : $aid", '');

    Header("Location: admin.php?op=links");
}

/**
 * [LinksDelLink description]
 *
 * @param   int   $lid  [$lid description]
 *
 * @return  void
 */
function LinksDelLink(int $lid): void
{
    DB::table('links_links')->where('lid', $lid)->delete();

    global $aid;
    logs::Ecr_Log('security', "DeleteLinks($lid) by AID : $aid", '');

    Header("Location: admin.php?op=links");
}

/**
 * [LinksModCat description]
 *
 * @param   string  $cat  [$cat description]
 *
 * @return  void
 */
function LinksModCat(string $cat): void
{
    global $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('weblinks'));
    adminhead($f_meta_nom, $f_titre);  

    $cat = explode('-', $cat);

    if (!array_key_exists(1, $cat)) {
        $cat[1] = 0;
    }

    if ($cat[1] == 0) {
        echo '
            <hr />
            <h3>'. adm_translate("Modifier la Catégorie")  .'</h3>';
        
        $categorie = DB::table('links_categories')->select('title', 'cdescription')->where('cid', $cat[0])->get();

        $cdescription = stripslashes($categorie['cdescription']);
        
        echo '
        <form action="admin.php" method="get" id="linksmodcat">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4 " for="title">'. adm_translate("Nom")  .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="title" name="title" value="'. $categorie['title']  .'" maxlength="255" required="required" />
                    <span class="help-block text-end"><span id="countcar_title"></span></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4 " for="cdescription">'. adm_translate("Description")  .'</label>
                <div class="col-sm-8">
                    <textarea class="form-control" id="cdescription" name="cdescription" rows="10" >'. $cdescription  .'</textarea>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-8 ms-sm-auto">
                    <input type="hidden" name="sub" value="0">
                    <input type="hidden" name="cid" value="'. $cat[0]  .'">
                    <input type="hidden" name="op" value="LinksModCatS">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-check fa-lg"></i>&nbsp;'. adm_translate("Modifier")  .'</button>
                    <a href="admin.php?op=LinksDelCat&amp;sub=0&amp;cid='. $cat[0]  .'" class="btn btn-danger"><i class="fas fa-trash fa-lg"></i>&nbsp;'. adm_translate("Effacer")  .'</a>
                </div>
            </div>
        </form>';
    } else {
        $categories = DB::table('links_categories')->select('title')->where('cid', $cat[0])->first();
        
        $subcategories = DB::table('links_subcategories')->select('title')->where('cid', $cat[1])->first();

        echo '
        <hr />
        <h3>'. adm_translate("Modifier la Catégorie")  .' </h3>
        <p class="lead">'. adm_translate("Nom de la Catégorie : ") . language::aff_langue($categories['ctitle'])  .'</p>
        <form action="admin.php" method="get" id="linksmodcat">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4 " for="title">'. adm_translate("Nom de la Sous-catégorie")  .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="title" name="title" value="'. $subcategories['title']  .'" maxlength="255" required="required">
                    <span class="help-block text-end"><span id="countcar_title"></span></span>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-8 offset-sm-4">
                    <input type="hidden" name="sub" value="1">
                    <input type="hidden" name="cid" value="'. $cat[0]  .'">
                    <input type="hidden" name="sid" value="'. $cat[1]  .'">
                    <input type="hidden" name="op" value="LinksModCatS">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-check fa-lg"></i>&nbsp;'. adm_translate("Modifier")  .'</button>
                    <a href="admin.php?op=LinksDelCat&amp;sub=1&amp;cid='. $cat[0]  .'&amp;sid='. $cat[1]  .'" class="btn btn-danger"><i class="fas fa-trash fa-lg"></i>&nbsp;'. adm_translate("Effacer")  .'</a>
                </div>
            </div>
        </form>';
    }

    $arg1 = '
        var formulid = ["linksmodcat"];
        inpandfieldlen("title",255);';

    css::adminfoot('fv', '', $arg1, '');
}

/**
 * [LinksModCatS description]
 *
 * @param   int     $cid           [$cid description]
 * @param   int     $sid           [$sid description]
 * @param   int     $sub           [$sub description]
 * @param   string  $title         [$title description]
 * @param   string  $cdescription  [$cdescription description]
 *
 * @return  void
 */
function LinksModCatS(int $cid, int $sid, int $sub, string $title, string $cdescription): void
{
    if ($sub == 0) {
        DB::table('links_categories')->where('cid', $cid)->update(array(
            'title'         => $title,
            'cdescription'  => $cdescription,
        ));

        global $aid;
        logs::Ecr_Log('security', "UpdateCatLinks($cid, $title) by AID : $aid", '');
    } else {
        DB::table('links_subcategories')->where('sid', $sid)->update(array(
            'title'       => $title,
        ));

        global $aid;
        logs::Ecr_Log('security', "UpdateSubCatLinks($cid, $title) by AID : $aid", '');
    }

    Header("Location: admin.php?op=links");
}

/**
 * [LinksDelCat description]
 *
 * @param   int   $cid  [$cid description]
 * @param   int   $sid  [$sid description]
 * @param   int   $sub  [$sub description]
 * @param   int   $ok   [$ok description]
 *
 * @return  void
 */
function LinksDelCat(int $cid, int $sid, int $sub, int $ok = 0): void
{
    if ($ok == 1) {
        if ($sub > 0) {
            DB::table('links_subcategories')->where('sid', $sid)->delete();
            DB::table('links_links')->where('sid', $sid)->delete();

            global $aid;
            logs::Ecr_Log('security', "DeleteSubCatLinks($sid) by AID : $aid", '');
        } else {
            DB::table('links_categories')->where('cid', $cid)->delete();
            DB::table('links_subcategories')->where('cid', $cid)->delete();
            DB::table('links_links')->where('cid', $cid)->where('sid', 0)->delete();

            global $aid;
            logs::Ecr_Log('security', "DeleteCatLinks($cid) by AID : $aid", '');
        }

        Header("Location: admin.php?op=links");
    } else {
        message_error('<div class="alert alert-danger">'. adm_translate("ATTENTION : Etes-vous sûr de vouloir effacer cette Catégorie et tous ses Liens ?")  .'</div><br />
        <a class="btn btn-danger me-2" href="admin.php?op=LinksDelCat&amp;cid='. $cid  .'&amp;sid='. $sid  .'&amp;sub='. $sub  .'&amp;ok=1" >'. adm_translate("Oui")  .'</a>');
    }
}

/**
 * [LinksDelNew description]
 *
 * @param   int   $lid  [$lid description]
 *
 * @return  void
 */
function LinksDelNew(int $lid): void 
{
    DB::table('links_newlink')->where('lid', $lid)->delete();

    global $aid;
    logs::Ecr_Log('security', "DeleteNewLinks($lid) by AID : $aid", '');

    Header("Location: admin.php?op=links");
}

/**
 * [LinksAddCat description]
 *
 * @param   string  $title         [$title description]
 * @param   string  $cdescription  [$cdescription description]
 *
 * @return  void
 */
function LinksAddCat(string $title, string $cdescription): void
{
    $links_categories = DB::table('links_categories')->select('cid')->where('title', $title)->first();

    if ($links_categories > 0) {
        message_error('<div class="alert alert-danger"><strong>'. adm_translate("Erreur : La Catégorie") ." $title ". adm_translate("existe déjà !")  .'</strong></div>');
    } else {
        DB::table('links_categories')->insert(array(
            'title'         => $title,
            'cdescription'  => $cdescription,
        ));

        global $aid;
        logs::Ecr_Log('security', "AddCatLinks($title) by AID : $aid", '');

        Header("Location: admin.php?op=links");
    }
}

/**
 * [LinksAddSubCat description]
 *
 * @param   int     $cid    [$cid description]
 * @param   string  $title  [$title description]
 *
 * @return  void
 */
function LinksAddSubCat(int $cid, string $title): void
{
    $links_subcategories = DB::table('links_subcategories')->select('cid')->where('title', $title)->where('cid', $cid)->first();

    if ($links_subcategories > 0) {
        message_error('<div class="alert alert-danger"><strong>'. adm_translate("Erreur : La Sous-catégorie") ." $title ". adm_translate("existe déjà !")  .'</strong></div>');
    } else {
        DB::table('links_subcategories')->insert(array(
            'cid'       => $cid,
            'title'     => $title,
        ));

        global $aid;
        logs::Ecr_Log('security', "AddSubCatLinks($title) by AID : $aid", '');

        Header("Location: admin.php?op=links");
    }
}

/**
 * [LinksAddEditorial description]
 *
 * @param   int     $linkid          [$linkid description]
 * @param   string  $editorialtitle  [$editorialtitle description]
 * @param   string  $editorialtext   [$editorialtext description]
 *
 * @return  void
 */
function LinksAddEditorial(int $linkid, string $editorialtitle, string $editorialtext): void
{
    global $aid; 

    DB::table('links_editorials')->insert(array(
        'linkid'                => $linkid,
        'adminid'               => $aid,
        'editorialtimestamp'    => 'now()',
        'editorialtext'         => stripslashes(str::FixQuotes($editorialtext)),
        'editorialtitle'        => $editorialtitle,
    ));

    logs::Ecr_Log('security', "AddEditorialLinks($linkid, $editorialtitle) by AID : $aid", '');

    message_error('<div class="alert alert-success"><strong>'. adm_translate("Editorial ajouté à la base de données")  .'</strong></div>');
}

/**
 * [LinksModEditorial description]
 *
 * @param   int     $linkid          [$linkid description]
 * @param   string  $editorialtitle  [$editorialtitle description]
 * @param   string  $editorialtext   [$editorialtext description]
 *
 * @return  void
 */
function LinksModEditorial(int $linkid, string $editorialtitle, string $editorialtext): void
{
    DB::table('links_editorials')->where('linkid', $linkid)->update(array(
        'editorialtext'       => stripslashes(str::FixQuotes($editorialtext)),
        'editorialtitle'      => $editorialtitle,
    ));

    global $aid;
    logs::Ecr_Log('security', "ModEditorialLinks($linkid, $editorialtitle) by AID : $aid", '');

    message_error('<div class="alert alert-success"><strong>'. adm_translate("Editorial modifié")  .'</strong></div>');
}

/**
 * [LinksDelEditorial description]
 *
 * @param   int   $linkid  [$linkid description]
 *
 * @return  void
 */
function LinksDelEditorial(int $linkid): void
{
    DB::table('links_editorials')->where('linkid', $linkid)->delete();

    global $aid;
    logs::Ecr_Log('security', "DeteteEditorialLinks($linkid) by AID : $aid", '');

    message_error('<div class="alert alert-success"><strong>'. adm_translate("Editorial supprimé de la base de données")  .'</strong></div>');
}

/**
 * [message_error description]
 *
 * @param   string  $ibid  [$ibid description]
 *
 * @return  void
 */
function message_error(string $ibid): void
{
    global $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('weblinks'));
    adminhead($f_meta_nom, $f_titre);

    echo '<hr />';
    echo $ibid;
    echo '<a href="admin.php?op=links" class="btn btn-secondary">'. adm_translate("Retour en arrière")  .'</a>';

    css::adminfoot('', '', '', '');
}

/**
 * [LinksAddLink description]
 *
 * @param   int     $new        [$new description]
 * @param   int     $lid        [$lid description]
 * @param   string  $title      [$title description]
 * @param   string  $url        [$url description]
 * @param   string  $cat        [$cat description]
 * @param   string  $xtext      [$xtext description]
 * @param   string  $name       [$name description]
 * @param   string  $email      [$email description]
 * @param   string  $submitter  [$submitter description]
 *
 * @return  void
 */
function LinksAddLink(int $new, int $lid, string $title, string $url, string $cat, string $xtext, string $name, string $email, string $submitter): void
{
    $links = DB::table('links_links')->select('url')->where('url', $url)->first();

    if ($links > 0) {
        message_error('<div class="alert alert-danger"><strong>'. adm_translate("Erreur : cette URL est déjà présente dans la base de données !")  .'</strong></div>');
    } else {
        if ($title == '') {
            message_error('<div class="alert alert-danger"><strong>'. adm_translate("Erreur : vous devez saisir un TITRE pour votre Lien !")  .'</strong></div>');
        }

        if ($url == '') {
            message_error('<div class="alert alert-danger"><strong>'. adm_translate("Erreur : vous devez saisir une URL pour votre Lien !")  .'</strong></div>');
        }

        if ($xtext == '') {
            message_error('<div class="alert alert-danger"><strong>'. adm_translate("Erreur : vous devez saisir une DESCRIPTION pour votre Lien !")  .'</strong></div>');
        }

        $cat = explode('-', $cat);
        
        if (!array_key_exists(1, $cat)) {
            $cat[1] = 0;
        }

        DB::table('links_links')->insert(array(
            'cid'               => $cat[0],
            'sid'               => $cat[1],
            'title'             => stripslashes(str::FixQuotes($title)),
            'url'               => stripslashes(str::FixQuotes($url)),
            'description'       => stripslashes(str::FixQuotes($xtext)),
            'date'              => 'now()',
            'name'              => stripslashes(str::FixQuotes($name)),
            'email'             => stripslashes(str::FixQuotes($email)),
            'hits'              => 0,
            'submitter'         => $submitter,
            'linkratingsummary' => 0,
            'totalvotes'        => 0,
            'totalcomments'     => 0,
            'topicid_card'      => 0,
        ));

        if ($new == 1) {
            DB::table('links_newlink')->where('lid', $lid)->delete();

            if ($email != '') {
                $nuke_url = Config::get('npds.nuke_url');
                
                $subject = html_entity_decode(adm_translate("Votre Lien"), ENT_COMPAT | ENT_HTML401, 'utf-8') ." : ". Config::get('npds.sitename');
                $message = adm_translate("Bonjour") ." $name :\n\n". adm_translate("Nous avons approuvé votre contribution à notre moteur de recherche.") ."\n\n". adm_translate("Titre de la page") ." : $title\n". adm_translate("URL de la Page : ") ."<a href=\"$url\">$url</a>\n". adm_translate("Description : ") ."$xtext\n". adm_translate("Vous pouvez utiliser notre moteur de recherche sur : ") ." <a href=\"$nuke_url/modules.php?ModPath=links&ModStart=links\">$nuke_url/modules.php?ModPath=links&ModStart=links</a>\n\n". adm_translate("Merci pour votre Contribution !") ."\n";
                $message .= Config::get('signature.message');
                
                mailler::send_email($email, $subject, $message, '', false, 'html', '');
            }
        }

        global $aid;
        logs::Ecr_Log('security', "AddLinks($title) by AID : $aid", '');

        message_error('<div class="alert alert-success"><strong>'. adm_translate("Nouveau Lien ajouté dans la base de données")  .'</strong></div>');
    }
}

settype($op, 'string');
settype($sid, 'integer');
settype($ok, 'integer');

switch ($op) {
    case 'links':
    case 'suite_links':
        links();
        break;

    case 'LinksDelNew':
        LinksDelNew($lid);
        break;

    case 'LinksAddCat':
        LinksAddCat($title, $cdescription);
        break;

    case 'LinksAddSubCat':
        LinksAddSubCat($cid, $title);
        break;

    case 'LinksAddLink':
        $submitter = isset($submitter) ? $submitter : '';

        LinksAddLink($new, $lid, $title, $url, $cat, $xtext, $name, $email, $submitter);
        break;

    case 'LinksAddEditorial':
        LinksAddEditorial($linkid, $editorialtitle, $editorialtext);
        break;

    case 'LinksModEditorial':
        LinksModEditorial($linkid, $editorialtitle, $editorialtext);
        break;

    case 'LinksDelEditorial':
        LinksDelEditorial($linkid);
        break;

    case 'LinksListBrokenLinks':
        LinksListBrokenLinks();
        break;

    case 'LinksDelBrokenLinks':
        LinksDelBrokenLinks($lid);
        break;

    case 'LinksIgnoreBrokenLinks':
        LinksIgnoreBrokenLinks($lid);
        break;

    case 'LinksListModRequests':
        LinksListModRequests();
        break;

    case 'LinksChangeModRequests':
        LinksChangeModRequests($requestid);
        break;

    case 'LinksChangeIgnoreRequests':
        LinksChangeIgnoreRequests($requestid);
        break;

    case 'LinksDelCat':
        LinksDelCat($cid, $sid, $sub, $ok);
        break;

    case 'LinksModCat':
        LinksModCat($cat);
        break;

    case 'LinksModCatS':
        LinksModCatS($cid, $sid, $sub, $title, $cdescription);
        break;
        
    case 'LinksModLink':
        LinksModLink($lid);
        break;

    case 'LinksModLinkS':
        LinksModLinkS($lid, $title, $url, $xtext, $name, $email, $hits, $cat);
        break;

    case 'LinksDelLink':
        LinksDelLink($lid);
        break;
}
