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

use npds\support\news\news;
use npds\support\cache\cache;
use npds\support\utility\code;
use npds\system\config\Config;
use npds\support\language\language;
use npds\support\metalang\metalang;
use npds\system\support\facades\DB;
use npds\system\support\facades\Request;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

if (!Request::query('sid') && !Request::query('tid')) {
    header("Location: index.php");
}

if (!$archive = Request::query('archive')) {
    $archive = 0;
}

$xtab = (!$archive) 
    ? news::news_aff2("libre", 
        DB::table('stories')
            ->select('sid', 'catid', 'ihome', 'time')
            ->where('sid', $sid)
            ->get(), 1, 1) 
    : news::news_aff2("archive", 
        DB::table('stories')
            ->select('sid', 'catid', 'ihome')
            ->where('sid', $sid)
            ->get(), 1, 1);

if (!empty($xtab)) {
    $sid        = $xtab[0]['sid']; 
    $catid      = $xtab[0]['catid']; 
    $aid        = $xtab[0]['aid']; 
    $title      = $xtab[0]['title']; 
    $time       = $xtab[0]['time']; 
    $hometext   = $xtab[0]['hometext']; 
    $bodytext   = $xtab[0]['bodytext']; 
    $comments   = $xtab[0]['comments']; 
    $counter    = $xtab[0]['counter']; 
    $topic      = $xtab[0]['topic']; 
    $informant  = $xtab[0]['informant']; 
    $notes      = $xtab[0]['notes'];
} else {
    header('Location: ' . site_url('index.php'));
}

DB::table('stories')->where('sid', $sid)->update(array(
    'counter'   => DB::raw('counter+1'),
));

include("themes/default/header.php");

