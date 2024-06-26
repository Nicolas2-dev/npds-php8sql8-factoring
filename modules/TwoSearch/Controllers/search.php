<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* NPDS Copyright (c) 2002-2022 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\support\date\date;
use npds\support\news\news;
use npds\support\cache\cache;
use npds\support\security\hack;
use npds\support\language\language;
use npds\system\cache\SuperCacheEmpty;

use npds\system\support\facades\Cache as SuperCache;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

$offset = 25;
$limit_full_search = 250;

if (!isset($min)) {
    $min = 0;
}

if (!isset($max)) {
    $max = $min + $offset;
}

if (!isset($member)) {
    $member = '';
}

if (!isset($query)) {
    $query_title = '';
    $query_body = '';
    $query = $query_body;
    $limit = " LIMIT 0, $limit_full_search";
} else {
    $query_title = hack::removeHack(stripslashes(urldecode($query))); // electrobug
    $query_body = hack::removeHack(stripslashes(htmlentities(urldecode($query), ENT_NOQUOTES, 'utf-8'))); // electrobug
    $query = $query_body;
    $limit = '';
}

include("themes/default/header.php");

$topic = (isset($topic) ? $topic : '');

if ($topic > 0) {

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT topicimage, topictext FROM " . $NPDS_Prefix . "topics WHERE topicid='$topic'");
    list($topicimage, $topictext) = sql_fetch_row($result);
} else {
    $topictext = __d('two_search', 'Tous les sujets');
    $topicimage = "all-topics.gif";
}

settype($type, 'string');

if ($type == 'users') {
    echo '<h2 class="mb-3">' . __d('two_search', 'Rechercher dans la base des utilisateurs') . '</h2><hr />';
} elseif ($type == 'sections') {
    echo '<h2 class="mb-3">' . __d('two_search', 'Rechercher dans les rubriques') . '</h2><hr />';
} elseif ($type == 'reviews') {
    echo '<h2 class="mb-3">' . __d('two_search', 'Rechercher dans les critiques') . '</h2><hr />';
} elseif ($type == 'archive') {
    echo '<h2 class="mb-3">' . __d('two_search', 'Rechercher dans') . ' <span class="text-lowercase">' . __d('two_search', 'Archives') . '</span></h2><hr />';
} else {
    echo '<h2 class="mb-3">' . __d('two_search', 'Rechercher dans') . ' ' . language::aff_langue($topictext) . '</h2><hr />';
}

echo '<form action="'. site_url('search.php') .'" method="get">';
    
// if (($type == 'users') OR ($type == 'sections') OR ($type == 'reviews')) {
//     echo "<img src=\"".$tipath."all-topics.gif\" align=\"left\" border=\"0\" alt=\"\" />";
// } else {
//     if ((($topicimage) or ($topicimage!="")) and (file_exists("$tipath$topicimage"))) {
//         echo "<img src=\"$tipath$topicimage\" align=\"right\" border=\"0\" alt=\"".language::aff_langue($topictext)."\" />";
//     }
// }

echo '
        <div class="mb-3">
            <input class="form-control" type="text" name="query" value="' . $query . '" />
        </div>';

// = DB::table('')->select()->where('', )->orderBy('')->get();

$toplist = sql_query("SELECT topicid, topictext FROM " . $NPDS_Prefix . "topics ORDER BY topictext");

echo '
    <div class="mb-3">
        <select class="form-select" name="topic">
            <option value="">' . __d('two_search', 'Tous les sujets') . '</option>';

$sel = '';
while (list($topicid, $topics) = sql_fetch_row($toplist)) {
    if ($topicid == $topic) { 
        $sel = 'selected="selected" ';
    }

    echo '<option ' . $sel . ' value="' . $topicid . '">' . substr_replace(language::aff_langue($topics), '...', 25, -1) . '</option>';
    $sel = '';
}

echo '
        </select>
    </div>
    <div class="mb-3">
        <select class="form-select" name="category">
            <option value="0">' . __d('two_search', 'Articles') . '</option>';

// = DB::table('')->select()->where('', )->orderBy('')->get();

$catlist = sql_query("SELECT catid, title FROM " . $NPDS_Prefix . "stories_cat ORDER BY title");

settype($category, "integer");

$sel = '';
while (list($catid, $title) = sql_fetch_row($catlist)) {
    if ($catid == $category) {
        $sel = 'selected="selected" ';
    }

    echo '<option ' . $sel . ' value="' . $catid . '">' . language::aff_langue($title) . '</option>';
    $sel = '';
}

