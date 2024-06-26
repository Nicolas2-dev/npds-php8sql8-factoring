<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* This version name NPDS Copyright (c) 2001-2020 by Philippe Brunier   */
/*                                                                      */
/* New Links.php Module with SFROM extentions                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
if (!stristr($_SERVER['PHP_SELF'], 'modules.php')) die();

function NewLinksDate($selectdate)
{
    global $ModPath, $ModStart, $links_DB, $admin;
    $dateDB = (date("d-M-Y", $selectdate));
    include("themes/default/header.php");
    mainheader('nl');
    $filen = "modules/$ModPath/http/links.ban_01.php";
    if (file_exists($filen)) {
        include($filen);
    }
    $newlinkDB = Date("Y-m-d", $selectdate);

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT lid FROM " . $links_DB . "links_links WHERE date LIKE '%$newlinkDB%'");
    $totallinks = sql_num_rows($result);

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT lid, url, title, description, date, hits, topicid_card, cid, sid FROM " . $links_DB . "links_links WHERE date LIKE '%$newlinkDB%' ORDER BY title ASC");
    $link_fiche_detail = '';
    include_once("modules/$ModPath/http/links-view.php");
    include("themes/default/footer.php");
}

function NewLinks($newlinkshowdays)
{
    global $ModPath, $ModStart, $links_DB;
    include("themes/default/header.php");
    mainheader('nl');
    $counter = 0;
    $allweeklinks = 0;
    while ($counter <= 7 - 1) {
        $newlinkdayRaw = (time() - (86400 * $counter));
        $newlinkday = date("d-M-Y", $newlinkdayRaw);
        $newlinkView = date("F d, Y", $newlinkdayRaw);
        $newlinkDB = Date("Y-m-d", $newlinkdayRaw);

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $result = sql_query("SELECT * FROM " . $links_DB . "links_links WHERE date LIKE '%$newlinkDB%'");
        $totallinks = sql_num_rows($result);
        $counter++;
        $allweeklinks = $allweeklinks + $totallinks;
    }

    $counter = 0;
    $allmonthlinks = 0;
    while ($counter <= 30 - 1) {
        $newlinkdayRaw = (time() - (86400 * $counter));
        $newlinkDB = Date("Y-m-d", $newlinkdayRaw);

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $result = sql_query("SELECT * FROM " . $links_DB . "links_links WHERE date LIKE '%$newlinkDB%'");
        $totallinks = sql_num_rows($result);
        $allmonthlinks = $allmonthlinks + $totallinks;
        $counter++;
    }
    echo '
    
    <div class="card card-body mb-3">
    <h3>' . __d('two_links', 'Nouveaux liens') . '</h3>
    ' . __d('two_links', 'Total des nouveaux liens pour la semaine dernière') . ' : ' . $allweeklinks . ' -/- ' . __d('two_links', 'Pour les 30 derniers jours') . ' : ' . $allmonthlinks;

    echo "<br />\n";

    echo "<blockquote>" . __d('two_links', 'Montrer :') . " [<a href=\"modules.php?ModStart=$ModStart&ModPath=$ModPath&op=NewLinks&newlinkshowdays=7\" class=\"noir\">" . __d('two_links', 'semaine') . "</a>, <a href=\"modules.php?ModStart=$ModStart&ModPath=$ModPath&op=NewLinks&newlinkshowdays=14\" class=\"noir\">2 " . __d('two_links', 'semaines') . "</a>, <a href=\"modules.php?ModStart=$ModStart&ModPath=$ModPath&op=NewLinks&newlinkshowdays=30\" class=\"noir\">30 " . __d('two_links', 'jours') . "</a>]</<blockquote>";
    $counter = 0;
    $allweeklinks = 0;
    echo '
        <blockquote>
        <ul>';
    while ($counter <= $newlinkshowdays - 1) {
        $newlinkdayRaw = (time() - (86400 * $counter));
        $newlinkday = date("d-M-Y", $newlinkdayRaw);
        $newlinkView = date(str_replace("%", "", __d('two_links', 'linksdatestring')), $newlinkdayRaw);
        $newlinkDB = Date("Y-m-d", $newlinkdayRaw);

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $result = sql_query("SELECT * FROM " . $links_DB . "links_links WHERE date LIKE '%$newlinkDB%'");
        $totallinks = sql_num_rows($result);
        $counter++;
        $allweeklinks = $allweeklinks + $totallinks;
        if ($totallinks > 0)
            echo "<li><a href=\"modules.php?ModStart=$ModStart&ModPath=$ModPath&op=NewLinksDate&selectdate=$newlinkdayRaw\">$newlinkView</a>&nbsp( $totallinks )</li>";
    }
    echo '
        </blockquote>
        </ul>
        </div>';
    SearchForm();
    $counter = 0;
    $allmonthlinks = 0;
    include("themes/default/footer.php");
}
