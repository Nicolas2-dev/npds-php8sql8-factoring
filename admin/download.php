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

use npds\system\assets\js;
use npds\system\assets\css;
use npds\system\auth\groupe;
use npds\system\support\editeur;
use npds\system\language\language;
use npds\system\support\facades\DB;
use npds\system\fmanager\FileManagement;

if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'DownloadAdmin';
$f_titre = adm_translate('Téléchargements');

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

global $language;
$hlpfile = "manuels/$language/downloads.html";

/**
 * [groupe description]
 *
 * @param   string  $groupe  [$groupe description]
 *
 * @return  string
 */
function groupe(string $groupe): string 
{
    $les_groupes = explode(',', $groupe);
    $mX = groupe::liste_group();
    $nbg = 0;
    $str = '';

    foreach ($mX as $groupe_id => $groupe_name) {
        $selectionne = 0;

        if ($les_groupes) {
            foreach ($les_groupes as $groupevalue) {
                if (($groupe_id == $groupevalue) and ($groupe_id != 0)) $selectionne = 1;
            }
        }

        if ($selectionne == 1) {
            $str .= '<option value="' . $groupe_id . '" selected="selected">' . $groupe_name . '</option>';
        } else {
            $str .= '<option value="' . $groupe_id . '">' . $groupe_name . '</option>';
        }

        $nbg++;
    }

    if ($nbg > 5) {
        $nbg = 5;
    }

    // si on veux traiter groupe multiple multiple="multiple"  et name="Mprivs"
    return ('
    <select multiple="multiple" class="form-select" id="mpri" name="Mprivs[]" size="' . $nbg . '">
    ' . $str . '
    </select>');
}

/**
 * [droits description]
 *
 * @param   int              [ description]
 * @param   string  $member  [$member description]
 *
 * @return  void
 */
function droits(int|string $member): void
{
    echo '
    <div class="mb-3">
        <div class="form-check form-check-inline">';
    $checked = ($member == -127) ? ' checked="checked"' : '';
    echo '
            <input type="radio" id="adm" name="privs" class="form-check-input" value="-127" ' . $checked . ' />
            <label class="form-check-label" for="adm">' . adm_translate("Administrateurs") . '</label>
        </div>
        <div class="form-check form-check-inline">';
    $checked = ($member == -1) ? ' checked="checked"' : '';
    echo '
            <input type="radio" id="ano" name="privs" class="form-check-input" value="-1" ' . $checked . ' />
            <label class="form-check-label" for="ano">' . adm_translate("Anonymes") . '</label>
        </div>';
    echo '
        <div class="form-check form-check-inline">';
    if ($member > 0) {
        echo '
            <input type="radio" id="mem" name="privs" value="1" class="form-check-input" checked="checked" />
            <label class="form-check-label" for="mem">' . adm_translate("Membres") . '</label>
        </div>
        <div class="form-check form-check-inline">
            <input type="radio" id="tous" name="privs" class="form-check-input" value="0" />
            <label class="form-check-label" for="tous">' . adm_translate("Tous") . '</label>
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-form-label col-sm-12" for="mpri">' . adm_translate("Groupes") . '</label>
        <div class="col-sm-12">';
        echo groupe($member) . '
        </div>
    </div>';
    } else {
        $checked = ($member == 0) ? ' checked="checked"' : '';
        echo '
            <input type="radio" id="mem" name="privs" class="form-check-input" value="1" />
            <label class="form-check-label" for="mem">' . adm_translate("Membres") . '</label>
        </div>
        <div class="form-check form-check-inline">
            <input type="radio" id="tous" name="privs" class="form-check-input" value="0"' . $checked . ' />
            <label class="form-check-label" for="tous">' . adm_translate("Tous") . '</label>
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-form-label col-sm-12" for="mpri">' . adm_translate("Groupes") . '</label>
        <div class="col-sm-12">';
        echo groupe($member) . '
        </div>
    </div>';
    }
}

/**
 * [DownloadAdmin description]
 *
 * @return  void
 */
function DownloadAdmin(): void
{
    global $hlpfile, $f_meta_nom, $f_titre, $adminimg;

    include("themes/default/header.php");

    GraphicAdmin($hlpfile);
    adminhead($f_meta_nom, $f_titre, $adminimg);

    echo '
    <hr />
    <h3 class="my-3">' . adm_translate("Catégories") . '</h3>';

    $pseudocatid = '';

    $downloads = DB::table('downloads')->select('dcategory')->orderBy('dcategory')->get();

    foreach ($downloads as $download) {

        echo '
        <h4 class="mb-2"><a class="tog" id="show_cat_' . $pseudocatid . '" title="Déplier la liste"><i id="i_cat_' . $pseudocatid . '" class="fa fa-caret-down fa-lg text-primary"></i></a>
        ' . language::aff_langue(stripslashes($download['dcategory'])) . '</h4>';
        
        echo '
        <div class="mb-3" id="cat_' . $pseudocatid . '" style="display:none;">
        <table data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-show-columns="true" data-mobile-responsive="true" data-buttons-class="outline-secondary" data-icons-prefix="fa" data-icons="icons">
            <thead>
                <tr>
                <th data-sortable="true" data-halign="center" data-align="right">' . adm_translate("ID") . '</th>
                <th data-sortable="true" data-halign="center" data-align="right">' . adm_translate("Compteur") . '</th>
                <th data-sortable="true" data-halign="center" data-align="center">Typ.</th>
                <th data-halign="center" data-align="center">' . adm_translate("URL") . '</th>
                <th data-sortable="true" data-halign="center" >' . adm_translate("Nom de fichier") . '</th>
                <th data-halign="center" data-align="center">' . adm_translate("Version") . '</th>
                <th data-halign="center" data-align="right">' . adm_translate("Taille de fichier") . '</th>
                <th data-halign="center" >' . adm_translate("Date") . '</th>
                <th data-halign="center" data-align="center">' . adm_translate("Fonctions") . '</th>
                </tr>
            </thead>
            <tbody>';

        $downloadsX = DB::table('downloads')->select('did', 'dcounter', 'durl', 'dfilename', 'dfilesize', 'ddate', 'dver', 'perms')->where('dcategory', addslashes($download['dcategory']))->orderBy('did', 'ASC')->get();

        foreach ($downloadsX as $download) {

            if ($download['perms'] == '0') {
                $dperm = '<span title="' . adm_translate("Anonymes") . '<br />' . adm_translate("Membres") . '<br />' . adm_translate("Administrateurs") . '" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-html="true"><i class="far fa-user fa-lg"></i><i class="fas fa-user fa-lg"></i><i class="fa fa-user-cog fa-lg"></i></span>';
            } else if ($download['perms'] == '1') { 
                $dperm = '<span title="' . adm_translate("Membres") . '" data-bs-toggle="tooltip" data-bs-placement="right"><i class="fas fa-user fa-lg"></i></span>';
            } else if ($download['perms'] == '-127') { 
                $dperm = '<span title="' . adm_translate("Administrateurs") . '" data-bs-toggle="tooltip" data-bs-placement="right"><i class="fas fa-user-cog fa-lg"></i></span>';
            } else if ($download['perms'] == '-1') {
                $dperm = '<span title="' . adm_translate("Anonymes") . '"  data-bs-toggle="tooltip" data-bs-placement="right"><i class="far fa-user fa-lg"></i></span>';
            } else {
                $dperm = '<span title="' . adm_translate("Groupes") . '" data-bs-toggle="tooltip" data-bs-placement="right"><i class="fa fa-users fa-lg"></i></span>';
            }

            echo '
                <tr>
                <td>' . $download['did'] . '</td>
                <td>' . $download['dcounter'] . '</td>
                <td>' . $dperm . '</td>
                <td><a href="' . $download['durl'] . '" title="' . adm_translate("Téléchargements") . '<br />' . $download['durl'] . '" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-html="true"><i class="fa fa-download fa-2x"></i></a></td>
                <td>' . $download['dfilename'] . '</td>
                <td><span class="small">' . $download['dver'] . '</span></td>
                <td><span class="small">';
                
            $Fichier = new FileManagement;
            
            if ($download['dfilesize'] != 0) {
                echo $Fichier->file_size_format($download['dfilesize'], 1);
            } else {
                echo $Fichier->file_size_auto($download['durl'], 2);
            }

            echo '</span></td>
                <td class="small">' . $download['ddate'] . '</td>
                <td>
                    <a href="admin.php?op=DownloadEdit&amp;did=' . $download['did'] . '" title="' . adm_translate("Editer") . '" data-bs-toggle="tooltip" data-bs-placement="right"><i class="fa fa-edit fa-lg"></i></a>
                    <a href="admin.php?op=DownloadDel&amp;did=' . $download['did'] . '&amp;ok=0" title="' . adm_translate("Effacer") . '" data-bs-toggle="tooltip" data-bs-placement="right"><i class="fas fa-trash fa-lg text-danger ms-2"></i></a>
                </td>
                </tr>';
        }

        echo '
                </tbody>
            </table>
        </div>';
        echo '
        <script type="text/javascript">
            //<![CDATA[
                $( document ).ready(function() {
                    tog("cat_' . $pseudocatid . '","show_cat_' . $pseudocatid . '","hide_cat_' . $pseudocatid . '");
                })
            //]]>
        </script>';

        $pseudocatid++;
    }

    echo '
    <hr />
    <h3 class="mb-3">' . adm_translate("Ajouter un Téléchargement") . '</h3>
    <form action="admin.php" method="post" id="downloadadd" name="adminForm">
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="durl">' . adm_translate("Télécharger URL") . '</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="durl" name="durl" maxlength="320" required="required" />
    &nbsp;<a href="javascript:void(0);" onclick="window.open(\'admin.php?op=FileManagerDisplay\', \'wdir\', \'width=650, height=450, menubar=no, location=no, directories=no, status=no, copyhistory=no, toolbar=no, scrollbars=yes, resizable=yes\');">
    <span class="">[' . adm_translate("Parcourir") . ']</span></a>
                <span class="help-block text-end" id="countcar_durl"></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="dcounter">' . adm_translate("Compteur") . '</label>
            <div class="col-sm-8">
                <input class="form-control" type="number" id="dcounter" name="dcounter" maxlength="30" />
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="dfilename">' . adm_translate("Nom de fichier") . '</label>
                <div class="col-sm-8">
                <input class="form-control" type="text" id="dfilename" name="dfilename" maxlength="255" required="required" />
                <span class="help-block text-end" id="countcar_dfilename"></span>
                </div>
            </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="dver">' . adm_translate("Version") . '</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" name="dver" id="dver" maxlength="6" />
                <span class="help-block text-end" id="countcar_dver"></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="dfilesize">' . adm_translate("Taille de fichier") . ' (bytes)</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="dfilesize" name="dfilesize" maxlength="31" />
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="dweb">' . adm_translate("Propriétaire de la page Web") . '</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="dweb" name="dweb" maxlength="255" />
                <span class="help-block text-end" id="countcar_dweb"></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="duser">' . adm_translate("Propriétaire") . '</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="duser" name="duser" maxlength="30" />
                <span class="help-block text-end" id="countcar_duser"></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="dcategory">' . adm_translate("Catégorie") . '</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="dcategory" name="dcategory" maxlength="250" required="required"/>
                <span class="help-block text-end" id="countcar_dcategory"></span>
                <select class="form-select" name="sdcategory" onchange="adminForm.dcategory.value=options[selectedIndex].value">
                <option>' . adm_translate("Catégorie") . '</option>';


    $download_categorie = DB::table('downloads')->select('dcategory')->orderBy('dcategory')->distinct()->get();

    foreach ($download_categorie as $categ) {
        $dcategory = stripslashes($categ['dcategory']);
        echo '<option value="' . $dcategory . '">' . language::aff_langue($dcategory) . '</option>';
    }

    echo '
                </select>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-12" for="xtext">' . adm_translate("Description") . '</label>
            <div class="col-sm-12">
                <textarea class="tin form-control" id="xtext" name="xtext" rows="20" ></textarea>
            </div>
        </div>
        ' . editeur::aff_editeur('xtext', '') . '
        <fieldset>
            <legend>' . adm_translate("Droits") . '</legend>';

    droits('0');

    echo '
        </fieldset>
        <input type="hidden" name="op" value="DownloadAdd" />
        <div class="mb-3 row">
            <div class="col-sm-12">
                <button class="btn btn-primary" type="submit">' . adm_translate("Ajouter") . '</button>
            </div>
        </div>
    </form>';

    $arg1 = '
            var formulid = ["downloadadd"];
            inpandfieldlen("durl",320);
            inpandfieldlen("dfilename",255);
            inpandfieldlen("dver",6);
            inpandfieldlen("dfilesize",31);
            inpandfieldlen("dweb",255);
            inpandfieldlen("duser",30);
            inpandfieldlen("dcategory",250);
    ';

    css::adminfoot('fv', '', $arg1, '');
}

/**
 * [DownloadEdit description]
 *
 * @param   int   $did  [$did description]
 *
 * @return  void
 */
function DownloadEdit(int $did): void
{
    global $hlpfile, $f_meta_nom, $f_titre, $adminimg;

    include("themes/default/header.php");

    GraphicAdmin($hlpfile);
    adminhead($f_meta_nom, $f_titre, $adminimg);

    $download = DB::table('downloads')
                    ->select('did', 'dcounter', 'durl', 'dfilename', 'dfilesize', 'ddate', 'dweb', 'duser', 'dver', 'dcategory', 'ddescription', 'perms')
                    ->where('did', $did)->first();

    echo '
    <hr />
    <h3 class="mb-3">' . adm_translate("Editer un Téléchargement") . '</h3>
    <form action="admin.php" method="post" id="downloaded" name="adminForm">
        <input type="hidden" name="did" value="' . $download['did'] . '" />
        <input type="hidden" name="dcounter" value="' . $download['dcounter'] . '" />
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="durl">' . adm_translate("Télécharger URL") . '</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="durl" name="durl" value="' . $download['durl'] . '" maxlength="320" required="required" />
                <span class="help-block text-end" id="countcar_durl"></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="dfilename">' . adm_translate("Nom de fichier") . '</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="dfilename" name="dfilename" id="dfilename" value="' . $download['dfilename'] . '" maxlength="255" required="required" />
                <span class="help-block text-end" id="countcar_dfilename"></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="dver">' . adm_translate("Version") . '</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" name="dver" id="dver" value="' . $download['dver'] . '" maxlength="6" />
                <span class="help-block text-end" id="countcar_dver"></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="dfilesize">' . adm_translate("Taille de fichier") . ' (bytes)</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="dfilesize" name="dfilesize" value="' . $download['dfilesize'] . '" maxlength="31" />
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="dweb">' . adm_translate("Propriétaire de la page Web") . '</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="dweb" name="dweb" value="' . $download['dweb'] . '" maxlength="255" />
                <span class="help-block text-end" id="countcar_dweb"></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="duser">' . adm_translate("Propriétaire") . '</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="duser" name="duser" value="' . $download['duser'] . '" maxlength="30" />
                <span class="help-block text-end" id="countcar_duser"></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="dcategory">' . adm_translate("Catégorie") . '</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="dcategory" name="dcategory" value="' . stripslashes($download['dcategory']) . '" maxlength="250" required="required" />
                <span class="help-block text-end"><span id="countcar_dcategory"></span></span>
                <select class="form-select" name="sdcategory" onchange="adminForm.dcategory.value=options[selectedIndex].value">';
    
    $download_categorie = DB::table('downloads')->select('dcategory')->orderBy('dcategory')->distinct()->get();

    foreach ($download_categorie as $categ) {

        $sel = (($categ['dcategory'] == $download['dcategory']) ? 'selected' : '');
        $Xdcategory = stripslashes($categ['dcategory']);
        echo '<option ' . $sel . ' value="' . $Xdcategory . '">' . language::aff_langue($Xdcategory) . '</option>';
    }

    echo '
                </select>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-12" for="xtext">' . adm_translate("Description") . '</label>
            <div class="col-sm-12">
                <textarea class="tin form-control" id="xtext" name="xtext" rows="20" >' . stripslashes($download['ddescription']) . '</textarea>
            </div>
        </div>
        ' . editeur::aff_editeur('xtext', '');

    echo '
        <fieldset>
            <legend>' . adm_translate("Droits") . '</legend>';

    droits($download['perms']);

    echo '
        </fieldset>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4">' . adm_translate("Changer la date") . '</label>
            <div class="col-sm-8">
                <div class="form-check my-2">
                <input type="checkbox" id="ddate" name="ddate" class="form-check-input" value="yes" />
                <label class="form-check-label" for="ddate">' . adm_translate("Oui") . '</label>
                </div>
            </div>
        </div>
        <div class="mb-3 row">
            <div class="col-sm-12">
                <input type="hidden" name="op" value="DownloadSave" />
                <input class="btn btn-primary" type="submit" value="' . adm_translate("Sauver les modifications") . '" />
            </div>
        </div>
    </form>';

    $arg1 = '
        var formulid = ["downloaded"];
        inpandfieldlen("durl",320);
        inpandfieldlen("dfilename",255);
        inpandfieldlen("dver",6);
        inpandfieldlen("dfilesize",31);
        inpandfieldlen("dweb",255);
        inpandfieldlen("duser",30);
        inpandfieldlen("dcategory",250);
    ';

    css::adminfoot('fv', '', $arg1, '');
}

/**
 * [DownloadSave description]
 *
 * @param   int     $did          [$did description]
 * @param   int     $dcounter     [$dcounter description]
 * @param   string  $durl         [$durl description]
 * @param   string  $dfilename    [$dfilename description]
 * @param   int     $dfilesize    [$dfilesize description]
 * @param   string  $dweb         [$dweb description]
 * @param   string  $duser        [$duser description]
 * @param   string  $ddate        [$ddate description]
 * @param   string  $dver         [$dver description]
 * @param   string  $dcategory    [$dcategory description]
 * @param   string  $sdcategory   [$sdcategory description]
 * @param   string  $description  [$description description]
 * @param   string  $privs        [$privs description]
 * @param   array   $Mprivs       [$Mprivs description]
 *
 * @return  void
 */
function DownloadSave(int $did, int $dcounter, string $durl, string $dfilename, int $dfilesize, string $dweb, string $duser, ?string $ddate = 'no', string $dver, string $dcategory, string $sdcategory, string $description, string $privs, array $Mprivs): void
{
    if ($privs == 1) {
        if ($Mprivs != '') {
            $privs = implode(',', $Mprivs);
        }
    }

    $sdcategory = addslashes($sdcategory);
    $dcategory = (!$dcategory) ? $sdcategory : addslashes($dcategory);
    $description = addslashes($description);

    if ($ddate == "yes") {
        DB::table('downloads')->where('did', $did)->update(array(
            'dcounter'      => $dcounter,
            'durl'          => $durl,
            'dfilename'     => $dfilename,
            'dfilesize'     => $dfilesize,
            'ddate'         => date("Y-m-d"),
            'dweb'          => $dweb,
            'duser'         => $duser,
            'dver'          => $dver,
            'dcategory'     => $dcategory,
            'ddescription'  => $description,
            'perms'         => $privs,

        ));
    } else {
        DB::table('downloads')->where('did', $did)->update(array(
            'dcounter'      => $dcounter,
            'durl'          => $durl,
            'dfilename'     => $dfilename,
            'dfilesize'     => $dfilesize,
            'dweb'          => $dweb,
            'duser'         => $duser,
            'dver'          => $dver,
            'dcategory'     => $dcategory,
            'ddescription'  => $description,
            'perms'         => $privs,
        ));
    }

    Header("Location: admin.php?op=DownloadAdmin");
}

/**
 * [DownloadAdd description]
 *
 * @param   int     $dcounter     [$dcounter description]
 * @param   string  $durl         [$durl description]
 * @param   string  $dfilename    [$dfilename description]
 * @param   int     $dfilesize    [$dfilesize description]
 * @param   string  $dweb         [$dweb description]
 * @param   string  $duser        [$duser description]
 * @param   string  $dver         [$dver description]
 * @param   string  $dcategory    [$dcategory description]
 * @param   string     $sdcategory   [$sdcategory description]
 * @param   string  $description  [$description description]
 * @param   string  $privs        [$privs description]
 * @param   array   $Mprivs       [$Mprivs description]
 *
 * @return  void
 */
function DownloadAdd(int $dcounter, string $durl, string $dfilename, int $dfilesize, string $dweb, string $duser, string $dver, string $dcategory, string $sdcategory, string $description, string $privs, array $Mprivs): void
{
    if ($privs == 1) {
        if ($Mprivs > 1 and $Mprivs <= 127 and $Mprivs != '') {
            $privs = $Mprivs;
        }
    }

    $sdcategory = addslashes($sdcategory);
    $dcategory = (!$dcategory) ? $sdcategory : addslashes($dcategory);
    $description = addslashes($description);

    if (($durl) and ($dfilename)) {
        DB::table('downloads')->insert(array(
            'dcounter'      => 0,
            'durl'          => $durl,
            'dfilename'     => $dfilename,
            'dfilesize'     => 0,
            'ddate'         => date("Y-m-d"),
            'dweb'          => $dweb,
            'duser'         => $duser,
            'dver'          => $dver,
            'dcategory'     => $dcategory,
            'ddescription'  => $description,
            'perms'         => $privs,
        ));
    }
    
    Header("Location: admin.php?op=DownloadAdmin");
}

/**
 * [DownloadDel description]
 *
 * @param   int   $did  [$did description]
 * @param   int   $ok   [$ok description]
 *
 * @return  void
 */
function DownloadDel(int $did, int $ok = 0): void
{
    global $f_meta_nom;

    if ($ok == 1) {
        DB::table('downloads')->where('did', $did)->delete();

        Header("Location: admin.php?op=DownloadAdmin");
    } else {
        global $hlpfile, $f_titre, $adminimg;

        include("themes/default/header.php");

        GraphicAdmin($hlpfile);
        adminhead($f_meta_nom, $f_titre, $adminimg);

        echo ' 
        <div class="alert alert-danger">
            <strong>' . adm_translate("ATTENTION : êtes-vous sûr de vouloir supprimer ce fichier téléchargeable ?") . '</strong>
        </div>
        <a class="btn btn-danger" href="admin.php?op=DownloadDel&amp;did=' . $did . '&amp;ok=1" >' . adm_translate("Oui") . '</a>&nbsp;<a class="btn btn-secondary" href="admin.php?op=DownloadAdmin" >' . adm_translate("Non") . '</a>';
        
        css::adminfoot('', '', '', '');
    }
}
