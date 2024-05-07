<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Major changes from ALAT 2004-2005                                    */
/* NPDS Copyright (c) 2002-2023 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\support\logs\logs;
use npds\support\assets\css;
use npds\support\auth\groupe;
use npds\support\str;
use npds\support\mail\mailler;
use npds\support\pixels\image;
use npds\support\editeur;
use npds\support\language\language;
use npds\system\support\facades\DB;

if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'sections';
$f_titre = adm_translate("Rubriques");

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

/**
 * [sousrub_select description]
 *
 * @param   int     $secid  [$secid description]
 *
 * @return  string
 */
function sousrub_select(int $secid): string
{
    global $radminsuper, $aid;
    
    $ok_pub = false;
    
    $tmp = '<select name="secid" class="form-select">';

    $result = DB::table('rubriques')->distinct()->select('rubid', 'rubname', 'ordre')->orderBy('ordre')->get();

    foreach ($result as $rubrique) {   
        
        $rubname = language::aff_langue($rubrique['rubname']); // not used ???
        
        $tmp .= '<optgroup label="'. language::aff_langue($rubrique['rubname']) .'">';

        if ($radminsuper == 1) {
            $result2 = DB::table('sections')->select('secid', 'secname', 'ordre')->where('rubid', $rubrique['rubid'])->orderBy('ordre')->get();
        } else {
            $result2 = DB::table('sections')
            ->distinct()
            ->select('sections.secid', 'sections.secname', 'sections.ordre')
            ->join('publisujet', 'sections.secid', '=', 'publisujet.secid2')
            ->where('sections.rubid', $rubrique['rubid'])
            ->where('publisujet.aid', $aid)
            ->orderBy('ordre')
            ->get();
        }

        foreach ($result2 as $section) {
            $secname = language::aff_langue($section['secname']);
            $secname = substr($secname, 0, 50);
            $tmp .= '<option value="'. $section['secid'] .'"';

            if ($section['secid'] == $secid) {
                $tmp .= ' selected="selected"';
            }

            $tmp .= '>'. $secname .'</option>';
            $ok_pub = true;
        }

        sql_free_result($result2);

        $tmp .= '</optgroup>';
    }

    $tmp .= '</select>';

    if (!$ok_pub) {
        ($tmp = '');
    }

    return $tmp;
}

/**
 * [droits_publication description]
 *
 * @param   int     $secid  [$secid description]
 *
 * @return  string
 */
function droits_publication(int $secid):  string|int 
{
    global $radminsuper, $aid;

    $droits = 0; // 3=mod - 4=delete

    if ($radminsuper != 1) {
        $result = DB::table('publisujet')->select('type')->where('secid2', $secid)->where('aid', $aid)->where('type', 'in', '(3,4)')->orderBy('type')->get();

        if ($result > 0) {
            foreach ($result as $publisujet) {
                $droits = $droits + $publisujet['type'];
            }
        }

    } else {
        $droits = 7;
    }

    return $droits;
}

/**
 * [sections description]
 *
 * @return  void
 */
