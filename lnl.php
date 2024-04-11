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

use npds\system\logs\logs;
use npds\system\routing\url;
use npds\system\mail\mailler;
use npds\system\utility\spam;
use npds\system\config\Config;
use npds\system\support\facades\DB;


if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

/**
 * [SuserCheck description]
 *
 * @param   string  $email  [$email description]
 *
 * @return  string
 */
function SuserCheck(string $email): string 
{
    global $stop;

    $stop = '';

    if ((!$email) || ($email == '') || (!preg_match('#^[_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4}$#i', $email))) {
        $stop = translate("Erreur : Email invalide");
    }

    if (strrpos($email, ' ') > 0) {
        $stop = translate("Erreur : une adresse Email ne peut pas contenir d'espaces");
    }

    if (mailler::checkdnsmail($email) === false) {
        $stop = translate("Erreur : DNS ou serveur de mail incorrect");
    }

    if (DB::table('users')
            ->select('email')
            ->where('email', $email)
            ->first() > 0) 
    {
        $stop = translate("Erreur : adresse Email déjà utilisée");
    }

    if (DB::table('lnl_outside_users')
            ->select('email')
            ->where('email', $email)
            ->first() > 0) 
    {
        if (DB::table('lnl_outside_users')
                ->select('email')
                ->where('email', $email)
                ->where('status', 'NOK')
                ->get() > 0) 
        {       
            DB::table('lnl_outside_users')
                ->where('email', $email)
                ->delete();
        } else {
            $stop = translate("Erreur : adresse Email déjà utilisée");
        }
    }

    return $stop;
}

/**
 * [error_handler description]
 *
 * @param   string  $ibid  [$ibid description]
 *
 * @return  void
 */
function error_handler(string $ibid): void 
{
    echo '
    <h2>' . translate("La lettre") . '</h2>
    <hr />
    <p class="lead mb-2">' . translate("Merci d'entrer l'information en fonction des spécifications") . '</p>
    <div class="alert alert-danger">' . $ibid . '</div>
    <a href="'. site_url('index.php') .'" class="btn btn-outline-secondary">' . translate("Retour en arrière") . '</a>';
}

/**
 * [subscribe description]
 *
 * @param   string  $var  [$var description]
 *
 * @return  void
 */
function subscribe(string $var): void
{
    if ($var != '') {
        include("themes/default/header.php");

        echo '
        <h2>' . translate("La lettre") . '</h2>
        <hr />
        <p class="lead mb-2">' . translate("Gestion de vos abonnements") . ' : <strong>' . $var . '</strong></p>
        <form action="'. site_url('lnl.php') .'" method="POST">
            ' . spam::Q_spambot() . '
            <input type="hidden" name="email" value="' . $var . '" />
            <input type="hidden" name="op" value="subscribeOK" />
            <input type="submit" class="btn btn-outline-primary me-2" value="' . translate("Valider") . '" />
            <a href="'. site_url('index.php') .'" class="btn btn-outline-secondary">' . translate("Retour en arrière") . '</a>
        </form>';

        include("themes/default/footer.php");
    } else {
        header('location: '. site_url('index.php'));
    }
}

/**
 * [subscribe_ok description]
 *
 * @param   string  $xemail  [$xemail description]
 *
 * @return  void
 */
function subscribe_ok(string $xemail): void
{
    global $stop;

    include("themes/default/header.php");

    if ($xemail != '') {

        SuserCheck($xemail);

        if ($stop == '') {
            $host_name = getip();
            
            // Troll Control
            $troll = DB::table('lnl_outside_users')
                        ->select(DB::raw('COUNT(*) as count'))
                        ->where('host_name', $host_name)
                        ->whereRaw('to_days(now()) - to_days(date) < 3')
                        ->first();

            if ($troll['count'] < 6) {
                DB::table('lnl_outside_users')->insert(array(
                    'email'         => $xemail,
                    'host_name'     => $host_name,
                    'date'          => date("Y-m-d H:m:s", time()),
                    'status'        => 'OK',
                ));

                // Email validation + url to unsubscribe
                $subject = html_entity_decode(translate("La lettre"), ENT_COMPAT | ENT_HTML401, 'utf-8') . ' / ' . Config::get('npds.sitename');
                $message = translate("Merci d'avoir consacré du temps pour vous enregistrer.") . '<br /><br />' . translate("Pour supprimer votre abonnement à notre lettre, merci d'utiliser") . ' : <br />'. site_url('lnl.php?op=unsubscribe&email=' . $xemail) .'<br /><br />';
                $message .= Config::get('signature.message');

                mailler::send_email($xemail, $subject, $message, '', true, 'html', '');

                echo '
                <div class="alert alert-success">' . translate("Merci d'avoir consacré du temps pour vous enregistrer.") . '</div>
                <a href="'. site_url('index.php') .'">' . translate("Retour en arrière") . '</a>';
            } else {
                $stop = translate("Compte ou adresse IP désactivée. Cet émetteur a participé plus de x fois dans les dernières heures, merci de contacter le webmaster pour déblocage.") . "<br />";
                
                error_handler($stop);
            }
        } else {
            error_handler($stop);}
    } else {
        error_handler(translate("Cette donnée ne doit pas être vide.") . "<br />");
    }

    include("themes/default/footer.php");
}

/**
 * [unsubscribe description]
 *
 * @param   string  $xemail  [$xemail description]
 *
 * @return  void
 */
function unsubscribe(string $xemail): void
{
    if ($xemail != '') {

        if ((!$xemail) || ($xemail == '') || (!preg_match('#^[_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4}$#i', $xemail))) {
            header('location: '. site_url('index.php'));
        }

        if (strrpos($xemail, ' ') > 0) { 
            header('location: '. site_url('index.php'));
        }

        if (DB::table('lnl_outside_users')->select('email')->where('email', $xemail)->first() > 0) {

            // $timeX = date("Y-m-d H:m:s", time()); // not used 
            $troll = DB::table('lnl_outside_users')
                    ->select(DB::raw('COUNT(*) as count'))
                    ->where('host_name', getip())
                    ->whereRaw('to_days(now()) - to_days(date) < 3')
                    ->first();

            // Troll Control
            if ($troll['count'] < 6) {
                DB::table('lnl_outside_users')->where('email', $xemail)->update(array(
                    'status'    => 'NOK',
                ));

                include("themes/default/header.php");

                echo '
                <div class="alert alert-success">' . translate("Merci") . '</div>
                <a href="'. site_url('index.php') .'">' . translate("Retour en arrière") . '</a>';

                include("themes/default/footer.php");
            } else {
                include("themes/default/header.php");

                $stop = translate("Compte ou adresse IP désactivée. Cet émetteur a participé plus de x fois dans les dernières heures, merci de contacter le webmaster pour déblocage.") . "<br />";
                error_handler($stop);

                include("themes/default/footer.php");
            }
        } else {
            url::redirect_url("index.php");
        }
    } else {
        url::redirect_url("index.php");
    }
}

settype($op, 'string');

switch ($op) {
    case 'subscribe':
        subscribe($email);
        break;

    case 'subscribeOK':
        //anti_spambot
        if (!spam::R_spambot($asb_question, $asb_reponse, "")) {
            logs::Ecr_Log("security", "LNL Anti-Spam : email=" . $email, "");
            
            url::redirect_url("index.php");
            die();
        }

        subscribe_ok($email);
        break;

    case 'unsubscribe':
        unsubscribe($email);
        break;
}
