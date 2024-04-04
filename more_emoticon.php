<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* snipe 2004                                                           */
/*                                                                      */
/* NPDS Copyright (c) 2002-2023 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\system\assets\css;
use npds\system\forum\forum;
use npds\system\config\Config;


if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

if (isset($user)) {
    if ($cookie[9] == '') {
        $cookie[9] = Config::get('npds.Default_Theme');
    }

    if (isset($theme)) {
        $cookie[9] = $theme;
    }

    $tmp_theme = $cookie[9];

    if (!$file = @opendir("themes/$cookie[9]")) {
        $tmp_theme = Config::get('npds.Default_Theme');
    }
} else {
    $tmp_theme = Config::get('npds.Default_Theme');
}

include('storage/meta/meta.php');

echo '<link rel="stylesheet" href="themes/_skins/default/bootstrap.min.css">';
echo css::import_css($tmp_theme, $language, '', '', '');

include('assets/formhelp.java.php');

echo '
        </head>
        <body class="p-2">
        ' . forum::putitems_more() . '
        </body>
    </html>';
