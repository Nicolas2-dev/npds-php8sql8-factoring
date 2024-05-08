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

use npds\support\logs\logs;
use npds\support\routing\url;
use npds\support\mail\mailler;
use npds\support\utility\spam;
use npds\system\config\Config;
use npds\system\support\facades\DB;
use npds\system\support\facades\Request;


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
    $stop = '';

    if ((!$email) || ($email == '') || (!preg_match('#^[_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4}$#i', $email))) {
        $stop = __d('two_newsleter', 'Erreur : Email invalide');
    }

    if (strrpos($email, ' ') > 0) {
        $stop = __d('two_newsleter', 'Erreur : une adresse Email ne peut pas contenir d\'espaces');
    }

    if (mailler::checkdnsmail($email) === false) {
        $stop = __d('two_newsleter', 'Erreur : DNS ou serveur de mail incorrect');
    }

    if (DB::table('users')
            ->select('email')
            ->where('email', $email)
            ->first() > 0) 
    {
        $stop = __d('two_newsleter', 'Erreur : adresse Email déjà utilisée');
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
            $stop = __d('two_newsleter', 'Erreur : adresse Email déjà utilisée');
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
    <h2>' . __d('two_newsleter', 'La lettre') . '</h2>
    <hr />
    <p class="lead mb-2">' . __d('two_newsleter', 'Merci d\'entrer l\'information en fonction des spécifications') . '</p>
    <div class="alert alert-danger">' . $ibid . '</div>
    <a href="'. site_url('index.php') .'" class="btn btn-outline-secondary">' . __d('two_newsleter', 'Retour en arrière') . '</a>';
}

/**
 * [subscribe description]
 *
 * @param   string  $var  [$var description]
 *
 * @return  void
 */
function subscribe(): void
{
    if ($xemail = Request::query('email')) {
        include("themes/default/header.php");

        echo '
        <h2>' . __d('two_newsleter', 'La lettre') . '</h2>
        <hr />
        <p class="lead mb-2">' . __d('two_newsleter', 'Gestion de vos abonnements') . ' : <strong>' . $xemail . '</strong></p>
        <form action="'. site_url('lnl.php') .'" method="POST">
            ' . spam::Q_spambot() . '
            <input type="hidden" name="email" value="' . $xemail . '" />
            <input type="hidden" name="op" value="subscribeOK" />
            <input type="submit" class="btn btn-outline-primary me-2" value="' . __d('two_newsleter', 'Valider') . '" />
            <a href="'. site_url('index.php') .'" class="btn btn-outline-secondary">' . __d('two_newsleter', 'Retour en arrière') . '</a>
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
function subscribe_ok(): void
{
    include("themes/default/header.php");

    $xemail = Request::input('email');

    //anti_spambot
    if (!spam::R_spambot(Request::input('asb_question'), Request::input('asb_reponse'), "")) {
        logs::Ecr_Log("security", "LNL Anti-Spam : email=" . $xemail, "");
        
        url::redirect_url("index.php");
        die();
    }

    if ($xemail) {

        $stop = SuserCheck($xemail);

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
                $subject = html_entity_decode(__d('two_newsleter', 'La lettre'), ENT_COMPAT | ENT_HTML401, 'utf-8') . ' / ' . Config::get('npds.sitename');
                $message = __d('two_newsleter', 'Merci d\'avoir consacré du temps pour vous enregistrer.') . '<br /><br />' . __d('two_newsleter', 'Pour supprimer votre abonnement à notre lettre, merci d\'utiliser') . ' : <br />'. site_url('lnl.php?op=unsubscribe&email=' . $xemail) .'<br /><br />';
                $message .= Config::get('signature.message');

                mailler::send_email($xemail, $subject, $message, '', true, 'html', '');

                echo '
                <div class="alert alert-success">' . __d('two_newsleter', 'Merci d\'avoir consacré du temps pour vous enregistrer.') . '</div>
                <a href="'. site_url('index.php') .'">' . __d('two_newsleter', 'Retour en arrière') . '</a>';
            } else {
                $stop = __d('two_newsleter', 'Compte ou adresse IP désactivée. Cet émetteur a participé plus de x fois dans les dernières heures, merci de contacter le webmaster pour déblocage.') . "<br />";
                
                error_handler($stop);
            }
        } else {
            error_handler($stop);}
    } else {
        error_handler(__d('two_newsleter', 'Cette donnée ne doit pas être vide.') . "<br />");
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
function unsubscribe(): void
{
    if ($xemail = Request::input('email')) {

        if ((!$xemail) || ($xemail == '') || (!preg_match('#^[_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4}$#i', $xemail))) {
            header('location: '. site_url('index.php'));
        }

        if (strrpos($xemail, ' ') > 0) { 
            header('location: '. site_url('index.php'));
        }

        if (DB::table('lnl_outside_users')->select('email')->where('email', $xemail)->first() > 0) {
 
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
                <div class="alert alert-success">' . __d('two_newsleter', 'Merci') . '</div>
                <a href="'. site_url('index.php') .'">' . __d('two_newsleter', 'Retour en arrière') . '</a>';

                include("themes/default/footer.php");
            } else {
                include("themes/default/header.php");

                $stop = __d('two_newsleter', 'Compte ou adresse IP désactivée. Cet émetteur a participé plus de x fois dans les dernières heures, merci de contacter le webmaster pour déblocage.') . "<br />";
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

switch (Request::input('op')) {
    case 'subscribe':
        subscribe();
        break;

    case 'subscribeOK':
        subscribe_ok();
        break;

    case 'unsubscribe':
        unsubscribe();
        break;
}