echo '
        </select>
    </div>';

// = DB::table('')->select()->where('', )->orderBy('')->get();

$thing = sql_query("SELECT aid FROM " . $NPDS_Prefix . "authors ORDER BY aid");

echo '
    <div class="mb-3">
        <select class="form-select" name="author">
            <option value="">' . __d('two_search', 'Tous les auteurs') . '</option>';

settype($author, 'string');

$sel = '';
while (list($authors_aid) = sql_fetch_row($thing)) {
    if ($authors_aid == $author) {
        $sel = 'selected="selected" ';
    }

    echo '<option ' . $sel . ' value="' . $authors_aid . '">' . $authors_aid . '</option>';
    $sel = '';
}

echo '
        </select>
    </div>';

settype($days, 'integer');

$sel1 = '';
$sel2 = '';
$sel3 = '';
$sel4 = '';
$sel5 = '';
$sel6 = '';

if ($days == '0') {
    $sel1 = 'selected="selected"';
} elseif ($days == "7") {
    $sel2 = 'selected="selected"';
} elseif ($days == "14") {
    $sel3 = 'selected="selected"';
} elseif ($days == "30") {
    $sel4 = 'selected="selected"';
} elseif ($days == "60") {
    $sel5 = 'selected="selected"';
} elseif ($days == "90") {
    $sel6 = 'selected="selected"';
}

echo '
        <div class="mb-3">
            <select class="form-select" name="days">
                <option ' . $sel1 . ' value="0">' . __d('two_search', 'Tous') . '</option>
                <option ' . $sel2 . ' value="7">1 ' . __d('two_search', 'semaine') . '</option>
                <option ' . $sel3 . ' value="14">2 ' . __d('two_search', 'semaines') . '</option>
                <option ' . $sel4 . ' value="30">1 ' . __d('two_search', 'mois') . '</option>
                <option ' . $sel5 . ' value="60">2 ' . __d('two_search', 'mois') . '</option>
                <option ' . $sel6 . ' value="90">3 ' . __d('two_search', 'mois') . '</option>
            </select>
        </div>';

if (($type == 'stories') or ($type == '')) {
    $sel1 = 'checked="checked"';
} elseif ($type == 'sections') {
    $sel3 = 'checked="checked"';
} elseif ($type == 'users') {
    $sel4 = 'checked="checked"';
}  elseif ($type == 'reviews') {
    $sel5 = 'checked="checked"';
} elseif ($type == 'archive') {
    $sel6 = 'checked="checked"';
}

echo '
        <div class="mb-3">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="sto" name="type" value="stories" ' . $sel1 . ' />
                <label class="form-check-label" for="sto">' . __d('two_search', 'Articles') . '</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="arc" name="type" value="archive" ' . $sel6 . ' />
                <label class="form-check-label" for="arc">' . __d('two_search', 'Archives') . '</label>
            </div>
        </div>
        <div class="mb-3">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="sec" name="type" value="sections" ' . $sel3 . ' />
                <label class="form-check-label" for="sec">' . __d('two_search', 'Rubriques') . '</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="use" name="type" value="users" ' . $sel4 . ' />
                <label class="form-check-label" for="use">' . __d('two_search', 'Utilisateurs') . '</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="rev" name="type" value="reviews" ' . $sel5 . ' />
                <label class="form-check-label" for="rev">' . __d('two_search', 'Critiques') . '</label>
            </div>
        </div>
        <div class="mb-3">
            <input class="btn btn-primary" type="submit" value="' . __d('two_search', 'Recherche') . '" />
        </div>
    </form>';

settype($min, 'integer');
settype($offset, 'integer');

