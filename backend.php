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

use npds\system\date\date;
use npds\system\news\news;
use npds\system\config\Config;
use npds\system\feed\FeedItem;
use npds\system\feed\FeedImage;
use npds\system\language\language;
use npds\system\language\metalang;
use npds\system\feed\UniversalFeedCreator;

include('boot/bootstrap.php');


function fab_feed($type, $filename, $timeout)
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
    $rss->syndicationURL = $nuke_url . "/backend.php?op=" . $type;

    $image = new FeedImage();
    $image->title = $sitename;
    $image->url = Config::get('npds.backend_image');
    $image->link = $nuke_url;
    $image->description = Config::get('npds.backend_title');
    $image->width = Config::get('npds.backend_width');
    $image->height = Config::get('npds.backend_height');
    $rss->image = $image;

    $storyhome = Config::get('npds.storyhome');

    $xtab = news::news_aff('index', "WHERE ihome='0' AND archive='0'", $storyhome, '');
    $story_limit = 0;
    
    while (($story_limit < $storyhome) and ($story_limit < sizeof($xtab))) {
        list($sid, $catid, $aid, $title, $time, $hometext, $bodytext, $comments, $counter, $topic, $informant, $notes) = $xtab[$story_limit];
        $story_limit++;
        $item = new FeedItem();
        $item->title = language::preview_local_langue(Config::get('backend_language'), str_replace('&quot;', '\"', $title));
        $item->link = $nuke_url . "/article.php?sid=$sid";
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
settype($op, 'string');

$op = strtoupper($op);
switch ($op) {
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
