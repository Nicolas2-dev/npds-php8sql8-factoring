<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* This version name NPDS Copyright (c) 2001-2023 by Philippe Brunier   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
/************************************************************************/
/* Module-Install Version 1.1 - Mai 2005                                */
/* --------------------------                                           */
/* Copyright (c) 2005 Boris L'Ordi-Dépanneur & Hotfirenet               */
/*                                                                      */
/* Version 1.2 - 22 Avril 2009                                          */
/* --------------------------                                           */
/*                                                                      */
/* Modifié par jpb et phr pour le rendre compatible avec Evolution      */
/* Version 1.3 - 2015                                                   */
/************************************************************************/
declare(strict_types=1);

use npds\support\assets\css;
use npds\system\support\facades\DB;

if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'modules';
$f_titre = adm_translate("Gestion, Installation Modules");

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

include("themes/default/header.php");

GraphicAdmin(manuel('modules'));
adminhead($f_meta_nom, $f_titre);

$handle = opendir('modules');
$modlist = '';
while (false !== ($file = readdir($handle))) {
    if (!@file_exists("modules/$file/kernel")) {
        if (is_dir("modules/$file") and ($file != '.') and ($file != '..')) {
            $modlist .= "$file ";
        }
    }
}
closedir($handle);
$modlist = explode(' ', rtrim($modlist));

foreach (DB::table('modules')->select('mnom')->get() as $module) {
    if (!in_array($module['mnom'], $modlist)) {
        DB::table('modules')->where('mnom', $module['mnom'])->delete();
    }
}

foreach ($modlist as $value) {
    $moexiste = DB::table('modules')->select('mnom')->where('mnom', $value)->first();

    if ($moexiste !== 1) {
        DB::table('modules')->insert(array(
            'mnom'       => $value,
            'minstall'   => 0,
        ));
    }
}

echo '
    <hr />
    <h3>'. adm_translate("Les modules") .'</h3>
    <table id="tad_modu" data-toggle="table" data-striped="false" data-show-toggle="true" data-mobile-responsive="true" data-buttons-class="outline-secondary" data-icons="icons" data-icons-prefix="fa">
        <thead>
            <tr>
                <th data-align="center" class="n-t-col-xs-1"><img class="adm_img" src="assets/images/admin/module.png" alt="icon_module" /></th>
                <th data-sortable="true">'. adm_translate('Nom') .'</th>
                <th data-align="center" class="n-t-col-xs-2" >'. adm_translate('Fonctions') .'</th>
            </tr>
        </thead>
        <tbody>';

$modules = DB::table('modules')->select('mid', 'mnom', 'minstall')->orderBy('mid')->get();

foreach ($modules as $module) {
    $icomod = '';
    $clatd = '';

    $icomod = file_exists("modules/" . $module["mnom"] . "/" . $module["mnom"] . ".png") ?
        '<img class="adm_img" src="modules/'. $module["mnom"] .'/'. $module["mnom"] .'.png" alt="icon_'. $module["mnom"] .'" title="" />' :
        '<img class="adm_img" src="assets/images/admin/module.png" alt="icon_module" title="" />';

    if ($module["minstall"] == 0) {
        $status_chngac = file_exists("modules/" . $module["mnom"] . "/install.conf.php") 
            ? '<a class="text-success" href="'. site_url('admin.php?op=Module-Install&amp;ModInstall='. $module["mnom"] .'&amp;subop=install') .'" ><i class="fa fa-compress fa-lg"></i><i class="fa fa-puzzle-piece fa-2x fa-rotate-90" title="'. adm_translate("Installer le module") .'" data-bs-toggle="tooltip"></i></a>' 
            : '<a class="text-success" href="'. site_url('admin.php?op=Module-Install&amp;ModInstall='. $module["mnom"] .'&amp;subop=install') .'"><i class="fa fa-check fa-lg"></i><i class="fa fa fa-puzzle-piece fa-2x fa-rotate-90" title="'. adm_translate("Pas d'installeur disponible") .' '. adm_translate("Marquer le module comme installé") .'" data-bs-toggle="tooltip"></i></a>';
        $clatd = 'table-danger';
    } else {
        $status_chngac =  file_exists("modules/" . $module["mnom"] . "/install.conf.php") 
            ? '<a class="text-danger" href="'. site_url('admin.php?op=Module-Install&amp;ModDesinstall='. $module["mnom"]) .'" ><i class="fa fa-expand fa-lg"></i><i class="fa fa fa-puzzle-piece fa-2x fa-rotate-90" title="'. adm_translate("Désinstaller le module") .'" data-bs-toggle="tooltip"></i></a>' 
            : '<a class="text-danger" href="'. site_url('admin.php?op=Module-Install&amp;ModDesinstall='. $module["mnom"]) .'" ><i class="fa fa fa-ban fa-lg"></i><i class="fa fa fa-puzzle-piece fa-2x fa-rotate-90" title="'. adm_translate("Marquer le module comme désinstallé") .'" data-bs-toggle="tooltip"</i></a>';
        $clatd = 'table-success';
    }

    echo '
            <tr>
                <td class="'. $clatd .'">'. $icomod .'</td>
                <td class="'. $clatd .'">'. $module["mnom"] .'</td>
                <td class="'. $clatd .'">'. $status_chngac .'</td>
            </tr>';
}

echo '
        </tbody>
    </table>';
    
css::adminfoot('', '', '', '');
