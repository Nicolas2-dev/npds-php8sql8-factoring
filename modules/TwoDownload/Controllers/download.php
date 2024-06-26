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

use npds\support\date\date;
use npds\support\auth\users;
use npds\support\str;
use npds\support\mail\mailler;
use npds\system\config\Config;
use npds\support\fmanager\File;
use npds\support\security\hack;
use npds\support\language\language;
use npds\system\support\facades\DB;
use npds\support\pagination\paginator;
use npds\support\fmanager\FileManagement;
use npds\system\support\facades\Request;


if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

/**
 * [geninfo description]
 *
 * @param   int   $did           [$did description]
 * @param   int   $out_template  [$out_template description]
 *
 * @return  void
 */
function geninfo(int $did, int $out_template): void 
{
    $res_download = DB::table('downloads')
        ->select('dcounter', 'durl', 'dfilename', 'dfilesize', 'ddate', 'dweb', 'duser', 'dver', 'dcategory', 'ddescription', 'perms')
        ->where('did', $did)
        ->first();

    $okfile = false;

    if (!stristr($res_download['perms'], ',')) { 
        $okfile = users::autorisation($res_download['perms']);
    } else {
        $ibidperm = explode(',', $res_download['perms']);
        
        foreach ($ibidperm as $v) {
            if (users::autorisation($v)) {
                $okfile = true;
                break;
            }
        }
    }

    if ($okfile) {

        if ($out_template == 1) {
            include('themes/default/header.php');

            echo '
            <h2 class="mb-3">' . __d('two_download', 'Chargement de fichiers') . '</h2>
            <div class="card">
                <div class="card-header"><h4>' . $res_download['filename'] . '<span class="ms-3 text-muted small">@' . $res_download['durl'] . '</h4></div>
                <div class="card-body">';
        }

        echo '<p><strong>' . __d('two_download', 'Taille du fichier') . ' : </strong>';

        $objZF = new FileManagement;

        if ($res_download['dfilesize'] != 0) {
            echo $objZF->file_size_format($res_download['dfilesize'], 1);
        } else {
            echo $objZF->file_size_auto($res_download['durl'], 2);
        }

        echo '</p>
                <p><strong>' . __d('two_download', 'Version') . '&nbsp;:</strong>&nbsp;' . $res_download['dver'] . '</p>
                <p><strong>' . __d('two_download', 'Date de chargement sur le serveur') . '&nbsp;:</strong>&nbsp;' . date::convertdate($res_download['ddate']) . '</p>
                <p><strong>' . __d('two_download', 'Chargements') . '&nbsp;:</strong>&nbsp;' . str::wrh($res_download['dcounter']) . '</p>
                <p><strong>' . __d('two_download', 'Catégorie') . '&nbsp;:</strong>&nbsp;' . language::aff_langue(stripslashes($res_download['dcategory'])) . '</p>
                <p><strong>' . __d('two_download', 'Description') . '&nbsp;:</strong>&nbsp;' . language::aff_langue(stripslashes($res_download['ddescription'])) . '</p>
                <p><strong>' . __d('two_download', 'Auteur') . '&nbsp;:</strong>&nbsp;' . $res_download['duser'] . '</p>
                <p><strong>' . __d('two_download', 'Page d\'accueil') . '&nbsp;:</strong>&nbsp;<a href="http://' . $res_download['dweb'] . '" target="_blank">' . $res_download['dweb'] . '</a></p>';
        
        if ($out_template == 1) {
            echo '
                <a class="btn btn-primary" href="'. site_url('download.php?op=mydown&amp;did='. $did) .'" target="_blank" title="'. __d('two_download', 'Charger maintenant') .'" data-bs-toggle="tooltip" data-bs-placement="right"><i class="fa fa-lg fa-download"></i></a>
                </div>
            </div>';

            include('themes/default/footer.php');
        }
    } else {
        Header('Location: ' . site_url('download.php'));
    }
}

/**
 * [tlist description]
 *
 * @return  void
 */
