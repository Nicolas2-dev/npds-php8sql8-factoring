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

use npds\support\auth\users;
use npds\support\routing\url;
use npds\support\utility\code;
use npds\system\config\Config;
use npds\support\language\language;
use npds\support\metalang\metalang;
use npds\system\cache\cacheManager;
use npds\system\cache\SuperCacheEmpty;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

function autorisation_section($userlevel)
{
    $okprint = false;
    $tmp_auto = explode(',', $userlevel);

    foreach ($tmp_auto as $userlevel) {
        $okprint = users::autorisation($userlevel);
        
        if ($okprint) {
            break;
        }
    }

    return ($okprint);
}

function listsections($rubric)
{
    global $admin, $NPDS_Prefix;

    include("themes/default/header.php");

    if (file_exists("config/sections.php")) {
        include("config/sections.php");
    }

    // start caching page
    global $SuperCache;
    if ((cacheManagerStart()->genereting_output == 1) or (cacheManagerStart()->genereting_output == -1) or (!$SuperCache)) {

        settype($rubric, 'integer');
        settype($nb_r, 'integer');

        if ($rubric) { 
            $sqladd = "AND rubid='" . $rubric . "'";
        } else {
            $sqladd = '';
        }

        if ($admin) {

            // = DB::table('')->select()->where('', )->orderBy('')->get();

            $result = sql_query("SELECT rubid, rubname, intro FROM " . $NPDS_Prefix . "rubriques WHERE rubname<>'Divers' AND rubname<>'Presse-papiers' $sqladd ORDER BY ordre");
            $nb_r = sql_num_rows($result);
        } else {

            // = DB::table('')->select()->where('', )->orderBy('')->get();

            $result = sql_query("SELECT rubid, rubname, intro FROM " . $NPDS_Prefix . "rubriques WHERE enligne='1' AND rubname<>'Divers' AND rubname<>'Presse-papiers' $sqladd ORDER BY ordre");
            $nb_r = sql_num_rows($result);
        }

        $aff = '';

        if ($rubric) {
            $aff .= '<span class="lead"><a href="'. site_url('sections.php') .'" title="' . __d('two_news', 'Retour à l\'index des rubriques') . '" data-bs-toggle="tooltip">Index</a></span><hr />';
        }

        $aff .= '
        <h2>' . __d('two_news', 'Rubriques') . '<span class="float-end badge bg-secondary">' . $nb_r . '</span></h2>';

        if (sql_num_rows($result) > 0) {
            while (list($rubid, $rubname, $intro) = sql_fetch_row($result)) {

                // = DB::table('')->select()->where('', )->orderBy('')->get();

                $result2 = sql_query("SELECT secid, secname, image, userlevel, intro FROM " . $NPDS_Prefix . "sections WHERE rubid='$rubid' ORDER BY ordre");
                $nb_section = sql_num_rows($result2);
                
                $aff .= '
                <hr />
                <h3>';

                if ($nb_section !== 0) {
                    $aff .= '<a href="#" class="arrow-toggle text-primary" data-bs-toggle="collapse" data-bs-target="#rub-' . $rubid . '" ><i class="toggle-icon fa fa-caret-down"></i></a>';
                } else {
                    $aff .= '<i class="fa fa-caret-down text-muted invisible "></i>';
                }

                $aff .= '
                    <a class="ms-2" href="'. site_url('sections.php?rubric=' . $rubid) .'">' . language::aff_langue($rubname) . '</a><span class=" float-end">#NEW#<span class="badge bg-secondary" title="' . __d('two_news', 'Sous-rubrique') . '" data-bs-toggle="tooltip" data-bs-placement="left">' . $nb_section . '</span></span>
                </h3>';

                if ($intro != '') {
                    $aff .= '<p class="text-muted">' . language::aff_langue($intro) . '</p>';
                }

                $aff .= '<div id="rub-' . $rubid . '" class="collapse" >';

                while (list($secid, $secname, $image, $userlevel, $intro) = sql_fetch_row($result2)) {
                    $okprintLV1 = autorisation_section($userlevel);
                    $aff1 = '';
                    $aff2 = '';

                    if ($okprintLV1) {

                        // = DB::table('')->select()->where('', )->orderBy('')->get();

                        $result3 = sql_query("SELECT artid, title, counter, userlevel, timestamp FROM " . $NPDS_Prefix . "seccont WHERE secid='$secid' ORDER BY ordre");
                        $nb_art = sql_num_rows($result3);
                        
                        $aff .= '
                        <div class="card card-body mb-2" id="rub_' . $rubid . 'sec_' . $secid . '">
                            <h4 class="mb-2">';

                        if ($nb_art !== 0) {
                            $aff .= '<a href="#" class="arrow-toggle text-primary" data-bs-toggle="collapse" data-bs-target="#sec' . $secid . '" aria-expanded="true" aria-controls="sec' . $secid . '"><i class="toggle-icon fa fa-caret-up"></i></a>&nbsp;';
                        }

                        $aff1 = language::aff_langue($secname) . '<span class=" float-end">#NEW#<span class="badge bg-secondary" title="' . __d('two_news', 'Articles') . '" data-bs-toggle="tooltip" data-bs-placement="left">' . $nb_art . '</span></span>';
                        
                        if ($image != '') {
                            if (file_exists("assets/images/sections/$image")) {
                                $imgtmp = "assets/images/sections/$image";
                            } else {
                                $imgtmp = $image;
                            }

                            $suffix = strtoLower(substr(strrchr(basename($image), '.'), 1));
                            $aff1 .= '<img class="img-fluid" src="' . $imgtmp . '" alt="' . language::aff_langue($secname) . '" /><br />';
                        }

                        $aff1 .= '</h4>';

                        if ($intro != '') {
                            $aff1 .= '<p class="">' . language::aff_langue($intro) . '</p>';
                        }

                        $aff2 = '
                        <div id="sec' . $secid . '" class="collapse show">
                        <div class="">';

                        $noartid = false;
                        while (list($artid, $title, $counter, $userlevel, $timestamp) = sql_fetch_row($result3)) {
                            $okprintLV2 = autorisation_section($userlevel);
                            $nouveau = '';

                            if ($okprintLV2) {
                                $noartid = true;
                                $nouveau = 'oo';

                                if ((time() - $timestamp) < (86400 * 7)) {
                                    $nouveau = '';
                                }

                                $aff2 .= '<a href="'. site_url('sections.php?op=viewarticle&amp;artid=' . $artid) .'">' . language::aff_langue($title) . '</a><span class="float-end"><small>' . __d('two_news', 'lu : ') . ' ' . $counter . ' ' . __d('two_news', 'Fois') . '</small>';
                                
                                if ($nouveau == '') {
                                    $aff2 .= '<i class="far fa-star ms-3 text-success"></i>';
                                    $aff1 = str_replace('#NEW#', '<span class="me-2 badge bg-success animated faa-flash">N</span>', $aff1);
                                    $aff = str_replace('#NEW#', '<span class="me-2 badge bg-success animated faa-flash">N</span>', $aff);
                                }

                                $aff2 .= '</span><br />';
                            }
                        }

                        $aff = str_replace('#NEW#', '', $aff);
                        $aff1 = str_replace('#NEW#', '', $aff1);
                        $aff2 .= '
                            </div>
                            </div>
                        </div>';
                    }
                    $aff .= $aff1 . $aff2;
                }
                $aff .= '</div>';
            }
        }
        echo $aff; //la sortie doit se faire en html !!!

        // if ($rubric) {
        //     echo '<a class="btn btn-secondary" href="'. site_url('sections.php') .'">'.__d('two_news', 'Return to Sections Index').'</a>';
        // }
        
        sql_free_result($result);
    }

    // end Caching page
    cacheManagerEnd();

    include("themes/default/footer.php");;
}

