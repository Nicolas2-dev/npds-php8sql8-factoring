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
/* 2003 by snipe / vote unique, implémentation de la table appli_log    */
/************************************************************************/
declare(strict_types=1);

use npds\support\auth\users;
use npds\support\str;
use npds\system\config\Config;
use npds\support\polls;
use npds\support\language\language;
use npds\system\support\facades\DB;
use npds\system\support\facades\Request;


if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php'); 
}

/**
 * [pollCollector description]
 *
 * @param   int     $pollID     [$pollID description]
 * @param   int     $voteID     [$voteID description]
 * @param   string  $forwarder  [$forwarder description]
 *
 * @return  void
 */
function pollCollector(int $pollID, int $voteID, string $forwarder): void
{
    if ($voteID) { 
        $al_id = Config::get('pollbooth.al_id'); 
        $al_nom = Config::get('pollbooth.al_nom');

        $voteValid = "1";

        $res_time = DB::table('poll_desc')
                ->select('timeStamp')
                ->where('pollID', $pollID)
                ->first();

        $cookieName = 'poll' . DB::getTablePrefix() . $res_time['timeStamp'];

        if (Request::cookie($cookieName) == "1") {
            $voteValid = "0";
        } else {
            setcookie($cookieName, 1, time() + 86400);
        }

        $query = DB::table('appli_log')
                    ->select('al_id')
                    ->where('al_id', $al_id)
                    ->where('al_subid', $pollID)
                    ->where('al_ip', $ip = Request::getIp());
           
        $user        = users::getUser();

        if ($user) {
            $cookie_user = users::cookieUser(0);
            $query->orWhere('al_uid', $cookie_user);
        } else {
            $cookie_user = "1";
        }

        if (Config::get('npds.setCookies') == "1") {
            if (Config::get('npds.dns_verif')) {
                $hostname = $query->orWhere('al_hostname', @gethostbyaddr($ip));
            } else {
                $hostname = "";
            }

            if ($voteValid = $query->first()) {
                $voteValid = "0";
            }
        }

        if ($voteValid == "1") {
            if (Config::get('npds.dns_verif')) {
                $hostname = @gethostbyaddr($ip);
            } else {
                $hostname = "";
            }

            DB::table('appli_log')->insert(array(
                'al_id'         => $al_id,
                'al_name'       => $al_nom,
                'al_subid'      => $pollID,
                'al_date'       => 'now()',
                'al_uid'        => $cookie_user,
                'al_data'       => $voteID,
                'al_ip'         => $ip,
                'al_hostname'   => $hostname,
            ));
            
            DB::table('poll_data')->where('pollID', $pollID)->where('voteID', $voteID)->update(array(
                'optionCount'       => DB::raw('optionCount+1'),
            ));
            
            DB::table('poll_desc')->where('pollID', $pollID)->update(array(
                'voters'       => DB::raw('voters+1'),
            ));
        }
    }

    Header("Location: $forwarder");
}

/**
 * [pollList description]
 *
 * @return  void
 */
function pollList(): void
{
    echo '
    <h2 class="mb-3">' . __d('two_polbooth', 'Sondage') . '</h2>
    <hr />
    <div class="row">';

    foreach (DB::table('poll_desc')
            ->select('pollID', 'pollTitle', 'voters')
            ->orderBy('timeStamp')
            ->get() as $object) 
    {
        $id = $object['pollID'];
        $pollTitle = $object['pollTitle']; 

        $sum = DB::table('poll_data')
            ->select(DB::raw('SUM(optionCount) AS count'))
            ->where('pollID', $id)
            ->first();

        echo '
        <div class="col-sm-8">' . language::aff_langue($pollTitle) . '</div>
        <div class="col-sm-4 text-end">(<a href="'. site_url('pollBooth.php?op=results&amp;pollID=' . $id) .'">' . __d('two_polbooth', 'Résultats') . '</a> - ' . $sum['count'] . ' ' . __d('two_polbooth', 'votes') . ')</div>';
    }

    echo '
    </div>';
}