function tlist(): void
{
    $dcategory = Request::query('dcategory');
    $sortby = Request::query('sortby');

    if (!isset($dcategory)) {
        $dcategory = addslashes(Config::get('npds.download_cat'));
    }

    $cate = stripslashes($dcategory);

    echo '
    <p class="lead">' . __d('two_download', 'Sélectionner une catégorie') . '</p>
    <div class="d-flex flex-column flex-sm-row flex-wrap justify-content-between my-3 border rounded">
        <p class="p-2 mb-0 ">';

    $acount = DB::table('downloads')
                ->select(DB::raw('COUNT(*)'))
                ->count();

    if (($cate == __d('two_download', 'Tous')) or ($cate == '')) {
        echo '<i class="fa fa-folder-open fa-2x text-muted align-middle me-2"></i><strong><span class="align-middle">' . __d('two_download', 'Tous') . '</span>
    <span class="badge bg-secondary ms-2 float-end my-2">' . $acount . '</span></strong>';
    } else {
        echo '<a href="' . site_url('download.php?dcategory=' . __d('two_download', 'Tous') . '&amp;sortby=' . $sortby) .'"><i class="fa fa-folder fa-2x align-middle me-2"></i><span class="align-middle">' . __d('two_download', 'Tous') . '</span></a><span class="badge bg-secondary ms-2 float-end my-2">' . $acount . '</span>';
    }

    echo '</p>';

    foreach (DB::table('downloads')
                ->distinct()
                ->select('dcategory', DB::raw('COUNT(dcategory) as count'))
                ->groupeBy('dcategory')
                ->orderBy('dcategory')
                ->get() as $download) 
    {
        $category = stripslashes($download['dcategory']);
        
        echo '<p class="p-2 mb-0">';
        
        if ($category == $cate) {
            echo '<i class="fa fa-folder-open fa-2x text-muted align-middle me-2"></i><strong class="align-middle">' . language::aff_langue($category) . '<span class="badge bg-secondary ms-2 float-end my-2">' . $download['count'] . '</span></strong>';
        } else {
            echo '<a href="' . site_url('download.php?dcategory=' . urlencode($category) . '&amp;sortby=' . $sortby) .'"><i class="fa fa-folder fa-2x align-middle me-2"></i><span class="align-middle">' . language::aff_langue($category) . '</span></a><span class="badge bg-secondary ms-2 my-2 float-end">' . $download['count'] . '</span>';
        }

        echo '</p>';
    }

    echo '</div>';
}

/**
 * [act_dl_tableheader description]
 *
 * @param   string  $dcategory    [$dcategory description]
 * @param   string  $fieldname    [$fieldname description]
 * @param   string  $englishname  [$englishname description]
 *
 * @return  void
 */
function act_dl_tableheader(string $dcategory, string $fieldname, string $englishname): void
{
    echo '
    <a class="d-none d-sm-inline" href="' . site_url('download.php?dcategory=' . $dcategory . '&amp;sortby=' . $fieldname) .'" title="' . __d('two_download', 'Croissant') . '" data-bs-toggle="tooltip" ><i class="fa fa-sort-amount-down"></i></a>&nbsp;
    ' . __d('two_download', '$englishname') . '&nbsp;
    <a class="d-none d-sm-inline" href="' . site_url('download.php?dcategory=' . $dcategory . '&amp;sortby=' . $fieldname . '&amp;sortorder=DESC') .'" title="' . __d('two_download', 'Décroissant') . '" data-bs-toggle="tooltip" ><i class="fa fa-sort-amount-up"></i></a>';
}

/**
 * [inact_dl_tableheader description]
 *
 * @param   string  $dcategory    [$dcategory description]
 * @param   string  $fieldname    [$fieldname description]
 * @param   string  $englishname  [$englishname description]
 *
 * @return  void
 */