function sections(): void
{
    global $aid, $radminsuper, $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('sections'));
    adminhead($f_meta_nom, $f_titre);

    $nb_rub = (($radminsuper == 1) 
        ? DB::table('rubriques')->select('rubid', 'rubname', 'enligne', 'ordre')->orderBy('ordre')->get()
        
        : DB::table('rubriques')
            ->distinct()
            ->select('rubriques.rubid', 'rubriques.rubname', 'rubriques.enligne', 'rubriques.ordre')
            ->join('sections', 'rubriques.rubid', '=', 'sections.rubid')
            ->join('publisujet', 'sections.secid', '=', 'publisujet.secid2')
            ->where('publisujetaid', $aid)
            ->orderBy('ordre')
            ->get()
    );

    echo '
    <hr />
    <ul class="list-group">';

    if ($nb_rub > 0) {
        echo '<li class="list-group-item list-group-item-action">
            <a href="'. site_url('admin.php?op=sections#ajouter publication') .'">
                <i class="fa fa-plus-square fa-lg me-2"></i>
                '. adm_translate("Ajouter une publication") .'
            </a></li>';
    }

    echo '<li class="list-group-item list-group-item-action">
        <a href="'. site_url('admin.php?op=new_rub_section&amp;type=rub') .'">
            <i class="fa fa-plus-square fa-lg me-2"></i>
            '. adm_translate("Ajouter une nouvelle Rubrique") .'
            </a>
        </li>';
    
    if ($nb_rub > 0) {
        echo '<li class="list-group-item list-group-item-action">
            <a href="'. site_url('admin.php?op=new_rub_section&amp;type=sec') .'" >
                <i class="fa fa-plus-square fa-lg me-2"></i>
                '. adm_translate("Ajouter une nouvelle Sous-Rubrique") .'
            </a>
        </li>';
    }

    if ($radminsuper == 1) {
        echo '
        <li class="list-group-item list-group-item-action">
            <a href="'. site_url('admin.php?op=ordremodule') .'">
                <i class="fa fa-sort-amount-up fa-lg me-2"></i>
                '. adm_translate("Changer l'ordre des rubriques") .'
            </a>
        </li>
        <li class="list-group-item list-group-item-action">
            <a href="#droits des auteurs">
                <i class="fa fa-user-edit fa-lg me-2"></i>
                '. adm_translate("Droits des auteurs") .'
                </a>
        </li>';
    }

    echo '<li class="list-group-item list-group-item-action">
            <a href="#publications en attente">
                <i class="fa fa-clock fa-lg me-2"></i>
                '. adm_translate("Publication(s) en attente de validation") .'
            </a>
        </li>
    </ul>';

    if ($nb_rub > 0) {
        $i = -1;

        echo '
        <hr />
        <h3 class="my-3">'. adm_translate("Liste des rubriques") .'</h3>';
        
        foreach ($nb_rub as $rubrique) {    
            $i++;

            if ($radminsuper == 1) {
                $href1 = '<a href="'. site_url('admin.php?op=rubriquedit&amp;rubid='. $rubrique['rubid']) .'" title="'. adm_translate("Editer la rubrique") .'" data-bs-toggle="tooltip" data-bs-placement="left"><i class="fa fa-edit fa-lg me-2"></i>&nbsp;';
                $href2 = '</a>';
                $href3 = '<a href="'. site_url('admin.php?op=rubriquedelete&amp;rubid='. $rubrique['rubid']) .'" class="text-danger" title="'. adm_translate("Supprimer la rubrique") .'" data-bs-toggle="tooltip" data-bs-placement="left"><i class="fas fa-trash fa-lg"></i></a>';
            } else {
                $href1 = '';
                $href2 = '';
                $href3 = '';
            }

            $rubname = language::aff_langue($rubrique['rubname']);

            if ($rubname == '') {
                $rubname = adm_translate("Sans nom");
            }

            if ($rubrique['enligne'] == 0) {
                $online = '<span class="badge bg-danger ms-1 p-2">'. adm_translate("Hors Ligne") .'</span>';
            } else if ($rubrique['enligne'] == 1) {
                $online = '<span class="badge bg-success ms-1 p-2">'. adm_translate("En Ligne") .'</span>';
            }

            echo '
            <div class="list-group-item bg-light py-2 lead">
                <a href="" class="arrow-toggle text-primary" data-bs-toggle="collapse" data-bs-target="#srub'. $i .'" >
                    <i class="toggle-icon fa fa-caret-down fa-lg"></i>
                </a>
                &nbsp;'. $rubname .' '. $online .' <span class="float-end">'. $href1 . $href2 . $href3 .'</span>
            </div>';

            if ($radminsuper == 1) {
                $result2= DB::table('sections')
                    ->distinct()
                    ->select('secid', 'secname', 'ordre')
                    ->where('rubid', $rubrique['rubid'])
                    ->orderBy('ordre')
                    ->get();
            
                } else {
                    $result2 = DB::table('sections')
                    ->distinct()
                    ->select('sections.secid', 'sections.secname', 'sections.ordre')
                    ->join('publisujet', 'sections.secid', '=', 'publisujet.secid2')
                    ->where('sections.rubid', $rubrique['rubid'])
                    ->where('publisujet.aid', $aid)
                    ->orderBy('ordre')
                    ->get();
            }

            if ($result2 > 0) {
                echo '
                <div id="srub'. $i .'" class=" mb-3 collapse ">
                <div class="list-group-item d-flex py-2">
                    <span class="badge bg-secondary me-2 p-2">'. count($result2) .'</span><strong class="">'. adm_translate("Sous-rubriques") .'</strong>';
                
                if ($radminsuper == 1) {
                    echo '<span class="ms-auto">
                        <a href="'. site_url('admin.php?op=ordrechapitre&amp;rubid='. $rubrique['rubid'] .'&amp;rubname='. $rubname) .'" title="'. adm_translate("Changer l'ordre des sous-rubriques") .'" data-bs-toggle="tooltip" data-bs-placement="left" >
                        <i class="fa fa-sort-amount-up fa-lg"></i></a></span>';
                }

                echo '</div>';

                foreach ($result2 as $section) {

                    $droit_pub = droits_publication($section['secid']);
                    $secname = language::aff_langue($section['secname']);

                    $result3 = DB::table('seccont')
                        ->select('artid', 'title')
                        ->where('secid', $section['secid'])
                        ->orderBy('ordre')
                        ->get();


                    echo '
                    <div class="list-group-item d-flex py-2">';
                        
                    echo ($result3 > 0) ?
                            '<a href="" class="arrow-toggle text-primary " data-bs-toggle="collapse" data-bs-target="#lst_sect_' .$section['secid'] .'" >
                                <i class="toggle-icon fa fa-caret-down fa-lg"></i></a>' :
                            '<span class=""> - </span>';

                    echo ' 
                        &nbsp;
                    '. $secname .'
                    <span class="ms-auto">
                        <a href="'. site_url('sections.php?op=listarticles&amp;secid='. $section['secid'] .'&amp;prev=1') .'" >
                            <i class="fa fa-eye fa-lg me-2 py-2"></i>
                        </a>';
                    
                    if ($droit_pub > 0 and $droit_pub != 4) {
                        // à revoir pas suffisant
                        echo '<a href="'. site_url('admin.php?op=sectionedit&amp;secid='. $section['secid']) .'" title="'. adm_translate("Editer la sous-rubrique") .'" data-bs-toggle="tooltip" data-bs-placement="left">
                            <i class="fa fa-edit fa-lg py-2 me-2"></i>
                        </a>';
                    }

                    if (($droit_pub == 7) or ($droit_pub == 4)) {
                        echo '<a href="'. site_url('admin.php?op=sectiondelete&amp;secid='. $section['secid']) .'" title="'. adm_translate("Supprimer la sous-rubrique") .'" data-bs-toggle="tooltip" data-bs-placement="left">
                            <i class="fas fa-trash fa-lg text-danger py-2"></i>
                        </a>';
                    }

                    echo '</span>
                    </div>';

                    if ($result3 > 0) {
 
                        echo '
                        <div id="lst_sect_'. $section['secid'] .'" class=" collapse">
                        <li class="list-group-item d-flex">
                        <span class="badge bg-secondary ms-4 p-2">'. count($result3) .'</span>&nbsp;<strong class=" text-capitalize">'. adm_translate("publications") .'</strong>';
                        
                        if ($radminsuper == 1) {
                            echo '<span class="ms-auto">
                                <a href="'. site_url('admin.php?op=ordrecours&secid='. $section['secid'] .'&amp;secname='. $secname) .'" title="'. adm_translate("Changer l'ordre des publications") .'" data-bs-toggle="tooltip" data-bs-placement="left">
                                    &nbsp;<i class="fa fa-sort-amount-up fa-lg"></i>
                                </a>
                            </span>';
                        }

                        echo '</li>';

                        foreach ($result3 as $seccont) {

                            if ($seccont['title'] == '') { 
                                $seccont['title'] = adm_translate("Sans titre");
                            }

                            echo '
                            <li class="list-group-item list-group-item-action d-flex">
                                <span class="ms-4">'. language::aff_langue($seccont['title']) .'</span>
                                <span class="ms-auto">
                                <a href="'. site_url('sections.php?op=viewarticle&amp;artid='. $seccont['artid'] .'&amp;prev=1') .'">
                                    <i class="fa fa-eye fa-lg"></i>
                                </a>&nbsp;';

                            // suffisant ?
                            if ($droit_pub > 0 and $droit_pub != 4) {
                                echo '<a href="'. site_url('admin.php?op=secartedit&amp;artid='. $seccont['artid']) .'" ><i class="fa fa-edit fa-lg"></i></a>&nbsp;';
                            }

                            if (($droit_pub == 7) or ($droit_pub == 4)) {
                                echo '<a href="'. site_url('admin.php?op=secartdelete&amp;artid='. $seccont['artid']) .'" class="text-danger" title="'. adm_translate("Supprimer") .'" data-bs-toggle="tooltip">
                                    <i class="far fa-trash fa-lg"></i>
                                </a>';
                            }

                            echo '
                            </span>
                        </li>';
                        }

                        echo '
                    </div>';
                    }
                }
                echo '</div>';
            }
        }

        echo '
        <hr />
        <h3 class="my-3">'. adm_translate("Editer une publication") .'</h3>
        <form action="'. site_url('admin.php') .'" method="post">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="artid">ID</label>
                <div class="col-sm-8">
                    <input type="number" class="form-control" id="artid" name="artid" min="0" max="999999999" />
                </div>
            </div>
            <input type="hidden" name="op" value="secartedit" />
        </form>';

        // Ajout d'une publication
        $autorise_pub = sousrub_select(0);

        if ($autorise_pub) {
            echo '
            <hr />
            <h3 class="mb-3"><a name="ajouter publication">'. adm_translate("Ajouter une publication") .'</a></h3>
            <form action="'. site_url('admin.php') .'" method="post" name="adminForm">
                <div class="mb-3 row">
                    <label class="col-form-label col-12" for="secid">'. adm_translate("Sous-rubrique") .'</label>
                    <div class="col-12">
                    '. $autorise_pub .'
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-form-label col-12" for="title">'. adm_translate("Titre") .'</label>
                    <div class=" col-12">
                        <textarea class="form-control" name="title" rows="2"></textarea>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-form-label col-12" for="content">'. adm_translate("Contenu") .'</label>
                    <div class=" col-12">
                        <textarea class="tin form-control" name="content" rows="30"></textarea>
                    </div>
                </div>
                '. editeur::aff_editeur('content', '') .'
                <input type="hidden" name="op" value="secarticleadd" />
                <input type="hidden" name="autho" value="'. $aid .'" />';

            groupe::droits("0");

            echo '
                <div class="mb-3">
                    <input class="btn btn-primary" type="submit" value="'. adm_translate("Ajouter") .'" />
                </div>
            </form>';

            // ca c'est pas bon incomplet
            if ($radminsuper != 1) {
                echo '<p class="blockquote">
                    '. adm_translate("Une fois que vous aurez validé cette publication, elle sera intégrée en base temporaire, et l'administrateur sera prévenu. Il visera cette publication et la mettra en ligne dans les meilleurs délais. Il est normal que pour l'instant, cette publication n'apparaisse pas dans l'arborescence.") .'
                    </p>';
            }
        }
    }

    $enattente = '';

    if ($radminsuper == 1) {
        $result = DB::table('seccont_tempo')
            ->distinct()
            ->select('artid', 'secid', 'title', 'content', 'author')
            ->orderBy('artid')
            ->get();

        foreach ($result as $seccont) { 

            $enattente .= '
            <li class="list-group-item list-group-item-action" >
                <div class="d-flex flex-row align-items-center">
                    <span class="flex-grow-1 pe-4">'. language::aff_langue($$tempo['title']) .'
                    <br />
                    <span class="text-muted">
                        <i class="fa fa-user fa-lg me-1"></i>['. $$tempo['author'] .']</span>
                    </span>
                    <span class="text-center">
                        <a href="'. site_url('admin.php?op=secartupdate&amp;artid='. $$tempo['artid']) .'">
                            '. adm_translate("Editer") .'<br /><i class="fa fa-edit fa-lg"></i>
                        </a>
                    </span>
                </div>';
        }

    } else {
        $result = DB::table('seccont_tempo')
            ->distinct()
            ->select('seccont_tempo.artid', 'seccont_tempo.title', 'seccont_tempo.author')
            ->joint('publisujet', 'seccont_tempo.secid', '=', 'publisujet.secid2')
            ->where('publisujet.aid', $aid)
            ->where('publisujet.type', '=', 1)
            ->orWhere('publisujet.type', '=', 2)
            ->get();

        foreach ($result as $seccont) {
            $enattente .= '
            <li class="list-group-item list-group-item-action" >
                <div class="d-flex flex-row align-items-center">
                    <span class="flex-grow-1 pe-4">'. language::aff_langue($seccont['title']) .'
                        <br />
                        <span class="text-muted"><i class="fa fa-user fa-lg me-1"></i>['. $seccont['author'] .']</span>
                    </span>
                    <span class="text-center">
                        <a href="'. site_url('admin.php?op=secartupdate&amp;artid='. $seccont['artid']) .'">
                            '. adm_translate("Editer") .'<br /><i class="fa fa-edit fa-lg"></i>
                        </a>
                    </span>
                </div>';
        }
    }

    echo '
    <hr />
    <h3 class="mb-3">
        <a name="publications en attente">
            <i class="far fa-clock fa-lg me-1"></i>'. adm_translate("Publication(s) en attente de validation") .'
        </a><span class="badge bg-danger float-end">'. count($result) .'</span></h3>
    <ul class="list-group">
    '. $enattente .'
    </ul>';

    if ($radminsuper == 1) {
        echo  '
        <hr />
        <h3 class="mb-3"><a name="droits des auteurs"><i class="fa fa-user-edit me-2"></i>'. adm_translate("Droits des auteurs") .'</a></h3>';
        
        $authors = DB::table('authors')
            ->select('aid', 'name', 'radminsuper')
            ->get();

        echo '<div class="row">';
        
        foreach($authors as $author) {
            if (!$author['radminsuper']) {
                echo '
                <div class="col-sm-4">
                <div class="card my-2 p-1">
                    <div class="card-body p-1">
                        <i class="fa fa-user fa-lg me-1"></i><br />'. $author['aid'] .'&nbsp;/&nbsp;'. $author['name'] .'<br />
                        <a href="'. site_url('admin.php?op=droitauteurs&amp;author='. $author['aid']) .'">'. adm_translate("Modifier l'information") .'</a>
                    </div>
                </div>
                </div>';
            }
        }
        echo '</div>';
    }

    css::adminfoot('', '', '', '');
}

/**
 * [new_rub_section description]
 *
 * @param   string  $type  [$type description]
 *
 * @return  void
 */
function new_rub_section(string $type): void
{
    global $aid, $radminsuper, $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('sections'));
    adminhead($f_meta_nom, $f_titre);

    $arg1 = '';

    if ($type == 'sec') {
        echo '
        <hr />
        <h3 class="mb-3">'. adm_translate("Ajouter une nouvelle Sous-Rubrique") .'</h3>
        <form action="'. site_url('admin.php') .'" method="post" id="newsection" name="adminForm">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="rubref">'. adm_translate("Rubriques") .'</label>
                <div class="col-sm-8">
                <select class="form-select" id="rubref" name="rubref">';

        if ($radminsuper == 1) {
            $result = DB::table('rubriques')->select('rubid', 'rubname')->orderBy('ordre')->get();
        } else {

            $result = DB::table('rubriques')
                ->distinct()
                ->select('rubriques.rubid', 'rubriques.rubname')
                ->leftJoin('sections', 'rubriques.rubid', '=', 'sections.rubid')
                ->leftJoin('publisujet', 'sections.secid', '=', 'publisujet.secid2')
                ->where('publisujet.aid', $aid)
                ->get();
        }

        foreach ($result as $rubrique) {
            echo '<option value="'. $rubrique['rubid'] .'">'. language::aff_langue($rubrique['rubname']) .'</option>';
        }

        echo '
                </select>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4 col-md-4" for="image">'. adm_translate("Image pour la Sous-Rubrique") .'</label>
                <div class="col-sm-8">
                <input type="text" class="form-control" name="image" />
                </div>
            </div>
            <div class="mb-3">
                <label class="col-form-label" for="secname">'. adm_translate("Titre") .'</label>
                <textarea  class="form-control" id="secname" name="secname" maxlength="255" rows="2" required="required"></textarea>
                <span class="help-block text-end"><span id="countcar_secname"></span></span>
            </div>
            <div class="mb-3">
                <label class="col-form-label" for="introd">'. adm_translate("Texte d'introduction") .'</label>
                <textarea class="tin form-control" name="introd" rows="30"></textarea>';

        echo editeur::aff_editeur("introd", '');

        echo '
            </div>';

        groupe::droits("0");

        echo '
        <div class="mb-3">
            <input type="hidden" name="op" value="sectionmake" />
            <button class="btn btn-primary col-sm-6 col-12 col-md-4" type="submit" /><i class="fa fa-plus-square fa-lg"></i>&nbsp;'. adm_translate("Ajouter") .'</button>
            <button class="btn btn-secondary col-sm-6 col-12 col-md-4" type="button" onclick="javascript:history.back()">'. adm_translate("Retour en arrière") .'</button>
        </div>
        </form>';

        $arg1 = '
            var formulid = ["newsection"];
            inpandfieldlen("secname",255);';

    } else if ($type == "rub") {
        echo '
            <hr />
            <h3 class="mb-3">'. adm_translate("Ajouter une nouvelle Rubrique") .'</h3>
            <form action="'. site_url('admin.php') .'" method="post" id="newrub" name="adminForm">
                <div class="mb-3">
                <label class="col-form-label" for="rubname">'. adm_translate("Nom de la Rubrique") .'</label>
                <textarea class="form-control" id="rubname" name="rubname" rows="2" maxlength="255" required="required"></textarea>
                <span class="help-block text-end" id="countcar_rubname"></span>
                </div>
                <div class="mb-3">
                <label class="col-form-label" for="introc">'. adm_translate("Texte d'introduction") .'</label>
                <textarea class="tin form-control" id="introc" name="introc" rows="30" ></textarea>
                </div>';

        echo editeur::aff_editeur('introc', '');

        echo '
                <div class="mb-3">
                <input type="hidden" name="op" value="rubriquemake" />
                <button class="btn btn-primary" type="submit"><i class="fa fa-plus-square fa-lg"></i>&nbsp;'. adm_translate("Ajouter") .'</button>
                <button class="btn btn-secondary" type="button" onclick="javascript:history.back()">'. adm_translate("Retour en arrière") .'</button>
                </div>
            </form>';

        $arg1 = '
            var formulid = ["newrub"];
            inpandfieldlen("rubname",255);';
    }

    css::adminfoot('fv', '', $arg1, '');
}

