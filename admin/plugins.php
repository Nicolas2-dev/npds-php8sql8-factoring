<?php

/************************************************************************/
/* DUNE by NPDS - admin prototype                                       */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* NPDS Copyright (c) 2002-2022 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\system\routing\url;

if (!function_exists('admindroits')) {
    include('die.php');
}

include("themes/default/header.php");

if ($ModPath != '') {
    if (file_exists("modules/$ModPath/$ModStart.php")) {
        include("modules/$ModPath/$ModStart.php"); 
    } 

} else {
    url::redirect_url(urldecode($ModStart));
}
