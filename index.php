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

use npds\support\news\news;
use npds\support\auth\users;
use npds\support\cache\cache;
use npds\support\theme\theme;
use npds\system\config\Config;
use npds\support\edito;
use npds\system\support\facades\Request;
use npds\modules\install\support\install;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

install::checkInstall();

/**
 * Redirect for default Start Page of the portal - look at Admin Preferences for choice
 *
 * @param   string  $op  [$op description]
 *
 * @return  void
 */
//function select_start_page(string $op): void
function select_start_page(): void
{
    //global $index; // ??? not used !!!         

    $Start_Page = Config::get('npds.Start_Page');

    if (!users::AutoReg()) {
        global $user;
        unset($user);
    }

    $op = Request::query('op');

    if (($Start_Page == '') 
    or ($op == "index.php") 
    or ($op == "edito") 
    or ($op == "edito-nonews")) {
        //$index = 1; // ??? not used !!!           
        theindex($op, '', '');
        die('');
    } else {
        Header("Location: $Start_Page");
    }
}

/**
 * [theindex description]
 *
 * @param   string  $op       [$op description]
 * @param   int               [ description]
 * @param   string  $catid    [$catid description]
 * @param   int               [ description]
 * @param   string  $marqeur  [$marqeur description]
 *
 * @return  void
 */
//function theindex(string $op, int|string $catid, int|string $marqeur): void
function theindex(): void
{
    include("themes/default/header.php");

    // start Caching page
    if (cache::cacheManagerStart2()) {

        // Appel de la publication de News et la purge automatique
        news::automatednews();

        $op         = Request::query('op');
        $catid      = (int) Request::query('catid');
        $marqeur    = (int) Request::query('marqeur');

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

switch (Request::query('op')) {

    case 'newindex':
    case 'edito-newindex':
    case 'newcategory':
        theindex();
        break;

    case 'newtopic':
        theindex();
        break;

    default:
        select_start_page();
        break;
}
