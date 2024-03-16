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

// Modification pour IZ-Xinstall - EBH - JPB & PHR
if (file_exists("storage/install/IZ-Xinstall.ok")) {
    if (file_exists("install.php") or is_dir("install")) {
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <title>NPDS IZ-Xinstall - Installation Configuration</title>
        </head>
        <body>
            <div style="text-align: center; font-size: 20px; font-family: Arial; font-weight: bold; color: #000000"><br />
                NPDS IZ-Xinstall - Installation &amp; Configuration
            </div>
            <div style="text-align: center; font-size: 20px; font-family: Arial; font-weight: bold; color: #ff0000"><br />
                Vous devez supprimer le r&eacute;pertoire "install" ET le fichier "install.php" avant de poursuivre !<br />
                You must remove the directory "install" as well as the file "install.php" before continuing!
            </div>
        </body>
    </html>';
        die();
    }
} else {
    if (file_exists("install.php") and is_dir("install"))
        header("location: install.php");
}

if (!function_exists("Mysql_Connexion"))
    include('boot/bootstrap.php');

// Redirect for default Start Page of the portal - look at Admin Preferences for choice
function select_start_page($op)
{
    global $Start_Page, $index;
    if (!AutoReg()) {
        global $user;
        unset($user);
    }
    if (($Start_Page == '') or ($op == "index.php") or ($op == "edito") or ($op == "edito-nonews")) {
        $index = 1;
        theindex($op, '', '');
        die('');
    } else
        Header("Location: $Start_Page");
}



function theindex($op, $catid, $marqeur)
{
    include("themes/default/header.php");

    // start caching page
    global $SuperCache;
    if ((cacheManagerStart()->genereting_output == 1) or (cacheManagerStart()->genereting_output == -1) or (!$SuperCache)) {
        // Appel de la publication de News et la purge automatique
        automatednews();

        global $theme;
        if (($op == 'newcategory') or ($op == 'newtopic') or ($op == 'newindex') or ($op == 'edito-newindex')) {
            aff_news($op, $catid, $marqeur);
        } else {
            if (file_exists("themes/$theme/central.php"))
                include("themes/$theme/central.php");
            else {
                if (($op == 'edito') or ($op == 'edito-nonews')) aff_edito();
                if ($op != 'edito-nonews') aff_news($op, $catid, $marqeur);
            }
        }
    }
    // end Caching page
    cacheManagerEnd();
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
        select_start_page($op, '');
        break;
}
