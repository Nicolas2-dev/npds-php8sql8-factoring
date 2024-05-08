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

use npds\support\auth\users;
use npds\support\forum\forum;
use npds\support\theme\theme;
use npds\system\config\Config;
use npds\support\language\language;
use npds\system\support\facades\DB;
use npds\system\support\facades\Request;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

include("auth.php");

$user = users::getUser();

if (!$user) {
    Header('Location: '. site_url('user.php'));
} else {
    include("themes/default/header.php");

    $userdata = forum::get_userdata(users::cookieUser(1));

    $resultT = DB::table('priv_msgs')
                ->distinct()
                ->select('dossier')
                ->where('to_userid', $userdata['uid'])
                ->where('dossier', '!=', '...')
                ->where('type_msg', 0)
                ->orderBy('dossier')
                ->get();

    users::member_menu($userdata['mns'], $userdata['uname']);

    echo '
    <div class="card card-body mt-3">
        <h2><a href="'. site_url('replypmsg.php?send=1') .'" title="' . __d('two_forum', 'Ecrire un nouveau message privé') . '" data-bs-toggle="tooltip" ><i class="fa fa-edit me-2"></i></a><span class="d-none d-xl-inline">&nbsp;' . __d('two_forum', 'Message personnel') . " - </span>" . __d('two_forum', 'Boîte de réception') . '</h2>
        <form id="viewpmsg-dossier" action="'. site_url('viewpmsg.php') .'" method="post">
            <div class="mb-3">
                <label class="sr-only" for="dossier" >' . __d('two_forum', 'Sujet') . '</label>
                <select class="form-select" name="dossier" onchange="document.forms[\'viewpmsg-dossier\'].submit()">
                <option value="...">' . __d('two_forum', 'Choisir un dossier/sujet') . '...</option>';

    $tempo["..."] = 0;

    $dossier = Request::input('dossier');

    foreach($resultT as $priv_msgs) {

        if (addslashes($priv_msgs['dossier']) == $dossier) { 
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }

        echo '<option ' . $sel . ' value="' . $priv_msgs['dossier'] . '">' . $priv_msgs['dossier'] . '</option>';
        
        $tempo[$priv_msgs['dossier']] = 0;
    }

    $sel = (isset($dossier) and $dossier == 'All') ? 'selected="selected"' : '';

    echo '
                <option ' . $sel . ' value="All">' . __d('two_forum', 'Tous les sujets') . '</option>
                </select>
            </div>
        </form>';

    $query = DB::table('priv_msgs')
                ->select('*')
                ->where('to_userid', $userdata['uid'])
                ->where('type_msg', 0);

    if (!$dossier) {
        $query->where('dossier', '...');
    } elseif ($dossier == "All") {
        // no where
    } else {
        $query->where('dossier', $dossier);
    }

    $resultID = $query->orderBy('msg_id', 'desc')->get();

    $total_messages = Request::query('total_messages');

    if (!$total_messages = count($resultID)) {
        echo '
        <div class="alert alert-danger lead">
            ' . __d('two_forum', 'Vous n\'avez aucun message.') . '
        </div>';
        $display = 0;
    } else {
        $display = 1;

        echo '
        <form name="prvmsg" method="get" action="'. site_url('replypmsg.php') .'" onkeypress="return event.keyCode != 13;">
            <table class="mb-3" data-toggle="table" data-show-toggle="true" data-mobile-responsive="true" data-buttons-class="outline-secondary" data-icons="icons" data-icons-prefix="fa" data-search="true" data-search-align="left"
                data-buttons-align="left"
                data-toolbar-align="left">
                <thead class="thead-default">
                <tr>
                    <th class="n-t-col-xs-1" data-halign="center" data-align="center">
                        <div class="form-check">
                            <input class="form-check-input is-invalid" id="allbox" name="allbox" onclick="CheckAll();" type="checkbox" value="" />
                            <label class="form-check-label" for="allbox">&nbsp;&nbsp;</label>
                        </div>
                    </th>
                    <th class="n-t-col-xs-1" data-align="center" ><i class="fas fa-long-arrow-alt-down"></i></th>';

        if (Config::get('npds.smilies')) {
            echo '<th class="n-t-col-xs-1" data-align="center" >&nbsp;</th>';
        }

        echo '
                    <th data-halign="center" data-sortable="true" data-align="left">' . __d('two_forum', 'de ') . '</th>
                    <th data-halign="center" data-sortable="true" >' . __d('two_forum', 'Sujet') . '</th>
                    <th data-halign="center" data-sortable="true" data-align="right">' . __d('two_forum', 'Date') . '</th>
                </tr>
                </thead>
                <tbody>';

        $count = 0;

        foreach($resultID as $myrow) {

            $myrow['subject'] = strip_tags($myrow['subject']);
            
            $posterdata = forum::get_userdata_from_id($myrow['from_userid']);

            if ($dossier == "All") {
                $myrow['dossier'] = "All";
            }

            if (!array_key_exists($myrow['dossier'], $tempo)) {
                $tempo[$myrow['dossier']] = 0;
            }

            echo '
                <tr>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input is-invalid" type="checkbox" onclick="CheckCheckAll();" id="msg_id' . $count . '" name="msg_id[' . $count . ']" value="' . $myrow['msg_id'] . '" />
                            <label class="form-check-label" for="msg_id' . $count . '">&nbsp;&nbsp;</label>
                        </div>
                    </td>';

            if ($myrow['read_msg'] == "1") {
                echo '<td><a href="'. site_url('readpmsg.php?start=' . $tempo[$myrow['dossier']] . '&amp;total_messages=' . $total_messages . '&amp;dossier=' . urlencode($myrow['dossier'])) .'" title="' . __d('two_forum', 'Lu') . '" data-bs-toggle="tooltip"><i class="far fa-envelope-open fa-lg "></i></a></td>';
            } else {
                echo '<td><a href="'. site_url('readpmsg.php?start=' . $tempo[$myrow['dossier']] . '&amp;total_messages=' . $total_messages . '&amp;dossier=' . urlencode($myrow['dossier'])) .'" title="' . __d('two_forum', 'Non lu') . '" data-bs-toggle="tooltip"><i class="fa fa-envelope fa-lg faa-shake animated"></i></a></td>';
            }

            if (Config::get('npds.smilies')) {
                if ($myrow['msg_image'] != '') {

                    if ($ibid = theme::theme_image("forum/subject/" . $myrow['msg_image'])) { 
                        $imgtmp = $ibid;
                    } else {
                        $imgtmp = "assets/images/forum/subject/" . $myrow['msg_image'];
                    }

                    echo '
                    <td><img class="n-smil" src="' . $imgtmp . '" alt="" /></td>';
                } else {
                    echo '
                    <td></td>';
                }
            }

            echo '<td>' . userpopover($posterdata['uname'], 40, 2);

            echo ($posterdata['uid'] <> 1) ? $posterdata['uname'] : Config::get('npds.sitename');

            echo '</td>
                    <td>' . language::aff_langue($myrow['subject']) . '</td>
                    <td class="small">' . $myrow['msg_time'] . '</td>
                </tr>';

            $tempo[$myrow['dossier']] = $tempo[$myrow['dossier']] + 1;
            $count++;
        }

        echo '
                </tbody>
            </table>';

        if ($display) {
            echo '
            <div class="mb-3 mt-3">
                <button class="btn btn-outline-danger btn-sm" type="submit" name="delete_messages" value="delete_messages" >' . __d('two_forum', 'Effacer') . '</button>
                <input type="hidden" name="total_messages" value="' . $total_messages . '" />
                <input type="hidden" name="type" value="inbox" />
            </div>';
        }

        echo '
        </form>';
    }

    echo '</div>';

    $resultID = DB::table('priv_msgs')
                            ->select('*')
                            ->where('from_userid', $userdata['uid'])
                            ->where('type_msg', 1)
                            ->orderBy('msg_id', 'desc')
                            ->get();

    $total_messages = count($resultID);

    echo '
        <div class="card card-body mt-3">
        <h2><a href="'. site_url('replypmsg.php?send=1') .'" title="' . __d('two_forum', 'Ecrire un nouveau message privé') . '" data-bs-toggle="tooltip" ><i class="fa fa-edit me-2"></i></a><span class="d-none d-xl-inline">&nbsp;' . __d('two_forum', 'Message personnel') . " - </span>" . __d('two_forum', 'Boîte d\'émission') . '<span class="badge bg-secondary float-end">' . $total_messages . '</span></h2>
        <form id="" name="prvmsgB" method="get" action="'. site_url('replypmsg.php') .'">
            <table class="mb-3" data-toggle="table" data-show-toggle="true" data-mobile-responsive="true" data-buttons-class="outline-secondary" data-icons="icons" data-icons-prefix="fa">
                <thead class="thead-default">
                <tr>
                    <th class="n-t-col-xs-1" data-halign="center" data-align="center" >
                        <div class="form-check">
                            <input class="form-check-input is-invalid" id="allbox_b" name="allbox" onclick="CheckAllB();" type="checkbox" value="Check All" />
                            <label class="form-check-label" for="allbox_b">&nbsp;</label>
                        </div>
                    </th>';

    if (Config::get('npds.smilies')) {
        echo '<th class="n-t-col-xs-1" data-align="center" >&nbsp;</th>';
    }

    echo '
                    <th data-halign="center" data-sortable="true" data-align="center">' . __d('two_forum', 'Envoyé à') . '</th>
                    <th data-halign="center" data-sortable="true" align="center">' . __d('two_forum', 'Sujet') . '</th>
                    <th data-halign="center" data-align="right" data-sortable="true" align="center">' . __d('two_forum', 'Date') . '</th>
                </tr>
            </thead>
            <tbody>';

    if (!$total_messages) {
        $display = 0;
        echo '
                <tr>
                <td colspan="6" align="center">' . __d('two_forum', 'Vous n\'avez aucun message.') . '</td>
                </tr>';
    } else {
        $display = 1;
    }

    $count = 0;

    foreach($resultID as $myrow) {
        echo '
                <tr>
                <td>
                    <div class="form-check">
                        <input class="form-check-input is-invalid" type="checkbox" onclick="CheckCheckAllB();" id="msg_idB' . $count . '" name="msg_id[' . $count . ']" value="' . $myrow['msg_id'] . '" />
                        <label class="form-check-label text-danger" for="msg_idB' . $count . '">&nbsp;</label>
                    </div>
                </td>';

        if (Config::get('npds.smilies')) {
            if ($myrow['msg_image'] != '') {

                $imgtmp = theme::theme_image_row('forum/subject/'. $myrow['msg_image'], 'assets/images/forum/subject/'. $myrow['msg_image']);

                echo '<td width="5%" align="center"><img class="n-smil" src="' . $imgtmp . '" alt="Image du topic" /></td>';
            } else {
                echo '<td width="5%" align="center">&nbsp;</td>';
            }
        }

        $myrow['subject'] = strip_tags($myrow['subject']);

        $posterdata = forum::get_userdata_from_id($myrow['to_userid']);

        echo '
                <td><a href="'. site_url('readpmsg.php?start=' . $count . '&amp;total_messages=' . $total_messages . '&amp;type=outbox') .'" >' . $posterdata['uname'] . '</a></td>
                <td>' . language::aff_langue($myrow['subject']) . '</td>
                <td>' . $myrow['msg_time'] . '</td>
                </tr>';
        $count++;
    }

    echo '
            </tbody>
        </table>';

    if ($display) {
        echo '
            <div class="mb-3 mt-3">
                <button class="btn btn-outline-danger btn-sm" type="submit" name="delete_messages" value="delete_messages" >' . __d('two_forum', 'Effacer') . '</button>
                <input type="hidden" name="total_messages" value="' . $total_messages . '" />
                <input type="hidden" name="type" value="outbox" />
            </div>';
    }

    echo '
        </form>
        </div>';

    ?>
    <script type="text/javascript">
        //<![CDATA[
            function CheckAll() {
                for (var i = 0; i < document.prvmsg.elements.length; i++) {
                    var e = document.prvmsg.elements[i];
                    if ((e.name != 'allbox') && (e.type == 'checkbox'))
                        e.checked = document.prvmsg.allbox.checked;
                }
            }

            function CheckCheckAll() {
                var TotalBoxes = 0,
                    TotalOn = 0;
                for (var i = 0; i < document.prvmsg.elements.length; i++) {
                    var e = document.prvmsg.elements[i];
                    if ((e.name != 'allbox') && (e.type == 'checkbox')) {
                        TotalBoxes++;
                        if (e.checked) {
                            TotalOn++;
                        }
                    }
                }
                if (TotalBoxes == TotalOn) {
                    document.prvmsg.allbox.checked = true;
                } else {
                    document.prvmsg.allbox.checked = false;
                }
            }

            function CheckAllB() {
                for (var i = 0; i < document.prvmsgB.elements.length; i++) {
                    var e = document.prvmsgB.elements[i];
                    if ((e.name != 'allbox') && (e.type == 'checkbox'))
                        e.checked = document.prvmsgB.allbox.checked;
                }
            }

            function CheckCheckAllB() {
                var TotalBoxes = 0,
                    TotalOn = 0;
                for (var i = 0; i < document.prvmsgB.elements.length; i++) {
                    var e = document.prvmsgB.elements[i];
                    if ((e.name != 'allbox') && (e.type == 'checkbox')) {
                        TotalBoxes++;
                        if (e.checked) {
                            TotalOn++;
                        }
                    }
                }
                if (TotalBoxes == TotalOn) {
                    document.prvmsgB.allbox.checked = true;
                } else {
                    document.prvmsgB.allbox.checked = false;
                }
            }
        //]]>
    </script>

    <?php
    include('themes/default/footer.php');
}
