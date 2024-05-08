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

use npds\support\assets\css;
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

include('auth.php');

/**
 * [cache_ctrl description]
 *
 * @return  void
 */
function cache_ctrl(): void
{
    if (Config::get('npds.cache_verif')) {
        header("Expires: Sun, 01 Jul 1990 00:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache, must revalidate");
        header("Pragma: no-cache");
    }
}

/**
 * [show_imm description]
 *
 * @return  void
 */
function show_imm(): void
{
    $user = users::getUser();
    
    if (!$user) {
        Header('Location: '. site_url('user.php'));
    } else {

        $theme = theme::getTheme();

        include("themes/$theme/theme.php");

        $userdata = forum::get_userdata($userdata[1]);

        $op = Request::input('op');

        $result = (($op != 'new_msg') 
            ? DB::table('priv_msgs')
                    ->select('*')
                    ->where('to_userid', $userdata['uid'])
                    ->where('read_msg', 1)
                    ->where('type_msg', 0)
                    ->orderBy(' msg_id', 'desc')
                    ->get()
            : DB::table('priv_msgs')
                    ->select('*')
                    ->where('to_userid', $userdata['uid'])
                    ->where('read_msg', 0)
                    ->where('type_msg', 0)
                    ->orderBy('msg_id', 'asc')
                    ->get()
        );
        
        $pasfin = false;

        foreach ($result as $myrow) {
            if ($pasfin == false) {
                $pasfin = true;
                
                cache_ctrl();
                
                include("storage/meta/meta.php");
                include("themes/default/view/include/header_head.inc");

                echo css::import_css($theme, Config::get('npds.language'), '', '', '');

                echo '
                </head>
                <body>
                    <div class="card card-body">';

            }
            $posterdata = forum::get_userdata_from_id($myrow['from_userid']);

            echo '
                <div class="card mb-3">
                <div class="card-body">
                <h3>' . __d('two_messenger', 'Message personnel') . ' ' . __d('two_messenger', 'de');

            if ($posterdata['uid'] == 1) {
                
                echo ' <span class="text-muted">' . Config::get('npds.sitename') . '</span></h3>';
            }

            if ($posterdata['uid'] <> 1) {
                echo ' <span class="text-muted">' . $posterdata['uname'] . '</span></h3>';
            }

            $myrow['subject'] = strip_tags($myrow['subject']);

            $posts = $posterdata['posts'];

            if ($posterdata['uid'] <> 1) {
                echo forum::member_qualif($posterdata['uname'], $posts, $posterdata['rang']);
            }

            echo '<br /><br />';

            if ($smilies) {
                if ($posterdata['user_avatar'] != '') {
                    
                    if (stristr($posterdata['user_avatar'], "users_private")) {
                        $imgtmp = $posterdata['user_avatar'];
                    } else {
                        $imgtmp = theme::theme_image('forum/avatar/'. $posterdata['user_avatar'], 'assets/images/forum/avatar/'. $posterdata['user_avatar']);
                    }

                    echo '<img class="btn-secondary img-thumbnail img-fluid n-ava" src="' . $imgtmp . '" alt="' . $posterdata['uname'] . '" />';
                }
            }

            if ($smilies) {
                if ($myrow['msg_image'] != '') {
                    $imgtmp = theme::theme_image('forum/subject/'. $myrow['msg_image'], 'assets/images/forum/subject/'. $myrow['msg_image']); 

                    echo '<img class="n-smil" src="' . $imgtmp . '"  alt="" />&nbsp;';
                }
            }

            echo __d('two_messenger', 'Envoyé') . ' : ' . $myrow['msg_time'] . '&nbsp;&nbsp;&nbsp';
            echo '<h4>' . language::aff_langue($myrow['subject']) . '</h4>';

            $message = stripslashes($myrow['msg_text']);

            if ($allow_bbcode) {
                $message = forum::smilie($message);
                $message = forum::aff_video_yt($message);
            }

            $message = str_replace("[addsig]", "<br /><br />" . nl2br($posterdata['user_sig']), language::aff_langue($message));
            echo $message . '<br />';

            // ??????
            // if ($posterdata['uid'] <> 1) {
            //     if (!$short_user) {
            //     }
            // }

            echo '
            </div>
            <div class="card-footer">';

            if ($posterdata['uid'] <> 1) {
                echo '
                <a class="me-3" href="'. site_url('readpmsg_imm.php?op=read_msg&amp;msg_id=' . $myrow['msg_id'] . '&amp;op_orig=' . $op . '&amp;sub_op=reply') .'" title="' . __d('two_messenger', 'Répondre') . '" data-bs-toggle="tooltip"><i class="fa fa-reply fa-lg me-1"></i>' . __d('two_messenger', 'Répondre') . '</a>';
            }
            
            echo '
                <a class="me-3" href="'. site_url('readpmsg_imm.php?op=read_msg&amp;msg_id=' . $myrow['msg_id'] . '&amp;op_orig=' . $op . '&amp;sub_op=read') .'" title="' . __d('two_messenger', 'Lu') . '" data-bs-toggle="tooltip"><i class="far fa-check-square fa-lg"></i></a>
                <a class="me-3" href="'. site_url('readpmsg_imm.php?op=delete&amp;msg_id=' . $myrow['msg_id'] . '&amp;op_orig=' . $op) .'" title="' . __d('two_messenger', 'Effacer') . '" data-bs-toggle="tooltip"><i class="fas fa-trash fa-lg text-danger"></i></a>
            </div>
            </div>';
        }

        if ($pasfin != true) {
            cache_ctrl();
            echo '<body onload="self.close();">';
        }
    }

    echo '
            </div>
        </body>
    </html>';
}

/**
 * [sup_imm description]
 *
 * @param   int   $msg_id  [$msg_id description]
 *
 * @return  void
 */
function sup_imm(): void
{
    if (!$cookie = users::cookieUser()) {
        Header('Location: '. site_url('user.php'));
    } else {
        $r = DB::table('priv_msgs')
                ->where('msg_id', Request::suery('msg_id'))
                ->where('to_userid', $cookie[0])
                ->delete();

        if (!$r) {
            forum::forumerror('0021');
        }
    }
}

/**
 * [read_imm description]
 *
 * @param   int     $msg_id  [$msg_id description]
 * @param   string  $sub_op  [$sub_op description]
 *
 * @return  void
 */
function read_imm(): void
{
    if (!$cookie = users::cookieUser()) {
        Header('Location: '. site_url('user.php'));
    } else {

        $r = DB::table('priv_msgs')->where('msg_id', Request::query('msg_id'))->where('to_userid', $cookie[0])->update(array(
            'read_msg'  => 1,
        ));

        if (!$r) {
            forum::forumerror('0021');
        }

        if (Request::query('sub_op') == 'reply') {
            echo '<script type="text/javascript">
                //<![CDATA[
                window.location="'. site_url('replypmsg.php?reply=1&msg_id='. Request::query('msg_id') .'&userid='. $cookie[0] .'&full_interface=short') .'";
                //]]>
                </script>';
            die();
        }

        echo '<script type="text/javascript">
                //<![CDATA[
                window.location="'. site_url('readpmsg_imm.php?op=new_msg') .'";
                //]]>
                </script>';
        die();
    }
}


switch (Request::input('op')) {
    case 'new_msg':
        show_imm();
        break;

    case 'read_msg':
        read_imm();
        break;

    case 'delete':
        sup_imm();
        show_imm();
        break;
        
    default:
        show_imm();
        break;
}
