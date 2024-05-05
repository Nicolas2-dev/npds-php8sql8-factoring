<?php
/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* NPDS Copyright (c) 2002-2020 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\support\date\date;
use npds\support\news\news;
use npds\system\config\Config;
use npds\support\feed\FeedItem;
use npds\support\feed\FeedImage;
use npds\support\language\language;
use npds\support\metalang\metalang;
use npds\system\support\facades\DB;
use npds\system\support\facades\Request;
use npds\support\feed\UniversalFeedCreator;

include('boot/bootstrap.php');

/**
 * [fab_feed description]
 *
 * @param   string  $type      [$type description]
 * @param   string  $filename  [$filename description]
 * @param   int     $timeout   [$timeout description]
 *
 * @return  void
 */
function fab_feed(string $type, string $filename, int $timeout): void
{
    $rss = new UniversalFeedCreator();
    $rss->useCached($type, $filename, $timeout);

    $sitename = Config::get('npds.sitename');
    $nuke_url = Config::get('npds.nuke_url');

    $rss->title = $sitename;
    $rss->description = Config::get('npds.slogan');
    $rss->descriptionTruncSize = 250;
    $rss->descriptionHtmlSyndicated = true;

    $rss->link = $nuke_url;
    $rss->syndicationURL = site_url('backend.php?op=' . $type);

    $image = new FeedImage();
    $image->title = $sitename;
    $image->url = Config::get('npds.backend_image');
    $image->link = $nuke_url;
    $image->description = Config::get('npds.backend_title');
    $image->width = Config::get('npds.backend_width');
    $image->height = Config::get('npds.backend_height');
    $rss->image = $image;

    $storyhome = Config::get('npds.storyhome');

    $xtab = news::news_aff2('index',
        DB::table('stories')
            ->select('sid', 'catid', 'ihome')
            ->where('ihome', 0)
            ->where('archive', 0)
            ->limit($storyhome)
            ->orderBy('sid', 'desc')
            ->get(), 
        $storyhome, 
        '');

    $story_limit = 0;
    
    while (($story_limit < $storyhome) and ($story_limit < sizeof($xtab))) {

        $sid        = $xtab[$story_limit]['sid']; 
        $aid        = $xtab[$story_limit]['aid']; 
        $title      = $xtab[$story_limit]['title']; 
        $time       = $xtab[$story_limit]['time']; 
        $hometext   = $xtab[$story_limit]['hometext']; 

        $story_limit++;
        $item = new FeedItem();
        $item->title = language::preview_local_langue(Config::get('backend_language'), str_replace('&quot;', '\"', $title));
        $item->link = site_url('article.php?sid='. $sid);
        $item->description = metalang::meta_lang(language::preview_local_langue(Config::get('backend_language'), $hometext));
        $item->descriptionHtmlSyndicated = true;
        $item->date = date::convertdateTOtimestamp($time) + ((int) Config::get('npds.gmt') * 3600);
        $item->source = $nuke_url;
        $item->author = $aid;

        $rss->addItem($item);
    }

    echo $rss->saveFeed($type, $filename);
}

// Format : RSS0.91, RSS1.0, RSS2.0, MBOX, OPML, ATOM
switch (Request::query('op')) {
    case 'MBOX':
        fab_feed('MBOX', 'storage/rss/MBOX-feed', 3600);
        break;

    case 'OPML':
        fab_feed('OPML', 'storage/rss/OPML-feed.xml', 3600);
        break;

    case 'ATOM':
        fab_feed('ATOM', 'storage/rss/ATOM-feed.xml', 3600);
        break;

    case 'RSS1.0':
        fab_feed('RSS1.0', 'storage/rss/RSS1.0-feed.xml', 3600);
        break;

    case 'RSS2.0':
        fab_feed('RSS2.0', 'storage/rss/RSS2.0-feed.xml', 3600);
        break;

    case 'RSS0.91':
        fab_feed('RSS0.91', 'storage/rss/RSS0.91-feed.xml', 3600);
        break;

    default:
        fab_feed('RSS1.0', 'storage/rss/RSS1.0-feed.xml', 3600);
        break;
}
