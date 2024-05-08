<?php

/************************************************************************/
/* DUNE by NPDS - admin prototype                                       */
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

use npds\support\assets\js;
use npds\support\logs\logs;
use npds\support\assets\css;
use npds\support\mail\mailler;
use npds\system\config\Config;
use npds\support\editeur;
use npds\support\language\language;
use npds\system\support\facades\DB;

if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'email_user';
$f_titre = __d('two_core', 'Diffusion d\un Message Interne');

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

/**
 * [email_user description]
 *
 * @return  void
 */
function email_user(): void
{
    global $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('email_user'));
    adminhead($f_meta_nom, $f_titre);

    echo '
        <hr />
        <form id="emailuseradm" action="'. site_url('admin.php') .'" method="post" name="AdmMI">
            <fieldset>
                <legend>'. __d('two_core', 'Message') .'</legend>
                <input type="hidden" name="op" value="send_email_to_user" />
                <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="expediteur">'. __d('two_core', 'Expédier en tant') .'</label>
                <div id="expediteur" class="col-sm-8 my-2">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="adm" name="expediteur" value="1" checked="checked" />
                        <label class="form-check-label" for="adm">'. __d('two_core', 'qu\'administrateur') .'</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="mem" name="expediteur" value="0" />
                        <label class="form-check-label" for="mem">'. __d('two_core', 'que membre') .'</label>
                    </div>
                </div>
                </div>
                <div id="div_username" class="mb-3 row">
                <label class="col-form-label col-sm-4" for="username">'. __d('two_core', 'Utilisateur') .'</label>
                <div class="col-sm-8">
                    <input  class="form-control" type="text" id="username" name="username" value="" />
                </div>
                </div>
                <div id="div_groupe" class="mb-3 row">
                <label class="col-form-label col-sm-4" for="groupe">'. __d('two_core', 'Groupe') .'</label>
                <div class="col-sm-8">
                    <select id="groupe" class="form-select" name="groupe" >
                        <option value="0" selected="selected">'. __d('two_core', 'Choisir un groupe');

    $groupes = DB::table('groupes')->select('groupe_id', 'groupe_name')->orderBy('groupe_id', 'ASC')->get();

    foreach ($groupes as $groupe) {
        echo '<option value="'. $groupe['groupe_id'] .'">'. $groupe['groupe_id'] .' - '. language::aff_langue($groupe['groupe_name']);
    }

    echo '
                    </select>
                </div>
                </div>
                <div id="div_all" class="mb-3 row">
                <label class="col-form-label col-sm-4" for="all">'. __d('two_core', 'Envoyer à tous les membres') .'</label>
                <div class="col-sm-8 ">
                    <div class="form-check my-2">
                        <input class="form-check-input" id="all" type="checkbox" name="all" value="1" />
                        <label class="form-check-label" for="all"></label>
                    </div>
                </div>
                </div>
                <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="subject">'. __d('two_core', 'Sujet') .'</label>
                <div class="col-sm-8">
                    <input  class="form-control" type="text" maxlength="100" id="subject" name="subject" required="required" />
                    <span class="help-block text-end"><span id="countcar_subject"></span></span>
                </div>
                </div>
                <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="message">'. __d('two_core', 'Corps de message') .'</label>
                <div class="col-sm-12">
                    <textarea class="tin form-control" rows="25" id="message" name="message"></textarea>
                </div>
                </div>';

    echo editeur::aff_editeur('AdmMI', '');

    echo '
                <div class="mb-3 row">
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary">'. __d('two_core', 'Envoyer') .'</button>
                </div>
                </div>
            </fieldset>
        </form>
    <script type="text/javascript">
    //<![CDATA[
    $("#all").on("click", function(){
        check = $("#all").is(":checked");
        if(check) {
        $("#div_username").addClass("collapse");
        $("#div_groupe").addClass("collapse");
        } else {
            $("#div_username").removeClass("collapse in");
            $("#div_groupe").removeClass("collapse in");
        }
    }); 
    $("#groupe").on("change", function(){
        sel = $("#groupe").val();
        if(sel!=0) {
        $("#div_username").addClass("collapse");
        $("#div_all").addClass("collapse");
        } else {
            $("#div_username").removeClass("collapse in");
            $("#div_all").removeClass("collapse in");
        }
    });
    $("#username").bind("change paste keyup", function() {
        ibid = $(this).val();
        if(ibid!="") {
        $("#div_groupe").addClass("collapse");
        $("#div_all").addClass("collapse");
        } else {
            $("#div_groupe").removeClass("collapse in");
            $("#div_all").removeClass("collapse in");
        }
    });
    //]]>
    </script>';

    $arg1 = '
    var formulid = ["emailuseradm"];
    inpandfieldlen("subject",100);';

    echo js::auto_complete('membre', 'uname', 'users', 'username', 86400);

    css::adminfoot('fv', '', $arg1, '');
}