// Fonction publications connexes

/**
 * [publishcompat description]
 *
 * @param   int   $article  [$article description]
 *
 * @return  void
 */
function publishcompat(int $article): void 
{
    global $aid, $radminsuper, $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('sections'));
    adminhead($f_meta_nom, $f_titre);

    $seccont = DB::table('seccont')
        ->select('title')
        ->where('artid', $article)
        ->first();

    echo '
    <hr />
    <h3 class="mb-3">'. adm_translate("Publications connexes") .' : <span class="text-muted">'. language::aff_langue($seccont['titre']) .'</span></h3>
    <form action="'. site_url('admin.php') .'" method="post">';

    $result = DB::table('rubriques')
        ->select('rubid', 'rubname', 'enligne', 'ordre')
        ->orderBy('ordre')
        ->get();

    $i = 0;
    foreach ($result as $rubrique) {    
        if ($rubrique['enligne'] == 0) {
            $online = adm_translate("Hors Ligne");
            $cla = "danger";
        } else if ($rubrique['enligne'] == 1) {
            $online = adm_translate("En Ligne");
            $cla = "success";
        }

        echo '
        <div class="list-group-item bg-light">
            <a class="arrow-toggle text-primary" data-bs-toggle="collapse" data-bs-target="#lst_'. $rubrique['rubid'] .'" >
                <i class="toggle-icon fa fa-caret-down fa-lg"></i>
            </a>&nbsp;'. language::aff_langue($rubrique['rubname']) .'<span class="badge bg-'. $cla .' float-end">'. $online .'</span>
        </div>';

        if ($radminsuper == 1) {
            $result2 = DB::table('sections')
                ->select('secid', 'secname')
                ->where('rubid', $rubrique['rubid'])
                ->orderBy('ordre')
                ->get();
        } else {

            $result2 = DB::table('sections')
                ->distinct()
                ->select('sections.secid', 'sections.secname', 'sections.ordre')
                ->join('publisujet', 'sections.secid', '=', 'publisujet.secid2')
                ->where('sections.rubid', '=', $rubrique['rubid'])
                ->where('publisujet.aid', '=', $aid)
                ->where('publisujet.type', '=', 1)
                ->orderBy('ordre')
                ->get();
        }
        
        if ($result2 > 0) {
            echo '<ul id="lst_'. $rubrique['rubid'] .'" class="list-group mb-1 collapse">';
            
            while (list($secid, $secname) = sql_fetch_row($result2)) {
                echo '<li class="list-group-item"><strong class="ms-3" title="'. adm_translate("sous-rubrique") .'" data-bs-toggle="tooltip">'. language::aff_langue($secname) .'</strong></li>';
                
                $result3 = DB::table('seccont')
                                ->select('artid', 'title')
                                ->where('secid', $section['secid'])
                                ->orderBy('ordre')
                                ->get();

                if ($result3 > 0) {

                    foreach($result3 as $seccont) {    
                        $i++;
                        $result4 = DB::table('compatsujet')
                            ->select('id2')
                            ->where('id2', $list['artid'])
                            ->where('id1', $article)
                            ->first();

                        echo '<li class="list-group-item list-group-item-action"><div class="form-check ms-3">';
                        
                        if ($result4 > 0) {
                            echo '<input class="form-check-input" type="checkbox"  id="admin_rub'. $i .'" name="admin_rub['. $i .']" value="'. $seccont['artid'] .'" checked="checked" />';
                        } else {
                            echo '<input class="form-check-input" type="checkbox" id="admin_rub'. $i .'" name="admin_rub['. $i .']" value="'. $seccont['artid'] .'" />';
                        }

                        echo '<label class="form-check-label" for="admin_rub'. $i .'">'. language::aff_langue($seccont['title']) .'</label></div></li>';
                    }
                }
            }
            echo '</ul>';
        }
    }

    echo '
        <input type="hidden" name="article" value="'. $article .'" />
        <input type="hidden" name="op" value="updatecompat" />
        <input type="hidden" name="idx" value="'. $i .'" />
        <div class="mb-3 mt-3">
            <button class="btn btn-primary" type="submit">'. adm_translate("Valider") .'</button>&nbsp;<input class="btn btn-secondary" type="button" onclick="javascript:history.back()" value="'. adm_translate("Retour en arrière") .'" />
        </div>
    </form>';

    css::adminfoot('', '', '', '');
}

/**
 * [updatecompat description]
 *
 * @param   int   $article    [$article description]
 * @param   int   $admin_rub  [$admin_rub description]
 * @param   int   $idx        [$idx description]
 *
 * @return  void
 */
function updatecompat(int $article, int $admin_rub, int $idx): void
{
    DB::table('compatsujet')->where('id1', $article)->delete();

    for ($j = 1; $j < ($idx + 1); $j++) {
        if ($admin_rub[$j] != '') {
            DB::table('compatsujet')->insert(array(
                'id1'       => $article,
                'id2'       => $admin_rub[$j],
            ));

        }
    }

    global $aid;
    logs::Ecr_Log('security', "UpdateCompatSujets($article) by AID : $aid", '');

    Header('Location: '. site_url('admin.php?op=secartedit&artid='. $article));
}
// Fonction publications connexes

// Fonctions RUBRIQUES

/**
 * [rubriquedit description]
 *
 * @param   int   $rubid  [$rubid description]
 *
 * @return  void
 */
function rubriquedit(int $rubid): void
{
    global $radminsuper, $f_meta_nom, $f_titre;

    if ($radminsuper != 1) {
        Header('Location: '. site_url('admin.php?op=sections'));
    }

    $rubrique = DB::table('rubriques')
        ->select('rubid', 'rubname', 'intro', 'enligne', 'ordre')
        ->where('rubid', $rubid)
        ->first();

    if (!$rubrique) { 
        Header('Location: '. site_url('admin.php?op=sections'));
    }

    include("themes/default/header.php");

    GraphicAdmin(manuel('sections'));
    adminhead($f_meta_nom, $f_titre);

    $section = DB::table('sections')
        ->select('secid')
        ->where('rubid', $rubrique['rubid'])
        ->first();

    $rubname = stripslashes($rubname);
    $intro = stripslashes($intro);
    
    echo '
    <hr />
    <h3 class="mb-3">'. adm_translate("Editer une Rubrique : ") .' <span class="text-muted">'. language::aff_langue($rubname) .' #'. $rubrique['rubid'] .'</span></h3>';
    
    if ($section) {
        echo '<span class="badge bg-secondary">'. count($section) .'</span>&nbsp;'. adm_translate("sous-rubrique(s) attachée(s)");
    }
    
    echo '
            <form id="rubriquedit" action="'. site_url('admin.php') .'" method="post" name="adminForm">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="rubname">'. adm_translate("Titre") .'</label>
                <div class="col-sm-12">
                <textarea class="form-control" id="rubname" name="rubname" maxlength ="255" rows="2" required="required">'. $rubname .'</textarea>
                <span class="help-block text-end"><span id="countcar_rubname"></span></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="introc">'. adm_translate("Texte d'introduction") .'</label>
                <div class="col-sm-12">
                <textarea class="tin form-control" id="introc" name="introc" rows="30" >'. $intro .'</textarea>
                </div>
            </div>
            '. editeur::aff_editeur('introc', '') .'
            <div class="mb-3 row">
                <label class="col-form-label col-sm-3 pt-0" for="enligne">'. adm_translate("En Ligne") .'</label>';

    if ($radminsuper == 1) {
        if ($enligne == 1) {
            $sel1 = 'checked="checked"';
            $sel2 = '';
        } else {
            $sel1 = '';
            $sel2 = 'checked="checked"';
        }
    }

    echo '
                <div class="col-sm-9">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="enligne_n" name="enligne" value="0" '. $sel2 .' />
                    <label class="form-check-label" for="enligne_n">'. adm_translate("Non") .'</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="enligne_y" name="enligne" value="1" '. $sel1 .' />
                    <label class="form-check-label" for="enligne_y">'. adm_translate("Oui") .'</label>
                </div>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-12">
                <input type="hidden" name="rubid" value="'. $rubrique['rubid'] .'" />
                <input type="hidden" name="op" value="rubriquechange" />
                <button class="btn btn-primary" type="submit">'. adm_translate("Enregistrer") .'</button>&nbsp;
                <input class="btn btn-secondary" type="button" value="'. adm_translate("Retour en arrière") .'" onclick="javascript:history.back()" />
                </div>
            </div>
        </form>';

    $arg1 = '
    var formulid = ["rubriquedit"];
    inpandfieldlen("rubname",255);';

    css::adminfoot('fv', '', $arg1, '');
}

/**
 * [rubriquemake description]
 *
 * @param   string  $rubname  [$rubname description]
 * @param   string  $introc   [$introc description]
 *
 * @return  void
 */
