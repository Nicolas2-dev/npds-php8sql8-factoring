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

use npds\system\assets\css;
use npds\system\theme\theme;
use npds\system\config\Config;
use npds\system\security\hack;
use npds\library\pages\pageref;
use npds\system\support\counter;
use npds\system\support\editeur;
use npds\system\support\referer;
use npds\system\language\language;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
    die();
}

function head($css_pages_ref, $css, $tmp_theme, $skin, $js, $m_description, $m_keywords)
{
    global $theme;

    if (Config::get('npds.gzhandler') == 1) {
        ob_start("ob_gzhandler");
    }
    
    include("themes/$tmp_theme/theme.php");

    // Meta
    if (file_exists("storage/meta/meta.php")) {
        $meta_op = '';
        include("storage/meta/meta.php");
    }

    // Favicon
    if (file_exists("themes/$tmp_theme/assets/images/favicon.ico")) {
        $favico = "themes/$tmp_theme/assets/images/favicon.ico";
    } else {
        $favico = 'assets/images/favicon.ico';
    }

    echo '<link rel="shortcut icon" href="' . $favico . '" type="image/x-icon" />';

    // Syndication RSS & autres
    $nuke_url = Config::get('npds.nuke_url');
    $sitename = Config::get('npds.sitename');

    // Canonical
    $scheme = strtolower($_SERVER['REQUEST_SCHEME'] ?? 'http');
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];
    
    echo '<link rel="canonical" href="' . ($scheme . '://' . $host . $uri) . '" />';

    // humans.txt
    if (file_exists("humans.txt")) {
        echo '<link type="text/plain" rel="author" href="' . $nuke_url . '/humans.txt" />';
    }

    echo '
    <link href="backend.php?op=RSS0.91" title="' . $sitename . ' - RSS 0.91" rel="alternate" type="text/xml" />
    <link href="backend.php?op=RSS1.0" title="' . $sitename . ' - RSS 1.0" rel="alternate" type="text/xml" />
    <link href="backend.php?op=RSS2.0" title="' . $sitename . ' - RSS 2.0" rel="alternate" type="text/xml" />
    <link href="backend.php?op=ATOM" title="' . $sitename . ' - ATOM" rel="alternate" type="application/atom+xml" />
    ';

    // Tiny_mce
    if (Config::get('editeur.tiny_mce_init')) {
        echo editeur::aff_editeur("tiny_mce", "begin");
    }

    // include externe JAVASCRIPT file from themes/default/view/include or themes/.../include for functions, codes in the <body onload="..." event...
    importExternalJavacript();

    // include externe file from themes/default/view/include or themes/.../include for functions, codes ... - skin motor
    if (file_exists("themes/default/view/include/header_head.inc")) {
        ob_start();
            include "themes/default/view/include/header_head.inc";
            $hH = ob_get_contents();
        ob_end_clean();

        if ($skin != '' and substr($tmp_theme, -3) == "_sk") {
            $hH = str_replace('assets/shared/bootstrap/dist/css/bootstrap.min.css', 'themes/_skins/' . $skin . '/bootstrap.min.css', $hH);
            $hH = str_replace('assets/shared/bootstrap/dist/css/extra.css', 'themes/_skins/' . $skin . '/extra.css', $hH);
        }
        echo $hH;
    }

    if (file_exists("themes/$tmp_theme/view/include/header_head.inc")) {
        include("themes/$tmp_theme/view/include/header_head.inc");
    }

    echo css::import_css($tmp_theme, Config::get('npds.language'), '', $css_pages_ref, $css);

    // Mod by Jireck - Chargeur de JS via routes/pages.php
    //importPageRefJs($js);
    // Mod by Jireck - Chargeur de JS via routes/pages.php
    // function importPageRefJs($js)
    // {
    if ($js) {
        $theme = theme::getTheme();

        if (is_array($js)) {
            foreach ($js as $k => $tab_js) {
                if (stristr($tab_js, 'http://') || stristr($tab_js, 'https://')) {
                    echo '<script type="text/javascript" src="' . $tab_js . '"></script>';
                } else {
                    if (file_exists("themes/$theme/assets/js/$tab_js") and ($tab_js != '')) {
                        echo '<script type="text/javascript" src="themes/' . $theme . '/assets/js/' . $tab_js . '"></script>';
                    } elseif (file_exists("$tab_js") and ($tab_js != "")) {
                        echo '<script type="text/javascript" src="' . $tab_js . '"></script>';
                    }
                }
            }
        } else {
            if (file_exists("themes/$theme/assets/js/$js")) {
                echo '<script type="text/javascript" src="themes/' . $theme . '/assets/js/' . $js . '"></script>';
            } elseif (file_exists("$js")) {
                echo '<script type="text/javascript" src="' . $js . '"></script>';
            }
        }
    }
    //}

    echo '
    </head>';

    include("themes/$tmp_theme/header.php");
}

