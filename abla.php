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

use npds\system\assets\css;
use npds\system\forum\forum;
use npds\system\routing\url;
use npds\system\support\str;
use npds\system\support\stats;
use npds\system\support\facades\DB;

if (!stristr($_SERVER['PHP_SELF'], 'admin.php')) {
    include("admin/die.php");
}

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

include("auth.php");

$f_meta_nom = 'abla';
$f_titre = translate("Tableau de bord");

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

global $admin;
if ($admin) {
    include("themes/default/header.php");

    GraphicAdmin(manuel('abla'));
    adminhead($f_meta_nom, $f_titre);

    list($membres, $totala, $totalb, $totalc, $totald, $totalz) = stats::req_stat();

    include("storage/logs/abla.log");

    $timex = time() - $xdate;

    if ($timex >= 86400) {
        $timex = round($timex / 86400) . ' ' . translate("Jour(s)");
    } elseif ($timex >= 3600) {
        $timex = round($timex / 3600) . ' ' . translate("Heure(s)");
    } elseif ($timex >= 60) {
        $timex = round($timex / 60) . ' ' . translate("Minute(s)");
    } else {
        $timex = $timex . ' ' . translate("Seconde(s)");
    }

    echo '
    <hr />
    <p class="lead mb-3">' . translate("Statistiques générales") . ' - ' . translate("Dernières stats") . ' : ' . $timex . ' </p>
    <table class="mb-2" data-toggle="table" data-classes="table mb-2">
        <thead class="collapse thead-default">
            <tr>
                <th class="n-t-col-xs-9"></th>
                <th class="text-end"></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>' . translate("Nb. pages vues") . ' : </td>
                <td>' . str::wrh($totalz) . ' (';

    if ($totalz > $xtotalz) {
        echo '<span class="text-success">+';
    } elseif ($totalz < $xtotalz) {
        echo '<span class="text-danger">';
    } else {
        echo '<span>';
    }

    echo str::wrh($totalz - $xtotalz) . '</span>)</td>
            </tr>
            <tr>
                <td>' . translate("Nb. de membres") . ' : </td>
                <td>' . str::wrh($membres) . ' (';

    if ($membres > $xmembres) {
        echo '<span class="text-success">+';
    } elseif ($membres < $xmembres) {
        echo '<span class="text-danger">';
    } else {
        echo '<span>';
    }

    echo str::wrh($membres - $xmembres) . '</span>)</td>
            </tr>
            <tr>
                <td>' . translate("Nb. d'articles") . ' : </td>
                <td>' . str::wrh($totala) . ' (';

    if ($totala > $xtotala) {
        echo '<span class="text-success">+';
    } elseif ($totala < $xtotala) {
        echo '<span class="text-danger">';
    } else {
        echo '<span>';
    }

    echo str::wrh($totala - $xtotala) . '</span>)</td>
            </tr>
            <tr>
                <td>' . translate("Nb. de forums") . ' : </td>
                <td>' . str::wrh($totalc) . ' (';

    if ($totalc > $xtotalc) {
        echo '<span class="text-success">+';
    } elseif ($totalc < $xtotalc) {
        echo '<span class="text-danger">';
    } else {
        echo '<span>';
    }

    echo str::wrh($totalc - $xtotalc) . '</span>)</td>
            </tr>
            <tr>
                <td>' . translate("Nb. de sujets") . ' : </td>
                <td>' . str::wrh($totald) . ' (';

    if ($totald > $xtotald) {
        echo '<span class="text-success">+';
    } elseif ($totald < $xtotald) {
        echo '<span class="text-danger">';
    } else {
        echo '<span>';
    }

    echo str::wrh($totald - $xtotald) . '</span>)</td>
            </tr>
            <tr>
                <td>' . translate("Nb. de critiques") . ' : </td>
                <td>' . str::wrh($totalb) . ' (';

    if ($totalb > $xtotalb) {
        echo '<span class="text-success">+';
    } elseif ($totalb < $xtotalb) {
        echo '<span class="text-danger">';
    } else {
        echo '<span>';
    }

    //LNL Email in outside table
    if ($count = DB::table('lnl_outside_users')->select('email')->count()) {
        $totalnl = $count;
    } else {
        $totalnl = 0;
    }

    echo str::wrh($totalb - $xtotalb) . '</span>)</td>
            </tr>
            <tr>
                <td>' . translate("Nb abonnés à lettre infos") . ' : </td>
                <td>' . str::wrh($totalnl) . ' (';

    if ($totalnl > $xtotalnl) {
        echo '<span class="text-success">+';
    } elseif ($totalnl < $xtotalnl) {
        echo '<span class="text-danger">';
    }  else {
        echo '<span>';
    }

    echo str::wrh($totalnl - $xtotalnl) . '</span>)</td>
            </tr>';

    $xfile = "<?php\n";
    $xfile .= "\$xdate = " . time() . ";\n";
    $xfile .= "\$xtotalz = $totalz;\n";
    $xfile .= "\$xmembres = $membres;\n";
    $xfile .= "\$xtotala = $totala;\n";
    $xfile .= "\$xtotalc = $totalc;\n";
    $xfile .= "\$xtotald = $totald;\n";
    $xfile .= "\$xtotalb = $totalb;\n";
    $xfile .= "\$xtotalnl = $totalnl;\n";

    echo '
        </tbody>
    </table>
    <p class="lead my-3">' . translate("Statistiques des chargements") . '</p>
    <table data-toggle="table" data-classes="table">
        <thead class=" thead-default">
            <tr>
                <th class="n-t-col-xs-9"></th>
                <th class="text-end"></th>
            </tr>
        </thead>
        <tbody>';

    $num_dow = 0;    
    foreach (DB::table('downloads')
                ->select('dcounter', 'dfilename')
                ->get() as $download) 
    {  
        echo '
            <tr>
                <td><span class="text-danger">';

        if (array_key_exists($num_dow, $xdownload)) {
            echo $xdownload[$num_dow][1];
        }

        echo '</span> -/- ' . $download['dfilename'] . '</td>
                <td><span class="text-danger">';

        if (array_key_exists($num_dow, $xdownload)) {
            echo $xdownload[$num_dow][2];
        }

        echo '</span> -/- ' . $download['dcounter'] . '</td>
            </tr>';

        $xfile .= "\$xdownload[$num_dow][1] = \"". $download['dfilename'] ."\";\n";
        $xfile .= "\$xdownload[$num_dow][2] = \"". $download['dcounter'] ."\";\n";

        $num_dow++;         
    }

    echo '
        </tbody>
    </table>
    <p class="lead my-3">Forums</p>
    <table class="table table-bordered table-sm" data-classes="table">
        <thead class="">
            <tr>
                <th>' . translate("Forum") . '</th>
                <th class="n-t-col-xs-2 text-center">' . translate("Sujets") . '</th>
                <th class="n-t-col-xs-2 text-center">' . translate("Contributions") . '</th>
                <th class="n-t-col-xs-3 text-end">' . translate("Dernières contributions") . '</th>
            </tr>
        </thead>';

    $num_for = 0;
    foreach (DB::table('catagories')
        ->select('cat_id', 'cat_title')
        ->orderBy('cat_id')
        ->get() as $categ) {    

        $forums = DB::table('forums')
            ->select('forums.forum_id', 'forums.forum_name', 'forums.forum_desc', 'forums.forum_access', 'forums.forum_moderator', 'forums.cat_id', 'forums.forum_type', 'forums.forum_pass', 'forums.arbre', 'forums.attachement', 'forums.forum_index', 'users.uname')
            ->join('users', 'forums.forum_moderator', "=", 'users.uid')
            ->where('forums.cat_id', '=', $categ['cat_id'])
            ->orderBy('forums.forum_index, forums.forum_id')
            ->get();

        if (!$forums) {
            forum::forumerror('0022');
        }

        if ($forums) {
            echo '
            <tbody>
                <tr>
                <td class="table-active" colspan="4">' . stripslashes($categ['cat_title']) . '</td>
                </tr>';
            
            foreach ($forums as $forum) {
                $num_for++;
                $last_post = forum::get_last_post($forum['forum_id'], 'forum', 'infos', true);
                
                echo '<tr>';

                $total_topics = forum::get_total_topics($forum['forum_id']);
                $name = stripslashes($forum['forum_name']);
                $xfile .= "\$xforum[$num_for][1] = \"$name\";\n";
                $xfile .= "\$xforum[$num_for][2] = $total_topics;\n";
                $desc = stripslashes($forum['forum_desc']);

                echo '<td>
                    <a tabindex="0" role="button" data-bs-trigger="focus" data-bs-toggle="popover" data-bs-placement="right" data-bs-content="' . $desc . '">
                        <i class="far fa-lg fa-file-alt me-2"></i>
                    </a>
                    <a href="' . site_url('viewforum.php?forum=' . $forum['forum_id']) .'" ><span class="text-danger">';
                
                if (array_key_exists($num_for, $xforum)) {
                    echo $xforum[$num_for][1];
                }

                echo '</span> -/- ' . $name . ' </a></td>
                <td class="text-center"><span class="text-danger">';

                if (array_key_exists($num_for, $xforum)) {
                    echo $xforum[$num_for][2];
                }

                echo '</span> -/- ' . $total_topics . '</td>';

                $total_posts = forum::get_total_posts($forum['forum_id'], "", "forum", false);
                $xfile .= "\$xforum[$num_for][3] = $total_posts;\n";

                echo '<td class="text-center"><span class="text-danger">';

                if (array_key_exists($num_for, $xforum)) {
                    echo $xforum[$num_for][3];
                }

                echo '</span> -/- ' . $total_posts . '</td>
                <td class="text-end small">' . $last_post . '</td>';
            }
        }
    }

    echo '
            </tr>
        </tbody>
    </table>';

    $file = fopen("storage/logs/abla.log", "w");
    $xfile .= "?>";
    
    fwrite($file, $xfile);
    fclose($file);

    css::adminfoot('', '', '', '');
} else {
    url::redirect_url("index.php");
}