function listarticles($secid)
{
    global $user, $prev, $NPDS_Prefix;

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT secname, rubid, image, intro, userlevel FROM " . $NPDS_Prefix . "sections WHERE secid='$secid'");
    list($secname, $rubid, $image, $intro, $userlevel) = sql_fetch_row($result);

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    list($rubname) = sql_fetch_row(sql_query("SELECT rubname FROM " . $NPDS_Prefix . "rubriques WHERE rubid='$rubid'"));

    $config = Config::get('sections');
    
    if ($config['sections_chemin'] == 1) {
        $chemin = '<span class="lead"><a href="'. site_url('sections.php') .'" title="' . __d('two_news', 'Retour à l\'index des rubriques') . '" data-bs-toggle="tooltip">Index</a>&nbsp;/&nbsp;<a href="'. site_url('sections.php?rubric=' . $rubid) .'">' . language::aff_langue($rubname) . '</a></span>';
    }

    $title =  language::aff_langue($secname);
    include("themes/default/header.php");;

    global $SuperCache;
    if ($SuperCache) {
        $cache_obj = new cacheManager();
        $cache_obj->startCachingPage();
    } else {
        $cache_obj = new SuperCacheEmpty();
    }

    if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!$SuperCache)) {
        $okprint1 = autorisation_section($userlevel);

        if ($okprint1) {

            // = DB::table('')->select()->where('', )->orderBy('')->get();

            $result = sql_query("SELECT artid, secid, title, content, userlevel, counter, timestamp FROM " . $NPDS_Prefix . "seccont WHERE secid='$secid' ORDER BY ordre");
            $nb_art = sql_num_rows($result);

            if ($prev == 1) {
                echo '<input class="btn btn-primary" type="button" value="' . __d('two_news', 'Retour à l\'administration') . '" onclick="javascript:history.back()" /><br /><br />';
            }

            if (function_exists("themesection_title")) {
                themesection_title($title);
            } else {
                echo $chemin . '
                <hr />
                <h3 class="mb-3">' . $title . '<span class="float-end"><span class="badge bg-secondary" title="' . __d('two_news', 'Articles') . '" data-bs-toggle="tooltip" data-bs-placement="left">' . $nb_art . '</span></h3>';
            }

            if ($intro != '') {
                echo language::aff_langue($intro);
            }

            if ($image != '') {
                if (file_exists("assets/images/sections/$image")) {
                    $imgtmp = "assets/images/sections/$image";
                } else {
                    $imgtmp = $image;
                }

                $suffix = strtoLower(substr(strrchr(basename($image), '.'), 1));
                echo '
                <p class="text-center"><img class="img-fluid" src="' . $imgtmp . '" alt="" /></p>';
            }

            echo '
                <p>' . __d('two_news', 'Voici les articles publiés dans cette rubrique.') . '</p>
            <div class="card card-body mb-3">';

            while (list($artid, $secid, $title, $content, $userlevel, $counter, $timestamp) = sql_fetch_row($result)) {
                $okprint2 = autorisation_section($userlevel);

                if ($okprint2) {
                    $nouveau = 'oo';

                    if ((time() - $timestamp) < (86400 * 7)) {
                        $nouveau = '';
                    }

                    echo '
                    <div class="mb-1">
                    <a href="'. site_url('sections.php?op=viewarticle&amp;artid=' . $artid) .'">' . language::aff_langue($title) . '</a><small>
                    ' . __d('two_news', 'lu : ') . ' ' . $counter . ' ' . __d('two_news', 'Fois') . '</small><span class="float-end"><a href="'. site_url('sections.php?op=printpage&amp;artid=' . $artid) .'" title="' . __d('two_news', 'Page spéciale pour impression') . '" data-bs-toggle="tooltip" data-bs-placement="left"><i class="fa fa-print fa-lg"></i></a></span>';
                        
                    if ($nouveau == '') {
                        echo '&nbsp;<i class="fa fa-star-o text-success"></i>';
                    }

                    echo '</div>';
                }
            }

            echo '
            </div>';
            /*
            echo '
            <a class="btn btn-secondary" href="'. site_url('sections.php') .'">'.__d('two_news', 'Return to Sections Index').'</a>';
            */
        } else {
            url::redirect_url("sections.php");
        }

        sql_free_result($result);
    }

    if ($SuperCache) {
        $cache_obj->endCachingPage();
    }

    include("themes/default/footer.php");;
}