// -----------------------
Config::Set('npds.theme.header', 1);
// -----------------------

// include externe file from themes/default/view/include for functions, codes ...
if (file_exists("themes/default/view/include/header_before.inc")) {
    include("themes/default/view/include/header_before.inc");
}

// take the right theme location !
$tmp_theme = theme::getTheme();
$skin = theme::getSkin();

// include page référence
//include('library/pages/pageref.php');

// LOAD routes/pages.php and Go ...
settype($PAGES, 'array');
settype($m_keywords, 'string');
settype($m_description, 'string');

global $pdst, $Titlesitename, $PAGES;

require_once("routes/pages.php");

// import routes/pages.php specif values from theme
if (file_exists("themes/" . $tmp_theme . "/routes/pages.php")) { 
    include("themes/" . $tmp_theme . "/routes/pages.php");
}

$page_uri = preg_split("#(&|\?)#", $_SERVER['REQUEST_URI']); //var_dump($page_uri);
$Npage_uri = count($page_uri);
$pages_ref = basename($page_uri[0]); //var_dump($pages_ref);

//var_dump($page_uri, $Npage_uri, $pages_ref);

// Static page and Module can have Bloc, Title ....
if ($pages_ref == "static.php") {
    $pages_ref = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], "static.php"));
}

if ($pages_ref == "modules.php") {
    if (isset($PAGES["modules.php?ModPath=$ModPath&ModStart=$ModStart*"]['title'])) {
        $pages_ref = "modules.php?ModPath=$ModPath&ModStart=$ModStart*";
    } else {
        $pages_ref = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], "modules.php"));
    }
}

// Admin function can have all the PAGES attributs except Title
if ($pages_ref == "admin.php") {
    if (array_key_exists(1, $page_uri)) {
        if (array_key_exists($pages_ref . "?" . $page_uri[1], $PAGES)) {
            if (array_key_exists('title', $PAGES[$pages_ref . "?" . $page_uri[1]])) {
                $pages_ref .= "?" . $page_uri[1];
            }
        }
    }
}

// extend usage of routes/pages.php : blocking script with part of URI for user, admin or with the value of a VAR
if ($Npage_uri > 1) {
    for ($uri = 1; $uri < $Npage_uri; $uri++) {
        if (array_key_exists($page_uri[$uri], $PAGES)) {
            if (!$$PAGES[$page_uri[$uri]]['run']) {
                header("location: " . $PAGES[$page_uri[$uri]]['title']);
                die();
            }
        }
    }
}