function rubriquemake(string $rubname, string $introc): void
{
    global $radminsuper, $aid;

    $rubname = stripslashes(str::FixQuotes($rubname));
    $introc = stripslashes(str::FixQuotes(image::dataimagetofileurl($introc, 'modules/upload/upload/rub')));

    DB::table('rubriques')->insert(array(
        'rubname'       => $rubname,
        'intro'         => $introc,
        'enligne'       => 0,
        'orde'          => 0,
    ));

    //mieux ? création automatique d'une sous rubrique avec droits ... ?
    if ($radminsuper != 1) {

        $rublast = DB::table('rubriques')
            ->select('rubid')
            ->orderBy('rubid', 'desc')
            ->limit(1)
            ->first();

        DB::table('sections')->insert(array(
            'secname'       => 'A modifier !',
            'image'         => '',
            'userlevel'     => '',
            'rubid'         => $rublast['rubid'],
            'intro'         => '<p>Cette sous-rubrique a été créé automatiquement. <br />Vous pouvez la personaliser et ensuite rattacher les publications que vous souhaitez.</p>',
            'ordre'         => 99,
            'counter'       => 0,
        )); 
        
        $seclast = DB::table('sections')
            ->select('secid')
            ->orderBy('secid', 'desc')
            ->limit(1)
            ->first();

        droitsalacreation($aid, $seclast['secid']);

        logs::Ecr_Log('security', "CreateSections(Vide) by AID : $aid (via system)", '');
    }
    //mieux ... ?

    logs::Ecr_Log('security', "CreateRubriques($rubname) by AID : $aid", '');

    Header('Location: '. site_url('admin.php?op=ordremodule'));
}

/**
 * [rubriquechange description]
 *
 * @param   int     $rubid    [$rubid description]
 * @param   string  $rubname  [$rubname description]
 * @param   string  $introc   [$introc description]
 * @param   int     $enligne  [$enligne description]
 *
 * @return  void
 */
function rubriquechange(int $rubid, string $rubname, string $introc, int $enligne): void
{
    $rubname = stripslashes(str::FixQuotes($rubname));
    $introc = image::dataimagetofileurl($introc, 'modules/upload/upload/rub');
    $introc = stripslashes(str::FixQuotes($introc));

    DB::table('rubriques')->where('rubid', $rubid)->update(array(
        'rubname'       => $rubname,
        'intro'         => $introc,
        'enligne'       => $enligne,
    ));

    global $aid;
    logs::Ecr_Log("security", "UpdateRubriques($rubid, $rubname) by AID : $aid", "");

    Header('Location: '. site_url('admin.php?op=sections'));
}
// Fonctions RUBRIQUES

// Fonctions SECTIONS

/**
 * [sectionedit description]
 *
 * @param   int   $secid  [$secid description]
 *
 * @return  void
 */
function sectionedit(int $secid): void 
{
    global $radminsuper, $f_meta_nom, $f_titre, $aid;

    include("themes/default/header.php");

    GraphicAdmin(manuel('sections'));
    adminhead($f_meta_nom, $f_titre);

    $section = DB::table('sections')
        ->select('secid', 'secname', 'image', 'userlevel', 'rubid', 'intro')
        ->where('secid', $secid)
        ->first();
        
    $secname = stripslashes($section['secname']);
    $intro = stripslashes($section['intro']);

    echo '
    <hr />
    <h3 class="mb-3">'. adm_translate("Sous-rubrique") .' : <span class="text-muted">'. language::aff_langue($secname) .'</span></h3>';

    $seccont = DB::table('seccont')
        ->select('artid')
        ->where('secid', $sections['secid'])
        ->count();

    if ($number)    {
        echo '<span class="badge bg-secondary p-2 me-2">'. $number .' </span>'. adm_translate("publication(s) attachée(s)");
    }

    echo '
            <form id="sectionsedit" action="'. site_url('admin.php') .'" method="post" name="adminForm">
            <div class="mb-3">
                <label class="col-form-label" for="rubref">'. adm_translate("Rubriques") .'</label>';


    if ($radminsuper == 1) {
        $result = DB::table('rubriques')
            ->select('rubid', 'rubname')
            ->orderBy('ordre')
            ->get();

    } else {
        $result = DB::table('rubriques')
            ->distinct()
            ->select('rubriques.rubid', 'rubriques.rubname')
            ->leftJoin('sections', 'rubriques.rubid', '=', 'sections.rubid')
            ->leftJoin('publisujet', 'sections.secid', '=', 'publisujet.secid2')
            ->where('publisujet.aid', $aid)
            ->get();
    }
    
    echo '<select class="form-select" id="rubref" name="rubref">';

    foreach ($result as $rubrique) {    
        $sel = $section['rubid'] == $rubrique['rubid'] ? 'selected="selected"' : '';
        echo '<option value="'. $rubrique['rubid'] .'" '. $sel .'>'. language::aff_langue($rubrique['rubname']) .'</option>';
    }

    echo '
                </select>
        </div>';

    // ici on a(vait) soit le select qui permet de changer la sous rubrique de rubrique (ca c'est good) soit un input caché 
    // avec la valeur fixé de la rubrique...donc ICI un author ne peut pas changer sa sous rubrique de rubrique ...
    // il devrait pouvoir le faire dans une sous-rubrique ou il a des "droits" ??

    /*
    if ($radminsuper==1) {
        echo '<select class="form-select" id="rubref" name="rubref">';

        $_rubriques = DB::table('rubriques')
            ->select('rubid', 'rubname')
            ->orderBy('ordre')
            ->get();

        foreach ($_rubriques as $rubrique) {
            $sel = $section['rubid'] == $rubrique['rubid'] ? 'selected="selected"' : '';
            echo '<option value="'.$rubrique['rubid'].'" '.$sel.'>'.language::aff_langue($rubrique['rubname']).'</option>';
        }

        echo '
                </select>
        </div>';
    } else {
        echo '<input type="hidden" name="rubref" value="'.$section['rubid'].'" />';

        $rubname = DB::table('rubriques')
            ->select('rubname')
            ->where('rubid', $section['rubid'])
            ->first();

        echo '<pan class="ms-2">'.language::aff_langue($rubname['rubname']).'</span>';
    }
    */

    //ici
    echo '
    <div class="mb-3">
        <label class="col-form-label" for="secname">'. adm_translate("Sous-rubrique") .'</label>
        <textarea class="form-control" id="secname" name="secname" rows="4" maxlength="255" required="required">'. $secname .'</textarea>
        <span class="help-block text-end"><span id="countcar_secname"></span></span>
    </div>
    <div class="mb-3">
        <label class="col-form-label" for="image">'. adm_translate("Image") .'</label>
        <input type="text" class="form-control" id="image" name="image" maxlength="255" value="'. $section['image'] .'" />
        <span class="help-block text-end"><span id="countcar_image"></span></span>
    </div>
    <div class="mb-3">
        <label class="col-form-label" for="introd">'. adm_translate("Texte d'introduction") .'</label>
        <textarea class="tin form-control" id="introd" name="introd" rows="20">'. $intro .'</textarea>
    </div>';

    echo editeur::aff_editeur('introd', '');

    groupe::droits($section['userlevel']);

    $droit_pub = droits_publication($secid);

    if ($droit_pub == 3 or $droit_pub == 7) {
        echo '<input type="hidden" name="secid" value="'. $secid .'" />
                <input type="hidden" name="op" value="sectionchange" />
                <button class="btn btn-primary" type="submit">'. adm_translate("Enregistrer") .'</button>';
    }

    echo '
    <input class="btn btn-secondary" type="button" value="'. adm_translate("Retour en arrière") .'" onclick="javascript:history.back()" />
    </form>';

    $arg1 = '
    var formulid = ["sectionsedit"];
    inpandfieldlen("secname",255);
    inpandfieldlen("image",255);
    ';

    css::adminfoot('fv', '', $arg1, '');
}

/**
 * [sectionmake description]
 *
 * @param   string  $secname   [$secname description]
 * @param   string  $image     [$image description]
 * @param   int     $members   [$members description]
 * @param   int     $Mmembers  [$Mmembers description]
 * @param   string  $rubref    [$rubref description]
 * @param   string  $introd    [$introd description]
 *
 * @return  void
 */
function sectionmake(string $secname, string $image, int $members, int $Mmembers, string $rubref, string $introd): void
{
    global $radminsuper, $aid;

    if (is_array($Mmembers) and ($members == 1)) {
        $members = implode(',', $Mmembers);
        if ($members == 0) {
            $members = 1;
        }
    }

    $secname = stripslashes(str::FixQuotes($secname));
    $rubref = stripslashes(str::FixQuotes($rubref));
    $image = stripslashes(str::FixQuotes($image));
    $introd = stripslashes(str::FixQuotes(image::dataimagetofileurl($introd, 'modules/upload/upload/sec')));

    DB::table('sections')->insert(array(
        'secname'       => $secname,
        'image'         => $image,
        'userlevel'     => $members,
        'ribid'         => $rubref,
        'intro'         => $introd,
        'ordre'         => 99,
        'counter'       => 0,
    ));

    if ($radminsuper != 1) {
        $desction = DB::table('sections')
            ->select('secid')
            ->orderBy('secid', 'desc')
            ->limit(1)
            ->get();

        droitsalacreation($aid, $desction['secid']);
    }

    logs::Ecr_Log('security', "CreateSections($secname) by AID : $aid", '');

    Header('Location: '. site_url('admin.php?op=sections'));
}

/**
 * [sectionchange description]
 *
 * @param   int     $secid     [$secid description]
 * @param   string  $secname   [$secname description]
 * @param   string  $image     [$image description]
 * @param   int     $members   [$members description]
 * @param   int     $Mmembers  [$Mmembers description]
 * @param   [type]  $rubref    [$rubref description]
 * @param   string  $introd    [$introd description]
 *
 * @return  void
 */
function sectionchange(int $secid, string $secname, string $image, int $members, int $Mmembers, $rubref, string $introd): void
{
    if (is_array($Mmembers) and ($members == 1)) {
        $members = implode(',', $Mmembers);
        if ($members == 0) {
            $members = 1;
        }
    }

    $secname = stripslashes(str::FixQuotes($secname));
    $image = stripslashes(str::FixQuotes($image));
    $introd = stripslashes(str::FixQuotes(image::dataimagetofileurl($introd, 'modules/upload/upload/sec')));

    DB::table('sections')->where('secid', $secid)->update(array(
        'secname'       => $secname,
        'image'         => $image,
        'userlevel'     => $members,
        'rubid'         => $rubref,
        'intro'         => $introd,
    ));

    global $aid;
    logs::Ecr_Log('security', "UpdateSections($secid, $secname) by AID : $aid", '');

    Header('Location: '. site_url('admin.php?op=sections'));
}
// Fonctions SECTIONS

// Fonction ARTICLES

