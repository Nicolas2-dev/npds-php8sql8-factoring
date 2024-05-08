<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* NPDS Copyright (c) 2002-2021 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\support\auth\users;
use npds\support\forum\forum;
use npds\support\theme\theme;
use npds\support\utility\spam;
use npds\system\config\Config;
use npds\support\language\language;
use npds\system\support\facades\DB;
use npds\system\support\facades\Request;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

include('auth.php');

$user = users::getUser();

if (!$user) {
    Header('Location: '. site_url('user.php'));
} else {
    include('themes/default/header.php');

    $userdata = forum::get_userdata(users::cookieUser(1));

    $start   = Request::query('start');
    $type    = Request::query('type');
    $dossier = Request::query('dossier');

    if ($type == 'outbox') {

        $resultID = DB::table('priv_msgs')
                ->select('*')
                ->where('from_userid', $userdata['uid'])
                ->where('type_msg', 1)
                ->orderBy('msg_id', 'desc')
                ->limit(1)->offset($start)
                ->first();
    } else {

        $query = DB::table('priv_msgs')
                    ->select('*')
                    ->where('to_userid', $userdata['uid'])
                    ->where('type_msg', 0);

        if ($dossier != 'All') {
            $query->where('dossier', $dossier);
        }
        
        if (!$dossier) {
            $query->where('dossier', '...');
        }

        $resultID = $query->orderBy('msg_id', 'desc')->limit(1)->offset($start)->first();
    }

    if (!$resultID) {
        forum::forumerror('0005');
    } else {
        if ($resultID['read_msg'] != '1') {
            
            $r = DB::table('priv_msgs')->where(' msg_id', $resultID['msg_id'])->update(array(
                'read_msg'   => 1,
            ));

            if (!$r) {
                forum::forumerror('0005');
            }
        }
    }

    $resultID['subject'] = strip_tags($resultID['subject']);

    if ($dossier == 'All') {
        $Xdossier = __d('two_messenger', 'Tous les sujets');
    } else {
        $Xdossier = StripSlashes($dossier);
    }

    echo '
        <h3>' . __d('two_messenger', 'Message personnel') . '</h3>
        <hr />';

    if (!$resultID) {
        echo '<div class="alert alert-danger lead">' . __d('two_messenger', 'Vous n\'avez aucun message.') . '</div>';
    } else {
        echo '
        <p class="lead">
            <a href="'. site_url('viewpmsg.php') .'">' . __d('two_messenger', 'Messages personnels') . '</a>&nbsp;&raquo;&raquo;&nbsp;' . $Xdossier . '&nbsp;&raquo;&raquo;&nbsp;' . language::aff_langue($resultID['subject']) . '
        </p>
        <div class="card mb-3">
            <div class="card-header">';
        
        if ($type == 'outbox') {
            $posterdata = forum::get_userdata_from_id($resultID['to_userid']);
        } else {
            $posterdata = forum::get_userdata_from_id($resultID['from_userid']);
        }

        $posts = $posterdata['posts'];
        
        if ($posterdata['uid'] <> 1) {
            $socialnetworks = array();
            $posterdata_extend = array();
            $res_id = array();
            $my_rs = '';

            if (!Config::get('npds.short_user')) {
                $posterdata_extend = forum::get_userdata_extend_from_id($posterdata['uid']);

                include('modules/reseaux-sociaux/reseaux-sociaux.conf.php');

                if ($posterdata_extend['M2'] != '') {
                    $socialnetworks = explode(';', $posterdata_extend['M2']);

                    foreach ($socialnetworks as $socialnetwork) {
                        $res_id[] = explode('|', $socialnetwork);
                    }

                    sort($res_id);
                    sort($rs);

                    foreach ($rs as $v1) {
                        foreach ($res_id as $y1) {
                            $k = array_search($y1[0], $v1);
                            if (false !== $k) {
                                $my_rs .= '<a class="me-3" href="';
                                
                                if ($v1[2] == 'skype'){ 
                                    $my_rs .= $v1[1] . $y1[1] . '?chat';
                                } else {
                                    $my_rs .= $v1[1] . $y1[1];
                                }
                                
                                $my_rs .= '" target="_blank"><i class="fab fa-' . $v1[2] . ' fa-2x text-primary"></i></a> ';
                                break;
                            } else {
                                $my_rs .= '';
                            }
                        }
                    }

                    $my_rsos[] = $my_rs;
                } else {
                    $my_rsos[] = '';
                }
            }

            $useroutils = '';
            $useroutils .= '<hr />';

            if ($posterdata['uid'] != 1 and $posterdata['uid'] != '') {
                $useroutils .= '<a class="list-group-item text-primary" href="'. site_url('user.php?op=userinfo&amp;uname=' . $posterdata['uname']) .'" target="_blank" title="' . __d('two_messenger', 'Profil') . '" data-bs-toggle="tooltip"><i class="fa fa-2x fa-user align-middle"></i><span class="ms-3 d-none d-md-inline">' . __d('two_messenger', 'Profil') . '</span></a>';
            }

            if ($posterdata['uid'] != 1) {
                $useroutils .= '<a class="list-group-item text-primary" href="'. site_url('powerpack.php?op=instant_message&amp;to_userid=' . $posterdata["uname"]) .'" title="' . __d('two_messenger', 'Envoyer un message interne') . '" data-bs-toggle="tooltip"><i class="far fa-envelope fa-2x align-middle "></i><span class="ms-3 d-none d-md-inline">' . __d('two_messenger', 'Message') . '</span></a>';
            }

            if ($posterdata['femail'] != '') {
                $useroutils .= '<a class="list-group-item text-primary" href="mailto:' . spam::anti_spam($posterdata['femail'], 1) . '" target="_blank" title="' . __d('two_messenger', 'Email') . '" data-bs-toggle="tooltip"><i class="fa fa-at fa-2x align-middle"></i><span class="ms-3 d-none d-md-inline">' . __d('two_messenger', 'Email') . '</span></a>';
            }

            if ($posterdata['url'] != '') {
                $useroutils .= '<a class="list-group-item text-primary" href="' . $posterdata['url'] . '" target="_blank" title="' . __d('two_messenger', 'Visiter ce site web') . '" data-bs-toggle="tooltip"><i class="fas fa-2x fa-external-link-alt align-middle"></i><span class="ms-3 d-none d-md-inline">' . __d('two_messenger', 'Visiter ce site web') . '</span></a>';
            }

            if ($posterdata['mns']) {
                $useroutils .= '<a class="list-group-item text-primary" href="'. site_url('minisite.php?op=' . $posterdata['uname']) .'" target="_blank" target="_blank" title="' . __d('two_messenger', 'Visitez le minisite') . '" data-bs-toggle="tooltip"><i class="fa fa-2x fa-desktop align-middle"></i><span class="ms-3 d-none d-md-inline">' . __d('two_messenger', 'Visitez le minisite') . '</span></a>';
            }
        }

        // if (Config::get('npds.smilies')) {
            if ($posterdata['user_avatar'] != '') {
                if (stristr($posterdata['user_avatar'], "users_private")) {
                    $imgtmp = $posterdata['user_avatar'];
                } else {
                    $imgtmp = theme::theme_image_row('forum/avatar/'. $posterdata['user_avatar'], 'assets/images/forum/avatar/'. $posterdata['user_avatar']);
                }

                if ($posterdata['uid'] <> 1) {
                    $aff_reso = isset($my_rsos[0]) ? $my_rsos[0] : '';
                    echo '<a style="position:absolute; top:1rem;" tabindex="0" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-html="true" data-bs-title="' . $posterdata['uname'] . '" data-bs-content=\'' . forum::member_qualif($posterdata['uname'], $posts, $posterdata['rang']) . '<br /><div class="list-group">' . $useroutils . '</div><hr />' . $aff_reso . '\'><img class=" btn-secondary img-thumbnail img-fluid n-ava" src="' . $imgtmp . '" alt="' . $posterdata['uname'] . '" /></a>';
                } else {
                    echo '<a style="position:absolute; top:1rem;" tabindex="0" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" title=\'<i class="fa fa-cogs fa-lg"></i>\'><img class=" btn-secondary img-thumbnail img-fluid n-ava" src="' . $imgtmp . '" alt="' . $posterdata['uname'] . '" /></a>';
                }
            }
        // }

        if ($posterdata['uid'] <> 1) {
            echo '&nbsp;<span style="position:absolute; left:6em;" class="text-muted"><strong>' . $posterdata['uname'] . '</strong></span>';
        } else {
            echo '&nbsp;<span style="position:absolute; left:6em;" class="text-muted"><strong>' . Config::get('npds.sitename') . '</strong></span>';
        }

        echo '<span class="float-end">';

        if (Config::get('npds.smilies')) {
            if ($resultID['msg_image'] != '') {
                $imgtmp = theme::theme_image_row('forum/subject/'. $resultID['msg_image'], 'assets/images/forum/subject/'. $resultID['msg_image']);

                echo '<img class="n-smil" src="' . $imgtmp . '" alt="icon_post" />';
            } else {
                $imgtmp = theme::theme_image_row('forum/subject/00.png', 'assets/images/forum/subject/00.png');

                echo '<img class="n-smil" src="' . $imgtmpPI . '" alt="icon_post" />';
            }
        }

        echo '</span>
                </div>
                <div class="card-body">
                <div class="card-text pt-2">
                    <div class="text-end small">' . __d('two_messenger', 'Envoyé') . ' : ' . $resultID['msg_time'] . '</div>
                    <hr /><strong>' . language::aff_langue($resultID['subject']) . '</strong><br />';

        $message = stripslashes($resultID['msg_text']);

        if (Config::get('forum.config.allow_bbcode')) {
            $message = forum::smilie($message);
            $message = forum::aff_video_yt($message);
        }

        $message = str_replace('[addsig]', '<br />' . nl2br($posterdata['user_sig']), language::aff_langue($message));

        echo $message;
        echo '
                </div>
                </div>
            </div>';

        $previous = $start - 1;
        $next = $start + 1;

        if ($type == 'outbox') {
            $tmpx = '&amp;type=outbox';
        } else {
            $tmpx = '&amp;dossier=' . urlencode(StripSlashes($dossier));
        }

        echo '<ul class="pagination d-flex justify-content-center">';

        if ($type != 'outbox') {
            if ($posterdata['uid'] <> 1) {
                echo '
                <li class="page-item">
                <a class="page-link" href="'. site_url('replypmsg.php?reply=1&amp;msg_id=' . $resultID['msg_id']) .'"><span class="d-none d-md-inline"></span><i class="fa fa-reply fa-lg me-2"></i><span class="d-none d-md-inline">' . __d('two_messenger', 'Répondre') . '</span></a>
                </li>';
            }
        }

        if ($previous >= 0) {
            echo '
                <li class="page-item">
                <a class="page-link" href="'. site_url('readpmsg.php?start=' . $previous . '&amp;total_messages=' . $total_messages . $tmpx) .'" >
                    <span class="d-none d-md-inline">' . __d('two_messenger', 'Message précédent') . '</span>
                    <span class="d-md-none" title="' . __d('two_messenger', 'Message précédent') . '" data-bs-toggle="tooltip"><i class="fa fa-angle-double-left fa-lg"></i></span>
                </a>
                </li>';
        } else {
            echo '
                <li class="page-item">
                <a class="page-link disabled" href="#">
                    <span class="d-none d-md-inline">' . __d('two_messenger', 'Message précédent') . '</span>
                    <span class="d-md-none" title="' . __d('two_messenger', 'Message précédent') . '" data-bs-toggle="tooltip"><i class="fa fa-angle-double-left fa-lg"></i></span>
                </a>
                </li>';
            }

        if ($next < $total_messages) {
            echo '
                <li class="page-item" >
                <a class="page-link" href="'. site_url('readpmsg.php?start=' . $next . '&amp;total_messages=' . $total_messages . $tmpx) .'" >
                    <span class="d-none d-md-inline">' . __d('two_messenger', 'Message suivant') . '</span>
                    <span class="d-md-none" title="' . __d('two_messenger', 'Message suivant') . '" data-bs-toggle="tooltip"><i class="fa fa-angle-double-right fa-lg"></i></span>
                </a>
                </li>';
        } else {
            echo '
                <li class="page-item">
                <a class="page-link disabled" href="#">
                    <span class="d-none d-md-inline">' . __d('two_messenger', 'Message suivant') . '</span>
                    <span class="d-md-none" title="' . __d('two_messenger', 'Message suivant') . '" data-bs-toggle="tooltip"><i class="fa fa-angle-double-right fa-lg"></i></span>
                </a>
                </li>';
        }

        echo '<li class="page-item">
                <a class="page-link" data-bs-toggle="collapse" href="#sortbox"><i class="fa fa-cogs fa-lg" title="' . __d('two_messenger', 'Classer ce message') . '" data-bs-toggle="tooltip"></i></a>
                </li>';

        if ($type != 'outbox') {
            echo '<li class="page-item"><a class="page-link " href="'. site_url('replypmsg.php?delete=1&amp;msg_id=' . $resultID['msg_id']) .'" title="' . __d('two_messenger', 'Supprimer ce message') . '" data-bs-toggle="tooltip"><i class="fas fa-trash fa-lg text-danger"></i></a></li>';
        } else {
            echo '<li class="page-item"><a class="page-link " href="'. site_url('replypmsg.php?delete=1&amp;msg_id=' . $resultID['msg_id'] . '&amp;type=outbox') .'"  title="' . __d('two_messenger', 'Supprimer ce message') . '" data-bs-toggle="tooltip"><i class="fas fa-trash fa-lg text-danger"></i></a></li>';
        }

        echo '</ul>';

        if ($type != 'outbox') {

            echo '
            <div class="collapse" id="sortbox">
                <div class="card card-body" >
                <p class="lead">' . __d('two_messenger', 'Classer ce message') . '</p>
                    <form action="'. site_url('replypmsg.php') .'" method="post">
                    <div class="mb-3 row">
                        <label class="col-form-label col-sm-4" for="dossier">' . __d('two_messenger', 'Sujet') . '</label>
                        <div class="col-sm-8">
                            <select class="form-select" id="dossier" name="dossier">';

            foreach (DB::table('priv_msgs')
                    ->distinct()
                    ->select('dossier')
                    ->where('to_userid', $userdata['uid'])
                    ->where('type_msg', 0)
                    ->orderBy('dossier')
                    ->get() as $priv_msg) 
            {
                echo '<option value="' . $priv_msg['dossier'] . '">' . $priv_msg['dossier'] . '</option>';
            }

            echo '
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-form-label col-sm-4" for="nouveau_dossier">' . __d('two_messenger', 'Nouveau dossier/sujet') . '</label>
                        <div class="col-sm-8">
                            <input type="texte" class="form-control" id="nouveau_dossier" name="nouveau_dossier" value="" size="24" />
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-8 ms-sm-auto">
                            <input type="hidden" name="msg_id" value="' . $resultID['msg_id'] . '" />
                            <input type="hidden" name="classement" value="1" />
                            <button type="submit" class="btn btn-primary" name="classe">OK</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>';
        }
    }
    
    include('themes/default/footer.php');
}