function viewarticle($artid, $page)
{
    global $NPDS_Prefix, $prev, $user, $numpage;
    
    $numpage = $page;

    if ($page == '') {

        //DB::table('')->where('', )->update(array(
        //    ''       => ,
        //));

        sql_query("UPDATE " . $NPDS_Prefix . "seccont SET counter=counter+1 WHERE artid='$artid'");
    }

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result_S = sql_query("SELECT artid, secid, title, content, counter, userlevel FROM " . $NPDS_Prefix . "seccont WHERE artid='$artid'");
    list($artid, $secid, $title, $Xcontent, $counter, $userlevel) = sql_fetch_row($result_S);

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    list($secid, $secname, $rubid) = sql_fetch_row(sql_query("SELECT secid, secname, rubid FROM " . $NPDS_Prefix . "sections WHERE secid='$secid'"));

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    list($rubname) = sql_fetch_row(sql_query("SELECT rubname FROM " . $NPDS_Prefix . "rubriques WHERE rubid='$rubid'"));

    $tmp_auto = explode(',', $userlevel);

    foreach ($tmp_auto as $userlevel) {
        $okprint = autorisation_section($userlevel);
        if ($okprint) {
            break;
        }
    }

    if ($okprint) {
        $pindex = substr(substr($page, 5), 0, -1);

        if ($pindex != '') {
            $pindex = __d('two_news', 'Page') . ' ' . $pindex;
        }

        $config = Config::get('sections');

        if ($config['sections_chemin'] == 1) {
            $chemin = '<span class="lead"><a href="'. site_url('sections.php') .'">Index</a>&nbsp;/&nbsp;<a href="'. site_url('sections.php?rubric=' . $rubid) .'">' . language::aff_langue($rubname) . '</a>&nbsp;/&nbsp;<a href="'. site_url('sections.php?op=listarticles&amp;secid=' . $secid) .'">' . language::aff_langue($secname) . '</a></span>';
        }

        $title = language::aff_langue($title);

        include("themes/default/header.php");

        global $SuperCache;
        if ($SuperCache) {
            $cache_obj = new cacheManager();
            $cache_obj->startCachingPage();
        } else {
            $cache_obj = new SuperCacheEmpty();
        }

        if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!$SuperCache)) {
            $words = sizeof(explode(' ', $Xcontent));

            if ($prev == 1) {
                echo '<input class="btn btn-secondary" type="button" value="' . __d('two_news', 'Retour à l\'administration') . '" onclick="javascript:history.back()" /><br /><br />';
            }

            if (function_exists("themesection_title")) {
                themesection_title($title);
            } else {
                echo $chemin . '
                <hr />
                <h3 class="mb-2">' . $title . '<span class="small text-muted"> - ' . $pindex . '</span></h3>
                <p><span class="text-muted small">(' . $words . ' ' . __d('two_news', 'mots dans ce texte )') . '&nbsp;-&nbsp;
            ' . __d('two_news', 'lu : ') . ' ' . $counter . ' ' . __d('two_news', 'Fois') . '</span><span class="float-end"><a href="'. site_url('sections.php?op=printpage&amp;artid=' . $artid) .'" title="' . __d('two_news', 'Page spéciale pour impression') . '" data-bs-toggle="tooltip" data-bs-placement="left"><i class="fa fa-print fa-lg ms-3"></i></a></span></p><hr />';
            }

            preg_match_all('#\[page.*\]#', $Xcontent, $rs);
            $ndepages = count($rs[0]);

            if ($page != '') {
                $Xcontent = substr($Xcontent, strpos($Xcontent, $page) + strlen($page));
                $multipage = true;
            } else {
                $multipage = false;
            }

            $pos_page = strpos($Xcontent, '[page');
            $longueur = mb_strpos($Xcontent, ']', $pos_page, 'iso-8859-1') - $pos_page + 1;

            if ($pos_page) {
                $pageS = substr($Xcontent, $pos_page, $longueur);
                $Xcontent = substr($Xcontent, 0, $pos_page);

                $Xcontent .= '
                <nav class="d-flex mt-3">
                <ul class="mx-auto pagination pagination-sm">
                <li class="page-item disabled"><a class="page-link" href="#">' . $ndepages . ' pages</a></li>';

                if ($pageS !== '[page0]') {
                    $Xcontent .= '
                    <li class="page-item"><a class="page-link" href="'. site_url('sections.php?op=viewarticle&amp;artid=' . $artid) .'">' . __d('two_news', 'Début de l\'article') . '</a></li>';
                }

                $Xcontent .= '
                    <li class="page-item active"><a class="page-link">' . preg_replace('#\[(page)(.*)(\])#', '\1 \2', $pageS) . '</a></li>
                    <li class="page-item"><a class="page-link" href="'. site_url('sections.php?op=viewarticle&amp;artid=' . $artid . '&amp;page=' . $pageS) .'" >' . __d('two_news', 'Page suivante') . '</a></li>
                </ul>
                </nav>';
            } elseif ($multipage) {
                $Xcontent .= '
                <nav class="d-flex mt-3">
                <ul class="mx-auto pagination pagination-sm">
                <li class="page-item"><a class="page-link" href="'. site_url('sections.php?op=viewarticle&amp;artid=' . $artid . '&amp;page=[page0]') .'">' . __d('two_news', 'Début de l\'article') . '</a></li>
                </ul>
                </nav>';
            }

            $Xcontent = code::aff_code(language::aff_langue($Xcontent));
            echo '<div id="art_sect">' . metalang::meta_lang($Xcontent) . '</div>';

            $artidtempo = $artid;

            if ($rubname != 'Divers') {

                // echo '<hr /><p><a class="btn btn-secondary" href="'. site_url(sections.php'') .'">'.__d('two_news', 'Return to Sections Index').'</a></p>'; 
                // echo '<h4>***<strong>'.__d('two_news', 'Back to chapter:').'</strong></h4>';
                // echo '<ul class="list-group"><li class="list-group-item"><a href="'. site_url('sections.php?op=listarticles&amp;secid='.$secid) .'">'.language::aff_langue($secname).'</a></li></ul>';

                // = DB::table('')->select()->where('', )->orderBy('')->get();

                $result3 = sql_query("SELECT artid, secid, title, userlevel FROM " . $NPDS_Prefix . "seccont WHERE (artid<>'$artid' AND secid='$secid') ORDER BY ordre");
                $nb_article = sql_num_rows($result3);

                if ($nb_article > 0) {
                    echo '
                    <h4 class="my-3">' . __d('two_news', 'Autres publications de la sous-rubrique') . '<span class="badge bg-secondary float-end">' . $nb_article . '</span></h4>
                    <ul class="list-group">';

                    while (list($artid, $secid, $title, $userlevel) = sql_fetch_row($result3)) {
                        $okprint2 = autorisation_section($userlevel);

                        if ($okprint2) {
                            echo '<li class="list-group-item list-group-item-action"><a href="'. site_url('sections.php?op=viewarticle&amp;artid=' . $artid) .'">' . language::aff_langue($title) . '</a></li>';
                        }
                    }
                    echo '</ul>';
                }
            }

            $artid = $artidtempo;

            // = DB::table('')->select()->where('', )->orderBy('')->get();

            $resultconnexe = sql_query("SELECT id2 FROM " . $NPDS_Prefix . "compatsujet WHERE id1='$artid'");

            if (sql_num_rows($resultconnexe) > 0) {
                echo '
                <h4 class="my-3">' . __d('two_news', 'Cela pourrait vous intéresser') . '<span class="badge bg-secondary float-end">' . sql_num_rows($resultconnexe) . '</span></h4>
                <ul class="list-group">';

                while (list($connexe) = sql_fetch_row($resultconnexe)) {

                    // = DB::table('')->select()->where('', )->orderBy('')->get();

                    $resultpdtcompat = sql_query("SELECT artid, title, userlevel FROM " . $NPDS_Prefix . "seccont WHERE artid='$connexe'");
                    list($artid2, $title, $userlevel) = sql_fetch_row($resultpdtcompat);

                    $okprint2 = autorisation_section($userlevel);

                    if ($okprint2) {
                        echo '<li class="list-group-item list-group-item-action"><a href="'. site_url('sections.php?op=viewarticle&amp;artid=' . $artid2) .'">' . language::aff_langue($title) . '</a></li>';
                    }
                }
                echo '</ul>';
            }
        }

        sql_free_result($result_S);

        if ($SuperCache) {
            $cache_obj->endCachingPage();
        }

        include("themes/default/footer.php");;
    } else {
        header('Location: '. site_url('sections.php'));
    }
}