function inact_dl_tableheader(string $dcategory, string $fieldname, string $englishname): void
{
    echo '
    <a class="d-none d-sm-inline" href="' . site_url('download.php?dcategory=' . $dcategory . '&amp;sortby=' . $fieldname) .'" title="' . __d('two_download', 'Croissant') . '" data-bs-toggle="tooltip"><i class="fa fa-sort-amount-down" ></i></a>&nbsp;
    ' . __d('two_download', '$englishname') . '&nbsp;
    <a class="d-none d-sm-inline" href="' . site_url('download.php?dcategory=' . $dcategory . '&amp;sortby=' . $fieldname . '&amp;sortorder=DESC') .'" title="' . __d('two_download', 'Décroissant') . '" data-bs-toggle="tooltip"><i class="fa fa-sort-amount-up" ></i></a>';
}

/**
 * [dl_tableheader description]
 *
 * @return  void
 */
function dl_tableheader(): void
{
    echo '</td>
    <td>';
}

/**
 * [popuploader description]
 *
 * @param   int     $did        [$did description]
 * @param   string  $dfilename  [$dfilename description]
 * @param   bool    $aff        [$aff description]
 *
 * @return  [type]
 */
function popuploader(int $did, string $dfilename, bool $aff)
{
    $out_template = 0;

    if ($aff) {
        echo '
            <a class="me-3" href="#" data-bs-toggle="modal" data-bs-target="#mo' . $did . '" title="' . __d('two_download', 'Information sur le fichier') . '" data-bs-toggle="tooltip"><i class="fa fa-info-circle fa-2x"></i></a>
            <a href="' . site_url('download.php?op=mydown&amp;did=' . $did) .'" target="_blank" title="' . __d('two_download', 'Charger maintenant') . '" data-bs-toggle="tooltip"><i class="fa fa-download fa-2x"></i></a>
            <div class="modal fade" id="mo' . $did . '" tabindex="-1" role="dialog" aria-labelledby="my' . $did . '" aria-hidden="true">
                <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <h4 class="modal-title text-start" id="my' . $did . '">' . __d('two_download', 'Information sur le fichier') . ' - ' . $dfilename . '</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" title=""></button>
                    </div>
                    <div class="modal-body text-start">';

        geninfo($did, $out_template);

        echo '
                    </div>
                    <div class="modal-footer">
                        <a class="" href="' . site_url('download.php?op=mydown&amp;did=' . $did) .'" title="' . __d('two_download', 'Charger maintenant') . '"><i class="fa fa-2x fa-download"></i></a>
                    </div>
                </div>
                </div>
            </div>';
    }
}

/**
 * [SortLinks description]
 *
 * @param   string  $dcategory  [$dcategory description]
 * @param   string  $sortby     [$sortby description]
 *
 * @return  void
 */
function SortLinks(string $dcategory, string $sortby): void
{
    $dcategory = stripslashes($dcategory);

    echo '
        <thead>
            <tr>
                <th class="text-center">' . __d('two_download', 'Fonctions') . '</th>
                <th class="text-center n-t-col-xs-1" data-sortable="true" data-sorter="htmlSorter">' . __d('two_download', 'Type') . '</th>
                <th class="text-center">';

    if ($sortby == 'dfilename' or !$sortby) {
        act_dl_tableheader($dcategory, "dfilename", "Nom");
    } else {
        inact_dl_tableheader($dcategory, "dfilename", "Nom");
    }

    echo '</th>
                <th class="text-center">';

    if ($sortby == "dfilesize") {
        act_dl_tableheader($dcategory, "dfilesize", "Taille");
    } else {
        inact_dl_tableheader($dcategory, "dfilesize", "Taille");
    }

    echo '</th>
                <th class="text-center">';

    if ($sortby == "dcategory") {
        act_dl_tableheader($dcategory, "dcategory", "Catégorie");
    } else {
        inact_dl_tableheader($dcategory, "dcategory", "Catégorie");
    }

    echo '</th>
                <th class="text-center">';

    if ($sortby == "ddate") {
        act_dl_tableheader($dcategory, "ddate", "Date");
    } else {
        inact_dl_tableheader($dcategory, "ddate", "Date");
    }

    echo '</th>
                <th class="text-center">';

    if ($sortby == "dver") {
        act_dl_tableheader($dcategory, "dver", "Version");
    } else {
        inact_dl_tableheader($dcategory, "dver", "Version");
    }

    echo '</th>
                <th class="text-center">';

    if ($sortby == "dcounter") {
        act_dl_tableheader($dcategory, "dcounter", "Compteur");
    } else {
        inact_dl_tableheader($dcategory, "dcounter", "Compteur");
    }

    echo '</th>';

    if (users::getUser() or users::autorisation(-127)) {
        echo '<th class="text-center n-t-col-xs-1"></th>';
    }

    echo '
            </tr>
        </thead>';
}

