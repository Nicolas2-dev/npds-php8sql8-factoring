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

use npds\support\date\date;
use npds\support\news\news;
use npds\support\utility\code;
use npds\system\config\Config;
use npds\support\security\hack;
use npds\support\language\language;
use npds\support\metalang\metalang;
use npds\system\support\facades\DB;
use npds\system\support\facades\Request;


if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

/**
 * [PrintPage description]
 *
 * @param   string  $oper  [$oper description]
 * @param   string  $DB    [$DB description]
 * @param   string         [ description]
 * @param   int     $nl    [$nl description]
 * @param   string         [ description]
 * @param   int     $sid   [$sid description]
 *
 * @return  void
 */
function PrintPage(string $oper, string $DB, string|int $nl, string|int $sid): void
{
    global $datetime;

    $sitename = Config::get('npds.sitename');

    $aff = true;
    if ($oper == 'news') {

        $xtab = news::news_aff2('libre', DB::table('stories')
                ->select('sid', 'catid', 'ihome', 'time')
                ->where('sid', $sid)
                ->get(), 1, 1);

        $sid      = $xtab[0]['sid'];
        $title    = $xtab[0]['title'];
        $time     = $xtab[0]['time'];
        $hometext = $xtab[0]['hometext'];
        $bodytext = $xtab[0]['bodytext'];
        $topic    = $xtab[0]['topic'];
        $notes    = $xtab[0]['notes'];

        if ($topic != '') {
            $rez_topic = DB::table('topics')
                            ->select('topictext')
                            ->where('topicid', $topic)
                            ->first();
        } else {
            $aff = false;
        }
    }

    if ($oper == 'archive') {

        $xtab = news::news_aff2('archive', DB::table('stories')
                    ->select('sid', 'catid', 'ihome')
                    ->where('sid', $sid)
                    ->get(), 1, 1);

        $sid      = $xtab[0]['sid'];
        $title    = $xtab[0]['title'];
        $time     = $xtab[0]['time'];
        $hometext = $xtab[0]['hometext'];
        $bodytext = $xtab[0]['bodytext'];
        $topic    = $xtab[0]['topic'];
        $notes    = $xtab[0]['notes'];

        if ($topic != '') {
            $rez_topic = DB::table('topics')
                            ->select('topictext')
                            ->where('topicid', $topic)
                            ->first();
        } else {
            $aff = false;
        }
    }

    if ($oper == 'links') {
        $DB = hack::removeHack(stripslashes(htmlentities(urldecode($DB), ENT_NOQUOTES, 'utf-8')));
        
        $rez_link = DB::table($DB .'links_links')
                        ->select('url', 'title', 'description', 'date')
                        ->where('lid', $sid)
                        ->first();

        $title       = stripslashes($rez_link['title']);
        $description = stripslashes($rez_link['description']);
        $time        = $rez_link['time'];
    }

    if ($oper == 'static') {
        if (preg_match('#^[a-z0-9_\.-]#i', $sid) 
        and !stristr($sid, ".*://") 
        and !stristr($sid, "..") 
        and !stristr($sid, "../") 
        and !stristr($sid, 'script') 
        and !stristr($sid, "cookie") 
        and !stristr($sid, 'iframe') 
        and  !stristr($sid, 'applet') 
        and !stristr($sid, 'object') 
        and !stristr($sid, 'meta')) {
            
            if (file_exists('storage/static/'. $sid)) {
                ob_start();
                    include('storage/static/'. $sid);
                    $remp = ob_get_contents();
                ob_end_clean();
                
                if ($DB) {
                    $remp = metalang::meta_lang(code::aff_code(language::aff_langue($remp)));
                }
                
                if ($nl) {
                    $remp = nl2br(str_replace(' ', '&nbsp;', htmlentities($remp, ENT_QUOTES, 'utf-8')));
                }

                $title = $sid;
            } else {
                $aff = false;
            }
        } else {
            $remp = '<div class="alert alert-danger">' . __d('two_news', 'Merci d\'entrer l\'information en fonction des spécifications') . '</div>';
            $aff = false;
        }
    }

    if ($aff == true) {

        if (isset($time)) {
            $datetime = date::formatTimestamp($time);
        }

        Config::set('Titlesitename', 'NPDS - ' . __d('two_news', 'Page spéciale pour impression') . ' / ' . $title);
        include("storage/meta/meta.php");

        echo '
            <link rel="stylesheet" href="assets/shared/bootstrap/dist/css/bootstrap.min.css" />
        </head>
        <body>
            <div max-width="640" class="container p-1 n-hyphenate">
                <div>';

        $site_logo = Config::get('npds.site_logo');

        $pos = strpos($site_logo, '/');

        if ($pos) {
            echo '<img class="img-fluid d-block mx-auto" src="' . $site_logo . '" alt="website logo" />';
        } else {
            echo '<img class="img-fluid d-block mx-auto" src="assets/images/' . $site_logo . '" alt="website logo" />';
        }

        echo '<h1 class="d-block text-center my-4">' . language::aff_langue($title) . '</h1>';

        if (($oper == 'news') or ($oper == 'archive')) {
            $hometext = metalang::meta_lang(code::aff_code(language::aff_langue($hometext)));
            $bodytext = metalang::meta_lang(code::aff_code(language::aff_langue($bodytext)));

            echo '
                <span class="float-end text-capitalize" style="font-size: .8rem;"> ' . $datetime . '</span><br />
                <hr />
                <h2 class="mb-3">' . __d('two_news', 'Sujet : ') . ' ' . language::aff_langue($rez_topic['topictext']) . '</h2>
            </div>
            <div>' . $hometext . '<br /><br />';

            if ($bodytext != '') {
                echo $bodytext . '<br /><br />';
            }

            echo metalang::meta_lang(code::aff_code(language::aff_langue($notes)));

            echo '
            </div>';

            if ($oper == 'news') {
                echo '
                <hr />
                <p class="text-center">' . __d('two_news', 'Cet article provient de') . ' ' . $sitename . '<br />
                ' . __d('two_news', 'L\'url pour cet article est : ') . '
                <a href="'. site_url('article.php?sid=' . $sid) .'">'. site_url('article.php?sid=' . $sid) .'</a>
                </p>';
            } else {
                echo '
                <hr />
                <p class="text-center">' . __d('two_news', 'Cet article provient de') . ' ' . $sitename . '<br />
                ' . __d('two_news', 'L}\'url pour cet article est : ') . '
                <a href="'. site_url('article.php?sid=' . $sid . '&amp;archive=1') .'">'. site_url('article.php?sid=' . $sid . '&amp;archive=1') .'</a>
                </p>';
            }
        }

        if ($oper == 'links') {
            echo '<span class="float-end text-capitalize" style="font-size: .8rem;">' . $datetime . '</span><br /><hr />';

            if ($rez_link['url'] != '') {
                echo '<h2 class="mb-3">' . __d('two_news', 'Liens') . ' : ' . $rez_link['url'] . '</h2>';
            }

            echo '
            <div>' . language::aff_langue($description) . '</div>
            <hr />
            <p class="text-center">' . __d('two_news', 'Cet article provient de') . ' ' . $sitename . '<br />
            <a href="'. site_url('') .'">'. site_url('') .'</a></p>';
        }

        if ($oper == 'static') {
            echo '
            <div>
                ' . $remp . '
            </div>
            <hr />
            <p class="text-center">' . __d('two_news', 'Cet article provient de') . ' ' . $sitename . '<br />
            <a href="'. site_url('static.php?op=' . $sid . '&npds=1') .'">'. site_url('static.php?op=' . $sid . '&npds=1') .'</a></p>';
        }

        echo '
            </div>
        </body>
        </html>';
    } else {
        header('location: '. site_url('index.php'));
    }
}

$sid = Request::query('sid');
$lid = Request::query('lid');

if (!empty($sid)) {
    $tab = explode(':', $sid);

    if ($tab[0] == "static") {
        PrintPage("static", Request::query('metalang'), Request::query('nl'), $tab[1]);
    } else {
        $archive = Request::query('archive');
        
        if (!isset($archive)) {
            PrintPage("news", '', '', $sid);
        } else {
            PrintPage("archive", '', '', $sid);
        }
    }
} elseif (!empty($lid)) {
    PrintPage("links", Request::query('DB'), '', $lid);
} else {
    header('location: '. site_url('index.php'));
}
