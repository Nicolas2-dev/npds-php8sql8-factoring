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

use npds\support\assets\css;
use npds\system\support\facades\DB;

if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'hreferer';
$f_titre = __d('two_core', 'Sites Référents');

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

/**
 * [hreferer description]
 *
 * @param   int   $filter  [$filter description]
 *
 * @return  void
 */
function hreferer(int $filter): void
{
    global $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('referer'));
    adminhead($f_meta_nom, $f_titre);

    settype($filter, 'integer');

    if (!$filter) {
        $filter = 2048;
    }

    echo '
    <hr />
    <h3>'. __d('two_core', 'Qui parle de nous ?') .'</h3>
    <form action="'. site_url('admin.php') .'" method="post">
        <input type="hidden" name="op" value="hreferer" />
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="filter">'. __d('two_core', 'Filtre') .'</label>
            <div class="col-sm-4">
                <input type="number" class="form-control" name="filter" min="0" max="99999" value="'. $filter .'" />
            </div>
            <div class="col-sm-4 xs-hidden"></div>
            <div class="clearfix"></div>
        </div>
    </form>
    <table id ="tad_refe" data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-mobile-responsive="true" data-icons="icons" data-icons-prefix="fa" data-buttons-class="outline-secondary">
    <thead>
        <tr>
            <th data-sortable="true" data-halign="center">Url</th>
            <th class="n-t-col-xs-1" data-sortable="true" data-halign="center" data-align="right">Hit</th>
        </tr>
    </thead>
    <tbody>';

    // faire la function groupeBy() dans builder
    //$hresult = sql_query("SELECT ,  FROM " . $NPDS_Prefix . " GROUP BY filter ORDER BY  DESC");
    
    $referers = DB::table('referer')
        ->select('url', DB::raw('COUNT(url) AS TheCount, substring(url, 1, '. $filter .' ) AS filter'))
        ->orderBy('TheCount', 'desc')
        ->get();

    foreach ($referers as $referer) {
        echo '
        <tr>
            <td>';

        if ($referer['TheCount'] == 1) {
            echo '<a href="'. $referer['url'] .'" target="_blank">';
        }

        if ($filter != 2048) {
            echo '<span>'. substr($referer['url'], 0, $filter) .'</span><span class="text-muted">'. substr($referer['url'], $filter) .'</span>';
        } else {
            echo $referer['url'];
        }
        
        if ($referer['TheCount'] == 1) {
            echo '</a>';
        }

        echo '</a></td>
            <td>'. $referer['TheCount'] .'</td>
        </tr>';
    }

    echo '
    </tbody>
    </table>
    <br />
    <ul class="nav nav-pills">
        <li class="nav-item"><a class="text-danger nav-link" href="'. site_url('admin.php?op=delreferer') .'" >'. __d('two_core', 'Effacer les Référants') .'</a></li>
        <li class="nav-item"><a class="nav-link" href="'. site_url('admin.php?op=archreferer&amp;filter='. $filter .'') .'">'. __d('two_core', 'Archiver les Référants') .'</a></li>
    </ul>';

    css::adminfoot('', '', '', '');
}

/**
 * [delreferer description]
 *
 * @return  void
 */
function delreferer(): void
{
    DB::table('referer')->delete();

    Header('Location: '. site_url('admin.php?op=AdminMain'));
}

/**
 * [archreferer description]
 *
 * @param   int  $filter  [$filter description]
 *
 * @return  void
 */
function archreferer(int $filter): void
{
    $file = fopen("storage/logs/referers.log", "w");
    $content = "===================================================\n";
    $content .= "Date : " . date("d-m-Y") . "-/- NPDS - HTTP Referers\n";
    $content .= "===================================================\n";

    foreach (DB::table('referer')->select('url')->get() as $referer) {
        $content .= $referer['url'] ."\n";
    }

    $content .= "===================================================\n";
    fwrite($file, $content);
    fclose($file);

    Header('Location: '. site_url('admin.php?op=hreferer&filter='. $filter));
}

settype($filter, 'integer');

switch ($op) {
    case 'hreferer':
        hreferer($filter);
        break;

    case 'archreferer':
        archreferer($filter);
        break;
        
    case 'delreferer':
        delreferer();
        break;
}
