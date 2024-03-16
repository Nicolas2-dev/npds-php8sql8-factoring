<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* NPDS Copyright (c) 2002-2019 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

use npds\system\cache\cacheManager;
use npds\system\cache\SuperCacheEmpty;

if (!function_exists("Mysql_Connexion"))
    include('boot/bootstrap.php');

if ($SuperCache)
    $cache_obj = new cacheManager();
else
    $cache_obj = new SuperCacheEmpty();

include("themes/default/header.php");

if (($SuperCache) and (!$user))
    $cache_obj->startCachingPage();

if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!$SuperCache) or ($user)) {
    $inclusion = false;
    if (file_exists("themes/$theme/view/top.html"))
        $inclusion = "themes/$theme/view/top.html";
    elseif (file_exists("themes/default/view/top.html"))
        $inclusion = "themes/default/view/top.html";
    else
        echo "html/top.html / not find !<br />";
    if ($inclusion) {
        ob_start();
        include($inclusion);
        $Xcontent = ob_get_contents();
        ob_end_clean();
        echo meta_lang(aff_langue($Xcontent));
    }
}

// -- SuperCache
if (($SuperCache) and (!$user))
    $cache_obj->endCachingPage();
include("themes/default/footer.php");