if ($type == "stories" or $type == "archive" or !$type) {
    if ($category > 0){ 
        $categ = "AND catid='$category' ";
    } elseif ($category == 0) {
        $categ = '';
    }

    if ($type == 'stories' or !$type) {

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $q = "SELECT s.sid, s.aid, s.title, s.time, a.url, s.topic, s.informant, s.ihome FROM " . $NPDS_Prefix . "stories s, " . $NPDS_Prefix . "authors a WHERE s.archive='0' AND s.aid=a.aid $categ";
    } else  {

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $q = "SELECT s.sid, s.aid, s.title, s.time, a.url, s.topic, s.informant, s.ihome FROM " . $NPDS_Prefix . "stories s, " . $NPDS_Prefix . "authors a WHERE s.archive='1' AND s.aid=a.aid $categ";
    }
    
    if (isset($query)) {
        $q .= "AND (s.title LIKE '%$query_title%' OR s.hometext LIKE '%$query_body%' OR s.bodytext LIKE '%$query_body%' OR s.notes LIKE '%$query_body%') ";
    }

    // Membre OU Auteur
    if ($member != '') {
        $q .= "AND s.informant='$member' ";
    } else {
        if ($author != '') {
            $q .= "AND s.aid=' $author' ";
        }
    }

    if ($topic != '') {
        $q .= "AND s.topic='$topic' ";
    }

    if ($days != '' && $days != 0) {
        $q .= "AND TO_DAYS(NOW()) - TO_DAYS(time) <= '$days' ";
    }

    $q .= " ORDER BY s.time DESC" . $limit;
    $t = $topic;
    $x = 0;

    if ($SuperCache) {
        $cache_clef = "[objet]==> $q";
        $cache_obj = SuperCache::getInstance();
        $cache_obj->setTimingObjet($cache_clef, 3600);
        $tab_sid = $cache_obj->startCachingObjet($cache_clef);

        if ($tab_sid != '') {
            $x = count($tab_sid);
        }
    } else {
        $cache_obj = new SuperCacheEmpty();
    }

    if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!$SuperCache)) {
        $result = sql_query($q);

        if ($result) {
            while (list($sid, $aid, $title, $time, $url, $topic, $informant, $ihome) = sql_fetch_row($result)) {
                if (news::ctrl_aff($ihome, 0)) {
                    $tab_sid[$x]['sid'] = $sid;
                    $tab_sid[$x]['aid'] = $aid;
                    $tab_sid[$x]['title'] = $title;
                    $tab_sid[$x]['time'] = $time;
                    $tab_sid[$x]['url'] = $url;
                    $tab_sid[$x]['topic'] = $topic;
                    $tab_sid[$x]['informant'] = $informant;
                    $x++;
                }
            }
        }
    }

    if ($SuperCache) {
        $cache_obj->endCachingObjet($cache_clef, $tab_sid);
    }

    echo '
        <table id ="search_result" data-toggle="table" data-striped="true" data-mobile-responsive="true" data-icons-prefix="fa" data-icons="icons">
            <thead>
                <tr>
                <th data-sortable="true">' . __d('two_search', 'Résultats') . '</th>
                </tr>
            </thead>
            <tbody>';

    if ($x < $offset) {
        $increment = $x;
    }

    if (($min + $offset) <= $x) {
        $increment = $offset;
    }

    if (($x - $min) < $offset) {
        $increment = ($x - $min);
    }

    for ($i = $min; $i < ($increment + $min); $i++) {
        $furl = site_url('article.php?sid=' . $tab_sid[$i]['sid']);

        if ($type == 'archive') {
            $furl .= '&amp;archive=1';
        }

        $datetime = date::formatTimestamp($tab_sid[$i]['time']);
        echo '
                <tr>
                <td><span>[' . ($i + 1) . ']</span>&nbsp;' . __d('two_search', 'Contribution de') . ' <a href="'. site_url('user.php?op=userinfo&amp;uname=' . $tab_sid[$i]['informant']) .'">' . $tab_sid[$i]['informant'] . '</a> :<br /><strong><a href="' . $furl . '">' . language::aff_langue($tab_sid[$i]['title']) . '</a></strong><br /><span>' . __d('two_search', 'Posté par ') . '<a href="' . $tab_sid[$i]['url'] . '" >' . $tab_sid[$i]['aid'] . '</a></span> ' . __d('two_search', 'le') . ' ' . $datetime . '</td>
                </tr>';
    }

    echo '
            </tbody>
        </table>';

    if ($x == 0) {
        echo '
            <div class="alert alert-danger lead" role="alert">
                <i class="fa fa-exclamation-triangle fa-lg me-2"></i>' . __d('two_search', 'Aucune correspondance à votre recherche n\'a été trouvée') . ' !
            </div>';
    }

    $prev = ($min - $offset);
    echo '<br /><p align="left">(' . __d('two_search', 'Total') . ' : ' . $x . ')&nbsp;&nbsp;';
    
    if ($prev >= 0) {
        echo '<a href="search.php?author=' . $author . '&amp;topic=' . $t . '&amp;min=' . $prev . '&amp;query=' . $query . '&amp;type=' . $type . '&amp;category=' . $category . '&amp;member=' . $member . '&amp;days=' . $days . '">';
        echo $offset . ' ' . __d('two_search', 'réponses précédentes') . '</a>';
    }

    if ($min + $increment < $x) {
        if ($prev >= 0) {
            echo "&nbsp;|&nbsp;";
        }

        echo "<a href=\"". site_url('search.php?author='. $author .'&amp;topic='. $t .'&amp;min='. $max .'&amp;query='. $query .'&amp;type='. $type .'amp;category='. $category .'&amp;member='. $member .'&amp;days='. $days) ."\">";
        echo __d('two_search', 'réponses suivantes') . "</a>";
    }

    echo '</p>';

    // reviews
} elseif ($type == 'reviews') {

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT id, title, text, reviewer FROM " . $NPDS_Prefix . "reviews WHERE (title LIKE '%$query_title%' OR text LIKE '%$query_body%') ORDER BY date DESC LIMIT $min,$offset");
    
    if ($result) {
        $nrows  = sql_num_rows($result);
    }
    
    $x = 0;
    if ($nrows > 0) {
        echo '
        <table id ="search_result" data-toggle="table" data-striped="true" data-icons-prefix="fa" data-icons="icons">
            <thead>
                <tr>
                <th data-sortable="true">' . __d('two_search', 'Résultats') . '</th>
                </tr>
            </thead>
            <tbody>';

        while (list($id, $title, $text, $reviewer) = sql_fetch_row($result)) {
            $furl = site_url('reviews.php?op=showcontent&amp;id='. $id);
            echo '
                <tr>
                <td><a href="' . $furl . '">' . $title . '</a> ' . __d('two_search', 'par') . ' <i class="fa fa-user text-muted"></i>&nbsp;' . $reviewer . '</td>
                </tr>';
            $x++;
        }
        echo '
            </tbody>
        </table>';
    } else {
        echo '<div class="alert alert-danger lead">' . __d('two_search', 'Aucune correspondance à votre recherche n\'a été trouvée') . '</div>';
    }

    $prev = $min - $offset;

    echo '
        <p align="left">
            <ul class="pagination pagination-sm">
                <li class="page-item disabled"><a class="page-link" href="#">' . $nrows . '</a></li>';

    if ($prev >= 0) {
        echo '<li class="page-item"><a class="page-link" href="'. site_url('search.php?author=' . $author . '&amp;topic=' . $t . '&amp;min=' . $prev . '&amp;query=' . $query . '&amp;type=' . $type) .'" >' . $offset . ' ' . __d('two_search', 'réponses précédentes') . '</a></li>';
    }

    if ($x >= ($offset - 1)) {
        echo '<li class="page-item"><a class="page-link" href="'. site_url('search.php?author=' . $author . '&amp;topic=' . $t . '&amp;min=' . $max . '&amp;query=' . $query . '&amp;type=' . $type) .'" >' . __d('two_search', 'réponses suivantes') . '</a></li>';
    }

    echo '
            </ul>
        </p>';
    // sections
} elseif ($type == 'sections') {
    $t = '';

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT artid, secid, title, content FROM " . $NPDS_Prefix . "seccont WHERE (title LIKE '%$query_title%' OR content LIKE '%$query_body%') ORDER BY artid DESC LIMIT $min,$offset");
    if ($result) {
        $nrows  = sql_num_rows($result);
    }

    $x = 0;
    if ($nrows > 0) {
        echo '
        <table id ="search_result" data-toggle="table" data-striped="true" data-icons-prefix="fa" data-icons="icons">
            <thead>
                <tr>
                <th data-sortable="true">' . __d('two_search', 'Résultats') . '</th>
                </tr>
            </thead>
            <tbody>';

        while (list($artid, $secid, $title, $content) = sql_fetch_row($result)) {

            // = DB::table('')->select()->where('', )->orderBy('')->get();

            $rowQ2 = cache::Q_Select("SELECT secname, rubid FROM " . $NPDS_Prefix . "sections WHERE secid='$secid'", 3600);
            $row2 = $rowQ2[0];

            // = DB::table('')->select()->where('', )->orderBy('')->get();

            $rowQ3 = cache::Q_Select("SELECT rubname FROM " . $NPDS_Prefix . "rubriques WHERE rubid='" . $row2['rubid'] . "'", 3600);
            $row3 = $rowQ3[0];

            if ($row3['rubname'] != 'Divers' and $row3['rubname'] != 'Presse-papiers') {
                $surl = site_url('sections.php?op=listarticles&amp;secid='. $secid);
                $furl = site_url('sections.php?op=viewarticle&amp;artid='. $artid);
                echo '
                <tr>
                <td><a href="' . $furl . '">' . language::aff_langue($title) . '</a> ' . __d('two_search', 'dans la sous-rubrique') . ' <a href="' . $surl . '">' . language::aff_langue($row2['secname']) . '</a></td>
                </tr>';
                $x++;
            }
        }

        echo '
            </tbody>
        </table>';

        if ($x == 0) {
            echo '<div class="alert alert-danger lead">' . __d('two_search', 'Aucune correspondance à votre recherche n\'a été trouvée') . '</div>';
        }
    } else {
        echo '<div class="alert alert-danger lead">' . __d('two_search', 'Aucune correspondance à votre recherche n\'a été trouvée') . '</div>';
    }

    $prev = $min - $offset;

    echo '
        <p align="left">
            <ul class="pagination pagination-sm">
                <li class="page-item disabled"><a class="page-link" href="#">' . $nrows . '</a></li>';
                
    if ($prev >= 0) {
        echo '<li class="page-item"><a class="page-link" href="'. site_url('search.php?author=' . $author . '&amp;topic=' . $t . '&amp;min=' . $prev . '&amp;query=' . $query . '&amp;type=' . $type) .'">' . $offset . ' ' . __d('two_search', 'réponses précédentes') . '</a></li>';
    }

    if ($x >= ($offset - 1)) {
        echo '<li class="page-item"><a class="page-link" href="'. site_url('search.php?author=' . $author . '&amp;topic=' . $t . '&amp;min=' . $max . '&amp;query=' . $query . '&amp;type=' . $type) .'">' . __d('two_search', 'réponses suivantes') . '</a></li>';
    }

    echo '
            </ul>
        </p>';

    // users
} elseif ($type == 'users') {
    if (($member_list and $user) or $admin) {

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $result = sql_query("SELECT uname, name FROM " . $NPDS_Prefix . "users WHERE (uname LIKE '%$query_title%' OR name LIKE '%$query_title%' OR bio LIKE '%$query_title%') ORDER BY uname ASC LIMIT $min,$offset");
        
        if ($result) {
            $nrows  = sql_num_rows($result);
        }

        $x = 0;
        if ($nrows > 0) {
            echo '
            <table id ="search_result" data-toggle="table" data-striped="true" data-icons-prefix="fa" data-icons="icons">
                <thead>
                    <tr>
                    <th data-sortable="true">' . __d('two_search', 'Résultats') . '</th>
                    </tr>
                </thead>
                <tbody>';

            while (list($uname, $name) = sql_fetch_row($result)) {
                $furl = site_url('user.php?op=userinfo&amp;uname='. $uname);
                
                if ($name == '') {
                    $name = __d('two_search', 'Aucun nom n\'a été entré');
                }

                echo '
                <tr>
                    <td><a href="' . $furl . '"><i class="fa fa-user text-muted me-2"></i>' . $uname . '</a> (' . $name . ')</td>
                </tr>';
                $x++;
            }

            echo '
                <tbody>
            </table>';
        } else {
            echo '<div class="alert alert-danger lead" role="alert">' . __d('two_search', 'Aucune correspondance à votre recherche n\'a été trouvée') . '</div>';
        }

        $prev = $min - $offset;

        echo '
        <p align="left">
            <ul class="pagination pagination-sm">
                <li class="page-item disabled"><a class="page-link" href="#">' . $nrows . '</a></li>';

        if ($prev >= 0){
            echo '<li class="page-item"><a class="page-link" href="'. site_url('search.php?author=' . $author . '&amp;topic=' . $t . '&amp;min=' . $prev . '&amp;query=' . $query . '&amp;type=' . $type) .'">' . $offset . ' ' . __d('two_search', 'réponses précédentes') . '</a></li>';
        }

        if ($x >= ($offset - 1)){
            echo '<li class="page-item"><a class="page-link" href="'. site_url('search.php?author=' . $author . '&amp;topic=' . $t . '&amp;min=' . $max . '&amp;query=' . $query . '&amp;type=' . $type) .'" >' . __d('two_search', 'réponses suivantes') . '</a></li>';
        }

        echo '
            </ul>
        </p>';
    }
}

include("themes/default/footer.php");
