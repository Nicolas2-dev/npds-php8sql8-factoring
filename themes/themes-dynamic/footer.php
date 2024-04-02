<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* DYNAMIC THEME engine for NPDS                                        */
/* NPDS Copyright (c) 2002-2019 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

use npds\system\theme\theme;
use npds\system\language\language;
use npds\system\language\metalang;

$rep = false;

settype($ContainerGlobal, 'string');

$theme = theme::getTheme();

if (file_exists("themes/" . $theme . "/view/footer.html")) {
    $rep = $theme;
} elseif (file_exists("themes/default/view/footer.html")) {
    $rep = "default";
} else {
    echo "footer.html manquant / not find !<br />";
    die();
}

if ($rep) {

    ob_start();
        include("themes/" . $rep . "/view/footer.html");
        $Xcontent = ob_get_contents();
    ob_end_clean();

    if ($ContainerGlobal) {
        $Xcontent .= $ContainerGlobal;
    }

    echo metalang::meta_lang(language::aff_langue($Xcontent));
}