/**
 * [secartedit description]
 *
 * @param   int   $artid  [$artid description]
 *
 * @return  void
 */
function secartedit(int $artid): void
{
    global $f_meta_nom, $f_titre;

    $seccont = DB::table('seccont')
        ->select('author', 'artid', 'secid', 'title', 'content', 'userlevel')
        ->where('artid', $artid)
        ->first();

    if (!$seccont['artid']) {
        Header('Location: '. site_url('admin.php?op=sections'));
    }

    include("themes/default/header.php");

    GraphicAdmin(manuel('sections'));
    adminhead($f_meta_nom, $f_titre);

    $title = stripslashes($seccont['title']);
    $content = stripslashes(image::dataimagetofileurl($seccont['content'], 'storage/cache/s'));

    echo '
    <hr />
    <h3 class="mb-3">'. adm_translate("Editer une publication") .'</h3>
        <form action="'. site_url('admin.php') .'" method="post" id="secartedit" name="adminForm">
            <input type="hidden" name="artid" value="'. $seccont['artid'] .'" />
            <input type="hidden" name="op" value="secartchange" />
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="secid">'. adm_translate("Sous-rubriques") .'</label>
                <div class="col-sm-8">';

    // la on déraille ???
    $tmp_autorise = sousrub_select($seccont['secid']);

    if ($tmp_autorise) {
        echo $tmp_autorise;
    } else {
        $sections = DB::table('sections')
            ->select('secname')
            ->where('secid', $seccont['secid'])
            ->first();

        echo "<b>" . language::aff_langue($sections['secname']) . "</b>";
        echo '<input type="hidden" name="secid" value="'. $seccont['secid'] .'" />';
    }

    echo '
                </div>
            </div>';

    if ($tmp_autorise) {
        echo '<a href="'. site_url('admin.php?op=publishcompat&amp;article='. $seccont['artid']) .'">'. adm_translate("Publications connexes") .'</a>';
    }

    echo '
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="title">'. adm_translate("Titre") .'</label>
                <div class="col-sm-12">
                <textarea class="form-control" id="title" name="title" rows="2">'. $title .'</textarea>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="content">'. adm_translate("Contenu") .'</label>
                <div class="col-sm-12">
                <textarea class="tin form-control" id="content" name="content" rows="30" >'. $content .'</textarea>
                </div>
            </div>';

    echo editeur::aff_editeur('content', '');

    echo '
            <div class="mb-3 row">
            <div class="col-sm-12">';

    groupe::droits($seccont['userlevel']);

    $droits_pub = droits_publication($seccont['secid']);

    if ($droits_pub == 3 or $droits_pub == 7) {
        echo '<input class="btn btn-primary" type="submit" value="'. adm_translate("Enregistrer") .'" />&nbsp;';
    }

    echo '
                <input class="btn btn-secondary" type="button" value="'. adm_translate("Retour en arrière") .'" onclick="javascript:history.back()" />
            </div>
        </div>
    </form>';

    css::adminfoot('', '', '', '');
}

/**
 * [secartupdate description]
 *
 * @param   [type]  $artid  [$artid description]
 *
 * @return  void
 */
function secartupdate($artid): void 
{
    global $aid, $radminsuper, $f_meta_nom, $f_titre;

    $seccont_tempo = DB::table('seccont_tempo')
        ->select('author', 'artid', 'secid', 'title', 'content', 'userlevel')
        ->where('artid', $artid)
        ->first();

    $publisujet = DB::table('publisujet')
        ->select('type')
        ->where('secid2', $seccont_tempo['secid'])
        ->where('aid', $aid)
        ->where('type', 1)
        ->first();

    if ($publisujet['type'] == 1) {
        $debut = '<div class="alert alert-info">'. adm_translate("Vos droits de publications vous permettent de mettre à jour ou de supprimer ce contenu mais pas de la mettre en ligne sur le site.") .'</div>';
        
        $fin = '
        <div class="mb-3 row">
            <div class="col-12">
                <select class="form-select" name="op">
                <option value="secartchangeup" selected="selected">'. adm_translate("Mettre à jour") .'</option>
                <option value="secartdelete2">'. adm_translate("Supprimer") .'</option>
                </select>
            </div>
        </div>
        <input type="submit" class="btn btn-primary" name="submit" value="'. adm_translate("Ok") .'" />';
    }

    $publisujet = DB::table('publisujet')
        ->select('type')
        ->where('secid2', $seccont_tempo['secid'])
        ->where('aid', $aid)
        ->where('type', 2)
        ->first();

    if (($publisujet['type'] == 2) or ($radminsuper == 1)) {
        $debut = '
        <div class="alert alert-success">'. adm_translate("Vos droits de publications vous permettent de mettre à jour, de supprimer ou de le mettre en ligne sur le site ce contenu.") .'<br /></div>';
        
        $fin = '
        <div class="mb-3 row">
            <div class="col-12">
                <select class="form-select" name="op">
                <option value="secartchangeup" selected="selected">'. adm_translate("Mettre à jour") .'</option>
                <option value="secartdelete2">'. adm_translate("Supprimer") .'</option>
                <option value="secartpublish">'. adm_translate("Publier") .'</option>
                </select>
            </div>
        </div>
        <input type="submit" class="btn btn-primary" name="submit" value="'. adm_translate("Ok") .'" />';
    }

    $fin .= '&nbsp;<input class="btn btn-secondary" type="button" value="'. adm_translate("Retour en arrière") .'" onclick="javascript:history.back()" />';
    
    include("themes/default/header.php");
    
    GraphicAdmin(manuel('sections'));
    adminhead($f_meta_nom, $f_titre);

    echo '
    <hr />
    <h3 class="mb-3">'. adm_translate("Editer une publication") .'</h3>';

    echo $debut;

    $title = stripslashes($seccont_tempo['title']);
    $content = stripslashes(image::dataimagetofileurl($seccont_tempo['content'], 'storage/cache/s'));

    echo '
    <form id="secartupdate" action="'. site_url('admin.php') .'" method="post" name="adminForm">
        <input type="hidden" name="artid" value="'. $artid .'" />
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="secid">'. adm_translate("Sous-rubrique") .'</label>
            <div class="col-sm-8">';

    $tmp_autorise = sousrub_select($seccont_tempo['secid']); /// a affiner pas bon car dans certain cas on peut donc publier dans une sous rubrique sur laquelle on n'a pas les droits
    
    if ($tmp_autorise) {
        echo $tmp_autorise;
    } else {
        $section = DB::table('sections')->select('secname')->where('secid', $seccont_tempo['secid'])->first();

        echo '
                <strong>'. language::aff_langue($section['secname']) .'</strong>
                <input type="hidden" name="secid" value="'. $seccont_tempo['secid'] .'" />';
    }

    echo '
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-12" for="title">'. adm_translate("Titre") .'</label>
            <div class=" col-12">
                <textarea class="form-control" id="title" name="title" rows="2">'. $title .'</textarea>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-12" for="content">'. adm_translate("Contenu") .'</label>
            <div class=" col-12">
                <textarea class="tin form-control" id="content" name="content" rows="30">'. $content .'</textarea>
            </div>
        </div>
                '. editeur::aff_editeur('content', '');

    groupe::droits($seccont_tempo['userlevel']);

    echo $fin;
    echo '
        </form>';

    css::adminfoot('', '', '', '');
}

/**
 * [secarticleadd description]
 *
 * @param   int     $secid     [$secid description]
 * @param   [type]  $title     [$title description]
 * @param   string  $content   [$content description]
 * @param   string  $autho     [$autho description]
 * @param   int     $members   [$members description]
 * @param   string  $Mmembers  [$Mmembers description]
 *
 * @return  void
 */
function secarticleadd(int $secid, $title, string $content, string $autho, int $members, string $Mmembers): void
{
    global $radminsuper;

    // pas de removehack pour l'entrée des données ???????
    if (is_array($Mmembers) and ($members == 1)) {
        $members = implode(',', $Mmembers);
    }

    $title = stripslashes(str::FixQuotes($title));

    if ($secid != "0") {
        if ($radminsuper == 1) {
            $timestamp = time();
            $content = stripslashes(str::FixQuotes(image::dataimagetofileurl($content, 'modules/upload/upload/s')));

            DB::table('seccont')->insert(array(
                'secid'         => $secid,
                'title'         => $title,
                'content'       => $content,
                'counter'       => 0,
                'author'        => $autho,
                'ordre'         => 99,
                'userlevel'     => $members,
                'timestamp'     => $timestamp,
            ));

            global $aid;
            logs::Ecr_Log("security", "CreateArticleSections($secid, $title) by AID : $aid", "");
        } else {
            $content = stripslashes(str::FixQuotes(image::dataimagetofileurl($content, 'storage/cache/s')));

            DB::table('seccont_tempo')->insert(array(
                'secid'         => $secid,
                'title'         => $title,
                'content'       => $content,
                'counter'       => 0,
                'author'        => $autho,
                'ordre'         => 99,
                'userlevel'     => $members,
            ));

            global $aid;
            logs::Ecr_Log('security', "CreateArticleSectionsTempo($secid, $title) by AID : $aid", '');
        }
    }

    Header('Location: '. site_url('admin.php?op=sections'));
}

/**
 * [secartchange description]
 *
 * @param   int     $artid     [$artid description]
 * @param   int     $secid     [$secid description]
 * @param   string  $title     [$title description]
 * @param   string  $content   [$content description]
 * @param   int     $members   [$members description]
 * @param   string  $Mmembers  [$Mmembers description]
 *
 * @return  void
 */
function secartchange(int $artid, int $secid, string $title, string $content, int $members, string $Mmembers): void
{
    if (is_array($Mmembers) and ($members == 1)) {
        $members = implode(',', $Mmembers);
    }

    $title = stripslashes(str::FixQuotes($title));
    $content = stripslashes(str::FixQuotes(image::dataimagetofileurl($content, 'modules/upload/upload/s')));
    $timestamp = time();

    if ($secid != '0') {
        DB::table('seccont')->where('artid', $artid)->update(array(
            'secid'         => $secid,
            'title'         => $title,
            'content'       => $content,
            'userlevel'     => $members,
            'timestamp'     => $timestamp,
        ));

        global $aid;
        logs::Ecr_Log("security", "UpdateArticleSections($artid, $secid, $title) by AID : $aid", "");
    }

    Header('Location: '. site_url('admin.php?op=secartedit&artid='. $artid));
}

/**
 * [secartchangeup description]
 *
 * @param   int     $artid     [$artid description]
 * @param   int     $secid     [$secid description]
 * @param   string  $title     [$title description]
 * @param   string  $content   [$content description]
 * @param   int     $members   [$members description]
 * @param   string  $Mmembers  [$Mmembers description]
 *
 * @return  void
 */
