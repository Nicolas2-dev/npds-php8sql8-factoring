<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* This version name NPDS Copyright (c) 2001-2020 by Philippe Brunier   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\system\assets\css;
use npds\system\theme\theme;
use npds\system\config\Config;
use npds\system\language\language;
use npds\system\support\facades\DB;


if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

include('themes/'. $theme = theme::getTheme() .'/theme.php');

Config::set('npds.Titlesitename', 'META-LANG');
include("storage/meta/meta.php");

echo css::import_css($theme, Config::get('npds.language'), theme::getSkin(), '', '');

echo '
    <div class="p-2">
    <table class="table table-striped table-responsive table-hover table-sm table-bordered" >
        <thead class="thead-default">
            <tr>
                <th>'. translate("META") .'</th>
                <th>'. translate("Type") .'</th>
                <th>'. translate("Description") .'</th>
            </tr>
        </thead>';

$cur_type = '';

$ibid = 0;
foreach (DB::table('metalang ')
            ->select('def', 'content', 'type_meta', 'type_uri', 'uri', 'description')
            ->orderBy('type_meta, def', 'asc')
            ->get() as $meta) 
{   
    if ($cur_type == '') {
        $cur_type = $meta['type_meta'];
    }
    
    if ($meta['type_meta'] != $cur_type) {
        echo '
            </tr>
            <tr>
                <td class="lead" colspan="3">'. $meta['type_meta'] .'</td>
            </tr>
        <tbody>';

        $cur_type = $meta['type_meta'];
    }

    if (isset($_SERVER['HTTP_REFERER']) and strstr($_SERVER['HTTP_REFERER'], 'submit.php')) {
        $def_modifier = '<a class="tooltipbyclass" href="#" onclick="javascript:parent.tinymce.activeEditor.selection.setContent('. $meta['def'] .');top.tinymce.activeEditor.windowManager.close();
    " title="'. translate("Cliquer pour utiliser ce méta-mot dans votre texte.") .'">'. $meta['def'] .'</a>';
    } else {
        $def_modifier = $meta['def'];
    }

    echo '<tr>
                <td valign="top" align="left"><strong>'. $def_modifier .'</strong></td>
                <td class="table-secondary" valign="top" align="left">'. $meta['type_meta'] .'</td>';

    if ($meta['type_meta'] == "smil") {
        eval($meta['content']);
        
        echo '<td valign="top" align="left">'. $cmd .'</td> 
            </tr>';
    } elseif ($meta['type_meta'] == "mot") {
        echo '<td valign="top" align="left">'. $meta['content'] .'</td>
            </tr>';

    } else {
        echo '<td valign="top" align="left">'. language::aff_langue( (string) $meta['description']) .'</td>
            </tr>';
    }

    $ibid++;
}

echo '
            <tr><td colspan="3" >'. sprintf(translate('Meta-lang pour %s : %d => %s'), '<a href="http://www.npds.org">NPDS</a>', $ibid, ($ibid > 2 ? 'Metas' : 'Meta')) .' 
                </td>
            </tr>
        </tbody>
    </table>
    </div>
    <script type="text/javascript" src="assets/js/jquery.min.js"></script>
    <script type="text/javascript" src="assets/shared/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="assets/js/npds_adapt.js"></script>';