function PrintSecPage($artid)
{
    global $NPDS_Prefix, $language, $Titlesitename;

    include("storage/meta/meta.php");

    echo '
            <link rel="stylesheet" href="assets/shared/bootstrap/dist/css/bootstrap.min.css" />
        </head>
        <body>
            <div id="print_sect" max-width="640" class="container p-1 n-hyphenate">
                <p class="text-center">';

    $site_logo = Config::get('npds.site_logo');

    $pos = strpos($site_logo, "/");

    echo $pos 
        ? '<img src="' . $site_logo . '" alt="logo" />' 
        : '<img src="assets/images/' . $site_logo . '" alt="logo" />';

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT title, content FROM " . $NPDS_Prefix . "seccont WHERE artid='$artid'");
    list($title, $content) = sql_fetch_row($result);

    echo '<strong class="my-3 d-block">' . language::aff_langue($title) . '</strong></p>';
    $content = code::aff_code(language::aff_langue($content));
    $pos_page = strpos($content, "[page");

    if ($pos_page) {
        $content = str_replace("[page", str_repeat("-", 50) . "&nbsp;[page", $content);
    }

    echo metalang::meta_lang($content);

    echo '
                <hr />
                <p class="text-center">
                ' . __d('two_news', 'Cet article provient de') . ' ' . Config::get('npds.sitename') . '<br /><br />
                ' . __d('two_news', 'L\'url pour cet article est : ') . '
                <a href="'. site_url('sections.php?op=viewarticle&amp;artid=' . $artid) .'">'. site_url('sections.php?op=viewarticle&amp;artid=' . $artid) .'</a>
                </p>
                </div>
                <script type="text/javascript" src="assets/js/jquery.min.js"></script>
                <script type="text/javascript" src="assets/shared/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
                <script type="text/javascript" src="assets/js/npds_adapt.js"></script>
            </body>
        </html>';
}

function verif_aff($artid)
{
    global $NPDS_Prefix;

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT secid FROM " . $NPDS_Prefix . "seccont WHERE artid='$artid'");
    list($secid) = sql_fetch_row($result);

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT userlevel FROM " . $NPDS_Prefix . "sections WHERE secid='$secid'");
    list($userlevel) = sql_fetch_row($result);

    $okprint = false;
    $okprint = autorisation_section($userlevel);

    return ($okprint);
}

settype($op, 'string');

switch ($op) {
    case 'viewarticle':
        if (verif_aff($artid)) {
            
            settype($page, 'string');
            viewarticle($artid, $page);
        } else {
            header('location: '. site_url('sections.php'));
        }
        break;

    case 'listarticles':
        listarticles($secid);
        break;

    case 'printpage':
        if (verif_aff($artid)) {
            PrintSecPage($artid);
        } else {
            header('location: '. site_url('sections.php'));
        }
        break;

    default:
        settype($rubric, 'string');
        listsections($rubric);
        break;
}