function secartchangeup(int $artid, int $secid, string $title, string $content, int $members, string $Mmembers): void
{
    if (is_array($Mmembers) and ($members == 1)) {
        $members = implode(',', $Mmembers);
    }
    
    $title = stripslashes(str::FixQuotes($title));
    $content = stripslashes(str::FixQuotes(image::dataimagetofileurl($content, 'storage/cache/s')));
    
    if ($secid != '0') {
        DB::table('seccont_tempo')->where('artid', $artid)->update(array(
            'secid'         => $secid,
            'title'         => $title,
            'content'       => $content,
            'userlevel'     => $members,
        ));

        global $aid;
        logs::Ecr_Log('security', "UpdateArticleSectionsTempo($artid, $secid, $title) by AID : $aid", '');
    }

    Header('Location: '. site_url('admin.php?op=secartupdate&artid='.$artid));
}

/**
 * [secartpublish description]
 *
 * @param   int     $artid     [$artid description]
 * @param   int     $secid     [$secid description]
 * @param   string  $title     [$title description]
 * @param   string  $content   [$content description]
 * @param   [type]  $author    [$author description]
 * @param   int     $members   [$members description]
 * @param   string  $Mmembers  [$Mmembers description]
 *
 * @return  void
 */
function secartpublish(int $artid, int $secid, string $title, string $content, $author, int $members, string $Mmembers): void
{
    if (is_array($Mmembers) and ($members == 1)) {
        $members = implode(',', $Mmembers);
    }

    $content = stripslashes(str::FixQuotes(image::dataimagetofileurl($content, 'modules/upload/upload/s')));
    $title = stripslashes(str::FixQuotes($title));

    if ($secid != '0') {
        DB::table('seccont_tempo')->where('artid', $artid)->delete();

        $timestamp = time();

        DB::table('seccont')->insert(array(
            'secid'         => $secid,
            'title'         => $title,
            'content'       => $content,
            'counter'       => 0,
            'author'        => $author,
            'ordre'         => 99,
            'userlevl'      => $members,
            'timestamp'     => $timestamp,
        ));

        global $aid;
        logs::Ecr_Log('security', "PublicateArticleSections($artid, $secid, $title) by AID : $aid", '');

        $author = DB::table('authors')->select('email')->where('aid', $author)->first();

        $sujet = html_entity_decode(adm_translate("Validation de votre publication"), ENT_COMPAT | ENT_HTML401, 'utf-8');
        $message = adm_translate("La publication que vous aviez en attente vient d'être validée");

        global $notify_from;
        mailler::send_email($author['email'], $sujet, $message, $notify_from, true, "html", '');
    }

    Header('Location: '. site_url('admin.php?op=sections'));
}
// Fonction ARTICLES

// Fonctions de DELETE

/**
 * [rubriquedelete description]
 *
 * @param   int   $rubid  [$rubid description]
 * @param   int   $ok     [$ok description]
 *
 * @return  void
 */
function rubriquedelete(int $rubid, int $ok = 0): void
{
    // protection
    global $radminsuper;
    if (!$radminsuper) {
        Header('Location: '. site_url('admin.php?op=sections'));
    }

    if ($ok == 1) {
        $sections = DB::table('sections')->select('secid')->where('rubid', $rubid)->get();

        if ($sections > 0) {
            foreach ($sections as $section) {

                $_seccont = DB::table('seccont')->select('artid')->where('secid', $section['secid'])->get();

                if ($_seccont > 0) {

                    foreach($_seccont as $seccont) {
                        DB::table('seccont')->where('artid', $seccont['artid'])->delete();
                        DB::table('compatsujet')->where('id1', $seccont['artid'])->delete();
                    }
                }
            }
        }

        DB::table('sections')->where('rubid', $rubid)->delete();
        DB::table('rubriques')->where('rubid', $rubid)->delete();

        global $aid;
        logs::Ecr_Log("security", "DeleteRubriques($rubid) by AID : $aid", "");

        Header('Location: '. site_url('admin.php?op=sections'));
    } else {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('sections'));
        adminhead($f_meta_nom, $f_titre);

        $rubrique = DB::table('rubriques')->select('rubname')->where('rubid', $rubid)->get();

        echo '
        <hr />
        <h3 class="mb-3 text-danger">'. adm_translate("Effacer la Rubrique : ") .'<span class="text-muted">'. language::aff_langue($rubrique['rubname']) .'</span></h3>
        <div class="alert alert-danger">
            <strong>'. adm_translate("Etes-vous sûr de vouloir effacer cette Rubrique ?") .'</strong><br /><br />
            <a class="btn btn-danger btn-sm" href="'. site_url('admin.php?op=rubriquedelete&amp;rubid='. $rubrique['rubid'] .'&amp;ok=1') .'" role="button">'. adm_translate("Oui") .'</a>&nbsp;<a class="btn btn-secondary btn-sm" href="'. site_url('admin.php?op=sections') .'" role="button">'. adm_translate("Non") .'</a>
        </div>';

        css::adminfoot('', '', '', '');
    }
}

/**
 * [sectiondelete description]
 *
 * @param   int   $secid  [$secid description]
 * @param   int   $ok     [$ok description]
 *
 * @return  void
 */
function sectiondelete(int $secid, int $ok = 0): void 
{
    // protection
    $tmp = droits_publication($secid);

    if (($tmp != 7) and ($tmp != 4)) {
        Header('Location: '. site_url('admin.php?op=sections'));
    }

    if ($ok == 1) {
        $_seccont = DB::table('seccont')->select('artid')->where('secid', $secid)->get();

        if ($_seccont > 0) {
            foreach ($_seccont as $seccont) {
                DB::table('compatsujet')->where('id1', $seccont['artid'])->delete();
            }
        }

        DB::table('seccont')->where('secid', $secid)->delete();
        DB::table('sections')->where('secid', $secid)->delete();

        global $aid;
        logs::Ecr_Log("security", "DeleteSections($secid) by AID : $aid", "");

        Header('Location: '. site_url('admin.php?op=sections'));
    } else {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('sections'));
        adminhead($f_meta_nom, $f_titre);

        $section = DB::table('sections')->select('secname')->where('secid', $secid)->orderBy('')->get();

        echo '
        <hr />
        <h3 class="mb-3 text-danger">'. adm_translate("Effacer la sous-rubrique : ") .'<span class="text-muted">'. language::aff_langue($section['secname']) .'</span></h3>
        <div class="alert alert-danger">
            <strong>'. adm_translate("Etes-vous sûr de vouloir effacer cette sous-rubrique ?") .'</strong><br /><br />
            <a class="btn btn-danger btn-sm" href="'. site_url('admin.php?op=sectiondelete&amp;secid='. $secid .'&amp;ok=1') .'" role="button">'. adm_translate("Oui") .'</a>&nbsp;<a class="btn btn-secondary btn-sm" role="button" href="'. site_url('admin.php?op=sections') .'" >'. adm_translate("Non") .'</a>
        </div>';

        css::adminfoot('', '', '', '');
    }
}

/**
 * [secartdelete description]
 *
 * @param   int   $artid  [$artid description]
 * @param   int   $ok     [$ok description]
 *
 * @return  void
 */
function secartdelete(int $artid, int $ok = 0): void
{
    // protection
    $seccont = DB::table('seccont')->select('secid')->where('artid', $artid)->first();

    $tmp = droits_publication($seccont['secid']);

    if (($tmp != 7) and ($tmp != 4)) {
        Header('Location: '. site_url('admin.php?op=sections'));
    }

    if ($ok == 1) {
        $seccont = DB::table('seccont')->select('content')->where('artid', $artid)->get();

        $rechuploadimage = '#modules/upload/upload/s\d+_\d+_\d+.[a-z]{3,4}#m';
        preg_match_all($rechuploadimage, $seccont['content'], $uploadimages);
        
        foreach ($uploadimages[0] as $imagetodelete) {
            unlink($imagetodelete);
        }

        DB::table('seccont')->where('artid', $artid)->delete();
        DB::table('compatsujet')->where('id1', $artid)->delete();

        global $aid;
        logs::Ecr_Log("security", "DeleteArticlesSections($artid) by AID : $aid", "");

        Header('Location: '. site_url('admin.php?op=sections'));
    } else {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('sections'));
        adminhead($f_meta_nom, $f_titre);

        $seccont = DB::table('seccont')->select('title')->where('artid', $artid)->first();

        echo '
        <hr />
        <h3 class="mb-3 text-danger">'. adm_translate("Effacer la publication :") .' <span class="text-muted">'. language::aff_langue($seccont['title']) .'</span></h3>
        <p class="alert alert-danger">
            <strong>'. adm_translate("Etes-vous certain de vouloir effacer cette publication ?") .'</strong><br /><br />
            <a class="btn btn-danger btn-sm" href="'. site_url('admin.php?op=secartdelete&amp;artid='. $artid .'&amp;ok=1') .'" role="button">'. adm_translate("Oui") .'</a>&nbsp;<a class="btn btn-secondary btn-sm" role="button" href="'. site_url('admin.php?op=sections') .'" >'. adm_translate("Non") .'</a>
        </p>';

        include("themes/default/footer.php");
    }
}

/**
 * [secartdelete2 description]
 *
 * @param   int   $artid  [$artid description]
 * @param   int   $ok     [$ok description]
 *
 * @return  void
 */
function secartdelete2(int $artid, int $ok = 0): void
{
    if ($ok == 1) {
        DB::table('seccont_tempo')->where('artid', $artid)->delete();

        global $aid;
        logs::Ecr_Log('security', "DeleteArticlesSectionsTempo($artid) by AID : $aid", '');

        Header('Location: '. site_url('admin.php?op=sections'));
    } else {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('sections'));
        adminhead($f_meta_nom, $f_titre);

        $seccont_tempo = DB::table('seccont_tempo')->select('title')->where('artid', $artid)->first();

        echo '
        <hr />
        <h3 class="mb-3 text-danger">'. adm_translate("Effacer la publication :") .' <span class="text-muted">'. language::aff_langue($seccont_tempo['title']) .'</span></h3>
        <p class="alert alert-danger">
            <strong>'. adm_translate("Etes-vous certain de vouloir effacer cette publication ?") .'</strong><br /><br />
            <a class="btn btn-danger btn-sm" href="'. site_url('admin.php?op=secartdelete2&amp;artid='. $artid .'&amp;ok=1') .'" role="button">'. adm_translate("Oui") .'</a>&nbsp;<a class="btn btn-secondary btn-sm" role="button" href="'. site_url('admin.php?op=sections') .'" >'. adm_translate("Non") .'</a>
        </p>';

        include("themes/default/footer.php");
    }
}
// Fonctions de DELETE