/**
 * [send_email_to_user description]
 *
 * @param   string  $username    [$username description]
 * @param   string  $subject     [$subject description]
 * @param   string  $message     [$message description]
 * @param   string  $all         [$all description]
 * @param   string  $groupe      [$groupe description]
 * @param   string  $expediteur  [$expediteur description]
 *
 * @return  void
 */
function send_email_to_user(string $username, string $subject, string $message, string $all, string $groupe, string $expediteur): void
{
    global $f_meta_nom, $f_titre;

    if ($subject != '') {
        if ($expediteur == 1) {
            $emetteur = 1;
        } else {
            global $user;
            if ($user) {
                $userX = base64_decode($user);
                $userdata = explode(':', $userX);
                $emetteur = $userdata[0];
            } else {
                $emetteur = 1;
            }
        }

        if ($all) {
            $users = DB::table('users')->select('uid', 'user_langue')->get();

            foreach ($users as $user) {
                $tab_to_userid[] = $user['uid'] .':'. $user['user_langue'];
            }
        } else {
            if ($groupe) {

                $users = DB::table('users_status')
                            ->leftJoin('users', 'users_status.uid', '=', 'users.uid')
                            ->select('users_status.uid', 'users_status.users_groupe', 'users.user_language')
                            ->where('users_status.groupe', '!=', '')
                            ->orderBy('users_status.uid', 'ASC')
                            ->get();

                foreach ($users as $user) {
                    $tab_groupe = explode(',', $user['users_groupe']);
                    
                    if ($tab_groupe) {
                        foreach ($tab_groupe as $groupevalue) {
                            if ($groupevalue == $groupe) {
                                $tab_to_userid[] = $user['uid'] .':'. $user['user_langue'];
                            }
                        }
                    }
                }
            } else {
                $users = DB::table('users')->select('uid', 'user_langue')->where('uname', $username)->first();

                foreach($users as $user) {
                    $tab_to_userid[] = $user['uid'] .':'. $user['user_langue'];
                }
            }
        }

        if (($subject == '') or ($message == '')) {
            header('location: '. site_url('admin.php'));
        }

        $message = str_replace('\n', '<br />', $message);

        $time = date(__d('two_core', 'dateinternal'), time() + ((int) Config::get('npds.gmt') * 3600));
        
        $pasfin = false;
        $count = 0;

        include_once("language/multilang.php");

        while ($count < sizeof($tab_to_userid)) {
            $to_tmp = explode(':', $tab_to_userid[$count]);
            $to_userid = $to_tmp[0];

            if (($to_userid != '') and ($to_userid != 1)) {

                $resultX = DB::table('priv_msgs')->insert(array(
                    'msg_image'      => $image,
                    'subject'        => $subject,
                    'from_userid'    => $emetteur,
                    'to_userid'      => $to_userid,
                    'msg_time'       => $time,
                    'msg_text'       => $message,
                ));
            
                if ($resultX) {
                    $pasfin = true;
                }

                // A copy in email if necessary
                if (Config::get('npds.subscribe')) {
                    $old_message = $message;
                    
                    $sujet = translate_ml($to_tmp[1], 'Vous avez un nouveau message.');
                    $message = translate_ml($to_tmp[1], 'Bonjour') . ", <br /><br /><a href=\"". site_url('viewpmsg.php') ."\">" . translate_ml($to_tmp[1], "Cliquez ici pour lire votre nouveau message.") . "</a><br /><br />";
                    $message .= Config::get('signature.message');
                    
                    mailler::copy_to_email($to_userid, $sujet, $message);
                    $message = $old_message;
                }
            }
            $count++;
        }
    }

    global $aid;
    logs::Ecr_Log('security', "SendEmailToUser($subject) by AID : $aid", '');

    include("themes/default/header.php");

    GraphicAdmin(manuel('email_user'));
    adminhead($f_meta_nom, $f_titre);

    echo '<hr />';
    
    if ($pasfin) {
        echo '<div class="alert alert-success"><strong>"'. stripslashes($subject) .'"</strong> '. __d('two_core', 'a été envoyée') .'.</div>';
    } else {
        echo '<div class="alert alert-danger"><strong>"'. stripslashes($subject) .'"</strong>'. __d('two_core', 'n\'a pas été envoyée') .'.</div>';
    }
        
    css::adminfoot('', '', '', '');
}

switch ($op) {
    case 'send_email_to_user':
        send_email_to_user($username, $subject, $message, $all, $groupe, $expediteur);
        break;

    case 'email_user':
        email_user();
        break;
}
