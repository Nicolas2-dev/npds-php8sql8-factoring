<?php

/************************************************************************/
/* DUNE by NPDS - admin prototype                                       */
/* ===========================                                          */
/*                                                                      */
/* M. PASCAL aKa EBH (plan.net@free.fr)                                 */
/*                                                                      */
/* NPDS Copyright (c) 2002-2023 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\support\logs\logs;
use npds\support\assets\css;
use npds\system\config\Config;
use npds\system\support\facades\DB;

if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'OptimySQL';
$f_titre = adm_translate("Optimisation de la base de données") .' : '. Config::get('database.default.database');

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

$date_opt = date(adm_translate("dateforop"));
$heure_opt = date("h:i a");

include("themes/default/header.php");

GraphicAdmin(manuel('optimysql'));
adminhead($f_meta_nom, $f_titre);

// Insertion de valeurs d'initialisation de la table (si nécessaire)
$optimy = DB::table('optimy')->select('optid')->first();

if (!$optimy['optid'] or ($optimy['optid'] == '')) {
    DB::table('optimy')->insert(array(
        'optid'       => 1,
        'optgain'     => 0,
        'optdate'     => '',
        'opthour'     => '',
        'optcount'     => 0,
    ));
}

// Extraction de la date et de l'heure de la précédente optimisation
$last_opti = '';

$optimy = DB::table('optimy')->select('optdate', 'opthour')->where('optid', 1)->first();

if (!$optimy['optdate'] or ($optimy['optdate'] == '') or !$optimy['opthour'] or ($optimy['opthour'] == '')) {
} else {
    $last_opti = adm_translate("Dernière optimisation effectuée le") . " : " . $optimy['optdate'] . " " . adm_translate(" à ") . " " . $optimy['opthour'] . "<br />\n";
}

$tot_data = 0;
$tot_idx = 0;
$tot_all = 0;
$li_tab_opti = '';

if ($tables = DB::select('SHOW TABLE STATUS')) {

    foreach ($tables as $table) {
        $tot_data = $table['Data_length'];
        $tot_idx  = $table['Index_length'];
        $total = ($tot_data + $tot_idx);
        $total = ($total / 1024);
        $total = round($total, 3);
        $gain = $table['Data_free'];
        $gain = ($gain / 1024);

        settype($total_gain, 'integer');

        $total_gain += $gain;
        $gain = round($gain, 3);
        
        $resultat = DB::optimyTable($table['Name']);

        if ($gain == 0) {
            $li_tab_opti .= '
            <tr class="table-success">
                <td align="right">' . $table['Name'] .'</td>
                <td align="right">' . $total .' Ko</td>
                <td align="center">' . adm_translate("optimisée") .'</td>
                <td align="center"> -- </td>
            </tr>';
        } else {
            $li_tab_opti .= '
            <tr class="table-danger">
                <td align="right">' . $table['Name'] .'</td>
                <td align="right">' . $total .' Ko</td>
                <td class="text-danger" align="center">' . adm_translate("non optimisée") .'</td>
                <td align="right">' . $gain .' Ko</td>
            </tr>';
        }
    }
}

$total_gain = round($total_gain, 3);

// Historique des gains
// Extraction du nombre d'optimisation effectuée
$optimys = DB::table('optimy')->select('optgain', 'optcount')->where('optid', 1)->first();

$newgain = ($optimys['optgain'] + $total_gain);
$newcount = ($optimys['optcount'] + 1);

// Enregistrement du nouveau gain
DB::table('optimy')->where('optid', 1)->update(array(
    'optgain'       => $newgain,
    'optdate'       => $date_opt,
    'opthour'       => $heure_opt,
    'optcount'      => $newcount,
));

// Lecture des gains précédents et addition
$optimy = DB::table('optimy')->select('optgain', 'optcount')->where('optid', 1)->first();

echo '<hr /><p class="lead">' . adm_translate("Optimisation effectuée") .' : ' . adm_translate("Gain total réalisé") .' ' . $total_gain .' Ko</br>';
echo $last_opti;
echo '
    ' . adm_translate("A ce jour, vous avez effectué ") .' ' . $optimy['optcount'] .' optimisation(s) ' . adm_translate(" et réalisé un gain global de ") .' ' . $optimys['optgain'] .' Ko.</p>
    <table id="tad_opti" data-toggle="table" data-striped="true" data-show-toggle="true" data-mobile-responsive="true" data-icons="icons" data-icons-prefix="fa">
    <thead>
        <tr>
            <th data-sortable="true" data-halign="center" data-align="center">' . adm_translate('Table') .'</th>
            <th data-halign="center" data-align="center">' . adm_translate('Taille actuelle') .'</th>
            <th data-sortable="true" data-halign="center" data-align="center">' . adm_translate('Etat') .'</th>
            <th data-halign="center" date-align="center">' . adm_translate('Gain réalisable') .'</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td>' . adm_translate("Gain total réalisé") .' : </td>
            <td>' . $optimy['optgain'] .' Ko</td>
        </tr>
    </tfoot>
    <tbody>';

echo $li_tab_opti;

echo '
    </tbody>
    </table>';

css::adminfoot('', '', '', '');

global $aid;
logs::Ecr_Log('security', "OptiMySql() by AID : $aid", '');