// Fonctions de classement
/**
 * [ordremodule description]
 *
 * @return  void
 */
function ordremodule(): void
{
    global $radminsuper, $f_meta_nom, $f_titre;

    if ($radminsuper <> 1) {  
        Header('Location: '. site_url('admin.php?op=sections'));
    }

    include("themes/default/header.php");

    GraphicAdmin(manuel('sections'));
    adminhead($f_meta_nom, $f_titre);

    // data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-mobile-responsive="true" data-icons-prefix="fa" data-icons="icons"
    
    echo '
    <hr />
    <h3 class="mb-3">'. adm_translate("Changer l'ordre des rubriques") .'</h3>
    <form action="'. site_url('admin.php') .'" method="post" id="ordremodule" name="adminForm">
        <table class="table table-borderless table-sm table-hover table-striped">
            <thead>
                <tr>
                <th data-sortable="true" class="n-t-col-xs-2">'. adm_translate("Index") .'</th>
                <th data-sortable="true" class="n-t-col-xs-10">'. adm_translate("Rubriques") .'</th>
                </tr>
            </thead>
            <tbody>';

    $rubriques = DB::table('rubriques')->select('rubid', 'rubname', 'ordre')->orderBy('ordre')->get();

    $i = 0;
    $fv_parametres = '';
        
    foreach($rubriques as $rubrique) {
        $i++;
        
        echo '<tr>
            <td>
                <div class="mb-3 mb-0">
                    <input type="hidden" name="rubid['. $i .']" value="'. $rubrique['rubid'] .'" />
                    <input type="text" class="form-control" id="ordre'. $i .'" name="ordre['. $i .']" value="'. $rubrique['ordre'] .'" maxlength="4" required="required" />
                </div>
            </td>
            <td>
                <label class="col-form-label" for="ordre'. $i .'">
                    '. language::aff_langue($rubrique['rubname']) .'</label>
                </td>
            </tr>';

        $fv_parametres .= '
            "ordre['. $i .']": {
            validators: {
                regexp: {
                regexp:/^\d{1,4}$/,
                message: "0-9"
                }
            }
        },';
    }

    echo '
            </tbody>
        </table>
        <div class="mb-3 mt-3">
            <input type="hidden" name="i" value="'. $i .'" />
            <input type="hidden" name="op" value="majmodule" />
            <button type="submit" class="btn btn-primary" >'. adm_translate("Valider") .'</button>
            <button class="btn btn-secondary" onclick="javascript:history.back()" >'. adm_translate("Retour en arrière") .'</button>
        </div>
    </form>';

    $arg1 = 'var formulid = ["ordremodule"];';

    css::adminfoot('fv', $fv_parametres, $arg1, '');
}

/**
 * [ordrechapitre description]
 *
 * @return  void
 */
function ordrechapitre(): void
{
    global $rubname, $rubid, $radminsuper, $f_meta_nom, $f_titre;

    if ($radminsuper <> 1) {
        Header('Location: '. site_url('admin.php?op=sections'));
    }

    include("themes/default/header.php");

    GraphicAdmin(manuel('sections'));
    adminhead($f_meta_nom, $f_titre);

    echo '
    <hr />
    <h3 class="mb-3">'. adm_translate("Changer l'ordre des sous-rubriques") .' '. adm_translate("dans") .' / <span class="text-muted">'. $rubname .'</span></h3>
    <form action="'. site_url('admin.php') .'" method="post" id="ordrechapitre" name="adminForm">
        <table class="table table-borderless table-sm table-hover table-striped">
            <thead>
                <tr>
                <th data-sortable="true" class="n-t-col-xs-2">'. adm_translate("Index") .'</th>
                <th data-sortable="true" class="n-t-col-xs-10">'. adm_translate("Sous-rubriques") .'</th>
                </tr>
            </thead>
            <tbody>';

    $sections = DB::table('sections')->select('secid', 'secname', 'ordre')->where('rubid', $rubid)->orderBy('ordre')->get();

    $i = 0;
    $fv_parametres = '';

    foreach ($sections as $section) {
        $i++;

        echo '<tr>
            <td>
                <div class="mb-3 mb-0">
                    <input type="hidden" name="secid['. $i .']" value="'. $section['secid'] .'" />
                    <input type="text" class="form-control" name="ordre['. $i .']" id="ordre'. $i .'" value="'. $section['ordre'] .'" maxlength="3" required="required" />
                </div>
            </td>
            <td><label class="col-form-label" for="ordre'. $i .'">'. language::aff_langue($section['secname']) .'</label></td>
        </tr>';

        $fv_parametres .= '
            "ordre['. $i .']": {
            validators: {
                regexp: {
                regexp:/^\d{1,3}$/,
                message: "0-9"
                },
                between: {
                min: 1,
                max: '. $numrow .',
                message: "1 ... '. $numrow .'"
                }
            }
        },';
    }

    echo '
            </tbody>
        </table>
        <div class="mb-3 mt-3">
            <input type="hidden" name="op" value="majchapitre" />
            <input type="submit" class="btn btn-primary" value="'. adm_translate("Valider") .'" />
            <button class="btn btn-secondary" onclick="javascript:history.back()" >'. adm_translate("Retour en arrière") .' </button>
        </div>
    </form>';

    $arg1 = '
        var formulid = ["ordrechapitre"];';

    css::adminfoot('fv', $fv_parametres, $arg1, '');
}

/**
 * [ordrecours description]
 *
 * @return  void
 */
function ordrecours(): void
{
    global $secid, $radminsuper, $f_meta_nom, $f_titre;

    if ($radminsuper <> 1) {
        Header('Location: '. site_url('admin.php?op=sections'));
    }

    include("themes/default/header.php");

    GraphicAdmin(manuel('sections'));
    adminhead($f_meta_nom, $f_titre);

    $section = DB::table('sections')->select('secname')->where('secid', $secid)->first();

    echo '
    <hr />
    <h3 class="mb-3">'. adm_translate("Changer l'ordre") .' '. adm_translate("des") .' '. adm_translate("publications") .' / '. language::aff_langue($section['secname']) .'</h3>
    <form id="ordrecours" action="'. site_url('admin.php') .'" method="post" name="adminForm">
        <table class="table table-borderless table-sm table-hover table-striped">
            <thead>
                <tr>
                <th data-sortable="true" class="n-t-col-xs-2">'. adm_translate("Index") .'</th>
                <th data-sortable="true" class="n-t-col-xs-10">'. adm_translate("Publications") .'</th>
                </tr>
            </thead>
            <tbody>';

    $seccont = DB::table('seccont')->select('artid', 'title', 'ordre')->where('secid', $secid)->orderBy('ordre')->get();

    $i = 0;
    $fv_parametres = '';

    foreach ($seccont as $list) {
        $i++;
        echo '<tr>
            <td>
                <div class="mb-3 mb-0">
                    <input type="hidden" name="artid['. $i .']" value="'. $list['artid'] .'" />
                    <input type="text" class="form-control" id="ordre'. $i .'" name="ordre['. $i .']" value="'. $list['ordre'] .'"  maxlength="4" required="required" />
                </div>
            </td>
            <td><label class="col-form-label" for="ordre'. $i .'">'. language::aff_langue($list['title']) .'</label></td>
        </tr>';

        $fv_parametres .= '
            "ordre['. $i .']": {
            validators: {
                regexp: {
                regexp:/^\d{1,4}$/,
                message: "0-9"
                },
                between: {
                min: 1,
                max: '. count($numrow) .',
                message: "1 ... '. count($numrow) .'"
                }
            }
        },';
    }

    echo '
            </tbody>
        </table>
        <div class="mb-3 mt-3">
            <input type="hidden" name="op" value="majcours" />
            <input type="submit" class="btn btn-primary" value="'. adm_translate("Valider") .'" />
            <input type="button" class="btn btn-secondary" value="'. adm_translate("Retour en arrière") .'" onclick="javascript:history.back()" />
        </div>
    </form>';

    $arg1 = 'var formulid = ["ordrecours"];';

    css::adminfoot('fv', $fv_parametres, $arg1, '');
}

/**
 * [updateordre description]
 *
 * @param   array           [ description]
 * @param   string  $rubid  [$rubid description]
 * @param   array           [ description]
 * @param   string  $artid  [$artid description]
 * @param   array           [ description]
 * @param   string  $secid  [$secid description]
 * @param   string  $op     [$op description]
 * @param   int     $ordre  [$ordre description]
 *
 * @return  void
 */
function updateordre(array|string $rubid, array|string $artid, array|string $secid, string $op, int $ordre): void
{
    global $radminsuper;

    if ($radminsuper != 1) {
        Header('Location: '. site_url('admin.php?op=sections'));
    }

    if ($op == "majmodule") {
        $i = count( (array) $rubid);

        for ($j = 1; $j < ($i + 1); $j++) {
            $rub = $rubid[$j];
            $ord = $ordre[$j];
            DB::table('rubriques')->where('rubid', $rub)->update(array(
                'ordre'       => $ord,
            ));
        }
    }

    if ($op == "majchapitre") {
        $i = count( (array) $secid);
        for ($j = 1; $j < ($i + 1); $j++) {
            $sec = $secid[$j];
            $ord = $ordre[$j];
            DB::table('sections')->where('secid', $sec)->update(array(
                'ordre'       => $ord,
            ));
        }
    }

    if ($op == "majcours") {
        $i = count( (array) $artid);
        for ($j = 1; $j < ($i + 1); $j++) {
            $art = $artid[$j];
            $ord = $ordre[$j];
            DB::table('seccont')->where('artid', $art)->update(array(
                'ordre'       => $ord,
            ));
        }
    }

    Header('Location: '. site_url('admin.php?op=sections'));
}
// Fonctions de classement

// Fonctions DROIT des AUTEURS

/**
 * [publishrights description]
 *
 * @param   string  $author  [$author description]
 *
 * @return  void
 */