/**
 * [listdownloads description]
 *
 * @param   string  $dcategory  [$dcategory description]
 * @param   string  $sortby     [$sortby description]
 * @param   string  $sortorder  [$sortorder description]
 *
 * @return  void
 */
function listdownloads(string $dcategory, string $sortby, string $sortorder): void
{
    if (!isset($dcategory)) {
        $dcategory = addslashes(Config::get('npds.download_cat'));
    }

    if (!$sortby) {
        $sortby = 'dfilename';
    }

    if (($sortorder != "ASC") && ($sortorder != "DESC")) {
        $sortorder = "ASC";
    }

    echo '<p class="lead">';

    echo __d('two_download', 'Affichage filtré pour') . "&nbsp;<i>";

    if ($dcategory == __d('two_download', 'Tous')) {
        echo '<b>' . __d('two_download', 'Tous') . '</b>';
    } else {
        echo '<b>' . language::aff_langue(stripslashes($dcategory)) . '</b>';
    }

    echo '</i>&nbsp;' . __d('two_download', 'trié par ordre') . '&nbsp;';

    // Shiney SQL Injection 11/2011
    $sortby2 = '';

    if ($sortby == 'dfilename') {
        $sortby2 = __d('two_download', 'Nom') . "";
    }

    if ($sortby == 'dfilesize') {
        $sortby2 = __d('two_download', 'Taille du fichier') . "";
    }

    if ($sortby == 'dcategory') {
        $sortby2 = __d('two_download', 'Catégorie') . "";
    }

    if ($sortby == 'ddate') {
        $sortby2 = __d('two_download', 'Date de création') . "";
    }

    if ($sortby == 'dver') {
        $sortby2 = __d('two_download', 'Version') . "";}


    if ($sortby == 'dcounter')  {
        $sortby2 = __d('two_download', 'Chargements') . "";
    }

    // Shiney SQL Injection 11/2011
    if ($sortby2 == '') {
        $sortby = 'dfilename';
    }

    echo __d('two_download', 'de') . '&nbsp;<i><b>' . $sortby2 . '</b></i>
    </p>';

    echo '
    <table class="table table-hover mb-3 table-sm" id ="lst_downlo" data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-show-columns="true"
    data-mobile-responsive="true" data-buttons-class="outline-secondary" data-icons-prefix="fa" data-icons="icons">';

    sortlinks($dcategory, $sortby);

    echo '<tbody>';

    if ($dcategory == __d('two_download', 'Tous')) {
        $total = DB::table('downloads')->select(DB::raw('COUNT(*) as count'))->count();
    } else {
        $total = DB::table('downloads')->select(DB::raw('COUNT(*) as count'))->where('dcategory', addslashes($dcategory))->count();
    }

    $perpage = Config::get('npds.perpage');

    $page = Request::query('page');

    if (!isset($page)) {
        $page = 0;
    }

    if ($total > $perpage) {
        $pages = ceil($total / $perpage);
        
        if ($page > $pages) {
            $page = $pages;
        }

        if (!$page) {
            $page = 1;
        }

        $offset = ($page - 1) * $perpage;
    } else {
        $offset = 0;
        $pages = 1;
        $page = 1;
    }

    //  
    $nbPages = ceil($total / $perpage);
    $current = 1;

    if ($page >= 1) {
        $current = $page;
    } else if ($page < 1) {
        $current = 1;
    } else {
        $current = $nbPages;
    }

    if ($dcategory == __d('two_download', 'Tous')) {
        $result  = DB::table('downloads')
                    ->select('*')
                    ->orderBy($sortby, $sortorder)
                    ->limit($perpage)
                    ->offset($offset)
                    ->get();
    } else {
        $result  = DB::table('downloads')
                    ->select('*')
                    ->where('dcategory', addslashes($dcategory))
                    ->limit($perpage)
                    ->offset($offset)
                    ->get();
    }

    foreach ($result as $download) { 

        // keep for extension
        $Fichier = new File($download['durl']); 
        $FichX = new FileManagement;
        $okfile = '';
        
        if (!stristr($download['perms'], ',')) {
            $okfile = users::autorisation($download['perms']);
        } else {
            $ibidperm = explode(',', $download['perms']);
            
            foreach ($ibidperm as $v) {
                if (users::autorisation($v) == true) {
                    $okfile = true;
                    break;
                }
            }
        }

        echo '
            <tr>
                <td class="text-center">';

        if ($okfile == true) {
            echo popuploader((int) $download['did'], $download['dfilename'], true);
        } else {
            echo popuploader((int) $download['did'], $download['dfilename'], false);
            echo '<span class="text-danger"><i class="fa fa-ban fa-lg me-1"></i>' . __d('two_download', 'Privé') . '</span>';
        }

        echo '</td>
                <td class="text-center">' . $Fichier->Affiche_Extention('webfont') . ' </td>
                <td>';

        if ($okfile == true) {
            echo '<a href="' . site_url('download.php?op=mydown&amp;did=' . $download['did']) .'" target="_blank">' . $download['dfilename'] . '</a>';
        } else {
            echo '<span class="text-danger"><i class="fa fa-ban fa-lg me-1"></i>...</span>';
        }

        echo '</td>
                <td class="small text-center">';

        if ($download['dfilesize'] != 0) {
            echo $FichX->file_size_format($download['dfilesize'], 1);
        } else {
            echo $FichX->file_size_auto($download['durl'], 2);
        }

        echo '</td>
                <td>' . language::aff_langue(stripslashes($download['dcategory'])) . '</td>
                <td class="small text-center">' . date::convertdate($download['ddate']) . '</td>
                <td class="small text-center">' . $download['dver'] . 'hhh</td>
                <td class="small text-center">' . str::wrh($download['dcounter']) . '</td>';

        if (users::getUser() != '' or users::autorisation(-127)) {
            echo '<td>';

            if (($okfile == true and users::getUser() != '') or users::autorisation(-127)) {
                echo '<a href="' . site_url('download.php?op=broken&amp;did=' . $download['did']) .'" title="' . __d('two_download', 'Rapporter un lien rompu') . '" data-bs-toggle="tooltip"><i class="fas fa-lg fa-unlink"></i></a>';
            }

            echo '</td>';
        }

        echo '</tr>';
    }

    echo '
        </tbody>
    </table>';

    $dcategory = StripSlashes($dcategory);

    echo '<div class="mt-3"></div>' . paginator::paginate_single(site_url('download.php?dcategory='. $dcategory .'&amp;sortby='. $sortby .'&amp;sortorder='. $sortorder .'&amp;page='), '', $nbPages, $current, $adj = 3, '', $page);
}

