<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* NPDS Copyright (c) 2002-2019 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\system\config\Config;
use npds\system\support\editeur;
use npds\system\language\language;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

function footmsg()
{
    $foot = '<p align="center">';
    
    if ($foot1 = Config::get('npds.foot1')) {
        $foot .= stripslashes($foot1) . '<br />';
    }

    if ($foot2 = Config::get('npds.foot2')) {
        $foot .= stripslashes($foot2) . '<br />';
    }

    if ($foot3 = Config::get('npds.foot3')) {
        $foot .= stripslashes($foot3) . '<br />';
    }

    if ($foot4 = Config::get('npds.foot4')){  
        $foot .= stripslashes($foot4);
    }

    $foot .= '</p>';

    echo language::aff_langue($foot);
}

function foot()
{
    global $user, $cookie9;
    
    $Default_Theme = Config::get('npds.Default_Theme');

    if ($user) {
        $user2 = base64_decode($user);
        $cookie = explode(':', $user2);
        
        if ($cookie[9] == '') {
            $cookie[9] = $Default_Theme;
        }

        $ibix = explode('+', urldecode($cookie[9]));
        if (!$file = @opendir("themes/$ibix[0]")) {
            include("themes/$Default_Theme/footer.php");
        } else {
            include("themes/$ibix[0]/footer.php");
        }
    } else {
        include("themes/$Default_Theme/footer.php");
    }
    
    if ($user) {
        $cookie9 = $ibix[0];
    }
}

global $cookie9; 

$Default_Theme = Config::get('npds.Default_Theme');

if (Config::get('npds.tiny_mce')) {
    echo editeur::aff_editeur('tiny_mce', 'end');
}

// include externe file from themes/default/view/include for functions, codes ...
if (file_exists("themes/default/view/include/footer_before.inc")) {
    include("themes/default/view/include/footer_before.inc");
}

foot();

// include externe file from modules/themes include for functions, codes ...
if (isset($user)) {
    if (file_exists("themes/$cookie9/view/include/footer_after.inc")) {
        include("themes/$cookie9/view/include/footer_after.inc");
    } else {
        if (file_exists("themes/default/view/include/footer_after.inc")) {
            include("themes/default/view/include/footer_after.inc");
        }
    }
} else {
    if (file_exists("themes/$Default_Theme/view/include/footer_after.inc")) {
        include("themes/$Default_Theme/view/include/footer_after.inc");
    } else {
        if (file_exists("themes/default/view/include/footer_after.inc")) {
            include("themes/default/view/include/footer_after.inc");
        }
    }
}

echo '
        </body>
    </html>';

include("sitemap.php");

global $dblink;

if (!Config::get('npds.database.mysql_p')) {
    sql_close($dblink);
}