function publishrights(string $author): void
{
    global $radminsuper, $f_meta_nom, $f_titre;

    if ($radminsuper != 1) {
        Header('Location: '. site_url('admin.php?op=sections'));
    }

    include("themes/default/header.php");

    GraphicAdmin(manuel('sections'));
    adminhead($f_meta_nom, $f_titre);

    echo '
    <hr />
    <h3 class="mb-3"><i class="fa fa-user-edit me-2"></i>'. adm_translate("Droits des auteurs") .' : <span class="text-muted">'. $author .'</span></h3>
    <form action="'. site_url('admin.php') .'" method="post">';

    $rubriques = DB::table('rubriques')
        ->select('rubid', 'rubname')
        ->orderBy('ordre')
        ->get();

    $i = 0;
    $scrr = '';
    $scrsr = '';

    foreach ($rubriques as $rubrique) {
        echo '
            <table class="table table-bordered table-sm" data-toggle="" data-classes=""  data-striped="true" data-icons-prefix="fa" data-icons="icons">
                <thead class="thead-light">
                <tr class="table-secondary"><th colspan="5"><span class="form-check"><input class="form-check-input" id="ckbrall_'. $rubrique['rubid'] .'" type="checkbox" /><label class="form-check-label lead" for="ckbrall_'. $rubrique['rubid'] .'">'. language::aff_langue($rubrique['rubname']) .'</label></span></th></tr>
                <tr class="">
                    <th class="colspan="2" n-t-col-xs-3" data-sortable="true">'. adm_translate("Sous-rubriques") .'</th>
                    <th class="n-t-col-xs-2 text-center" data-halign="center" data-align="center">'. adm_translate("Créer") .'</th>
                    <th class="n-t-col-xs-2 text-center" data-halign="center" data-align="center">'. adm_translate("Publier") .'</th>
                    <th class="n-t-col-xs-2 text-center" data-halign="center" data-align="center">'. adm_translate("Modifier") .'</th>
                    <th class="n-t-col-xs-2 text-center" data-halign="center" data-align="center">'. adm_translate("Supprimer") .'</th>
                </tr>
                </thead>
                <tbody>';

        $scrr .= '
                $("#ckbrall_'. $rubrique['rubid'] .'").change(function(){
                    $(".ckbr_'. $rubrique['rubid'] .'").prop("checked", $(this).prop("checked"));
                });';

        $sections = DB::table('sections')
            ->select('secid', 'secname')
            ->where('rubid', $rubrique['rubid'])
            ->orderBy('ordre')
            ->get();

        foreach ($sections as $section) {

            $publi_sujet = DB::table('publisujet')
                ->select('type')
                ->where('secid2', $section['secid'])
                ->where('aid', $author)
                ->first();

            $i++;
            $crea = '';
            $publi = '';
            $modif = '';
            $supp = '';

            if ($publi_sujet > 0) {
                foreach ($publi_sujet as $publisujet) {
                    
                    if ($publisujet['type'] == 1) {
                        $crea = 'checked="checked"';
                    } else if ($publisujet['type'] == 2) {
                        $publi = 'checked="checked"';
                    } else if ($publisujet['type'] == 3) {
                        $modif = 'checked="checked"';
                    } else if ($publisujet['type'] == 4) {
                        $supp = 'checked="checked"';
                    }
                }
            }

            echo '
                <tr>
                    <td><div class="form-check"><input class="form-check-input" id="ckbsrall_'. $section['secid'] .'" type="checkbox" /><label class="form-check-label" for="ckbsrall_'. $section['secid'] .'">'. language::aff_langue($section['secname']) .'</label></div></td>
                    <td class="text-center"><div class="form-check"><input class="form-check-input ckbsr_'. $section['secid'] .' ckbr_'. $rubrique['rubid'] .'" type="checkbox" id="creation'. $i .'" name="creation['. $i .']" value="'. $section['secid'] .'" '. $crea .' /><label class="form-check-label" for="creation'. $i .'"></label></div></td>
                    <td class="text-center"><div class="form-check"><input class="form-check-input ckbsr_'. $section['secid'] .' ckbr_'. $rubrique['rubid'] .'" type="checkbox" id="publication'. $i .'" name="publication['. $i .']" value="'. $section['secid'] .'" '. $publi .' /><label class="form-check-label" for="publication'. $i .'"></label></div></td>
                    <td class="text-center"><div class="form-check"><input class="form-check-input ckbsr_'. $section['secid'] .' ckbr_'. $rubrique['rubid'] .'" type="checkbox" id="modification'. $i .'" name="modification['. $i .']" value="'. $section['secid'] .'" '. $modif .' /><label class="form-check-label" for="modification'. $i .'"></label></div></td>
                    <td class="text-center"><div class="form-check"><input class="form-check-input ckbsr_'. $section['secid'] .' ckbr_'. $rubrique['rubid'] .'" type="checkbox" id="suppression'. $i .'" name="suppression['. $i .']" value="'. $section['secid'] .'" '. $supp .' /><label class="form-check-label" for="suppression'. $i .'"></label></div></td>
                </tr>';

            $scrsr .= '
                $("#ckbsrall_'. $section['secid'] .'").change(function(){
                    $(".ckbsr_'. $section['secid'] .'").prop("checked", $(this).prop("checked"));
                });';
        }

        echo '
                </tbody>
            </table>
        <br />';
    }

    echo '<input type="hidden" name="chng_aid" value="'. $author .'" />
            <input type="hidden" name="op" value="updatedroitauteurs" />
            <input type="hidden" name="maxindex" value="'. $i .'" />
            <input class="btn btn-primary me-3" type="submit" value="'. adm_translate("Valider") .'" />
            <input class="btn btn-secondary" type="button" onclick="javascript:history.back()" value="'. adm_translate("Retour en arrière") .'" />
    </form>';

    echo '
    <script type="text/javascript">
    //<![CDATA[
        $(document).ready(function(){
        '. $scrr . $scrsr .'
        });
    //]]>
    </script>';

    css::adminfoot('', '', '', '');
}

/**
 * [droitsalacreation description]
 *
 * @param   string  $chng_aid  [$chng_aid description]
 * @param   int     $secid     [$secid description]
 *
 * @return  void
 */
function droitsalacreation(string $chng_aid, int $secid): void 
{
    $lesdroits = array('1', '2', '3');

    // if($secid > 0)
    foreach ($lesdroits as $droit) {
        DB::table('publisujet')->insert(array(
            'aid'       => $chng_aid,
            'secid2'       => $secid,
            'type'       => $droit,
        ));        
    }
    //  else {
        // DB::table('publisujet')->insert(array(
        //     'aid'       => $chng_aid,
        //     'secid2'       => $secid,
        //     'type'       => 1,
        // ));

    // }
}

/**
 * [updaterights description]
 *
 * @param   string  $chng_aid      [$chng_aid description]
 * @param   int     $maxindex      [$maxindex description]
 * @param   array   $creation      [$creation description]
 * @param   array   $publication   [$publication description]
 * @param   array   $modification  [$modification description]
 * @param   array   $suppression   [$suppression description]
 *
 * @return  void
 */
function updaterights(string $chng_aid, int $maxindex, array $creation, array $publication, array $modification, array $suppression): void 
{
    global $radminsuper;

    if ($radminsuper != 1) {
        Header('Location: '. site_url('admin.php?op=sections'));
    }

    DB::table('publisujet')->where('aid', $chng_aid)->delete();

    for ($j = 1; $j < ($maxindex + 1); $j++) {
        if (array_key_exists($j, $creation)) {
            if ($creation[$j] != '') {
                DB::table('publisujet')->insert(array(
                    'aid'       => $chng_aid,
                    'secid2'    => $creation[$j],
                    'type'      => 1,
                ));
            }
        }

        if (array_key_exists($j, $publication)) {
            if ($publication[$j] != '') {
                DB::table('publisujet')->insert(array(
                    'aid'       => $chng_aid,
                    'secid2'    => $publication[$j],
                    'type'      => 2,
                ));
            }
        }

        if (array_key_exists($j, $modification)) {
            if ($modification[$j] != '') {
                DB::table('publisujet')->insert(array(
                    'aid'       => $chng_aid,
                    'secid2'    => $modification[$j],
                    'type'      => 3,
                ));
            }
        }

        if (array_key_exists($j, $suppression)) {
            if ($suppression[$j] != '') {
                DB::table('publisujet')->insert(array(
                    'aid'       => $chng_aid,
                    'secid2'    => $suppression[$j],
                    'type'      => 4,
                ));
            }
        }
    }

    global $aid;
    logs::Ecr_Log('security', "UpdateRightsPubliSujet($chng_aid) by AID : $aid", '');

    Header('Location: '. site_url('admin.php?op=sections'));
}
// Fonctions DROIT des AUTEURS

settype($Mmembers, 'array');
settype($suppression, 'array');
settype($modification, 'array');
settype($publication, 'array');
settype($ok, 'integer');

switch ($op) {
    case 'new_rub_section':
        new_rub_section($type);
        break;

    case 'sections':
        sections();
        break;

    case 'sectionedit':
        sectionedit($secid);
        break;

    case 'sectionmake':
        sectionmake($secname, $image, $members, $Mmembers, $rubref, $introd);
        break;

    case 'sectiondelete':
        sectiondelete($secid, $ok);
        break;

    case 'sectionchange':
        sectionchange($secid, $secname, $image, $members, $Mmembers, $rubref, $introd);
        break;

    case 'rubriquedit':
        rubriquedit($rubid);
        break;

    case 'rubriquemake':
        rubriquemake($rubname, $introc);
        break;

    case 'rubriquedelete':
        rubriquedelete($rubid, $ok);
        break;

    case 'rubriquechange':
        rubriquechange($rubid, $rubname, $introc, $enligne);
        break;

    case 'secarticleadd':
        secarticleadd($secid, $title, $content, $autho, $members, $Mmembers);
        break;

    case 'secartedit':
        secartedit($artid);
        break;

    case 'secartchange':
        secartchange($artid, $secid, $title, $content, $members, $Mmembers);
        break;

    case 'secartchangeup':
        secartchangeup($artid, $secid, $title, $content, $members, $Mmembers);
        break;

    case 'secartdelete':
        secartdelete($artid, $ok);
        break;

    case 'secartpublish':
        secartpublish($artid, $secid, $title, $content, $author, $members, $Mmembers);
        break;

    case 'secartupdate':
        secartupdate($artid);
        break;

    case 'secartdelete2':
        secartdelete2($artid, $ok);
        break;

    case 'ordremodule':
        ordremodule();
        break;

    case 'ordrechapitre':
        ordrechapitre();
        break;

    case 'ordrecours':
        ordrecours();
        break;

    case 'majmodule':
        updateordre($rubid, '', '', $op, $ordre);
        break;

    case 'majchapitre':
        updateordre('', '', $secid, $op, $ordre);
        break;

    case 'majcours':
        updateordre('', $artid, '', $op, $ordre);
        break;

    case 'publishcompat':
        publishcompat($article);
        break;
    case 'updatecompat':
        updatecompat($article, $admin_rub, $idx);
        break;

    case 'droitauteurs':
        publishrights($author);
        break;
        
    case 'updatedroitauteurs':
        updaterights($chng_aid, $maxindex, $creation, $publication, $modification, $suppression);
        break;
}