/**
 * [main description]
 *
 * @return  void
 */
function main(): void
{
    $dcategory = Request::query('dcategory');
    $sortorder = Request::query('sortorder');
    $sortby = Request::query('sortby');

    if (!isset($dcategory)) {
        $dcategory = addslashes(Config::get('npds.download_cat'));
    } else {
        $dcategory  = hack::removeHack(stripslashes(htmlspecialchars(urldecode((string) $dcategory), ENT_QUOTES, 'utf-8')));
        $dcategory = str_replace("&#039;", "\'", $dcategory);
    }

    if (!isset($sortorder)) {
        $sortorder = 'ASC';
    }

    if (!isset($sortby)) {
        $sortby = '';
    } else {
        $sortby  = hack::removeHack(stripslashes(htmlspecialchars(urldecode((string) $sortby), ENT_QUOTES, 'utf-8')));        
    }

    include("themes/default/header.php");

    echo '
    <h2>' . __d('two_download', 'Chargement de fichiers') . '</h2>
    <hr />';

    tlist();

    if ($dcategory != __d('two_download', 'Aucune catégorie')) {
        listdownloads($dcategory, $sortby, $sortorder);
    }

    if (file_exists("storage/static/download.ban.txt")) {
        include("storage/static/download.ban.txt");
    }

    include("themes/default/footer.php");
}

