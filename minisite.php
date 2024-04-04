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

use npds\system\auth\groupe;
use npds\system\forum\forum;
use npds\system\theme\theme;
use npds\system\config\Config;
use npds\system\security\hack;
use npds\system\language\language;
use npds\system\language\metalang;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

settype($gr_name, 'string');
settype($new_pages, 'string');

define('CITRON', 'tarteaucitron');

function convert_ressources($Xcontent)
{
    global $op;

    for ($i = 0; $i < strlen($Xcontent); $i++) {
        if (strtoupper(substr($Xcontent, $i, 4)) == "src=") {
            if ((strtoupper(substr($Xcontent, $i + 4, 3)) != "HTT") 
            and (strtoupper(substr($Xcontent, $i + 4, 4)) != "\"HTT")) {
                $Xcontent = substr_replace($Xcontent, "src=getfile.php?att_id=$op&amp;apli=minisite&amp;att_type=&amp;att_size=&amp;att_name=", $i, 4);
            }
            $i = $i + 4;
        }
    }

    return language::aff_langue($Xcontent);
}

// NPDS copyright ... don't remove !
$copyright = '<span class="blog_sname">' . Config::get('npds.sitename') . '</span>&nbsp;<span class="blog_npds">NPDS&nbsp;HUB-BLOG&nbsp;<a href="http://www.npds.org">NPDS</a></span>';

// Troll Control for security
$affich = false;

if (($op != '') and ($op)) {
    if (preg_match('#^[a-z0-9_\.-]#i', $op) 
    and !stristr($op, ".*://") 
    and !stristr($op, "..") 
    and !stristr($op, "../") 
    and !stristr($op, "script") 
    and !stristr($op, "cookie") 
    and !stristr($op, "iframe") 
    and !stristr($op, "applet") 
    and !stristr($op, "object") 
    and !stristr($op, "meta")) {
        
        global $user, $super_admintest;
        
        $adminblog = ($super_admintest) ? true : false;
        $dir = "storage/users_private/$op/mns/";

        if (dirname($op) != 'groupe') {
            // single user
            $userdata = forum::get_userdata($op);
            
            if ($userdata['mns'] == true) {
                $affich = true;
                
                if (stristr($userdata['user_avatar'], "users_private")) {
                    $direktori = '';
                } else {
                    global $theme;
                    $direktori = 'assets/images/forum/avatar/';
                    
                    if (function_exists("theme_image")) {
                        if (theme::theme_image("forum/avatar/blank.gif")) {
                            $direktori = "themes/$theme/images/forum/avatar/";
                        }
                    }
                }

                $avatar_mns = $direktori . $userdata['user_avatar'];
            }

            $userX = base64_decode($user);
            $userdataX = explode(':', $userX);

            if (array_key_exists(1, $userdataX)) {
                if ($userdataX[1] == $op) {
                    $adminblog = true;
                }
            }
        } else {
            // groupe
            if (is_dir($dir)) {
                $affich = true;
                $avatar_mns = 'storage/users_private/' . $op . '/groupe.png';
                
                function adm_translate()
                {
                }

                $gX = groupe::liste_group();

                foreach ($gX as $g_id => $g_name) {
                    if ($g_id == basename($op)) {
                        $gr_name = $g_name;
                    }
                }
            }

            $tabgp = groupe::valid_group($user);

            if (is_array($tabgp)) {
                foreach ($tabgp as $auto) {
                    if ($auto == basename($op)) {
                        $adminblog = true;
                    }
                }
            }
        }
    }
}