/**
 * [pollResults description]
 *
 * @param   int   $pollID  [$pollID description]
 *
 * @return  void
 */
function pollResults(int $pollID): void
{
    if (!isset($pollID) or empty($pollID)) {
        $pollID = 1;
    }

    $res = DB::table('poll_desc')
            ->select('pollID', 'pollTitle', 'timeStamp')
            ->where('pollID', $pollID)
            ->first();

    echo '<h3 class="my-3">' . $res['pollTitle'] . '</h3>';

    $sum = DB::table('poll_data')
            ->select(DB::raw('SUM(optionCount) AS count'))
            ->where('pollID', $pollID)
            ->first();

    echo '<h4><span class="badge bg-secondary">' . $sum['count'] . '</span>&nbsp;' . __d('two_polbooth', 'Résultats') . '</h4>';

    for ($i = 1; $i <= Config::get('npds.maxOptions'); $i++) {

        $object = DB::table('poll_data')
                ->select('optionText', 'optionCount', 'voteID')
                ->where('pollID', $pollID)
                ->where('voteID', $i)
                ->first();

        if (!is_null($object)) {
            $optionText = $object['optionText'];
            $optionCount = $object['optionCount'];
        } else {
            $optionText = '';
            $optionCount = 0;
        }

        if ($optionText != "") {
            if ($sum) {
                $percent = 100 * $optionCount / $sum;
                $percentInt = (int)$percent;
            } else{
                $percentInt = 0;
            }

            echo '
            <div class="row">
                <div class="col-sm-5 mt-3">' . language::aff_langue($optionText) . '</div>
                <div class="col-sm-7">
                    <span class="badge bg-secondary mb-1">' . str::wrh($optionCount) . '</span>
                        <div class="progress">
                        <span class="progress-bar" role="progressbar" aria-valuenow="' . $percentInt . '%" aria-valuemin="0" aria-valuemax="100" style="width:' . $percentInt . '%;" title="' . $percentInt . '%" data-bs-toggle="tooltip"></span>
                        </div>
                </div>
            </div>';
        }
    }

    echo '<br />';
    echo '<p class="text-center"><b>' . __d('two_polbooth', 'Nombre total de votes: ') . ' ' . $sum['count'] . '</b></p><br />';

    if (Config::get('npds.setCookies') > 0) {
        echo '<p class="text-danger">' . __d('two_polbooth', 'Un seul vote par sondage.') . '</p>';
    }
}

/**
 * Construit le blocs sondages / code du mainfile avec autre présentation
 *
 * @param   int   $pollID     [$pollID description]
 * @param   int   $pollClose  [$pollClose description]
 *
 * @return  void
 */
