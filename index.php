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

use npds\system\news\news;
use npds\system\auth\users;
use npds\system\cache\cache;
use npds\system\theme\theme;
use npds\system\config\Config;
use npds\system\support\edito;
use npds\modules\install\support\install;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

install::checkInstall();

// Redirect for default Start Page of the portal - look at Admin Preferences for choice
function select_start_page($op)
{
    global $index;

    $Start_Page = Config::get('npds.Start_Page');

    if (!users::AutoReg()) {
        global $user;
        unset($user);
    }

    if (($Start_Page == '') 
    or ($op == "index.php") 
    or ($op == "edito") 
    or ($op == "edito-nonews")) {
        $index = 1;
        theindex($op, '', '');
        die('');
    } else {
        Header("Location: $Start_Page");
    }
}

function theindex($op, $catid, $marqeur)
{
    include("themes/default/header.php");

    // start Caching page
    if (cache::cacheManagerStart2()) {

        // Appel de la publication de News et la purge automatique
        news::automatednews();

        if (($op == 'newcategory') 
        or ($op == 'newtopic') 
        or ($op == 'newindex') 
        or ($op == 'edito-newindex')) {
            news::aff_news($op, $catid, $marqeur);
        } else {
            $theme = theme::getTheme();

            if (file_exists("themes/$theme/central.php")) {
                include("themes/$theme/central.php");
            } else {
                if (($op == 'edito') or ($op == 'edito-nonews')) {
                    edito::aff_edito();
                }

                if ($op != 'edito-nonews') {
                    news::aff_news($op, $catid, $marqeur);
                }
            }
        }
    }
    
    // end Caching page
    cache::cacheManagerEnd();

    include("themes/default/footer.php");
}

settype($op, 'string');
settype($catid, 'integer');
settype($marqeur, 'integer');
settype($topic, 'integer');

switch ($op) {

    case 'newindex':
    case 'edito-newindex':
    case 'newcategory':
        theindex($op, $catid, $marqeur);
        break;

    case 'newtopic':
        theindex($op, $topic, $marqeur);
        break;

    default:
        select_start_page($op);
        break;
}
