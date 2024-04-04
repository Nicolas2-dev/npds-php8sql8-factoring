<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* NPDS Copyright (c) 2002-2022 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\system\assets\css;
use npds\system\support\str;
use npds\system\language\language;
use npds\system\support\facades\DB;

if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'Ephemerids';
$f_titre = adm_translate("Ephémérides");

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

/**
 * [Ephemerids description]
 *
 * @return  void
 */
function Ephemerids(): void
{
    global $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('ephem'));
    adminhead($f_meta_nom, $f_titre);

    echo '
    <hr />
    <h3 class="mb-3">' . adm_translate("Ajouter un éphéméride") . '</h3>
    <form action="admin.php" method="post">
        <div class="row g-3 mb-3">
            <div class="col-sm-4">
                <div class="form-floating">
                <select class="form-select" id="did" name="did">';

    $nday = '1';
    while ($nday <= 31) {
        echo '<option name="did">' . $nday . '</option>';
        $nday++;
    }

    echo '
                </select>
                <label for="did">' . adm_translate("Jour") . '</label>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-floating">
                <select class="form-select" id="mid" name="mid">';

    $nmonth = "1";                
    while ($nmonth <= 12) {
        echo '<option name="mid">' . $nmonth . '</option>';
        $nmonth++;
    }

    echo '
                </select>
                <label for="mid">' . adm_translate("Mois") . '</label>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-floating">
                <input class="form-control" type="number" id="yid" name="yid" maxlength="4" size="5" />
                <label for="yid">' . adm_translate("Année") . '</label>
                </div>
            </div>
        </div>
        <div class="form-floating mb-3">
            <textarea name="content" class="form-control" style="height:120px;"></textarea>
            <label for="content">' . adm_translate("Description de l'éphéméride") . '</label>
        </div>
        <button class="btn btn-primary" type="submit">' . adm_translate("Envoyer") . '</button>
        <input type="hidden" name="op" value="Ephemeridsadd" />
    </form>
    <hr />
    <h3 class="mb-3">' . adm_translate("Maintenance des Ephémérides (Editer/Effacer)") . '</h3>
    <form action="admin.php" method="post">
        <div class="row g-3">
            <div class="col-4">
                <div class="form-floating mb-3">
                <select class="form-select" id="did" name="did">';

    $nday = "1";                
    while ($nday <= 31) {
        echo '<option name="did">' . $nday . '</option>';
        $nday++;
    }

    echo '
                </select>
                <label for="did">' . adm_translate("Jour") . '</label>
                </div>
            </div>
            <div class="col-4">
                <div class="form-floating mb-3">
                <select class="form-select" id="mid" name="mid">';

    $nmonth = "1";
    while ($nmonth <= 12) {
        echo '<option name="mid">' . $nmonth . '</option>';
        $nmonth++;
    }

    echo '
                </select>
                <label for="mid">' . adm_translate("Mois") . '</label>
                </div>
            </div>
        </div>
        <input type="hidden" name="op" value="Ephemeridsmaintenance" />
        <button class="btn btn-primary" type="submit">' . adm_translate("Editer") . '</button>
    </form>';

    css::adminfoot('', '', '', '');
}

/**
 * [Ephemeridsadd description]
 *
 * @param   int     $did      [$did description]
 * @param   int     $mid      [$mid description]
 * @param   int     $yid      [$yid description]
 * @param   string  $content  [$content description]
 *
 * @return  void
 */
function Ephemeridsadd(int $did, int $mid, int $yid, string $content): void
{
    DB::table('ephem')->insert(array(
        'did'       => $did,
        'mid'       => $mid,
        'yid'       => $yid,
        'content '  => stripslashes(str::FixQuotes($content) . ""),
    ));

    Header("Location: admin.php?op=Ephemerids");
}

/**
 * [Ephemeridsmaintenance description]
 *
 * @param   int   $did  [$did description]
 * @param   int   $mid  [$mid description]
 *
 * @return  void
 */
