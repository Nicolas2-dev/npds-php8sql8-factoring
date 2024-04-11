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

use npds\system\auth\users;
use npds\system\cache\cache;
use npds\system\forum\forum;
use npds\system\language\language;
use npds\system\support\facades\DB;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

/**
 * [mapsections description]
 *
 * @return  void
 */
function mapsections(): void
{
    $tmp = '';

    $result = DB::table('rubriques')
            ->select('rubid', 'rubname')
            ->where('enligne', 1)
            ->where('rubname', '<>', 'Divers')
            ->where('rubname', '<>', 'Presse-papiers')
            ->orderBy('ordre')
            ->get();

    if ($result > 0) {
        foreach ($result as $rubrique) {   
            if ($rubrique['rubname'] != '') {
                $tmp .= '<li>' . language::aff_langue($rubrique['rubname']);
            }
            
            $result2 = DB::table('sections')
                    ->select('secid', 'secname', 'image', 'userlevel', 'intro')
                    ->where('rubid', $rubrique['rubid'])
                    ->where('userlevel', 0)
                    ->orWhere('userlevel', '')
                    ->orderBy('ordre')
                    ->get();

            if ($result2 > 0) {
                foreach ($result2 as $section) {    
                    if (users::autorisation($section['userlevel'])) {
                        $tmp .= '<ul><li>' . language::aff_langue($section['secname']);

                        foreach (DB::table('seccont')
                                    ->select('artid', 'title')
                                    ->where('secid', $section['secid'])
                                    ->get() as $seccont) 
                        {
                            $tmp .= "<ul>
                            <li><a href=\"". site_url('sections.php?op=viewarticle&amp;artid='. $seccont['artid']) ."\">" . language::aff_langue($seccont['title']) . '</a></li></ul>';
                        }

                        $tmp .= '</li>
                        </ul>';
                    }
                }
            }
            $tmp .= '</li>';
        }
    }

    if ($tmp != '')
        echo '
            <h3>
            <a class="" data-bs-toggle="collapse" href="#collapseSections" aria-expanded="false" aria-controls="collapseSections">
            <i class="toggle-icon fa fa-caret-down"></i></a>&nbsp;' . translate("Rubriques") . '
            <span class="badge bg-secondary float-end">' . count($result) . '</span>
            </h3>
        <div class="collapse" id="collapseSections">
            <div class="card card-body">
            <ul class="list-unstyled">' . $tmp . '</ul>
            </div>
        </div>
        <hr />';
}

/**
 * [mapforum description]
 *
 * @return  void
 */
function mapforum() :void 
{
    $tmp = '';
    $tmp .= forum::RecentForumPosts_fab('', 10, 0, false, 50, false, '<li>', false);

    if ($tmp != '') {
        echo '
        <h3>
            <a data-bs-toggle="collapse" href="#collapseForums" aria-expanded="false" aria-controls="collapseForums"><i class="toggle-icon fa fa-caret-down"></i></a>&nbsp;' . translate("Forums") . '
        </h3>
        <div class="collapse" id="collapseForums">
            <div class="card card-body">
                ' . $tmp . '
            </div>
        </div>
        <hr />';
    }
}

/**
 * [maptopics description]
 *
 * @return  void
 */
function maptopics(): void
{
    $lis_top = '';

    foreach ($count = DB::table('topics')
                        ->select('topicid', 'topictext')
                        ->orderBy('topicname')
                        ->get() as $topic) 
    {
        $nb_article = DB::table('stories')
                        ->select('sid')
                        ->where('topic', $topic['topicid'])
                        ->count();

        $lis_top .= '
        <li><a href="'. site_url('search.php?query=&amp;topic=' . $topic['topicid']) .'">' . language::aff_langue($topic['topictext']) . '</a>&nbsp;<span class="">(' . $nb_article . ')</span></li>';
    }

    if ($lis_top != '') {
        echo '
        <h3>
            <a class="" data-bs-toggle="collapse" href="#collapseTopics" aria-expanded="false" aria-controls="collapseTopics"><i class="toggle-icon fa fa-caret-down"></i></a>&nbsp;' . translate("Sujets") . '
            <span class="badge bg-secondary float-end">' . count($count) . '</span>
        </h3>
        <div class="collapse" id="collapseTopics">
            <div class="card card-body">
                <ul class="list-unstyled">' . $lis_top . '</ul>
            </div>
        </div>
        <hr />';
    }
}

/**
 * [mapcategories description]
 *
 * @return  void
 */
function mapcategories(): void
{
    $lis_cat = '';

    foreach($count = DB::table('stories_cat')
                        ->select('catid', 'title')
                        ->orderBy('title')
                        ->get() as $storie) 
    {
        $nb_article = DB::table('stories')
                        ->select('sid')
                        ->where('catid', $storie['catid'])
                        ->count();

        $lis_cat .= '<li><a href="'. site_url('index.php?op=newindex&amp;catid=' . $storie['catid']) .'">' . language::aff_langue($storie['title']) . '</a> <span class="float-end badge bg-secondary"> ' . $nb_article . ' </span></li>' . "\n";
    }

    if ($lis_cat != '') {
        echo '
        <h3>
            <a class="" data-bs-toggle="collapse" href="#collapseCategories" aria-expanded="false" aria-controls="collapseCategories"><i class="toggle-icon fa fa-caret-down"></i></a>&nbsp;' . translate("Catégories") . '
            <span class="badge bg-secondary float-end">' . count($count) . '</span>
        </h3>
        <div class="collapse" id="collapseCategories">
            <div class="card card-body">
                <ul class="list-unstyled">' . $lis_cat . '</ul>
            </div>
        </div>
        <hr />';
    }
}

/**
 * [mapfaq description]
 *
 * @return  void
 */
function mapfaq(): void
{
    $lis_faq = '';

    foreach ($count = DB::table('faqcategories')
                        ->select('id', 'categories')
                        ->orderBy('id', 'asc')
                        ->get() as $categ) 
    { 
        $catname = language::aff_langue($categ['categories']);
        $lis_faq .= "<li><a href=\"". site_url('faq.php?id_cat='. $categ['id'] .'&amp;myfaq=yes&amp;categories=' . urlencode($catname)) ."\">" . $catname . "</a></li>\n";
    }

    if ($lis_faq != '')
        echo '
        <h3>
            <a class="" data-bs-toggle="collapse" href="#collapseFaq" aria-expanded="false" aria-controls="collapseFaq"><i class="toggle-icon fa fa-caret-down"></i></a>&nbsp;' . translate("FAQ - Questions fréquentes") . '
            <span class="badge bg-secondary float-end">' . count($count) . '</span>
        </h3>
        <div class="collapse" id="collapseFaq">
            <div class="card card-body">
                <ul class="">' . $lis_faq . '</ul>
            </div>
        </div>
        <hr />';
}

include("themes/default/header.php");

// start Caching page
if (cache::cacheManagerStart2()) {

    echo '<h2>' . translate("Plan du site") . '</h2>
    <hr />';

    mapsections();
    mapforum();
    maptopics();
    mapcategories();
    mapfaq();

    echo '<br />';

    if (file_exists("themes/default/view/include/user.inc")) {
        include("themes/default/view/include/user.inc");
    }
}

// end Caching page
cache::cacheManagerEnd();

include("themes/default/footer.php");
