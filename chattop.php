<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* NPDS Copyright (c) 2002-2023 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\system\config\Config;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}


$nuke_url = '';
$meta_op = '';

Config::set('npds.Titlesitename', 'NPDS Chat');
include('storage/meta/meta.php');

echo '
    </head>
    <body>
    </body>
    </html>';
