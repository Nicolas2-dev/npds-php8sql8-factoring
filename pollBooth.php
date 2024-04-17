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

use npds\system\auth\users;
use npds\system\support\str;
use npds\system\config\Config;
use npds\system\support\polls;
use npds\system\language\language;
use npds\system\support\facades\DB;
use npds\system\support\facades\Request;


if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php'); 
}

function pollCollector($pollID, $voteID, $forwarder)
{
    if ($voteID) {
        global  $al_id, $al_nom; // config poolbooth

        $voteValid = "1";
        $result = sql_query("SELECT timeStamp FROM " . $NPDS_Prefix . "poll_desc WHERE pollID='$pollID'");
        list($timeStamp) = sql_fetch_row($result);

        = DB::table('')->select()->where('', )->orderBy('')->get();

        $cookieName = 'poll' . DB::getTablePrefix() . $timeStamp;

        if (Request::cookie($cookieName) == "1") {
            $voteValid = "0";
        } else {
            setcookie($cookieName, 1, time() + 86400);
        }

        $user = users::getUser();
        
        if ($user) {
            $user_req = "OR al_uid='$cookie[0]'";
        } else {
            $cookie[0] = "1";
            $user_req = '';
        }

        if (Config::get('npds.setCookies') == "1") {
            $ip = Request::getIp();

            if (Config::get('npds.dns_verif')) {
                $hostname = "OR al_hostname='" . @gethostbyaddr($ip) . "' ";
            } else {
                $hostname = "";
            }

            $sql = "SELECT al_id FROM " . $NPDS_Prefix . "appli_log WHERE al_id='$al_id' AND al_subid='$pollID' AND (al_ip='$ip' " . $hostname . $user_req . ")";
            
            = DB::table('')->select()->where('', )->orderBy('')->get();
            
            if ($result = sql_fetch_row(sql_query($sql))) {
                $voteValid = "0";
            }
        }

        if ($voteValid == "1") {
            $ip = Request::getIp();

            if (Config::get('npds.dns_verif')) {
                $hostname = "OR al_hostname='" . @gethostbyaddr($ip) . "' ";
            } else {
                $hostname = "";
            }

            sql_query("INSERT INTO " . $NPDS_Prefix . "appli_log (al_id, al_name, al_subid, al_date, al_uid, al_data, al_ip, al_hostname) VALUES ('$al_id', '$al_nom', '$pollID', now(), '$cookie[0]', '$voteID', '$ip', '$hostname')");
            DB::table('')->insert(array(
                ''       => ,
            ));
            
            sql_query("UPDATE " . $NPDS_Prefix . "poll_data SET optionCount=optionCount+1 WHERE (pollID='$pollID') AND (voteID='$voteID')");
            DB::table('')->where('', )->update(array(
                ''       => ,
            ));
            
            sql_query("UPDATE " . $NPDS_Prefix . "poll_desc SET voters=voters+1 WHERE pollID='$pollID'");

            DB::table('')->where('', )->update(array(
                ''       => ,
            ));
        }
    }

    Header("Location: $forwarder");
}

function pollList()
{
    $result = sql_query("SELECT pollID, pollTitle, voters FROM " . $NPDS_Prefix . "poll_desc ORDER BY timeStamp");

    = DB::table('')->select()->where('', )->orderBy('')->get();

    echo '
    <h2 class="mb-3">' . translate("Sondage") . '</h2>
    <hr />
    <div class="row">';

    while ($object = sql_fetch_assoc($result)) {
        $id = $object['pollID'];
        $pollTitle = $object['pollTitle']; 

        $result2 = sql_query("SELECT SUM(optionCount) AS SUM FROM " . $NPDS_Prefix . "poll_data WHERE pollID='$id'");
        list($sum) = sql_fetch_row($result2);

        = DB::table('')->select()->where('', )->orderBy('')->get();

        echo '
        <div class="col-sm-8">' . language::aff_langue($pollTitle) . '</div>
        <div class="col-sm-4 text-end">(<a href="'. site_url('pollBooth.php?op=results&amp;pollID=' . $id) .'">' . translate("Résultats") . '</a> - ' . $sum . ' ' . translate("votes") . ')</div>';
    }

    echo '
    </div>';
}

