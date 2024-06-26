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

use npds\support\assets\css;
use npds\system\config\Config;
use npds\support\utility\crypt;
use npds\support\language\language;


// For More security
if (!stristr($_SERVER['PHP_SELF'], 'modules.php')) die();
if (strstr($ModPath, '..') || strstr($ModStart, '..') || stristr($ModPath, 'script') || stristr($ModPath, 'cookie') || stristr($ModPath, 'iframe') || stristr($ModPath, 'applet') || stristr($ModPath, 'object') || stristr($ModPath, 'meta') || stristr($ModStart, 'script') || stristr($ModStart, 'cookie') || stristr($ModStart, 'iframe') || stristr($ModStart, 'applet') || stristr($ModStart, 'object') || stristr($ModStart, 'meta'))
    die();

global $language, $NPDS_Prefix, $user;
include_once("modules/$ModPath/language/$language/language.php");
// For More security

if (isset($user) and $user != '') {
    global $cookie;
    if ($cookie[9] != '') {
        $ibix = explode('+', urldecode($cookie[9]));
        if (array_key_exists(0, $ibix)) $theme = $ibix[0];
        else $theme = Config::get('npds.Default_Theme');
        if (array_key_exists(1, $ibix)) $skin = $ibix[1];
        else $skin = Config::get('npds.Default_Skin');
        $tmp_theme = $theme;
        if (!$file = @opendir("themes/$theme")) $tmp_theme = Config::get('npds.Default_Theme');
    } else
        $tmp_theme = Config::get('npds.Default_Theme');
} else {
    $theme = Config::get('npds.Default_Theme');
    $skin = Config::get('npds.Default_Skin');
    $tmp_theme = $theme;
}

$Titlesitename = "NPDS wspad";
include("storage/meta/meta.php");
echo '<link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon" />';
echo css::import_css($tmp_theme, Config::get('npds.language'), $skin, '', '');
echo '
    </head>
    <body style="padding: 10px; background:#ffffff;">';
$wspad = rawurldecode(crypt::decrypt($pad));
$wspad = explode("#wspad#", $wspad);

// = DB::table('')->select()->where('', )->orderBy('')->get();

$row = sql_fetch_assoc(sql_query("SELECT content, modtime, editedby, ranq  FROM " . $NPDS_Prefix . "wspad WHERE page='" . $wspad[0] . "' AND member='" . $wspad[1] . "' AND ranq='" . $wspad[2] . "'"));
echo '
        <h2>' . $wspad[0] . '</h2>
        <span class="">[ ' . __d('two_wspad', 'révision') . ' : ' . $row['ranq'] . ' - ' . $row['editedby'] . " / " . date(__d('two_wspad', 'dateinternal'), $row['modtime'] + ((int)$gmt * 3600)) . ' ]</span>
        <hr />
        ' . language::aff_langue($row['content']) . '
    </body>
    </html>';
