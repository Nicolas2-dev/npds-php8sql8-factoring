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
use npds\system\language\language;


if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

$tmp_theme = theme::getTheme();

include("themes/$tmp_theme/theme.php");

$Titlesitename = "META-LANG";
include("storage/meta/meta.php");

echo css::import_css($tmp_theme, $language, theme::getSkin(), '', '');

global $NPDS_Prefix;
$Q = sql_query("SELECT def, content, type_meta, type_uri, uri, description FROM " . $NPDS_Prefix . "metalang ORDER BY 'type_meta','def' ASC");

echo '
    <div class="p-2">
    <table class="table table-striped table-responsive table-hover table-sm table-bordered" >
        <thead class="thead-default">
            <tr>
                <th>META</th>
                <th>Type</th>
                <th>Description</th>
            </tr>
        </thead>';

$cur_type = '';
$ibid = 0;

while (list($def, $content, $type_meta, $type_uri, $uri, $description) = sql_fetch_row($Q)) {
    if ($cur_type == '') {
        $cur_type = $type_meta;
    }

    if ($type_meta != $cur_type) {
        echo '
            </tr>
            <tr>
                <td class="lead" colspan="3">' . $type_meta . '</td>
            </tr>
        <tbody>';

        $cur_type = $type_meta;
    }

    if (isset($_SERVER['HTTP_REFERER']) and strstr($_SERVER['HTTP_REFERER'], 'submit.php')) {
        $def_modifier = "<a class=\"tooltipbyclass\" href=\"#\" onclick=\"javascript:parent.tinymce.activeEditor.selection.setContent(' " . $def . " ');top.tinymce.activeEditor.windowManager.close();
    \" title=\"Cliquer pour utiliser ce méta-mot dans votre texte.\">$def</a>";
    } else {
        $def_modifier = $def;
    }

    echo '<tr>
                <td valign="top" align="left"><strong>' . $def_modifier . '</strong></td>
                <td class="table-secondary" valign="top" align="left">' . $type_meta . '</td>';

    if ($type_meta == "smil") {
        eval($content);
        echo '<td valign="top" align="left">' . $cmd . '</td>
            </tr>';
    } elseif ($type_meta == "mot") {
        echo '<td valign="top" align="left">' . $content . '</td>
            </tr>';

    } else {
        echo '<td valign="top" align="left">' . language::aff_langue($description) . '</td>
            </tr>';
    }

    $ibid++;
}

echo '
            <tr><td colspan="3" >Meta-lang pour <a href="http://www.npds.org" >NPDS</a> ==> ' . $ibid . ' META(s)
                </td>
            </tr>
        </tbody>
    </table>
    </div>
    <script type="text/javascript" src="assets/js/jquery.min.js"></script>
    <script type="text/javascript" src="assets/shared/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="assets/js/npds_adapt.js"></script>';