/**
 * [transferfile description]
 *
 * @param   int   $did  [$did description]
 *
 * @return  void
 */
function transferfile(): void
{
    $res = DB::table('downloads')
            ->select('dcounter', 'durl', 'perms')
            ->where('did', $did = Request::query('did'))
            ->first();

    $dcounter = $res['dcounter']; 
    $durl = $res['durl'];
    $dperm = $res['perms'];

    if (!$durl) {
        include("themes/default/header.php");

        echo '
        <h2>' . __d('two_download', 'Chargement de fichiers') . '</h2>
        <hr />
        <div class="lead alert alert-danger">' . __d('two_download', 'Ce fichier n\'existe pas ...') . '</div>';

        include("themes/default/footer.php");
    } else {
        if (stristr($dperm, ',')) {
            $ibid = explode(',', $dperm);
            
            foreach ($ibid as $v) {
                $aut = true;
                
                if (users::autorisation($v) == true) {
                    $dcounter++;
                    DB::table('downloads')->where('did', $did)->update(array(
                        'dcounter'       => $dcounter,
                    ));

                    header("location: " . str_replace(basename($durl), rawurlencode(basename($durl)), $durl));
                    break;
                } else {
                    $aut = false;
                }
            }

            if ($aut == false) {
                Header('Location: ' . site_url('download.php'));
            }
        } else {
            if (users::autorisation($dperm)) {
                $dcounter++;
                DB::table('downloads')->where('did', $did)->update(array(
                    'dcounter'       => $dcounter,
                ));

                header("location: " . str_replace(basename($durl), rawurlencode(basename($durl)), $durl));
            } else
                Header('Location: ' . site_url('download.php'));
        }
    }
}

/**
 * [broken description]
 *
 * @param   int   $did  [$did description]
 *
 * @return  void
 */
function broken(): void
{
    if (users::getUser()) {
        if ($did = Request::query('did')) {
            
            $message = Config::get('npds.nuke_url') . "\n" . __d('two_download', 'Téléchargements') . " ID : $did\n" . __d('two_download', 'Auteur') . users::cookieUser(1) ." / IP : " . getip() . "\n\n";
            $message .= Config::get('signature.message');
            
            mailler::send_email(Config::get('npds.notify_email'), html_entity_decode(__d('two_download', 'Rapporter un lien rompu'), ENT_COMPAT | ENT_HTML401, 'utf-8'), nl2br($message), Config::get('npds.notify_from'), false, "html", '');
            
            include("themes/default/header.php");
            
            echo '
            <div class="alert alert-success">
            <p class="lead">' . __d('two_download', 'Pour des raisons de sécurité, votre nom d\'utilisateur et votre adresse IP vont être momentanément conservés.') . '<br />' . __d('two_download', 'Merci pour cette information. Nous allons l\'examiner dès que possible.') . '</p>
            </div>';

            include("themes/default/footer.php");
        } else {
            Header('Location: ' . site_url('download.php'));
        }
    } else {
        Header('Location: ' . site_url('download.php'));
    }
}

switch (Request::input('op')) {
    case 'main':
        main();
        break;
 
    case 'mydown':
        transferfile();
        break;

    case 'broken':
        broken();
        break;
        
    default:
        main();
        break;
}
