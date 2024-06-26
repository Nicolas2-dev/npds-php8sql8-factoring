<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Collab WS-Pad 1.5 by Developpeur and Jpb                             */
/*                                                                      */
/* NPDS Copyright (c) 2002-2022 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

use npds\support\utility\crypt;
use npds\support\language\language;

// For More security
if (!stristr($_SERVER['PHP_SELF'], 'modules.php')) die();
if (strstr($ModPath, '..') || strstr($ModStart, '..') || stristr($ModPath, 'script') || stristr($ModPath, 'cookie') || stristr($ModPath, 'iframe') || stristr($ModPath, 'applet') || stristr($ModPath, 'object') || stristr($ModPath, 'meta') || stristr($ModStart, 'script') || stristr($ModStart, 'cookie') || stristr($ModStart, 'iframe') || stristr($ModStart, 'applet') || stristr($ModStart, 'object') || stristr($ModStart, 'meta'))
    die();
global $NPDS_Prefix;
$wspad = rawurldecode(crypt::decrypt($pad));
$wspad = explode("#wspad#", $wspad);
switch ($type) {
    case "doc":
        include "library/html2doc.php";
        $htmltodoc = new HTML_TO_DOC();

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $row = sql_fetch_assoc(sql_query("SELECT content FROM " . $NPDS_Prefix . "wspad WHERE page='" . $wspad[0] . "' AND member='" . $wspad[1] . "' AND ranq='" . $wspad[2] . "'"));
        // nettoyage des SPAN
        $tmp = preg_replace('#style="[^\"]*\"#', "", language::aff_langue($row['content']));
        $htmltodoc->createDoc($tmp, $wspad[0] . "-" . $wspad[2], true);
        break;
    default:
        break;
}