function pollboxbooth(int $pollID, int $pollClose): void
{
    global $boxTitle, $boxContent, $block_title;

    if (!isset($pollID)) {
        $pollID = 1;
    }

    if (!isset($url)) {
        $url = sprintf(site_url('pollBooth.php?op=results&amp;pollID=%d'), $pollID);
    }

    $boxContent = '
    <form action="'. site_url('pollBooth.php') .'" method="post">
        <input type="hidden" name="pollID" value="' . $pollID . '" />
        <input type="hidden" name="forwarder" value="' . $url . '" />';

    $res_pooldesc = DB::table('poll_desc')
            ->select('pollTitle')
            ->where('pollID', $pollID)
            ->first();

    $boxTitle = $block_title == '' ? __d('two_polbooth', 'Sondage') : $block_title;

    $boxContent .= '<h4>' . language::aff_langue($res_pooldesc['pollTitle']) . '</h4>';

    $result= DB::table('poll_data')
            ->select('pollID', 'optionText', 'optionCount', 'voteID')
            ->where('pollID', $pollID)
            ->where('optionText', '<>', '')
            ->orderBy('voteID')
            ->get();

    $sum = 0;
    $j = 0;

    if (!$pollClose) {
        $boxContent .= '<div class="custom-controls-stacked">';
        
        foreach ($result as $object) {
            $boxContent .= '
                <div class="form-check">
                    <input type="radio" class="form-check-input" id="voteID' . $j . '" name="voteID" value="' . $object['voteID'] . '" />
                    <label class="form-check-label" for="voteID' . $j . '">' . language::aff_langue($object['optionText']) . '</label>
                </div>';
            $sum = $sum + $object['optionCount'];
            $j++;
        }

        $boxContent .= '
                </div>
                <div class="clearfix"></div>';
    } else {
        foreach ($result as $object) {
            $boxContent .= "&nbsp;" . language::aff_langue($object['optionText']) . "<br />\n";
            $sum = $sum + $object['optionCount'];
        }
    }

    if (!$pollClose) {
        $inputvote = '
            <button class="btn btn-primary btn-sm my-2" type="submit" value="' . __d('two_polbooth', 'Voter') . '" title="' . __d('two_polbooth', 'Voter') . '" />' . __d('two_polbooth', 'Voter') . '</button>';
    }

    $boxContent .= '
            <div class="mb-3">' . $inputvote . '</div>
    </form>';

    $boxContent .= '<div><ul><li><a href="'. site_url('pollBooth.php') .'">' . __d('two_polbooth', 'Anciens sondages') . '</a></li>';

    if (Config::get('npds.pollcomm')) {
        if (file_exists('modules/comments/config/pollBoth.conf.php')) {
            include('modules/comments/config/pollBoth.conf.php');
        }

        $numcom = DB::table('posts')
                ->select('*')
                ->where('forum_id', $forum)
                ->where('topic_id', $pollID)
                ->where('post_aff', 1)
                ->count();
        
        $boxContent .= '<li>' . __d('two_polbooth', 'Votes : ') . ' ' . $sum . '</li><li>' . __d('two_polbooth', 'Commentaire(s) : ') . ' ' . $numcom . '</li>';
    } else {
        $boxContent .= '<li>' . __d('two_polbooth', 'Votes : ') . ' ' . $sum . '</li>';
    }

    $boxContent .= '</ul></div>';

    echo '<div class="card card-body">' . $boxContent . '</div>';
}

/**
 * [PollMain_aff description]
 *
 * @return  void
 */
function PollMain_aff(): void 
{
    echo '<p><strong><a href="'. site_url('pollBooth.php') .'">' . __d('two_polbooth', 'Anciens sondages') . '</a></strong></p>';
}


$pollID = Request::query('pollID');

if (!isset($pollID)) {
    include('themes/default/header.php');
    pollList();
}

$forwarder  = Request::input('forwarder');
$voteID     = Request::input('voteID');

if (isset($forwarder)) {
    if (isset($voteID)) {
        pollCollector($pollID, $voteID, $forwarder);
    } else {
        Header("Location: $forwarder");
    }

} elseif (Request::input('op') == 'results') {
    list($ibid, $pollClose) = polls::pollSecur($pollID);

    if ($pollID == $ibid) {
        if (Config::get('pollbooth.header') != 1) {
            include('themes/default/header.php');
        }

        echo '<h2>' . __d('two_polbooth', 'Sondage') . '</h2><hr />';
        
        pollResults($pollID);

        if (!$pollClose) {
            $block_title = '<h3>' . __d('two_polbooth', 'Voter') . '</h3>';
            echo $block_title;

            pollboxbooth($pollID, $pollClose);
        } else {
            PollMain_aff();
        }

        if (Config::get('npds.pollcomm')) {
            if (file_exists('modules/comments/config/pollBoth.conf.php')) {
                include('modules/comments/config/pollBoth.conf.php');
                
                if ($pollClose == 99) {
                    Config::set('npds.generale.anonpost', 0);
                }

                include('modules/comments/http/comments.php');
            }
        }
    } else {
        Header("Location: $forwarder");
    }
}

include('themes/default/footer.php');