// -----------------------
// A partir de ce niveau - $PAGES[$pages_ref] doit exister - sinon c'est que la page n'est pas dans routes/pages.php
// -----------------------
if (array_key_exists($pages_ref, $PAGES)) {
    // on definit l'affichage des blocks ... left, right, both, ...
    if (array_key_exists('blocs', $PAGES[$pages_ref])) {
        $pdst = $PAGES[$pages_ref]['blocs'];
    }

    // bloquer l'exécution de la page avec l'attribut run = non
    if ($PAGES[$pages_ref]['run'] == "no") {
        // si pages_ref === index.php alors site closed
        if ($pages_ref == "index.php") {
            $Titlesitename = "NPDS";

            if (file_exists("storage/meta/meta.php")) {
                include("storage/meta/meta.php");
            }

            if (file_exists("storage/static/webclosed.txt")) {
                include("storage/static/webclosed.txt");
            }

            die();

            // page bloquer alors redirection sur index   
        } else {
            header("location: index.php");
        }

        // redirige la page sur une autre url
    } elseif (($PAGES[$pages_ref]['run'] != "yes") and (($PAGES[$pages_ref]['run'] != ""))) {
        header("location: " . $PAGES[$pages_ref]['run']);
    }

    // Assure la gestion des titres ALTERNATIFS
    // BUG BUG BUG BUG !!!!!
    $tab_page_ref = explode("|", $PAGES[$pages_ref]['title']);
    
    if (count($tab_page_ref) > 1) {
        if (strlen($tab_page_ref[1]) > 1) {
            $PAGES[$pages_ref]['title'] = $tab_page_ref[1];
        } else {
            $PAGES[$pages_ref]['title'] = $tab_page_ref[0];
        }

        $PAGES[$pages_ref]['title'] = strip_tags($PAGES[$pages_ref]['title']);
    }

    // check la fin du title pour determiner si il se termine par + ou -
    $fin_title = substr($PAGES[$pages_ref]['title'], -1);

    // on retire le + ou - du title
    $TitlesitenameX = language::aff_langue(substr($PAGES[$pages_ref]['title'], 0, strlen($PAGES[$pages_ref]['title']) - 1));

    $Titlesitename = '';

    // neutralise le bug en dessous bug #4
    if ($Titlesitename == '') {
        $Titlesitename = Config::get('npds.sitename');
    }

    if ($fin_title == "+") {
        $Titlesitename = $TitlesitenameX . " - " . $Titlesitename;
    } else if ($fin_title == '-') {
        $Titlesitename = $TitlesitenameX; 
    }

    // bug #4
    // if ($Titlesitename == '') {
    //     $Titlesitename = Config::get('npds.sitename'); // bug ne fonctionne pas !!!!
    // }

    // globalisation de la variable title pour marquetapage mais protection pour la zone admin
    if ($pages_ref != "admin.php") {
        global $title;
    }

    if (!$title) {
        if ($fin_title == "+" or $fin_title == "-") {
            $title = $TitlesitenameX;
        } else {
            $title = language::aff_langue(substr($PAGES[$pages_ref]['title'], 0, strlen($PAGES[$pages_ref]['title'])));
        }
    } else {
        $title = hack::removeHack($title);
    }

    // meta description
    settype($m_description, 'string');
    if (array_key_exists('meta-description', $PAGES[$pages_ref]) and ($m_description == '')) {
        $m_description = language::aff_langue($PAGES[$pages_ref]['meta-description']);
    }


    // meta keywords
    settype($m_keywords, 'string');
    if (array_key_exists('meta-keywords', $PAGES[$pages_ref]) and ($m_keywords == '')) {
        $m_keywords = language::aff_langue($PAGES[$pages_ref]['meta-keywords']);
    }
}

// Initialisation de TinyMce

if (Config::get('npds.tiny_mce')) {
    if (array_key_exists($pages_ref, $PAGES)) {
        if (array_key_exists('TinyMce', $PAGES[$pages_ref])) {
            Config::set('editeur.tiny_mce_init', true);

            if (array_key_exists('TinyMce-theme', $PAGES[$pages_ref])) {
                Config::set('editeur.tiny_mce_theme', $PAGES[$pages_ref]['TinyMce-theme']);
            }

            if (array_key_exists('TinyMceRelurl', $PAGES[$pages_ref])) {
                Config::set('editeur.tiny_mce_relurl', $PAGES[$pages_ref]['TinyMceRelurl']);
            }
        } else {
            Config::set('editeur.tiny_mce_init', false);
            Config::set('npds.tiny_mce', false);
        }
    } else {
        Config::set('editeur.tiny_mce_init', false);
        Config::set('npds.tiny_mce', false);
    }
} else {
    Config::set('editeur.tiny_mce_init', false);
}

// Chargeur de CSS via routes/pages.php

if (array_key_exists($pages_ref, $PAGES)) {
    if (array_key_exists('css', $PAGES[$pages_ref])) {
        $css_pages_ref = $pages_ref;
        $css = $PAGES[$pages_ref]['css'];
    } else {
        $css_pages_ref = '';
        $css = '';
    }
} else {
    $css_pages_ref = '';
    $css = '';
}

// Mod by Jireck - Chargeur de JS via routes/pages.php
if (array_key_exists($pages_ref, $PAGES)) {
    if (array_key_exists('js', $PAGES[$pages_ref])) {
        $js = $PAGES[$pages_ref]['js'];
        if ($js != '') {
            global $pages_js;
            $pages_js = $js;
        }
    } else
        $js = '';
} else
    $js = '';

head($css_pages_ref, $css, $tmp_theme, $skin, $js, $m_description, $m_keywords);

// Referer update
referer::refererUpdate();

// Counter update
counter::counterUpadate();

// include externe file from themes/default/view/include for functions, codes ...
if (file_exists("themes/default/view/include/header_after.inc")) {
    include("themes/default/view/include/header_after.inc");
}
