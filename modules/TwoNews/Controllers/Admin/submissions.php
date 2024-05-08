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
use npds\support\assets\css;
use npds\support\language\language;
use npds\system\support\facades\DB;

if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'submissions';
$f_titre = __d('two_news', 'Article en attente de validation');

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

/**
 * [submissions description]
 *
 * @return  void
 */
function submissions(): void
{
    global $aid, $radminsuper, $f_meta_nom, $f_titre;

    $dummy = 0;

    include("themes/default/header.php");

    GraphicAdmin(manuel('submissions'));
    adminhead($f_meta_nom, $f_titre);

    $queues = DB::table('queue')->select('qid', 'subject', 'timestamp', 'topic', 'uname')->orderBy('timestamp')->get();

    if ($queues == 0) {
        echo '
    <hr />
    <h3>'. __d('two_news', 'Pas de nouveaux Articles postés') .'</h3>';
    } else {
        echo '
    <hr />
    <h3>'. __d('two_news', 'Nouveaux Articles postés') .'<span class="badge bg-danger float-end">'. sql_num_rows($result) .'</span></h3>
    <table id="tad_subm" data-toggle="table" data-striped="true" data-show-toggle="true" data-search="true" data-mobile-responsive="true" data-buttons-class="outline-secondary" data-icons="icons" data-icons-prefix="fa">
        <thead>
            <tr>
                <th data-halign="center"><i class="fa fa-user fa-lg"></i></th>
                <th data-sortable="true" data-sorter="htmlSorter" data-halign="center">'. __d('two_news', 'Sujet') .'</th>
                <th data-sortable="true" data-sorter="htmlSorter" data-halign="center">'. __d('two_news', 'Titre') .'</th>
                <th data-halign="center" data-align="right">'. __d('two_news', 'Date') .'</th>
                <th class="n-t-col-xs-2" data-halign="center" data-align="center">'. __d('two_news', 'Fonctions') .'</th>
            </tr>
        </thead>
        <tbody>';

        foreach($queues as $queue) {
            if ($queue['topic'] < 1) {
                $$queue['topic'] = 1;
            }

            $affiche = false;

            $topics = DB::table('topics')->select('topicadmin', 'topictext', 'topicimage')->where('topicid', $queue['topic'])->first();

            if ($radminsuper) {
                $affiche = true;
            } else {
                $topicadminX = explode(',', $topics['topicadmin']);
                
                for ($i = 0; $i < count($topicadminX); $i++) {
                    if (trim($topicadminX[$i]) == $aid) {
                        $affiche = true;
                    }
                }
            }

            echo '
            <tr>
                <td>'. userpopover($queue['uname'], '40', 2) .' '. $queue['uname'] .'</td>
                <td>';

            if ($queue['subject'] == '') {
                $queue['subject'] = __d('two_news', 'Aucun Sujet');
            }

            $subject = language::aff_langue($queue['subject']);

            if ($affiche) {
                echo '<img class=" " src="assets/images/topics/'. $topics['topicimage'] .'" height="30" width="30" alt="avatar" />&nbsp;
                <a href="'. site_url('admin.php?op=topicedit&amp;topicid='. $queue['topic']) .'" class="adm_tooltip">'. language::aff_langue($topics['topictext']) .'</a></td>
                <td align="left"><a href="'. site_url('admin.php?op=DisplayStory&amp;qid='. $queue['qid']) .'">'. ucfirst($subject) .'</a></td>';
            } else {
                echo language::aff_langue($topics['topictext']) .'</td>
                <td><i>'. ucfirst($subject) .'</i></td>';
            }

            echo '
                <td class="small">'. date::formatTimestamp($queue['timestamp']) .'</td>';

            if ($affiche) {
                echo '
                <td><a class="" href="'. site_url('admin.php?op=DisplayStory&amp;qid='. $queue['qid']) .'">
                        <i class="fa fa-edit fa-lg" title="'. __d('two_news', 'Editer') .'" data-bs-toggle="tooltip" ></i>
                    </a>
                    <a class="text-danger" href="'. site_url('admin.php?op=DeleteStory&amp;qid='. $queue['qid']) .'">
                        <i class="fas fa-trash fa-lg ms-3" title="'. __d('two_news', 'Effacer') .'" data-bs-toggle="tooltip" ></i>
                    </a>
                </td>
            </tr>';
            } else {
                echo '
                <td>&nbsp;</td>
            </tr>';
        }

            $dummy++;
        }

        if ($dummy < 1) {
            echo '<h3>'. __d('two_news', 'Pas de nouveaux Articles postés') .'</h3>';
        } else {
            echo '
            </tbody>
        </table>';
        }
    }
    
    css::adminfoot('', '', '', '');
}

switch ($op) {
    default:
        submissions();
        break;
}