// start Caching page
if (cache::cacheManagerStart2()) {
    
    $title      = language::aff_langue(stripslashes($title));
    $hometext   = code::aff_code(language::aff_langue(stripslashes($hometext)));
    $bodytext   = code::aff_code(language::aff_langue(stripslashes($bodytext)));
    $notes      = code::aff_code(language::aff_langue(stripslashes($notes)));

    if ($notes != '') {
        $notes = '<div class="note blockquote">' . __d('two_news', 'Note') . ' : ' . $notes . '</div>';
    }

    $bodytext = $bodytext == '' 
        ? metalang::meta_lang($hometext . '<br />' . $notes) 
        : metalang::meta_lang($hometext . '<br />' . $bodytext . '<br />' . $notes);

    if ($informant == '') {
        $informant = Config::get('npds.anonymous');
    }

    list($topicname, $topicimage, $topictext) = news::getTopics($sid);

    if ($catid != 0) {

        $stories_cat = DB::table('stories_cat')
                        ->select('title')
                        ->where('catid', $catid)
                        ->first();

        $title = '<a href="'. site_url('index.php?op=newindex&amp;catid=' . $catid) . '">
                <span>' . language::aff_langue($stories_cat['title']) . '</span>
            </a> : ' . $title;
            
    }

    $boxtitle = __d('two_news', 'Liens relatifs');
    $boxstuff = '<ul>';

    $related = DB::table('related')
                ->select('name', 'url')
                ->where('tid', $topic)
                ->get();
    
    foreach ($related as $val) {
        $boxstuff .= '<li>
            <a href="' . $val['url'] . '" target="_blank">
                <span>' . $val['name'] . '</span>
            </a>
        </li>';
    }

    $boxstuff .= '
        </ul>
        <ul>
            <li>
                <a href="'. site_url('search.php?topic=' . $topic) . '" >
                    ' . __d('two_news', 'En savoir plus à propos de') . ' : 
                </a>
                <span class="h5">
                    <span class="badge bg-secondary" title="' . $topicname . '
                        <hr />
                        ' . language::aff_langue($topictext) . '" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="right">
                        ' . language::aff_langue($topicname) . '
                    </span>
                </span>
            </li>
            <li>
                <a href="'. site_url('search.php?member=' . $informant) . '" >
                    ' . __d('two_news', 'Article de') . ' ' . $informant . '
                </a> 
                ' . userpopover($informant, 36, '') . '
            </li>
        </ul>
        <div>
            <span class="fw-semibold">
                ' . __d('two_news', 'L\'article le plus lu à propos de') . ' : 
            </span>
            <span class="h5">
                <span class="badge bg-secondary" title="' . $topicname . '
                    <hr />
                    ' . language::aff_langue($topictext) . '" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="right">
                    ' . language::aff_langue($topicname) . '
                </span>
            </span>
        </div>';

    $xtab = news::news_aff2("big_story", 
        DB::table('stories')
            ->select('sid', 'catid', 'ihome', 'counter')
            ->where('topic', $topic)
            ->orderBy('counter', 'desc')
            ->get(), 1, 1
    );

    $boxstuff .= '
        <ul>
            <li>
                <a href="'. site_url('article.php?sid=' . $xtab[0]['sid']) . '" >
                    ' . language::aff_langue($xtab[0]['title']) . '
                </a>
            </li>
        </ul>
        <div>
            <span class="fw-semibold">' . __d('two_news', 'Les dernières nouvelles à propos de') . ' : </span>
            <span class="h5">
                <span class="badge bg-secondary" title="' . $topicname . '
                    <hr />' . language::aff_langue($topictext) . '" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="right">
                    ' . language::aff_langue($topicname) . '
                </span>
            </span>
        </div>';

    $xtab = (!$archive) 
        ?  news::news_aff2("libre", 
                DB::table('stories')
                ->select('sid', 'catid', 'ihome', 'time')
                ->where('topic', $topic)
                ->where('archive', 0)
                ->orderBy('sid', 'desc')
                ->limit(5)
                ->offset(0)
                ->get(), 0, 5
            ) 
        : news::news_aff2("archive", 
            DB::table('stories')
                ->select('sid', 'catid', 'ihome')
                ->where('topic', $topic)
                ->where('archive', 1)
                ->orderBy('sid', 'asc')            
                ->limit(5)
                ->offset(0)
                ->get(), 0, 5
        );

    $story_limit = 0;
    $boxstuff .= '<ul>';

    while (($story_limit < 5) and ($story_limit < sizeof($xtab))) {

        $sid1   = $xtab[$story_limit]['sid']; 
        $catid1 = $xtab[$story_limit]['catid']; 
        $aid1   = $xtab[$story_limit]['aid']; 
        $title1 = $xtab[$story_limit]['title'];

        $title1 = language::aff_langue(addslashes($title1));
        
        $boxstuff .= '<li>
            <a href="'. site_url('article.php?sid=' . $sid1 . '&amp;archive=' . $archive) . '" >
                ' . language::aff_langue(stripslashes($title1)) . '
            </a>
        </li>';
    
        $story_limit++;
    }

    $boxstuff .= '
        </ul>
        <p align="center">
            <a href="'. site_url('print.php?sid=' . $sid . '&amp;archive=' . $archive) .'" >
                <i class="fa fa-print fa-2x me-3" title="' . __d('two_news', 'Page spéciale pour impression') . '" data-bs-toggle="tooltip"></i>
            </a>
            <a href="'. site_url('friend.php?op=FriendSend&amp;sid=' . $sid . '&amp;archive=' . $archive) .'">
                <i class="fa fa-2x fa-at" title="' . __d('two_news', 'Envoyer cet article à un ami') . '" data-bs-toggle="tooltip"></i>
            </a>
        </p>';

    if (!$archive) {
        $previous_tab = news::news_aff2("libre", 
            DB::table('stories')
                ->select('sid', 'catid', 'ihome', 'time')
                ->where('sid', '<', $sid)
                ->orderBy('sid', 'desc')
                ->get(), 0, 1
        );

        $next_tab = news::news_aff2("libre", 
                DB::table('stories')
                ->select('sid', 'catid', 'ihome', 'time')
                ->where('sid', '>', $sid)
                ->orderBy('sid', 'asc')
                ->get(), 0, 1
        );
    } else {
        $previous_tab = news::news_aff2("archive", 
            DB::table('stories')
                ->select('sid', 'catid', 'ihome')
                ->where('sid', '<', $sid)
                ->orderBy('sid', 'desc')
                ->get(),0, 1
        );

        $next_tab = news::news_aff2("archive", 
            DB::table('stories')
                ->select('sid', 'catid', 'ihome')
                ->where('sid', '>', $sid)
                ->orderBy('sid', 'asc')
                ->get(),0, 1
        );
    }

    if (array_key_exists(0, $previous_tab)) {
        $previous_sid = $previous_tab[0]['sid'];
    } else {
        $previous_sid = 0;
    }

    if (array_key_exists(0, $next_tab)) {
        $next_sid = $next_tab[0]['sid'];
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
cache::cacheManagerEnd();

include("themes/default/footer.php");
