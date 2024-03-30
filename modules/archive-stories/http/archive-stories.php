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

use npds\system\news\news;
use npds\system\cache\cache;
use npds\system\language\language;
use npds\system\support\facades\DB;
use npds\system\pagination\paginator;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

include("modules/$ModPath/config/archive-stories.php");
include("modules/$ModPath/config/cache.timings.php");

if (!isset($start)) { 
    $start = 0;
}

include("themes/default/header.php");

// start caching page
if ((cacheManagerStart()->genereting_output == 1) or (cacheManagerStart()->genereting_output == -1) or (!$SuperCache)) {
    
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
        $count = cache::Q_select2(
            DB::table('stories')->where('archive', $arch)->count('sid'), 3600, 'archives'
        );
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

    $xtab = $arch == 0 
        ? news::news_aff("libre", "WHERE archive='$arch' ORDER BY sid DESC LIMIT $start, $maxcount", $start, $maxcount) 
        : news::news_aff("archive", "WHERE archive='$arch' ORDER BY sid DESC LIMIT $start, $maxcount", $start, $maxcount);

    $ibid = 0;
    $story_limit = 0;

    while (($story_limit < $maxcount) and ($story_limit < sizeof($xtab))) {
        list($sid, $catid, $aid, $title, $time, $hometext, $bodytext, $comments, $counter, $topic, $informant) = $xtab[$story_limit];
        
        $story_limit++;

        $printP = '<a href="print.php?sid=' . $sid . '&amp;archive=' . $arch . '"><i class="fa fa-print fa-lg" title="' . translate("Page spéciale pour impression") . '" data-bs-toggle="tooltip" data-bs-placement="left"></i></a>';
        $sendF = '<a class="ms-4" href="friend.php?op=FriendSend&amp;sid=' . $sid . '&amp;archive=' . $arch . '"><i class="fa fa-at fa-lg" title="' . translate("Envoyer cet article à un ami") . '" data-bs-toggle="tooltip" data-bs-placement="left" ></i></a>';
        
        if ($catid != 0) {
            $resultm = sql_query("SELECT title FROM " . $NPDS_Prefix . "stories_cat WHERE catid='$catid'");
            list($title1) = sql_fetch_row($resultm);
            $title = '<a href="article.php?sid=' . $sid . '&amp;archive=' . $arch . '" >' . language::aff_langue(ucfirst($title)) . '</a> [ <a href="index.php?op=newindex&amp;catid=' . $catid . '">' . language::aff_langue($title1) . '</a> ]';
        } else {
            $title = '<a href="article.php?sid=' . $sid . '&amp;archive=' . $arch . '" >' . language::aff_langue(ucfirst($title)) . '</a>';
        }

        $locale = language::getLocale();
        $datetime = ucfirst(htmlentities(\PHP81_BC\strftime(translate("datestring"), $time, $locale), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'utf-8'));

        echo '
            <tr>
            <td>' . $title . '</td>
            <td>' . $counter . '</td>
            <td><small>' . $datetime . '</small></td>
            <td>' . userpopover($informant, 40, 2) . ' ' . $informant . '</td>
            <td>' . $printP . $sendF . '</td>
            </tr>';
    }

    echo '
            </tbody>
        </table>
        <div class="d-flex my-3 justify-content-between flex-wrap">
        <ul class="pagination pagination-sm">
            <li class="page-item disabled"><a class="page-link" href="#" >' . translate("Nb. d'articles") . ' ' . $count . ' </a></li>
            <li class="page-item disabled"><a class="page-link" href="#" >' . $nbPages . ' ' . translate("pages") . '</a></li>
        </ul>';

    echo paginator::paginate('modules.php?ModPath=archive-stories&amp;ModStart=archive-stories&amp;start=', '&amp;count=' . $count, $nbPages, $current, 1, $maxcount, $start);
    
    echo '</div>';
}

// end Caching page
cacheManagerEnd();

include("themes/default/footer.php");