function Ephemeridsmaintenance(int $did, int $mid): void
{
    global $f_meta_nom, $f_titre;

    $resultX = DB::table('ephem')
                    ->select('eid', 'did', 'mid', 'yid', 'content')
                    ->where('did', $did)
                    ->where('mid', $mid)
                    ->orderBy('yid', 'ASC')
                    ->get();

    if (!$resultX) {
        header("location: admin.php?op=Ephemerids");
    }

    include("themes/default/header.php");

    GraphicAdmin(manuel('ephem'));
    adminhead($f_meta_nom, $f_titre);

    echo '
    <hr />
    <h3>' . adm_translate("Maintenance des Ephémérides") . '</h3>
    <table data-toggle="table" data-striped="true" data-mobile-responsive="true" data-search="true" data-show-toggle="true" data-icons="icons" data-icons-prefix="fa">
        <thead>
            <tr>
                <th class="n-t-col-xs-2" data-sortable="true" data-halign="center" data-align="right" >
                    ' . adm_translate('Année') . '
                </th>
                <th data-halign="center" >
                    ' . adm_translate('Description') . '
                </th>
                <th class="n-t-col-xs-2" data-halign="center" data-align="center" >
                    ' . adm_translate('Fonctions') . '
                </th>
            </tr>
        </thead>
        <tbody>';

    foreach ($resultX as $ephem) {
        echo '
            <tr>
                <td>
                    ' . $ephem['yid'] . '
                </td>
                <td>
                    ' . language::aff_langue($ephem['content']) . '
                </td>
                <td>
                    <a href="admin.php?op=Ephemeridsedit&amp;eid=' . $ephem['eid'] . '&amp;did=' . $ephem['did'] . '&amp;mid=' . $ephem['mid'] . '" title="' . adm_translate("Editer") . '" data-bs-toggle="tooltip" >
                        <i class="fa fa-edit fa-lg me-2"></i>
                    </a>&nbsp;
                    <a href="admin.php?op=Ephemeridsdel&amp;eid=' . $ephem['eid'] . '&amp;did=' . $ephem['did'] . '&amp;mid=' . $ephem['mid'] . '" title="' . adm_translate("Effacer") . '" data-bs-toggle="tooltip">
                        <i class="fas fa-trash fa-lg text-danger"></i>
                    </a>
            </tr>';
    }

    echo '
            </tbody>
        </table>';

    css::adminfoot('', '', '', '');
}

/**
 * [Ephemeridsdel description]
 *
 * @param   int   $eid  [$eid description]
 * @param   int   $did  [$did description]
 * @param   int   $mid  [$mid description]
 *
 * @return  void
 */
function Ephemeridsdel(int $eid, int $did, int $mid): void
{
    DB::table('ephem')->where('eid', $eid)->delete();

    Header("Location: admin.php?op=Ephemeridsmaintenance&did=$did&mid=$mid");
}

/**
 * [Ephemeridsedit description]
 *
 * @param   int   $eid  [$eid description]
 * @param   int   $did  [$did description]
 * @param   int   $mid  [$mid description]
 *
 * @return  void
 */
function Ephemeridsedit(int $eid, int $did, int $mid): void
{
    global $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('ephem'));
    adminhead($f_meta_nom, $f_titre);

    $ephem = DB::table('ephem')->select('yid', 'content')->where('eid', $eid)->first();

    echo '
    <hr />
    <h3>' . adm_translate("Editer éphéméride") . '</h3>
    <form action="admin.php" method="post">
        <div class="form-floating mb-3">
            <input class="form-control" type="number" name="yid" value="' . $ephem['yid'] . '" max="2500" />
            <label for="yid">' . adm_translate("Année") . '</label>
        </div>
        <div class="form-floating mb-3">
            <textarea name="content" id="content" class="form-control" style="height:120px;">' . $ephem['content'] . '</textarea>
            <label for="content">' . adm_translate("Description de l'éphéméride") . '</label>
        </div>
        <input type="hidden" name="did" value="' . $did . '" />
        <input type="hidden" name="mid" value="' . $mid . '" />
        <input type="hidden" name="eid" value="' . $eid . '" />
        <input type="hidden" name="op" value="Ephemeridschange" />
        <button class="btn btn-primary" type="submit">' . adm_translate("Envoyer") . '</button>
    </form>';

    css::adminfoot('', '', '', '');
}

/**
 * [Ephemeridschange description]
 *
 * @param   int     $eid      [$eid description]
 * @param   int     $did      [$did description]
 * @param   int     $mid      [$mid description]
 * @param   int     $yid      [$yid description]
 * @param   string  $content  [$content description]
 *
 * @return  void
 */
function Ephemeridschange(int $eid, int $did, int $mid, int $yid, string $content): void
{
    DB::table('ephem')->where('eid', $eid)->update(array(
        'yid'       => $yid,
        'content'   => stripslashes(str::FixQuotes($content) . ""),
    ));

    Header("Location: admin.php?op=Ephemeridsmaintenance&did=$did&mid=$mid");
}

switch ($op) {
    case 'Ephemeridsedit':
        Ephemeridsedit($eid, $did, $mid);
        break;

    case 'Ephemeridschange':
        Ephemeridschange($eid, $did, $mid, $yid, $content);
        break;

    case 'Ephemeridsdel':
        Ephemeridsdel($eid, $did, $mid);
        break;

    case 'Ephemeridsmaintenance':
        Ephemeridsmaintenance($did, $mid);
        break;

    case 'Ephemeridsadd':
        Ephemeridsadd($did, $mid, $yid, $content);
        break;

    case 'Ephemerids':
        Ephemerids();
        break;
}
