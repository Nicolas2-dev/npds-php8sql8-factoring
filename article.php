<?php
/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* This version name NPDS Copyright (c) 2001-2023 by Philippe Brunier   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\system\news\news;
use npds\system\utility\code;
use npds\system\config\Config;
use npds\system\language\language;
use npds\system\language\metalang;
use npds\system\support\facades\DB;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

settype($sid, "integer");
settype($archive, "integer");

if (!isset($sid) && !isset($tid)) {
    header("Location: index.php");
}

if (!isset($archive)) {
    $archive = 0;
}

$xtab = (!$archive) 
    ? news::news_aff("libre", "WHERE sid='$sid'", 1, 1) 
    : news::news_aff("archive", "WHERE sid='$sid'", 1, 1);

list($sid, $catid, $aid, $title, $time, $hometext, $bodytext, $comments, $counter, $topic, $informant, $notes) = $xtab[0];
if (!$aid) {
    header("Location: index.php");
}

DB::table('stories')->where('sid', $sid)->update(array(
    'counter'       => DB::raw('counter+1'),
));

include("themes/default/header.php");

// Include cache manager
if ((cacheManagerStart()->genereting_output == 1) or (cacheManagerStart()->genereting_output == -1) or (!Config::get('cache.config.SuperCache'))) {
    $title = language::aff_langue(stripslashes($title));
    $hometext = code::aff_code(language::aff_langue(stripslashes($hometext)));
    $bodytext = code::aff_code(language::aff_langue(stripslashes($bodytext)));
    $notes = code::aff_code(language::aff_langue(stripslashes($notes)));

    if ($notes != '') {
        $notes = '<div class="note blockquote">' . translate("Note") . ' : ' . $notes . '</div>';
    }

    $bodytext = $bodytext == '' 
        ? metalang::meta_lang($hometext . '<br />' . $notes) 
        : metalang::meta_lang($hometext . '<br />' . $bodytext . '<br />' . $notes);

    if ($informant == '') {
        $informant = Config::get('npds.anonymous');
    }

    news::getTopics($sid);

    if ($catid != 0) {

        $stories_cat = DB::table('stories_cat')->select('title')->where('catid', $catid)->first();

        $title = '<a href="index.php?op=newindex&amp;catid=' . $catid . '"><span>' . language::aff_langue($stories_cat['title1']) . '</span></a> : ' . $title;
    }

    $boxtitle = translate("Liens relatifs");
    $boxstuff = '<ul>';

    $related = DB::table('related')->select('name', 'url')->where('tid', $topic)->get();
    
    foreach ($related as $val) {
        $boxstuff .= '<li><a href="' . $val['url'] . '" target="_blank"><span>' . $val['name'] . '</span></a></li>';
    }

    $boxstuff .= '
        </ul>
        <ul>
            <li><a href="search.php?topic=' . $topic . '" >' . translate("En savoir plus à propos de") . ' : </a><span class="h5"><span class="badge bg-secondary" title="' . $topicname . '<hr />' . language::aff_langue($topictext) . '" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="right">' . language::aff_langue($topicname) . '</span></span></li>
            <li><a href="search.php?member=' . $informant . '" >' . translate("Article de") . ' ' . $informant . '</a> ' . userpopover($informant, 36, '') . '</li>
        </ul>
        <div><span class="fw-semibold">' . translate("L'article le plus lu à propos de") . ' : </span><span class="h5"><span class="badge bg-secondary" title="' . $topicname . '<hr />' . language::aff_langue($topictext) . '" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="right">' . language::aff_langue($topicname) . '</span></span></div>';

    $xtab = news::news_aff("big_story", "WHERE topic=$topic", 1, 1);
    list($topstory, $ttitle) = $xtab[0];

    $boxstuff .= '
        <ul>
            <li><a href="article.php?sid=' . $topstory . '" >' . language::aff_langue($ttitle) . '</a></li>
        </ul>
        <div><span class="fw-semibold">' . translate("Les dernières nouvelles à propos de") . ' : </span><span class="h5"><span class="badge bg-secondary" title="' . $topicname . '<hr />' . language::aff_langue($topictext) . '" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="right">' . language::aff_langue($topicname) . '</span></span></div>';

    $xtab = (!$archive) 
        ? news::news_aff("libre", "WHERE topic=$topic AND archive='0' ORDER BY sid DESC LIMIT 0,5", 0, 5) 
        : news::news_aff("archive", "WHERE topic=$topic AND archive='1' ORDER BY sid DESC LIMIT 0,5", 0, 5);

    $story_limit = 0;
    $boxstuff .= '<ul>';

    while (($story_limit < 5) and ($story_limit < sizeof($xtab))) {
        list($sid1, $catid1, $aid1, $title1) = $xtab[$story_limit];
        $story_limit++;
        
        $title1 = language::aff_langue(addslashes($title1));
        $boxstuff .= '<li><a href="article.php?sid=' . $sid1 . '&amp;archive=' . $archive . '" >' . language::aff_langue(stripslashes($title1)) . '</a></li>';
    }

    $boxstuff .= '
        </ul>
        <p align="center">
            <a href="print.php?sid=' . $sid . '&amp;archive=' . $archive . '" ><i class="fa fa-print fa-2x me-3" title="' . translate("Page spéciale pour impression") . '" data-bs-toggle="tooltip"></i></a>
            <a href="friend.php?op=FriendSend&amp;sid=' . $sid . '&amp;archive=' . $archive . '"><i class="fa fa-2x fa-at" title="' . translate("Envoyer cet article à un ami") . '" data-bs-toggle="tooltip"></i></a>
        </p>';

    if (!$archive) {
        $previous_tab = news::news_aff("libre", "WHERE sid<'$sid' ORDER BY sid DESC ", 0, 1);
        $next_tab = news::news_aff("libre", "WHERE sid>'$sid' ORDER BY sid ASC ", 0, 1);
    } else {
        $previous_tab = news::news_aff("archive", "WHERE sid<'$sid' ORDER BY sid DESC", 0, 1);
        $next_tab = news::news_aff("archive", "WHERE sid>'$sid' ORDER BY sid ASC ", 0, 1);
    }

    if (array_key_exists(0, $previous_tab)) {
        list($previous_sid) = $previous_tab[0];
    } else {
        $previous_sid = 0;
    }

    if (array_key_exists(0, $next_tab)) {
        list($next_sid) = $next_tab[0];
    } else {
        $next_sid = 0;
    }

    themearticle($aid, $informant, $time, $title, $bodytext, $topic, $topicname, $topicimage, $topictext, $sid, $previous_sid, $next_sid, $archive);

    // theme sans le système de commentaire en meta-mot !
    if (!function_exists("Caff_pub")) {
        if (file_exists("modules/comments/config/article.conf.php")) {
            include("modules/comments/config/article.conf.php");
            include("modules/comments/http/comments.php");
        }
    }
}

// end Caching page
cacheManagerEnd();

include("themes/default/footer.php");
