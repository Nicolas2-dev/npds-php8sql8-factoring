<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* NPDS Copyright (c) 2002-2023 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\support\routing\url;
use npds\system\support\facades\Request;


if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

/**
 * [filtre_module description]
 *
 * @param   string  $strtmp  [$strtmp description]
 *
 * @return  string
 */
function filtre_module(string $strtmp): string|bool|url
{
    if (
        strstr($strtmp, '..') ||
        strstr($strtmp, '..') ||
        stristr($strtmp, 'script') ||
        stristr($strtmp, 'cookie') ||
        stristr($strtmp, 'iframe') ||
        stristr($strtmp, 'applet') ||
        stristr($strtmp, 'object') ||
        stristr($strtmp, 'meta')       
    ) {
        url::redirect_url('die.php?op=module');
    } else {
        return $strtmp != '' ? true : false;
    }
}

$ModPath = Request::input('ModPath');
$ModStart = Request::input('ModStart');

if (filtre_module($ModPath) and filtre_module($ModStart)) {
    if (file_exists("modules/$ModPath/http/$ModStart.php")) {
        include("modules/$ModPath/http/$ModStart.php");
        die();

    } elseif (file_exists("modules/$ModPath/$ModStart.php")) {
        include("modules/$ModPath/$ModStart.php");
        die();
        
    } else {
        Header('Location: '. site_url('die.php?op=module-exist'));
    }
} else {
    Header('Location: '. site_url('die.php?op=module'));
}