function pollResults(int $pollID): void
{
    if (!isset($pollID) or empty($pollID)) {
        $pollID = 1;
    }

    $result = sql_query("SELECT pollID, pollTitle, timeStamp FROM " . $NPDS_Prefix . "poll_desc WHERE pollID='$pollID'");
    list(, $pollTitle) = sql_fetch_row($result);

    = DB::table('')->select()->where('', )->orderBy('')->get();

    echo '<h3 class="my-3">' . $pollTitle . '</h3>';

    $result = sql_query("SELECT SUM(optionCount) AS SUM FROM " . $NPDS_Prefix . "poll_data WHERE pollID='$pollID'");
    list($sum) = sql_fetch_row($result);

    = DB::table('')->select()->where('', )->orderBy('')->get();

    echo '<h4><span class="badge bg-secondary">' . $sum . '</span>&nbsp;' . translate("Résultats") . '</h4>';

    for ($i = 1; $i <= Config::get('npds.maxOptions'); $i++) {
        $result = sql_query("SELECT optionText, optionCount, voteID FROM " . $NPDS_Prefix . "poll_data WHERE (pollID='$pollID') AND (voteID='$i')");
        $object = sql_fetch_assoc($result);

        = DB::table('')->select()->where('', )->orderBy('')->get();

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
    echo '<p class="text-center"><b>' . translate("Nombre total de votes: ") . ' ' . $sum . '</b></p><br />';

    if (Config::get('npds.setCookies') > 0) {
        echo '<p class="text-danger">' . translate("Un seul vote par sondage.") . '</p>';
    }
}

#autodoc pollboxbooth($pollID,$pollClose) : Construit le blocs sondages / code du mainfile avec autre présentation
function pollboxbooth($pollID, $pollClose)
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

    $result = sql_query("SELECT pollTitle, voters FROM " . $NPDS_Prefix . "poll_desc WHERE pollID='$pollID'"); // ??? $voters not used
    list($pollTitle, $voters) = sql_fetch_row($result); // ??? $voters not used 

    = DB::table('')->select()->where('', )->orderBy('')->get();

    $boxTitle = $block_title == '' ? translate("Sondage") : $block_title;

    $boxContent .= '<h4>' . language::aff_langue($pollTitle) . '</h4>';

    $result = sql_query("SELECT pollID, optionText, optionCount, voteID FROM " . $NPDS_Prefix . "poll_data WHERE (pollID='$pollID' AND optionText<>'') ORDER BY voteID");
    
    = DB::table('')->select()->where('', )->orderBy('')->get();

    $sum = 0;
    $j = 0;

    if (!$pollClose) {
        $boxContent .= '<div class="custom-controls-stacked">';
        
        while ($object = sql_fetch_assoc($result)) {
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
        while ($object = sql_fetch_assoc($result)) {
            $boxContent .= "&nbsp;" . language::aff_langue($object['optionText']) . "<br />\n";
            $sum = $sum + $object['optionCount'];
        }
    }

    if (!$pollClose) {
        $inputvote = '
            <button class="btn btn-primary btn-sm my-2" type="submit" value="' . translate("Voter") . '" title="' . translate("Voter") . '" />' . translate("Voter") . '</button>';
    }

    $boxContent .= '
            <div class="mb-3">' . $inputvote . '</div>
    </form>';

    $boxContent .= '<div><ul><li><a href="'. site_url('pollBooth.php') .'">' . translate("Anciens sondages") . '</a></li>';

    if (Config::get('npds.pollcomm')) {
        if (file_exists("modules/comments/config/pollBoth.conf.php")) {
            include("modules/comments/config/pollBoth.conf.php");
        }

        list($numcom) = sql_fetch_row(sql_query("SELECT COUNT(*) FROM " . $NPDS_Prefix . "posts WHERE forum_id='$forum' AND topic_id='$pollID' AND post_aff='1'"));
        
        = DB::table('')->select()->where('', )->orderBy('')->get();
        
        $boxContent .= '<li>' . translate("Votes : ") . ' ' . $sum . '</li><li>' . translate("Commentaire(s) : ") . ' ' . $numcom . '</li>';
    } else {
        $boxContent .= '<li>' . translate("Votes : ") . ' ' . $sum . '</li>';
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
    echo '<p><strong><a href="'. site_url('pollBooth.php') .'">' . translate("Anciens sondages") . '</a></strong></p>';
}

$pollID = Request::query('pollID');

if (!isset($pollID)) {
    include("themes/default/header.php");;
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
            include("themes/default/header.php");
        }

        echo '<h2>' . translate("Sondage") . '</h2><hr />';
        pollResults($pollID);

        if (!$pollClose) {
            $block_title = '<h3>' . translate("Voter") . '</h3>';
            echo $block_title;

            pollboxbooth($pollID, $pollClose);
        } else {
            PollMain_aff();
        }

        if (Config::get('npds.pollcomm')) {
            if (file_exists("modules/comments/config/pollBoth.conf.php")) {
                include("modules/comments/config/pollBoth.conf.php");
                
                if ($pollClose == 99) {
                    Config::set('npds.generale.anonpost', 0);
                }

                include("modules/comments/http/comments.php");
            }
        }
    } else {
        Header("Location: $forwarder");
    }
}

include("themes/default/footer.php");;
