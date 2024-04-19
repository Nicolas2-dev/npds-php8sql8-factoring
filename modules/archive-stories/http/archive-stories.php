<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* From ALL STORIES Add-On ... ver. 1.4.1a                              */
/*                                                                      */
/* NPDS Copyright (c) 2002-2023 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

use npds\support\news\news;
use npds\support\cache\cache;
use npds\system\config\Config;
use npds\support\utility\crypt;
use npds\support\language\language;
use npds\system\support\facades\DB;
use npds\support\pagination\paginator;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

include("modules/$ModPath/config/archive-stories.php");
include("modules/$ModPath/config/cache.timings.php");

include("themes/default/header.php");

// start Caching page
if (cache::cacheManagerStart2()) {   
    
    if (!isset($start)) { 
        $start = 0;
    }  

    if ($arch_titre) {
        echo language::aff_langue($arch_titre);
    }
    
    echo '
    <hr />
    <table id ="lst_art_arch" data-toggle="table"  data-striped="true" data-search="true" data-show-toggle="true" data-show-columns="true" data-mobile-responsive="true" data-icons-prefix="fa" data-buttons-class="outline-secondary" data-icons="icons">
        <thead>
            <tr>
                <th data-sortable="true" data-sorter="htmlSorter" data-halign="center" class="n-t-col-xs-4">' . translate("Articles") . '</th>
                <th data-sortable="true" data-halign="center" data-align="right" class="n-t-col-xs-1">' . translate("lus") . '</th>
                <th data-halign="center" data-align="right">' . translate("Posté le") . '</th>
                <th data-sortable="true" data-halign="center" data-align="left">' . translate("Auteur") . '</th>
                <th data-halign="center" data-align="center" class="n-t-col-xs-2">' . translate("Fonctions") . '</th>
            </tr>
        </thead>
        <tbody>';

    if (!isset($count)) {
        $stories_count = cache::Q_select3(
            DB::table('stories')
                ->select('sid')
                ->where('archive', $arch)
                ->get(), 
            3600, 
            crypt::encrypt('stories(archives)')
        );

        $count = count($stories_count);        
    }

    $nbPages = ceil($count / $maxcount);
    $current = 1;

    if ($start >= 1) {
        $current = $start / $maxcount;
    } elseif ($start < 1) {
        $current = 0;
    } else {
        $current = $nbPages;
    }

    $xtab = (($arch == 0) 
        ? news::news_aff2("libre", 
            DB::table('stories')
                ->select('sid', 'catid', 'ihome', 'time')
                ->where('archive', $arch)
                ->orderBy('sid', 'desc')
                ->limit($maxcount)
                ->offset($start)
                ->get(), 
            $start, 
            $maxcount) 
        : news::news_aff2("archive", 
            DB::table('stories')
                ->select('sid', 'catid', 'ihome')
                ->where('archive', $arch)
                ->orderBy('sid', 'desc')
                ->limit($maxcount)
                ->offset($start)
                ->get(), 
            $start, 
            $maxcount) 
    );

    $ibid = 0;
    $story_limit = 0;

    while (($story_limit < $maxcount) and ($story_limit < sizeof($xtab))) {

        $sid        = $xtab[$story_limit]['sid'];
        $catid      = $xtab[$story_limit]['catid'];
        $aid        = $xtab[$story_limit]['aid'];
        $title      = $xtab[$story_limit]['title'];
        $time       = $xtab[$story_limit]['time'];
        $hometext   = $xtab[$story_limit]['hometext'];
        $bodytext   = $xtab[$story_limit]['bodytext'];
        $comments   = $xtab[$story_limit]['comments'];
        $counter    = $xtab[$story_limit]['counter'];
        $topic      = $xtab[$story_limit]['topic'];
        $informant  = $xtab[$story_limit]['informant'];

        $printP = '<a href="'. site_url('print.php?sid=' . $sid . '&amp;archive=' . $arch) .'">
                <i class="fa fa-print fa-lg" title="' . translate("Page spéciale pour impression") . '" data-bs-toggle="tooltip" data-bs-placement="left"></i>
            </a>';

        $sendF = '<a class="ms-4" href="'. site_url('friend.php?op=FriendSend&amp;sid=' . $sid . '&amp;archive=' . $arch) .'">
                <i class="fa fa-at fa-lg" title="' . translate("Envoyer cet article à un ami") . '" data-bs-toggle="tooltip" data-bs-placement="left" ></i>
            </a>';
        
        if ($catid != 0) {

            $stories_cat = DB::table('stories_cat')
                                ->select('title')
                                ->where('catid', $catid)
                                ->first();

            $title = '<a href="'. site_url('article.php?sid=' . $sid . '&amp;archive=' . $arch) .'" >
                    ' . language::aff_langue(ucfirst($title)) . '
                </a>
                [ <a href="'. site_url('index.php?op=newindex&amp;catid=' . $catid) .'">
                    ' . language::aff_langue($stories_cat['title']) . '
                </a> ]';
        } else {
            $title = '<a href="'. site_url('article.php?sid=' . $sid . '&amp;archive=' . $arch) .'" >
                    ' . language::aff_langue(ucfirst($title)) . '
                </a>';
        }

        $locale = Config::get('npds.locale');
        $datetime = ucfirst(htmlentities(\PHP81_BC\strftime(translate("datestring"), $time, $locale), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'utf-8'));

        echo '
            <tr>
            <td>' . $title . '</td>
            <td>' . $counter . '</td>
            <td><small>' . $datetime . '</small></td>
            <td>' . userpopover($informant, 40, 2) . ' ' . $informant . '</td>
            <td>' . $printP . $sendF . '</td>
            </tr>';

        $story_limit++;            
    }

    echo '
            </tbody>
        </table>
        <div class="d-flex my-3 justify-content-between flex-wrap">
        <ul class="pagination pagination-sm">
            <li class="page-item disabled"><a class="page-link" href="#" >' . translate("Nb. d'articles") . ' ' . $count . ' </a></li>
            <li class="page-item disabled"><a class="page-link" href="#" >' . $nbPages . ' ' . translate("pages") . '</a></li>
        </ul>';

    echo paginator::paginate(site_url('modules.php?ModPath=archive-stories&amp;ModStart=archive-stories&amp;start='), '&amp;count=' . $count, $nbPages, $current, 1, $maxcount, $start);
    
    echo '</div>';
}

// end Caching page
cacheManagerEnd();

include("themes/default/footer.php");
