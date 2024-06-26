<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/*                                                                      */
/* NPDS Copyright (c) 2001-2023 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\support\assets\css;
use npds\support\auth\users;
use npds\support\theme\theme;
use npds\system\config\Config;
use npds\support\utility\crypt;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

/**
 * [L_encrypt description]
 *
 * @param   string  $txt  [$txt description]
 *
 * @return  string
 */
function L_encrypt(string $txt): string 
{
    return crypt::encryptK($txt, substr(users::cookieUser(2), 8, 8));
}

if (!users::getUser()) {
    Header('Location: ' . site_url('user.php'));
} else {
    $theme = theme::getTheme();

    include("themes/$theme/theme.php");

    config::get('npds.Titlesitename', __d('two_carnet', 'Carnet d\'adresses'));
    include("storage/meta/meta.php");

    echo '<link id="bsth" rel="stylesheet" href="themes/_skins/default/bootstrap.min.css" />';

    // language pas bon
    echo css::import_css($theme, Config::get('npds.language'), "", "", "");

    include("assets/formhelp.java.php");

    $fic = "storage/users_private/" . users::cookieUser(1) . "/mns/carnet.txt";

    echo '
    </head>
    <body class="p-4">';

    if (file_exists($fic)) {
        
        $fp = fopen($fic, "r");
        if (filesize($fic) > 0) {
            $contents = fread($fp, filesize($fic));
        }
        fclose($fp);

        if (substr($contents, 0, 5) != "CRYPT") {
            $fp = fopen($fic, "w");
            fwrite($fp, "CRYPT" . L_encrypt($contents));
            fclose($fp);
        } else {
            $contents = crypt::decryptK(substr($contents, 5), substr(users::cookieUser(2), 8, 8));
        }

        echo '<div class="row">';

        foreach (explode("\n", $contents) as $tab) {
            $tabi = explode(';', $tab);
            
            if ($tabi[0] != '') {
                echo '
                <div class="border col-md-4 mb-1 p-3">
                    <a href="javascript: DoAdd(1,\'to_user\',\'' . $tabi[0] . ',\')";><b>' . $tabi[0] . '</b></a><br />
                    <a href="mailto:' . $tabi['1'] . '" >' . $tabi['1'] . '</a><br />
                    ' . $tabi['2'] . '
                </div>';
            }
        }

        echo '
            </div>';
    } else {
        echo '
            <div class="alert alert-secondary text-break">
                <span>' . __d('two_carnet', 'Vous pouvez charger un fichier carnet.txt dans votre miniSite') . '.</span><br />
                <span>' . __d('two_carnet', 'La structure de chaque ligne de ce fichier : nom_du_membre; adresse Email; commentaires') . '</span>
            </div>';
    }
    
    echo '
    </body>
    </html>';
}