if ($affich) {
    $fic = $dir . 'index.html';

    if (file_exists($fic)) {
        $Titlesitename = "Minisite - $op";
        $nuke_url = Config::get('npds.nuke_url') . '/minisite.php?op=' . $op;

        include("storage/meta/meta.php");

        echo '
            <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon" />
            <script type="text/javascript" src="assets/js/jquery.min.js"></script>
            <script type="text/javascript" src="assets/shared/bootstrap/dist/js/bootstrap.bundle.min.js"></script>';
        echo '<style type="text/css">';

        readfile($dir . "style.css");

        echo '</style>';

        if (defined('CITRON')) {
            echo '
                <script type="text/javascript"> var tarteaucitronForceLanguage = "' . language::language_iso(1, '', '') . '"; </script>
                <script type="text/javascript" src="assets/shared/tarteaucitron/tarteaucitron.js"></script>
                <script type="text/javascript">
                //<![CDATA[
                tarteaucitron.init({
                    "privacyUrl": "", /* Privacy policy url */
                    "hashtag": "#tarteaucitron", /* Ouverture automatique du panel avec le hashtag */
                    "cookieName": "tartaucitron", /* Cookie name */
                    "orientation": "top", /* le bandeau doit être en haut (top) ou en bas (bottom) ? */
                    "showAlertSmall": true, /* afficher le petit bandeau en bas à droite ? */
                    "cookieslist": true, /* Afficher la liste des cookies installés ? */
                    "adblocker": false, /* Afficher un message si un adblocker est détecté */
                    "AcceptAllCta" : true, /* Show the accept all button when highPrivacy on */
                    "highPrivacy": false, /* désactiver le consentement implicite (en naviguant) ? */
                    "handleBrowserDNTRequest": false, /* If Do Not Track == 1, disallow all */
                    "removeCredit": true, /* supprimer le lien vers la source ? */
                    "moreInfoLink": true, /* Show more info link */
                    "useExternalCss": false, /* If false, the tarteaucitron.css file will be loaded */
                    "cookieDomain": "" /* Nom de domaine sur lequel sera posé le cookie - pour les multisites / sous-domaines - Facultatif */
                });
                //]]
                </script>';
        }

        $Xcontent = '
            </head>
            <body>';

        $fp = fopen($fic, "r");
        if (filesize($fic) > 0) {
            $Xcontent .= fread($fp, filesize($fic));
        }
        fclose($fp);

        //compteur
        $compteur = $dir . "compteur.txt";

        if (!file_exists($compteur)) {
            $fp = fopen($compteur, "w");
            fwrite($fp, "1");
            fclose($fp);
        } else {
            $cpt = file($compteur);
            $cpt = $cpt[0] + 1;
            $fp = fopen($compteur, "w");
            fwrite($fp, $cpt);
            fclose($fp);
        }

        // Analyse et convertion des liens et images, blog, header, footer ...
        $perpage = strstr($Xcontent, '!blog_page!') 
            ? substr($Xcontent, strpos($Xcontent, "!blog_page!", 0) + 11, 2) 
            : 4;

        if (strstr($Xcontent, '!blog!')) {
            include("modules/blog/http/readnews.php");

            settype($startpage, 'integer');
            settype($perpage, 'integer');
            settype($action, 'string');

            $content = readnews($dir, $op, $perpage, $startpage, $action, $adminblog);

            if (strstr($content, '!l_new_pages!')) {
                $new_pages = substr($content, strpos($content, "!l_new_pages!") + 13);
                $content = substr($content, 0, strpos($content, "!l_new_pages!"));
            }
        }

        $Hcontent = '';
        if (strstr($Xcontent, '!l_header!')) {
            $l_fic = $dir . 'header.html';

            if (file_exists($l_fic)) {
                $fp = fopen($l_fic, 'r');

                if (filesize($l_fic) > 0) {
                    $Hcontent = convert_ressources(fread($fp, filesize($l_fic)));
                }

                fclose($fp);
            }
        }

        $Fcontent = '';
        if (strstr($Xcontent, '!l_footer!')) {
            $l_fic = $dir . 'footer.html';

            if (file_exists($l_fic)) {
                $fp = fopen($l_fic, 'r');

                if (filesize($l_fic) > 0) {
                    $Fcontent = convert_ressources(fread($fp, filesize($l_fic)));
                }

                fclose($fp);
            }
        }

        $blog_ajouter = (($adminblog) and (strstr($Xcontent, '!l_blog_ajouter!'))) ? '!l_blog_ajouterOK!' : '';
        $Xcontent = convert_ressources($Xcontent);

        // Meta-lang et removehack local
        $MNS_METALANG_words = array(
            "'!l_header!'i" => "$Hcontent",
            "'!l_footer!'i" => "$Fcontent",
            "'!blog_page!$perpage'i" => '',
            "'!l_compteur!'i" => $cpt,
            "'!l_new_pages!'i" => $new_pages,
            "'!l_blog_ajouter!'i" => $blog_ajouter,
            "'!blog!'i" => $content,
            "'!copyright!'i" => $copyright,
            "'!avatar!'i" => $avatar_mns,
            "'!id_mns!'i" => $op,
            "'!gr_name!'i" => language::aff_langue($gr_name)
        );

        $Xcontent = preg_replace(array_keys($MNS_METALANG_words), array_values($MNS_METALANG_words), $Xcontent);
        $Xcontent = metalang::meta_lang(hack::MNSremoveHack($Xcontent));

        //applique aff_video que sur la partie affichage
        $rupt = strpos($Xcontent, '#v_yt#');

        echo substr($Xcontent, 0, $rupt);
        echo forum::aff_video_yt(substr($Xcontent, $rupt + 6));

        if ($adminblog) {
            echo '
                <script type="text/javascript">
                    //<![CDATA[
                        $(".modal-body").load("modules/blog/view/matrice/readme.' . $language . '.txt"
                        , function(dataaide, textStatus, jqxhr) {
                            $("#aide_mns").html(dataaide.replace(/(\r\n|\n\r|\r|\n)/g, "<br />"));
                        });
                    //]]>
                </script>';
        }  

        if (defined('CITRON')) {
            echo '
                <script type="text/javascript">
                    //<![CDATA[
                        (tarteaucitron.job = tarteaucitron.job || []).push("vimeo");
                        (tarteaucitron.job = tarteaucitron.job || []).push("youtube");
                        (tarteaucitron.job = tarteaucitron.job || []).push("dailymotion");
                        //tarteaucitron.user.gtagUa = ""; /*uncomment the line and add your gtag*/
                        //tarteaucitron.user.gtagMore = function () { /* add here your optionnal gtag() */ };
                        //(tarteaucitron.job = tarteaucitron.job || []).push("gtag");/*uncomment the line*/
                    //]]
                </script>';
        }

        echo '
                <script type="text/javascript" src="assets/js/npds_adapt.js"></script>
                </body>
            </html>';
    }
}
