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
// Pour le lancement du Chat : chat.php?id=gp_id&auto=token_de_securite
// gp_id=ID du groupe au sens NPDS du terme => 0 : tous / -127 : Admin / -1 : Anonyme / 1 : membre / 2 ... 126 : groupe de membre
// token_de_securite = encrypt(serialize(gp_id)) => Permet d'éviter le lancement du Chat sans autorisation
declare(strict_types=1);

use npds\system\config\Config;
use npds\system\support\facades\Request;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

$meta_op = '';
$meta_doctype = '<!DOCTYPE html>';
Config::set('npds.Titlesitename', 'NPDS Chat');
include("storage/meta/meta.php");

$id = Request::query('id');
$auto = Request::query('auto');

echo '
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon" />
</head>  
    <div style="height:1vh;" class="">
        <iframe src="' . site_url('chatrafraich.php?repere=0&amp;aff_entetes=1&amp;connectes=-1&amp;id=' . $id . '&amp;auto=' . $auto) .'" frameborder="0" scrolling="no" noresize="noresize" name="rafraich" width="100%" height="100%"></iframe>
    </div>
    <div style="height:58vh;" class="">
        <iframe src="' . site_url('chattop.php') .'" frameborder="0" scrolling="yes" noresize="noresize" name="haut" width="100%" height="100%"></iframe>
    </div>
    <div style="height:39vh;" class="">
        <iframe src="' . site_url('chatinput.php?id=' . $id . '&amp;auto=' . $auto) .'" frameborder="0" scrolling="yes" noresize="noresize" name="bas" width="100%" height="100%"></iframe>
    </div>
</html>';
