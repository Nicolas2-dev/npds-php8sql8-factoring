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

use npds\support\date\date;
use npds\support\logs\logs;
use npds\support\news\news;
use npds\support\assets\css;
use npds\support\auth\users;
use npds\support\auth\groupe;
use npds\support\forum\forum;
use npds\support\str;
use npds\support\theme\theme;
use npds\support\mail\mailler;
use npds\support\pixels\image;
use npds\support\utility\spam;
use npds\system\config\Config;
use npds\support\cookie\cookie;
use npds\support\security\hack;
use npds\support\utility\crypt;
use npds\support\editeur;
use npds\support\language\language;
use npds\support\metalang\metalang;
use npds\system\cache\cacheManager;
use npds\system\support\facades\DB;
use npds\system\cache\SuperCacheEmpty;

if (!function_exists("Mysql_Connexion")) { 
    include('boot/bootstrap.php');
}

function message_error($ibid, $op)
{
    include("themes/default/header.php");

    echo '
    <h2>' . __d('two_users', 'Utilisateur') . '</h2>
    <div class="alert alert-danger lead">';
    echo $ibid;

    if (($op == 'only_newuser') or ($op == 'new user') or ($op == 'finish')) {
        hidden_form();
        echo '
            <input type="hidden" name="op" value="only_newuser" />
            <button class="btn btn-secondary mt-2" type="submit">' . __d('two_users', 'Retour en arrière') . '</button>
        </form>';
    } else {
        echo '<a class="btn btn-secondary mt-4" href="javascript:history.go(-1)" title="' . __d('two_users', 'Retour en arrière') . '">' . __d('two_users', 'Retour en arrière') . '</a>';
    }

    echo '
    </div>';

    include("themes/default/footer.php");
}

function message_pass($ibid)
{
    include("themes/default/header.php");
    echo $ibid;
    include("themes/default/footer.php");
}

function userCheck($uname, $email)
{
    global $NPDS_Prefix;

    $stop = '';
    if ((!$email) || ($email == '') || (!preg_match('#^[_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4}$#i', $email))) {
        $stop = '<i class="fa fa-exclamation me-2"></i>' . __d('two_users', 'Erreur : Email invalide');
    }

    if (strrpos($email, ' ') > 0) {
        $stop = '<i class="fa fa-exclamation me-2"></i>' . __d('two_users', 'Erreur : une adresse Email ne peut pas contenir d\'espaces');
    }

    if (mailler::checkdnsmail($email) === false) {
        $stop = __d('two_users', 'Erreur : DNS ou serveur de mail incorrect') . '!<br />';
    }

    if ((!$uname) || ($uname == '') || (preg_match('#[^a-zA-Z0-9_-]#', $uname))) {
        $stop = '<i class="fa fa-exclamation me-2"></i>' . __d('two_users', 'Erreur : identifiant invalide');
    }

    if (strlen($uname) > 25) {
        $stop = '<i class="fa fa-exclamation me-2"></i>' . __d('two_users', 'Votre surnom est trop long. Il doit faire moins de 25 caractères.');
    }

    if (preg_match('#^(root|adm|linux|webmaster|admin|god|administrator|administrador|nobody|anonymous|anonimo|an€nimo|operator|dune|netadm)$#i', $uname)) {
        $stop = '<i class="fa fa-exclamation me-2"></i>' . __d('two_users', 'Erreur : nom existant.');
    }

    if (strrpos($uname, ' ') > 0) {
        $stop = '<i class="fa fa-exclamation me-2"></i>' . __d('two_users', 'Il ne peut pas y avoir d\'espace dans le surnom.');
    }

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    if (sql_num_rows(sql_query("SELECT uname FROM " . $NPDS_Prefix . "users WHERE uname='$uname'")) > 0) {
        $stop = '<i class="fa fa-exclamation me-2"></i>' . __d('two_users', 'Erreur : cet identifiant est déjà utilisé');
    }

    if ($uname != 'edituser') {

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        if (sql_num_rows(sql_query("SELECT email FROM " . $NPDS_Prefix . "users WHERE email='$email'")) > 0) {
            $stop = '<i class="fa fa-exclamation me-2"></i>' . __d('two_users', 'Erreur : adresse Email déjà utilisée');
        }
    }

    return $stop;
}

function makePass()
{
    $makepass = '';
    $syllables = 'Er@1,In@2,Ti#a3,D#un4,F_e5,P_re6,V!et7,J!o8,Ne%s9,A%l0,L*en1,So*n2,Ch$a3,I$r4,L^er5,Bo^6,Ok@7,!Tio8,N@ar9,0Sim,1P$le,2B*la,3Te!n,4T~oe,5Ch~o,6Co,7Lat,8Spe,9Ak,0Er,1Po,2Co,3Lor,4Pen,5Cil!,6Li!,7Ght,8_Wh,9_At,T#he0,#He1,@Ck2,Is@3,M1am@,B2o+,3No@,Fi-4,0Ve!,A9ny#,Wa7y$,P8ol%,Iti^6,Cs~5,Ra*,@Dio,+Sou,-Rce,!Sea,#Rch,$Pa,&Per,^Com,~Bo,*Sp,Eak1*,S2t~,Fi^,R3st&,Gr#,O5up@,!Boy,Ea!,Gle*,4Tr*,+A1il,B0i+,_Bl9e,Br8b_,P7ri#,De6e!,$Ka3y,1En$,2Be-,4Se-';
    $syllable_array = explode(',', $syllables);
    
    srand( (int) microtime() * 1000000);
    
    for ($count = 1; $count <= 4; $count++) {
        if (rand() % 10 == 1) {
            $makepass .= sprintf("%0.0f", (rand() % 50) + 1);
        } else {
            $makepass .= sprintf("%s", $syllable_array[rand() % 62]);
        }
    }

    return $makepass;
}

function showimage()
{
    echo "
    <script type=\"text/javascript\">
    //<![CDATA[
    function showimage() {
    if (!document.images)
        return
        document.images.avatar.src=\n";

    if ($ibid = theme::theme_image("forum/avatar/blank.gif")) {
        $imgtmp = substr($ibid, 0, strrpos($ibid, "/") + 1);
    } else {
        $imgtmp = "assets/images/forum/avatar/";
    }

    echo "'$imgtmp' + document.Register.user_avatar.options[document.Register.user_avatar.selectedIndex].value\n";
    echo "}
    //]]>
    </script>";
}

function Only_NewUser()
{
    global $user, $memberpass;

    if (!$user) {
        global $smilies, $short_user, $memberpass;
        global $uname, $name, $email, $user_avatar, $user_occ, $user_from, $user_intrest, $user_sig, $user_viewemail, $pass, $vpass, $C1, $C2, $C3, $C4, $C5, $C6, $C7, $C8, $M1, $M2, $T1, $T2, $B1;
        
        include("themes/default/header.php");
        
        showimage();

        echo '
        <div>
        <h2 class="mb-3">' . __d('two_users', 'Utilisateur') . '</h2>
        <div class="card card-body mb-3">
            <h3>' . __d('two_users', 'Notes') . '</h3>
            <p>
            ' . __d('two_users', 'Les préférences de compte fonctionnent sur la base des cookies.') . ' ' . __d('two_users', 'Nous ne vendons ni ne communiquons vos informations personnelles à autrui.') . ' ' . __d('two_users', 'En tant qu\'utilisateur enregistré vous pouvez') . ' : 
                <ul>
                    <li>' . __d('two_users', 'Poster des commentaires signés') . '</li>
                    <li>' . __d('two_users', 'Proposer des articles en votre nom') . '</li>
                    <li>' . __d('two_users', 'Disposer d\'un bloc que vous seul verrez dans le menu (pour spécialistes, nécessite du code html)') . '</li>
                    <li>' . __d('two_users', 'Télécharger un avatar personnel') . '</li>
                    <li>' . __d('two_users', 'Sélectionner le nombre de news que vous souhaitez voir apparaître sur la page principale.') . '</li>
                    <li>' . __d('two_users', 'Personnaliser les commentaires') . '</li>
                    <li>' . __d('two_users', 'Choisir un look différent pour le site (si plusieurs proposés)') . '</li>
                    <li>' . __d('two_users', 'Gérer d\'autres options et applications') . '</li>
                </ul>
            </p>';

        if (!$memberpass) {
            echo '<div class="alert alert-success lead"><i class="fa fa-exclamation me-2"></i>' . __d('two_users', 'Le mot de passe vous sera envoyé à l\'adresse Email indiquée.') . '</div>';
        }

        echo '
        </div>
        <div class="card card-body mb-3">';

        include("support/sform/extend-user/extend-user.php");
        
        echo '</div>';
        css::adminfoot('fv', $fv_parametres, $arg1, '');
    } else {
        header('location: '. site_url('user.php'));
    }
}

function hidden_form()
{
    global $uname, $name, $email, $user_avatar, $user_occ, $user_from, $user_intrest, $user_sig, $user_viewemail, $pass, $vpass, $C1, $C2, $C3, $C4, $C5, $C6, $C7, $C8, $M1, $M2, $T1, $T2, $B1, $charte, $user_lnl;
    
    if (!$user_avatar) {
        $user_avatar = "blank.gif";
    }

    echo '
    <form action="'. site_url('user.php') .'" method="post">
        <input type="hidden" name="uname" value="' . $uname . '" />
        <input type="hidden" name="name" value="' . hack::removeHack($name) . '" />
        <input type="hidden" name="email" value="' . $email . '" />
        <input type="hidden" name="user_avatar" value="' . $user_avatar . '" />
        <input type="hidden" name="user_from" value="' . StripSlashes(hack::removeHack($user_from)) . '" />
        <input type="hidden" name="user_occ" value="' . StripSlashes(hack::removeHack($user_occ)) . '" />
        <input type="hidden" name="user_intrest" value="' . StripSlashes(hack::removeHack($user_intrest)) . '" />
        <input type="hidden" name="user_sig" value="' . StripSlashes(hack::removeHack($user_sig)) . '" />
        <input type="hidden" name="user_viewemail" value="' . $user_viewemail . '" />
        <input type="hidden" name="pass" value="' . hack::removeHack($pass) . '" />
        <input type="hidden" name="user_lnl" value="' . hack::removeHack($user_lnl) . '" />
        <input type="hidden" name="C1" value="' . StripSlashes(hack::removeHack($C1)) . '" />
        <input type="hidden" name="C2" value="' . StripSlashes(hack::removeHack($C2)) . '" />
        <input type="hidden" name="C3" value="' . StripSlashes(hack::removeHack($C3)) . '" />
        <input type="hidden" name="C4" value="' . StripSlashes(hack::removeHack($C4)) . '" />
        <input type="hidden" name="C5" value="' . StripSlashes(hack::removeHack($C5)) . '" />
        <input type="hidden" name="C6" value="' . StripSlashes(hack::removeHack($C6)) . '" />
        <input type="hidden" name="C7" value="' . StripSlashes(hack::removeHack($C7)) . '" />
        <input type="hidden" name="C8" value="' . StripSlashes(hack::removeHack($C8)) . '" />
        <input type="hidden" name="M1" value="' . StripSlashes(hack::removeHack($M1)) . '" />
        <input type="hidden" name="M2" value="' . StripSlashes(hack::removeHack($M2)) . '" />
        <input type="hidden" name="T1" value="' . StripSlashes(hack::removeHack($T1)) . '" />
        <input type="hidden" name="T2" value="' . StripSlashes(hack::removeHack($T2)) . '" />
        <input type="hidden" name="B1" value="' . StripSlashes(hack::removeHack($B1)) . '" />';
}

function confirmNewUser($uname, $name, $email, $user_avatar, $user_occ, $user_from, $user_intrest, $user_sig, $user_viewemail, $pass, $vpass, $user_lnl, $C1, $C2, $C3, $C4, $C5, $C6, $C7, $C8, $M1, $M2, $T1, $T2, $B1)
{
    global $smilies, $short_user, $memberpass;
    
    $uname = strip_tags($uname);

    if ($user_viewemail != 1) {
        $user_viewemail = '0';
    }

    $stop = userCheck($uname, $email);

    if ($memberpass) {
        if ((isset($pass)) and ($pass != $vpass)) {
            $stop = '<i class="fa fa-exclamation me-2"></i>' . __d('two_users', 'Les mots de passe sont différents. Ils doivent être identiques.');
        } elseif (strlen($pass) < Config::get('npds.minpass')) {
            $stop = '<i class="fa fa-exclamation me-2"></i>' . __d('two_users', 'Désolé, votre mot de passe doit faire au moins') . ' <strong>' . Config::get('npds.minpass') . '</strong> ' . __d('two_users', 'caractères');
        }
    }

    if (!$stop) {
        include("themes/default/header.php");

        echo '
        <h2>' . __d('two_users', 'Utilisateur') . '</h2>
        <hr />
        <h3 class="mb-3"><i class="fa fa-user me-2"></i>' . __d('two_users', 'Votre fiche d\'inscription') . '</h3>
        <div class="card">
            <div class="card-body">';

        include("support/sform/extend-user/aff_extend-user.php");

        echo '
            </div>
        </div>';

        hidden_form();

        global $charte;
        if (!$charte) {
            echo '
                <div class="alert alert-danger lead mt-3">
                    <i class="fa fa-exclamation me-2"></i>' . __d('two_users', 'Vous devez accepter la charte d\'utilisation du site') . '
                </div>
                <input type="hidden" name="op" value="only_newuser" />
                <input class="btn btn-secondary mt-1" type="submit" value="' . __d('two_users', 'Retour en arrière') . '" />
                </form>';
        } else {
            echo '
                <input type="hidden" name="op" value="finish" /><br />
                <input class="btn btn-primary mt-2" type="submit" value="' . __d('two_users', 'Terminer') . '" />
                </form>';
        }

        include("themes/default/footer.php");
    } else {
        message_error($stop, "new user");
    }
}

function finishNewUser($uname, $name, $email, $user_avatar, $user_occ, $user_from, $user_intrest, $user_sig, $user_viewemail, $pass, $user_lnl, $C1, $C2, $C3, $C4, $C5, $C6, $C7, $C8, $M1, $M2, $T1, $T2, $B1)
{
    global $NPDS_Prefix, $makepass, $adminmail, $AutoRegUser, $memberpass, $gmt, $NPDS_Key;

    $sitename = Config::get('npds.sitename');

    if (!isset($_SERVER['HTTP_REFERER'])) {
        logs::Ecr_Log('security', 'Ghost form in user.php registration. => NO REFERER', '');
        spam::L_spambot('', "false");
        include('admin/die.php');
        die();

    } else if ($_SERVER['HTTP_REFERER'] . $NPDS_Key !== Config::get('npds.nuke_url') . '/user.php' . $NPDS_Key) {
        logs::Ecr_Log('security', 'Ghost form in user.php registration. => ' . $_SERVER["HTTP_REFERER"], '');
        spam::L_spambot('', "false");
        include('admin/die.php');
        die();
    }

    $user_regdate = time() + ((int)$gmt * 3600);
    $stop = userCheck($uname, $email);

    if (!$stop) {
        include("themes/default/header.php");

        if (!$memberpass) {
            $makepass = makepass();
        } else {
            $makepass = $pass;
        }

        $AlgoCrypt = PASSWORD_BCRYPT;
        $min_ms = 100;
        $options = ['cost' => users::getOptimalBcryptCostParameter($makepass, $AlgoCrypt, $min_ms)];
        $hashpass = password_hash($makepass, $AlgoCrypt, $options);
        $cryptpass = crypt($makepass, $hashpass);
        $hashkey = 1;

        $Default_Theme = Config::get('npds.Default_Theme');
        $Default_Skin = Config::get('npds.Default_Skin');

        //DB::table('')->insert(array(
        //    ''       => ,
        //));

        $result = sql_query("INSERT INTO " . $NPDS_Prefix . "users VALUES (NULL,'$name','$uname','$email','','','$user_avatar','$user_regdate','$user_occ','$user_from','$user_intrest','$user_sig','$user_viewemail','','','$cryptpass', '1', '10','','0','0','0','','0','','$Default_Theme+$Default_Skin','10','0','0','1','0','','','$user_lnl')");

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        list($usr_id) = sql_fetch_row(sql_query("SELECT uid FROM " . $NPDS_Prefix . "users WHERE uname='$uname'"));

        //DB::table('')->insert(array(
        //    ''       => ,
        //));    

        $result = sql_query("INSERT INTO " . $NPDS_Prefix . "users_extend VALUES ('$usr_id','$C1','$C2','$C3','$C4','$C5','$C6','$C7','$C8','$M1','$M2','$T1','$T2', '$B1')");
        $attach = $user_sig ? 1 : 0;

        if (($AutoRegUser == 1) or (!isset($AutoRegUser))) {

            //DB::table('')->insert(array(
            //    ''       => ,
            //));

            $result = sql_query("INSERT INTO " . $NPDS_Prefix . "users_status VALUES ('$usr_id','0','$attach','0','1','1','')");
        } else {

            //DB::table('')->insert(array(
            //    ''       => ,
            //));

            $result = sql_query("INSERT INTO " . $NPDS_Prefix . "users_status VALUES ('$usr_id','0','$attach','0','1','0','')");
        }

        if ($result) {
            if ($memberpass) {
                echo '
                <h2>' . __d('two_users', 'Utilisateur') . '</h2>
                <hr />
                <h2><i class="fa fa-user me-2"></i>' . __d('two_users', 'Inscription') . '</h2>
                <p class="lead">' . __d('two_users', 'Votre mot de passe est : ') . '<strong>' . $makepass . '</strong></p>
                <p class="lead">' . __d('two_users', 'Vous pourrez le modifier après vous être connecté sur') . ' : <br /><a href="'. site_url('user.php?op=login&amp;uname=' . $uname . '&amp;pass=' . urlencode($makepass)) .'"><i class="fas fa-sign-in-alt fa-lg me-2"></i><strong>' . $sitename . '</strong></a></p>';

                $message = __d('two_users', 'Bienvenue sur') . " $sitename !\n\n" . __d('two_users', 'Vous, ou quelqu'un d'autre, a utilisé votre Email identifiant votre compte') . " ($email) " . __d('two_users', 'pour enregistrer un compte sur') . " $sitename.\n\n" . __d('two_users', 'Informations sur l\'utilisateur :') . " : \n\n";
                $message .=
                    __d('two_users', 'ID utilisateur (pseudo)') . ' : ' . $uname . "\n" .
                    __d('two_users', 'Véritable adresse Email') . ' : ' . $email . "\n";

                if ($name != '') {
                    $message .= __d('two_users', 'Votre véritable identité') . ' : ' . $name . "\n";
                }

                if ($user_from != '') {
                    $message .= __d('two_users', 'Votre situation géographique') . ' : ' . $user_from . "\n";
                }

                if ($user_occ != '') {
                    $message .= __d('two_users', 'Votre activité') . ' : ' . $user_occ . "\n";
                }

                if ($user_intrest != '') {
                    $message .= __d('two_users', 'Vos centres d\'intérêt') . ' : ' . $user_intrest . "\n";
                }

                if ($user_sig != '') {
                    $message .= __d('two_users', 'Signature') . ' : ' . $user_sig . "\n";
                }

                if (isset($C1) and $C1 != '') {
                    $message .= language::aff_langue('[fr]Activit&#x00E9; professionnelle[/fr][en]Professional activity[/en][es]Actividad profesional[/es][de]Berufliche T&#xE4;tigkeit[/de]') . ' : ' . $C1 . "\n";
                }

                if (isset($C2) and $C2 != '') {
                    $message .= language::aff_langue('[fr]Code postal[/fr][en]Postal code[/en][es]C&#xF3;digo postal[/es][de]Postleitzahl[/de]') . ' : ' . $C2 . "\n";
                }

                if (isset($T1) and $T1 != '') {
                    $message .= language::aff_langue('[fr]Date de naissance[/fr][en]Birth date[/en][es]Fecha de nacimiento[/es][de]Geburtsdatum[/de]') . ' : ' . $T1 . "\n";
                }

                // trad a revoir !!!!!
                $message .= "\n\n\n" . language::aff_langue("[fr]Conform&eacute;ment aux articles 38 et suivants de la loi fran&ccedil;aise n&deg; 78-17 du 6 janvier 1978 relative &agrave; l'informatique, aux fichiers et aux libert&eacute;s, tout membre dispose d&rsquo; un droit d&rsquo;acc&egrave;s, peut obtenir communication, rectification et/ou suppression des informations le concernant.[/fr][en]In accordance with Articles 38 et seq. Of the French law n &deg; 78-17 of January 6, 1978 relating to data processing, files and freedoms, any member has a right of access, can obtain communication, rectification and / or deletion of information about him.[/en][zh]&#26681;&#25454;1978&#24180;1&#26376;6&#26085;&#20851;&#20110;&#25968;&#25454;&#22788;&#29702;&#65292;&#26723;&#26696;&#21644;&#33258;&#30001;&#30340;&#27861;&#22269;78-17&#21495;&#27861;&#24459;&#65292;&#20219;&#20309;&#25104;&#21592;&#37117;&#26377;&#26435;&#36827;&#20837;&#65292;&#21487;&#20197;&#33719;&#24471;&#36890;&#20449;&#65292;&#32416;&#27491;&#21644;/&#25110; &#21024;&#38500;&#26377;&#20851;&#20182;&#30340;&#20449;&#24687;&#12290;[/zh][es]De conformidad con los art&iacute;culos 38 y siguientes de la ley francesa n &deg; 78-17 del 6 de enero de 1978, relativa al procesamiento de datos, archivos y libertades, cualquier miembro tiene derecho de acceso, puede obtener comunicaci&oacute;n, rectificaci&oacute;n y / o supresi&oacute;n de informaci&oacute;n sobre &eacute;l.[/es][de]Gem&auml;&szlig; den Artikeln 38 ff. Des franz&ouml;sischen Gesetzes Nr. 78-17 vom 6. Januar 1978 in Bezug auf Datenverarbeitung, Akten und Freiheiten hat jedes Mitglied ein Recht auf Zugang, kann Kommunikation, Berichtigung und / oder L&ouml;schung von Informationen &uuml;ber ihn.[/de]");
                $message .= "\n\n\n" . language::aff_langue("[fr]Ce message et les pi&egrave;ces jointes sont confidentiels et &eacute;tablis &agrave; l'attention exclusive de leur destinataire (aux adresses sp&eacute;cifiques auxquelles il a &eacute;t&eacute; adress&eacute;). Si vous n'&ecirc;tes pas le destinataire de ce message, vous devez imm&eacute;diatement en avertir l'exp&eacute;diteur et supprimer ce message et les pi&egrave;ces jointes de votre syst&egrave;me.[/fr][en]This message and any attachments are confidential and intended to be received only by the addressee. If you are not the intended recipient, please notify immediately the sender by reply and delete the message and any attachments from your system.[/en][zh]&#27492;&#28040;&#24687;&#21644;&#20219;&#20309;&#38468;&#20214;&#37117;&#26159;&#20445;&#23494;&#30340;&#65292;&#24182;&#19988;&#25171;&#31639;&#30001;&#25910;&#20214;&#20154;&#25509;&#25910;&#12290; &#22914;&#26524;&#24744;&#19981;&#26159;&#39044;&#26399;&#25910;&#20214;&#20154;&#65292;&#35831;&#31435;&#21363;&#36890;&#30693;&#21457;&#20214;&#20154;&#24182;&#22238;&#22797;&#37038;&#20214;&#21644;&#31995;&#32479;&#20013;&#30340;&#25152;&#26377;&#38468;&#20214;&#12290;[/zh][es]Este mensaje y cualquier adjunto son confidenciales y est&aacute;n destinados a ser recibidos por el destinatario. Si no es el destinatario deseado, notif&iacute;quelo al remitente de inmediato y responda al mensaje y cualquier archivo adjunto de su sistema.[/es][de]Diese Nachricht und alle Anh&auml;nge sind vertraulich und sollen vom Empf&auml;nger empfangen werden. Wenn Sie nicht der beabsichtigte Empf&auml;nger sind, benachrichtigen Sie bitte sofort den Absender und antworten Sie auf die Nachricht und alle Anlagen von Ihrem System.[/de]") . "\n\n\n";
                $message .= Config::get('signature.message');
                
                $subject = html_entity_decode(__d('two_users', 'Inscription'), ENT_COMPAT | ENT_HTML401, 'utf-8') . ' ' . $uname;
                
                mailler::send_email($email, $subject, $message, '', true, 'html', '');
            } else {
                $message = __d('two_users', 'Bienvenue sur') . " $sitename !\n\n" . __d('two_users', 'Vous, ou quelqu\'un d\'autre, a utilisé votre Email identifiant votre compte') . " ($email) " . __d('two_users', 'pour enregistrer un compte sur') . " $sitename.\n\n" . __d('two_users', 'Informations sur l\'utilisateur :') . "\n" . __d('two_users', '-Identifiant : ') . " $uname\n" . __d('two_users', '-Mot de passe : ') . " $makepass\n\n";
                $message .= Config::get('signature.message');
                
                $subject = html_entity_decode(__d('two_users', 'Mot de passe utilisateur pour'), ENT_COMPAT | ENT_HTML401, 'utf-8') . ' ' . $uname;
                
                mailler::send_email($email, $subject, $message, '', true, 'html', '');

                echo '
                <h2>' . __d('two_users', 'Utilisateur') . '</h2>
                <h2><i class="fa fa-user me-2"></i>Inscription</h2>
                <div class="alert alert-success lead"><i class="fa fa-exclamation me-2"></i>' . __d('two_users', 'Vous êtes maintenant enregistré. Vous allez recevoir un code de confirmation dans votre boîte à lettres électronique.') . '</div>';
            }

            //------------------------------------------------
            if (file_exists("themes/default/view/include/new_user.inc")) {
                include("themes/default/view/include/new_user.inc");
                
                global $gmt;
                
                $time = date(__d('two_users', 'dateinternal'), time() + ((int)$gmt * 3600));
                $message = metalang::meta_lang(AddSlashes(str_replace("\n", "<br />", $message)));
                
                //DB::table('')->insert(array(
                //    ''       => ,
                //));

                $sql = "INSERT INTO " . $NPDS_Prefix . "priv_msgs (msg_image, subject, from_userid, to_userid, msg_time, msg_text) ";
                $sql .= "VALUES ('', '$sujet', '$emetteur_id', '$usr_id', '$time', '$message')";
                sql_query($sql);
            }

            //------------------------------------------------
            $subject = html_entity_decode(__d('two_users', 'Inscription'), ENT_COMPAT | ENT_HTML401, 'utf-8') . ' : ' . $sitename;
            
            mailler::send_email($adminmail, $subject, "Infos :
            Nom : $name
            ID : $uname
            Email : $email", '', false, "text", '');
        }

        include("themes/default/footer.php");
    } else {
        message_error($stop, 'finish');
    }
}

function userinfo($uname)
{
    global $NPDS_Prefix, $admin;
    global $user, $smilies, $short_user;
    global $name, $email, $url, $bio, $user_avatar, $user_from, $user_occ, $user_intrest, $user_sig, $user_journal, $C7, $C8;

    $uname = hack::removeHack($uname);

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT uid, name, femail, url, bio, user_avatar, user_from, user_occ, user_intrest, user_sig, user_journal, mns FROM " . $NPDS_Prefix . "users WHERE uname='$uname'");
    list($uid, $name, $femail, $url, $bio, $user_avatar, $user_from, $user_occ, $user_intrest, $user_sig, $user_journal, $mns) = sql_fetch_row($result);
    
    if (!$uid) {
        header('location: '. site_url('index.php'));
    }

    global $cookie;

    include("themes/default/header.php");

    $email = hack::removeHack($femail);
    $name = stripslashes(hack::removeHack($name));
    $url = hack::removeHack($url);
    $bio = stripslashes(hack::removeHack($bio));
    $user_from = stripslashes(hack::removeHack($user_from));
    $user_occ = stripslashes(hack::removeHack($user_occ));
    $user_intrest = stripslashes(hack::removeHack($user_intrest));
    $user_sig = nl2br(hack::removeHack($user_sig));
    $user_journal = stripslashes(hack::removeHack($user_journal));
    $op = 'userinfo';

    if (stristr($user_avatar, "users_private")) {
        $direktori = '';
    } else {
        global $theme;

        // a revoir pour classe !!
        $direktori = 'assets/images/forum/avatar/';
        if (function_exists("theme_image")) {
            if (theme::theme_image("forum/avatar/blank.gif")) {
                $direktori = "themes/$theme/images/forum/avatar/";
            }
        }
    }

    $socialnetworks = array();
    $posterdata_extend = array();
    $res_id = array();
    $my_rs = '';
    $posterdata_extend = forum::get_userdata_extend_from_id($uid);

    if (!$short_user) {
        include('modules/reseaux-sociaux/config/reseaux-sociaux.conf.php');

        if (array_key_exists('M2', $posterdata_extend)) {
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
                        
                        if ($v1[2] == 'skype') { 
                            $my_rs .= $v1[1] . $y1[1] . '?chat';
                        } else {
                            $my_rs .= $v1[1] . $y1[1];
                        }

                        $my_rs .= '" target="_blank"><i class="fab fa-' . $v1[2] . ' fa-2x"></i></a> ';
                        break;
                    } else {
                        $my_rs .= '';
                    }
                }
            }
        }
    }

    $posterdata = forum::get_userdata_from_id($uid);
    $useroutils = '';

    if (($user) and ($uid != 1)) { 
        $useroutils .= '<a class=" text-primary me-3" href="'. site_url('powerpack.php?op=instant_message&amp;to_userid=' . $posterdata["uname"]) .'" ><i class="far fa-envelope fa-2x" title="' . __d('two_users', 'Envoyer un message interne') . '" data-bs-toggle="tooltip"></i></a>&nbsp;';
    }

    if (array_key_exists('femail', $posterdata)) {
        if ($posterdata['femail'] != '') {
            $useroutils .= '<a class=" text-primary me-3" href="mailto:' . spam::anti_spam($posterdata['femail'], 1) . '" target="_blank" ><i class="fa fa-at fa-2x" title="' . __d('two_users', 'Email') . '" data-bs-toggle="tooltip"></i></a>&nbsp;';
        }
    }

    if (array_key_exists('url', $posterdata)) {
        if ($posterdata['url'] != '') {
            $useroutils .= '<a class=" text-primary me-3" href="' . $posterdata['url'] . '" target="_blank" ><i class="fas fa-external-link-alt fa-2x" title="' . __d('two_users', 'Visiter ce site web') . '" data-bs-toggle="tooltip"></i></a>&nbsp;';
        }
    }

    if (array_key_exists('mns', $posterdata)) {
        if ($posterdata['mns']) {
            $useroutils .= '<a class=" text-primary me-3" href="'. site_url('minisite.php?op=' . $posterdata['uname']) .'" target="_blank" ><i class="fa fa-desktop fa-2x" title="' . __d('two_users', 'Visitez le minisite') . '" data-bs-toggle="tooltip"></i></a>&nbsp;';
        }
    }

    echo '
    <div class="d-flex flex-row flex-wrap">
        <div class="me-2 my-auto"><img src="' . $direktori . $user_avatar . '" class=" rounded-circle center-block n-ava-64 align-middle" /></div>
        <div class="align-self-center">
            <h2>' . __d('two_users', 'Utilisateur') . '<span class="d-inline-block text-muted ms-1">' . $uname . '</span></h2>';

    if (isset($cookie[1])) {
        if ($uname !== $cookie[1]) {
            echo $useroutils;
        }
    }

    echo $my_rs;

    if (isset($cookie[1])) {
        if ($uname == $cookie[1]) {
            echo '<p class="lead">' . __d('two_users', 'Si vous souhaitez personnaliser un peu le site, c\'est l\'endroit indiqué. ') . '</p>';
        }
    }

    echo '
        </div>
    </div>
    <hr />';

    if (isset($cookie[1])) {
        if ($uname == $cookie[1]) {
            users::member_menu($mns, $uname);
        }
    }

    include('modules/geoloc/config/geoloc.conf');

    echo '
    <div class="card card-body">
        <div class="row">';

    if (array_key_exists($ch_lat, $posterdata_extend) and array_key_exists($ch_lon, $posterdata_extend))
        if ($posterdata_extend[$ch_lat] != '' and $posterdata_extend[$ch_lon] != '') {
            $C7 = $posterdata_extend[$ch_lat];
            $C8 = $posterdata_extend[$ch_lon];
            echo '<div class="col-md-6">';
        } else {
            echo '<div class="col-md-12">';
        }

    include("support/sform/extend-user/aff_extend-user.php");

    echo '</div>';

    //==> geoloc
    if (array_key_exists($ch_lat, $posterdata_extend) and array_key_exists($ch_lon, $posterdata_extend))
        if ($posterdata_extend[$ch_lat] != '' and $posterdata_extend[$ch_lon] != '') {

            $content = '';
            $content .= '
            <div class="col-md-6">
                <div id="map_user" tabindex="300" style="width:100%; height:400px;" lang="' . language::language_iso(1, 0, 0) . '">
                <div id="ol_popup"></div>
                </div>
                <script type="module">
                //<![CDATA[
                if (typeof ol=="undefined")
                    $("head").append($("<script />").attr({"type":"text/javascript","src":"/assets/shared/ol/ol.js"}));
                $(function(){
                var 
                    iconFeature = new ol.Feature({
                        geometry: new ol.geom.Point(
                        ol.proj.fromLonLat([' . $posterdata_extend[$ch_lon] . ',' . $posterdata_extend[$ch_lat] . '])
                        ),
                        name: "' . $uname . '"
                    }),
                    iconStyle = new ol.style.Style({
                        image: new ol.style.Icon({
                        src: "' . $ch_img . $nm_img_mbcg . '"
                        })
                    });
                iconFeature.setStyle(iconStyle);
                var 
                    vectorSource = new ol.source.Vector({features: [iconFeature]}),
                    vectorLayer = new ol.layer.Vector({source: vectorSource}),
                    map = new ol.Map({
                        interactions: new ol.interaction.defaults({
                            constrainResolution: true, onFocusOnly: true
                        }),
                        target: document.getElementById("map_user"),
                        layers: [
                        new ol.layer.Tile({
                            source: new ol.source.OSM()
                        })
                        ],
                        view: new ol.View({
                        center: ol.proj.fromLonLat([' . $posterdata_extend[$ch_lon] . ', ' . $posterdata_extend[$ch_lat] . ']),
                        zoom: 12
                        })
                    });
                //Adding a marker on the map
                map.addLayer(vectorLayer);

                var element = document.getElementById("ol_popup");
                var popup = new ol.Overlay({
                    element: element,
                    positioning: "bottom-center",
                    stopEvent: false,
                    offset: [0, -20]
                });
                map.addOverlay(popup);

        // display popup on click
                map.on("click", function(evt) {
                var feature = map.forEachFeatureAtPixel(evt.pixel,
                    function(feature) {
                    return feature;
                    });
                if (feature) {
                    var coordinates = feature.getGeometry().getCoordinates();
                    popup.setPosition(coordinates);
                    $(element).popover({
                    placement: "top",
                    html: true,
                    content: feature.get("name")
                    });
                    $(element).popover("show");
                } else {
                    $(element).popover("hide");
                }
                });
                // change mouse cursor when over marker
                map.on("pointermove", function(e) {
                if (e.dragging) {
                    $(element).popover("hide");
                    return;
                }
                var pixel = map.getEventPixel(e.originalEvent);
                });
                // Create the graticule component
                var graticule = new ol.layer.Graticule();
                graticule.setMap(map);';

            $content .= file_get_contents('modules/geoloc/assets/js/ol-dico.js');

            $content .= '
                const targ = map.getTarget();
                const lang = targ.lang;
                for (var i in dic) {
                    if (dic.hasOwnProperty(i)) {
                        $("#map_user "+dic[i].cla).prop("title", dic[i][lang]);
                    }
                }
                $("#map_user .ol-zoom-in, #map_user .ol-zoom-out").tooltip({placement: "right", container: "#map_user",});
                $("#map_user .ol-rotate-reset, #map_user .ol-attribution button[title]").tooltip({placement: "left", container: "#map_user",});
                });

                //]]>
                </script>';

            $content .= '
            <div class="mt-3">
                <a href="'. site_url('modules.php?ModPath=geoloc&amp;ModStart=geoloc') .'"><i class="fa fa-globe fa-lg"></i>&nbsp;[fr]Carte[/fr][en]Map[/en][zh]&#x5730;&#x56FE;[/zh][es]Mapa[/es][de]Karte[/de]</a>';
            
            if ($admin) {
                $content .= '
                <a href="'. site_url('admin.php?op=Extend-Admin-SubModule&amp;ModPath=geoloc&amp;ModStart=admin/geoloc_set') .'"><i class="fa fa-cogs fa-lg ms-3"></i>&nbsp;[fr]Admin[/fr][en]Admin[/en][zh]Admin[/zh][es]Admin[/es][de]Admin[/de]</a>';
            }

            $content .= '
                </div>
            </div>';

            $content = language::aff_langue($content);
            echo $content;
        }
    //<== geoloc

    echo '
        </div>
    </div>';

    if ($uid != 1) {
        echo '
        <br />
        <h4>' . __d('two_users', 'Journal en ligne de ') . ' ' . $uname . '.</h4>
        <div id="online_user_journal" class="card card-body mb-3">' . metalang::meta_lang($user_journal) . '</div>';
    }

    $file = '';
    $handle = opendir('modules/comments/config');
    while (false !== ($file = readdir($handle))) {
        if (!preg_match('#\.conf\.php$#i', $file)) {
            continue;
        }

        $topic = "#topic#";
        
        include("modules/comments/config/$file");
        $filelist[$forum] = $url_ret;
    }

    closedir($handle);

    echo '
    <h4 class="my-3">' . __d('two_users', 'Les derniers commentaires de') . ' ' . $uname . '.</h4>
    <div id="last_comment_by" class="card card-body mb-3">';

    $url = '';

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT topic_id, forum_id, post_text, post_time FROM " . $NPDS_Prefix . "posts WHERE forum_id<0 and poster_id='$uid' ORDER BY post_time DESC LIMIT 0,10");
    
    while (list($topic_id, $forum_id, $post_text, $post_time) = sql_fetch_row($result)) {
        $url = str_replace("#topic#", $topic_id, $filelist[$forum_id]);
        echo '<p><a href="' . $url . '">' . __d('two_users', 'Posté : ') . date::convertdate($post_time) . '</a></p>';
        
        $message = forum::smilie(stripslashes($post_text));
        $message = forum::aff_video_yt($message);
        $message = str_replace('[addsig]', '', $message);
        
        if (stristr($message, "<a href")) {
            $message = preg_replace('#_blank(")#i', '_blank\1 class=\1noir\1', $message);
        }

        echo nl2br($message) . '<hr />';
    }

    echo '
        </div>
        <h4 class="my-3">' . __d('two_users', 'Les derniers articles de') . ' ' . $uname . '.</h4>
        <div id="last_article_by" class="card card-body mb-3">';

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    //"SELECT sid, catid, ihome, time FROM " . $NPDS_Prefix . "stories
    $xtab = news::news_aff("libre", "WHERE informant='$uname' ORDER BY sid DESC LIMIT 10", '', 10);

    $story_limit = 0;
    while (($story_limit < 10) and ($story_limit < sizeof($xtab))) {
        list($sid, $catid, $aid, $title, $time) = $xtab[$story_limit];
        $story_limit++;
        
        echo '
        <div class="d-flex">
            <div class="p-2"><a href="'. site_url('article.php?sid=' . $sid) .'">' . language::aff_langue($title) . '</a></div>
            <div class="ms-auto p-2">' . $time . '</div>
        </div>';
    }

    echo '
    </div>
    <h4 class="my-3">' . __d('two_users', 'Les dernières contributions de') . ' ' . $uname . '</h4>';

    $nbp = 10;
    $content = '';

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT * FROM " . $NPDS_Prefix . "posts WHERE forum_id > 0 AND poster_id=$uid ORDER BY post_time DESC LIMIT 0,50");
    $j = 1;

    while (list($post_id, $post_text) = sql_fetch_row($result) and $j <= $nbp) {

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        // Requete detail dernier post
        $res = sql_query("SELECT 
                us.topic_id, us.forum_id, us.poster_id, us.post_time, 
                uv.topic_title, 
                ug.forum_name, ug.forum_type, ug.forum_pass, 
                ut.uname 
            FROM 
                " . $NPDS_Prefix . "posts us, 
                " . $NPDS_Prefix . "forumtopics uv, 
                " . $NPDS_Prefix . "forums ug, 
                " . $NPDS_Prefix . "users ut 
            WHERE 
                us.post_id = $post_id 
                AND uv.topic_id = us.topic_id 
                AND uv.forum_id = ug.forum_id 
                AND ut.uid = us.poster_id LIMIT 1");

        list($topic_id, $forum_id, $poster_id, $post_time, $topic_title, $forum_name, $forum_type, $forum_pass, $uname) = sql_fetch_row($res);
        
        if (($forum_type == '5') or ($forum_type == '7')) {
            $ok_affich = false;
            $tab_groupe = groupe::valid_group($user);
            $ok_affich = groupe::groupe_forum($forum_pass, $tab_groupe);
        } else {
            $ok_affich = true;
        }

        if ($ok_affich) {

            // = DB::table('')->select()->where('', )->orderBy('')->get();

            // Nbre de postes par sujet
            $TableRep = sql_query("SELECT * FROM " . $NPDS_Prefix . "posts WHERE forum_id > 0 AND topic_id = '$topic_id'");
            $replys = sql_num_rows($TableRep) - 1;
            $id_lecteur = isset($cookie[0]) ? $cookie[0] : '0';

            // = DB::table('')->select()->where('', )->orderBy('')->get();

            $sqlR = "SELECT rid FROM " . $NPDS_Prefix . "forum_read WHERE topicid = '$topic_id' AND uid = '$id_lecteur' AND status != '0'";

            if (sql_num_rows(sql_query($sqlR)) == 0) {
                $image = '<a href="" title="' . __d('two_users', 'Non lu') . '" data-bs-toggle="tooltip"><i class="far fa-file-alt fa-lg faa-shake animated text-primary "></i></a>';
            } else {
                $image = '<a title="' . __d('two_users', 'Lu') . '" data-bs-toggle="tooltip"><i class="far fa-file-alt fa-lg text-primary"></i></a>';
            }

            $content .= '
            <p class="mb-0 list-group-item list-group-item-action flex-column align-items-start" >
                <span class="d-flex w-100 mt-1">
                <span>' . $post_time . '</span>
                <span class="ms-auto">
                <span class="badge bg-secondary ms-1" title="' . __d('two_users', 'Réponses') . '" data-bs-toggle="tooltip" data-bs-placement="left">' . $replys . '</span>
                </span>
            </span>
            <span class="d-flex w-100"><br /><a href="'. site_url('viewtopic.php?topic=' . $topic_id . '&forum=' . $forum_id) .'" data-bs-toggle="tooltip" title="' . $forum_name . '">' . $topic_title . '</a><span class="ms-auto mt-1">' . $image . '</span></span>
            </p>';
            $j++;
        }
    }

    echo $content;
    echo '
    <hr />';

    if ($posterdata['attachsig'] == 1) {
        echo '<p class="n-signature">' . $user_sig . '</p>';
    }

    include("themes/default/footer.php");
}

function main($user)
{
    global $stop, $smilies;

    if (!isset($user)) {
        include("themes/default/header.php");

        echo '<h2>' . __d('two_users', 'Utilisateur') . '</h2>';

        if ($stop == 99) {
            echo '<p class="alert alert-danger"><i class="fa fa-exclamation me-2"></i>' . __d('two_users', 'Vous n\'êtes pas encore autorisé à vous connecter.') . '</p>';
        } elseif ($stop) {
            echo '<p class="alert alert-danger"><i class="fa fa-exclamation me-2"></i>' . __d('two_users', 'Identifiant incorrect !') . '<br />' . __d('two_users', 'ou') . '<br /><i class="fa fa-exclamation me-2"></i>' . __d('two_users', 'Mot de passe erroné, refaites un essai.') . '</p>';
        }

        if (!$user) {
            echo '
            <div class="card card-body mb-3">
                <h3><a href="'. site_url('user.php?op=only_newuser') .'" role="button" title="' . __d('two_users', 'Nouveau membre') . '"><i class="fa fa-user-plus"></i>&nbsp;' . __d('two_users', 'Nouveau membre') . '</a></h3>
            </div>
            <div class="card card-body">
                <h3 class="mb-4"><i class="fas fa-sign-in-alt fa-lg me-2 align-middle"></i>' . __d('two_users', 'Connexion') . '</h3>
                <form action="'. site_url('user.php') .'" method="post" name="userlogin">
                <div class="row g-2">
                    <div class="col-sm-6">
                        <div class="mb-3 form-floating">
                            <input type="text" class="form-control" name="uname" id="inputuser" placeholder="' . __d('two_users', 'Identifiant') . '" required="required" />
                            <label for="inputuser">' . __d('two_users', 'Identifiant') . '</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-0 form-floating">
                            <input type="password" class="form-control" name="pass" id="inputPassuser" placeholder="' . __d('two_users', 'Mot de passe') . '" required="required" />
                            <label for="inputPassuser">' . __d('two_users', 'Mot de passe') . '</label>
                        </div>
                        <span class="help-block small float-end"><a href="'. site_url('user.php?op=forgetpassword') .'" title="' . __d('two_users', 'Vous avez perdu votre mot de passe ?') . '">' . __d('two_users', 'Vous avez perdu votre mot de passe ?') . '</a></span>
                    </div>
                </div>
                <input type="hidden" name="op" value="login" />
                <button class="btn btn-primary btn-lg" type="submit" title="' . __d('two_users', 'Valider') . '">' . __d('two_users', 'Valider') . '</button>
                </form>
            </div>
            <script type="text/javascript">//<![CDATA[document.userlogin.uname.focus();//]]></script>';

            // include externe file from themes/default/view/include for functions, codes ...
            if (file_exists("themes/default/view/include/user.inc")) {
                include("themes/default/view/include/user.inc");
            }
        }

        include("themes/default/footer.php");
    } elseif (isset($user)) {
        $cookie = cookie::cookiedecode($user);
        userinfo($cookie[1]);
    }
}

function logout()
{
    global $NPDS_Prefix, $user, $cookie;

    if ($cookie[1] != '') {
        DB::table('session')->where('username', $cookie[1])->delete();
    }

    setcookie('user', '', 0);
    unset($user);

    setcookie('user_language', '', 0);
    unset($user_language);

    Header('Location: '. site_url('index.php'));
}

function ForgetPassword()
{
    include("themes/default/header.php");

    echo '
    <h2 class="mb-3">' . __d('two_users', 'Utilisateur') . '</h2>
    <div class="card card-body">
        <div class="alert alert-danger lead"><i class="fa fa-exclamation me-2"></i>' . __d('two_users', 'Vous avez perdu votre mot de passe ?') . '</div>
        <div class="alert alert-success"><i class="fa fa-exclamation me-2"></i>' . __d('two_users', 'Pas de problème. Saisissez votre identifiant et le nouveau mot de passe que vous souhaitez utiliser puis cliquez sur envoyer pour recevoir un Email de confirmation.') . '</div>
        <form id="forgetpassword" action="'. site_url('user.php') .'" method="post">
            <div class="row g-2">
                <div class="col-sm-6 ">
                <div class="mb-3 form-floating">
                    <input type="text" class="form-control" name="uname" id="inputuser" placeholder="' . __d('two_users', 'Identifiant') . '" required="required" />
                    <label for="inputuser">' . __d('two_users', 'Identifiant') . '</label>
                </div>
                </div>
                <div class="col-sm-6">
                <div class="mb-3 form-floating">
                    <input type="password" class="form-control" name="code" id="inputpassuser" placeholder="' . __d('two_users', 'Mot de passe') . '" required="required" />
                    <label for="inputpassuser">' . __d('two_users', 'Mot de passe') . '</label>
                </div>
                <div class="progress" style="height: 0.4rem;">
                    <div id="passwordMeter_cont" class="progress-bar bg-danger" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                </div>
                </div>
            </div>
            <input type="hidden" name="op" value="mailpasswd" />
            <input class="btn btn-primary btn-lg my-3" type="submit" value ="' . __d('two_users', 'Envoyer') . '" />
        </form>
    </div>';

    $fv_parametres = '
        code: {
            validators: {
                checkPassword: {
                message: "Le mot de passe est trop simple."
                },
            }
        },';

    $arg1 = '
        var formulid = ["forgetpassword"];';

    css::adminfoot('fv', $fv_parametres, $arg1, 'foo');

    include("themes/default/footer.php");;
}

function mail_password($uname, $code)
{
    global $NPDS_Prefix;

    $uname = hack::removeHack(stripslashes(htmlspecialchars(urldecode($uname), ENT_QUOTES, 'utf-8')));

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT uname,email,pass FROM " . $NPDS_Prefix . "users WHERE uname='$uname'");
    $tmp_result = sql_fetch_row($result);

    if (!$tmp_result) {
        message_error(__d('two_users', 'Désolé, aucune information correspondante pour cet utlilisateur n\'a été trouvée') . "<br /><br />", '');
    } else {
        $host_name = getip();

        list($uname, $email, $pass) = $tmp_result;

        // On envoie une URL avec dans le contenu : username, email, le MD5 du passwd retenu et le timestamp
        $url = site_url('user.php?op=validpasswd&code=' . urlencode(crypt::encrypt($uname) . '#fpwd#' . crypt::encryptK($email . "#fpwd#" . $code . "#fpwd#" . time(), $pass)));

        $message = __d('two_users', 'Le compte utilisateur') . ' ' . $uname . ' ' . __d('two_users', 'at') . ' ' . Config::get('npds.sitename') . ' ' . __d('two_users', 'est associé à votre Email.') . "\n\n";
        $message .= __d('two_users', 'Un utilisateur web ayant l\'adresse IP ') . " $host_name " . __d('two_users', 'vient de demander une confirmation pour changer de mot de passe.') . "\n\n" . __d('two_users', 'Votre url de confirmation est :'') . " <a href=\"$url\">$url</a> \n\n" . __d('two_users', 'Si vous n\'avez rien demandé, ne vous inquiétez pas. Effacez juste ce Email. ') . "\n\n";
        $message .= Config::get('signature.message');

        $subject = __d('two_users', 'Confirmation du code pour') . ' ' . $uname;

        mailler::send_email($email, $subject, $message, '', true, 'html', '');

        message_pass('<div class="alert alert-success lead text-center"><i class="fa fa-exclamation"></i>&nbsp;' . __d('two_users', 'Confirmation du code pour') . ' ' . $uname . ' ' . __d('two_users', 'envoyée par courrier.') . '</div>');
        
        logs::Ecr_Log('security', 'Lost_password_request : ' . $uname, '');
    }
}

function valid_password($code)
{
    global $NPDS_Prefix;

    $ibid = explode("#fpwd#", $code);

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT email,pass FROM " . $NPDS_Prefix . "users WHERE uname='" . crypt::decrypt($ibid[0]) . "'");

    list($email, $pass) = sql_fetch_row($result);

    if ($email != '') {
        $ibid = explode("#fpwd#", crypt::decryptK($ibid[1], $pass));

        if ($email == $ibid[0]) {
            include("themes/default/header.php");

            echo '
        <p class="lead">' . __d('two_users', 'Vous avez perdu votre mot de passe ?') . '</p>
        <div class="card border rounded p-3">
            <div class="row">
                <div class="col-sm-7">
                <div class="blockquote">' . __d('two_users', 'Pour valider votre nouveau mot de passe, merci de le re-saisir.') . '<br />' . __d('two_users', 'Votre mot de passe est : ') . ' <strong>' . $ibid[1] . '</strong></div>
                </div>
                <div class="col-sm-5">
                <form id="lostpassword" action="'. site_url('user.php') .'" method="post">
                    <div class="mb-3 row">
                        <label class="col-form-label col-sm-12" for="passwd">' . __d('two_users', 'Mot de passe') . '</label>
                        <div class="col-sm-12">
                            <input type="password" class="form-control" name="passwd" placeholder="' . $ibid[1] . '" required="required" />
                        </div>
                    </div>
                    <input type="hidden" name="op" value="updatepasswd" />
                    <input type="hidden" name="code" value="' . $code . '" />
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <input class="btn btn-primary" type="submit" value="' . __d('two_users', 'Valider') . '" />
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>';

            include("themes/default/footer.php");;
        } else {
            message_pass('<div class="alert alert-danger lead text-center">' . __d('two_users', 'Erreur') . '</div>');
            logs::Ecr_Log('security', 'Lost_password_valid NOK Mail not match : ' . $ibid[0], '');
        }
    } else {
        message_pass('<div class="alert alert-danger lead text-center">' . __d('two_users', 'Erreur') . '</div>');
        logs::Ecr_Log('security', 'Lost_password_valid NOK Bad hash : ' . $ibid[0], '');
    }
}

function update_password($code, $passwd)
{
    global $NPDS_Prefix;

    $ibid = explode("#fpwd#", $code);
    $uname = urlencode(crypt::decrypt($ibid[0]));

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT email,pass FROM " . $NPDS_Prefix . "users WHERE uname='$uname'");
    list($email, $pass) = sql_fetch_row($result);

    if ($email != '') {
        $ibid = explode("#fpwd#", crypt::decryptK($ibid[1], $pass));

        if ($email == $ibid[0]) {

            // Le lien doit avoir été généré dans les 24H00
            if ((time() - $ibid[2]) < 86400) {

                // le mot de passe est-il identique
                if ($ibid[1] == $passwd) {
                    $AlgoCrypt = PASSWORD_BCRYPT;
                    $min_ms = 250;
                    $options = ['cost' => users::getOptimalBcryptCostParameter($ibid[1], $AlgoCrypt, $min_ms),];
                    $hashpass = password_hash($ibid[1], $AlgoCrypt, $options);
                    $cryptpass = crypt($ibid[1], $hashpass);
                    
                    //DB::table('')->where('', )->update(array(
                    //    ''       => ,
                    //));

                    sql_query("UPDATE " . $NPDS_Prefix . "users SET pass='$cryptpass', hashkey='1' WHERE uname='$uname'");

                    message_pass('<div class="alert alert-success lead text-center"><a class="alert-link" href="'. site_url('user.php') .'"><i class="fa fa-exclamation me-2"></i>' . __d('two_users', 'Mot de passe mis à jour. Merci de vous re-connecter') . '<i class="fas fa-sign-in-alt fa-lg ms-2"></i></a></div>');
                    logs::Ecr_Log('security', 'Lost_password_update OK : ' . $uname, '');
                } else {
                    message_pass('<div class="alert alert-danger lead text-center">' . __d('two_users', 'Erreur') . ' : ' . __d('two_users', 'Les mots de passe sont différents. Ils doivent être identiques.') . '</div>');
                    logs::Ecr_Log('security', 'Lost_password_update Password not match : ' . $uname, '');
                }
            } else {
                message_pass('<div class="alert alert-danger lead text-center">' . __d('two_users', 'Erreur') . ' : ' . __d('two_users', 'Votre url de confirmation est expirée') . ' > 24 h</div>');
                logs::Ecr_Log('security', 'Lost_password_update NOK Time > 24H00 : ' . $uname, '');
            }
        } else {
            message_pass('<div class="alert alert-danger lead text-center">' . __d('two_users', 'Erreur : Email invalide') . '</div>');
            logs::Ecr_Log('security', 'Lost_password_update NOK Mail not match : ' . $uname, '');
        }
    } else {
        message_pass('<div class="alert alert-danger lead text-center">' . __d('two_users', 'Erreur') . '</div>');
        logs::Ecr_Log('security', 'Lost_password_update NOK Empty Mail or bad user : ' . $uname, '');
    }
}

function docookie($setuid, $setuname, $setpass, $setstorynum, $setumode, $setuorder, $setthold, $setnoscore, $setublockon, $settheme, $setcommentmax, $user_langue)
{
    $info = base64_encode("$setuid:$setuname:" . md5($setpass) . ":$setstorynum:$setumode:$setuorder:$setthold:$setnoscore:$setublockon:$settheme:$setcommentmax");

    $user_cook_duration = Config::get('npds.user_cook_duration');

    if ($user_cook_duration <= 0) {
        $user_cook_duration = 1;
    }

    $timeX = time() + (3600 * $user_cook_duration);
    setcookie("user", "$info", $timeX);

    if ($user_langue != '') {
        setcookie('user_language', "$user_langue", $timeX);
    }
}

function login($uname, $pass)
{
    global $NPDS_Prefix, $setinfo;

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT pass, hashkey, uid, uname, storynum, umode, uorder, thold, noscore, ublockon, theme, commentmax, user_langue FROM " . $NPDS_Prefix . "users WHERE uname = '$uname'");
    if (sql_num_rows($result) == 1) {
        $setinfo = sql_fetch_assoc($result);
        
        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $result = sql_query("SELECT open FROM " . $NPDS_Prefix . "users_status WHERE uid='" . $setinfo['uid'] . "'");
        list($open_user) = sql_fetch_row($result);
        
        if ($open_user == 0) {
            Header('Location: '. site_url('user.php?stop=99'));
            return;
        }

        $dbpass = $setinfo['pass'];
        //$pass = utf8_decode($pass);

        if (password_verify($pass, $dbpass) or (strcmp($dbpass, $pass) == 0)) {
            if (!$setinfo['hashkey']) {
                $AlgoCrypt = PASSWORD_BCRYPT;
                $min_ms = 100;
                $options = ['cost' => users::getOptimalBcryptCostParameter($pass, $AlgoCrypt, $min_ms)];
                $hashpass = password_hash($pass, $AlgoCrypt, $options);
                $pass = crypt($pass, $hashpass);

                //DB::table('')->where('', )->update(array(
                //    ''       => ,
                //));

                sql_query("UPDATE " . $NPDS_Prefix . "users SET pass='$pass', hashkey='1' WHERE uname='$uname'");

                // = DB::table('')->select()->where('', )->orderBy('')->get();

                $result = sql_query("SELECT pass, hashkey, uid, uname, storynum, umode, uorder, thold, noscore, ublockon, theme, commentmax, user_langue FROM " . $NPDS_Prefix . "users WHERE uname = '$uname'");
                if (sql_num_rows($result) == 1) {
                    $setinfo = sql_fetch_assoc($result);
                }

                $dbpass = $setinfo['pass'];
                $scryptPass = crypt($dbpass, $hashpass);
            }
        } else {
            $scryptPass = '';
        }

        if (password_verify(urldecode($pass), $dbpass) or password_verify($pass, $dbpass)) {
            $CryptpPWD = $dbpass;
        } elseif (password_verify($dbpass, $scryptPass) or strcmp($dbpass, $pass) == 0) {
            $CryptpPWD = $pass;
        } else {
            Header('Location: '. site_url('user.php?stop=1'));
            return;
        }

        docookie($setinfo['uid'], $setinfo['uname'], $CryptpPWD, $setinfo['storynum'], $setinfo['umode'], $setinfo['uorder'], $setinfo['thold'], $setinfo['noscore'], $setinfo['ublockon'], $setinfo['theme'], $setinfo['commentmax'], $setinfo['user_langue']);

        $ip = getip();

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $result = sql_query("SELECT * FROM " . $NPDS_Prefix . "session WHERE host_addr='$ip' AND guest='1'");

        if (sql_num_rows($result) == 1) {
            DB::table('session')->where('host_addr', $ip)->where('guest', 1)->delete();
        }

        Header('Location: '. site_url('index.php'));
    } else {
        Header('Location: '. site_url('user.php?stop=1'));
    }
}

function edituser()
{
    global $NPDS_Prefix, $user, $smilies, $short_user, $subscribe, $member_invisible, $avatar_size;

    include("themes/default/header.php");

    $userinfo = users::getusrinfo($user);

    users::member_menu($userinfo['mns'], $userinfo['uname']);

    global $C1, $C2, $C3, $C4, $C5, $C6, $C7, $C8, $M1, $M2, $T1, $T2, $B1;

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT C1, C2, C3, C4, C5, C6, C7, C8, M1, M2, T1, T2, B1 FROM " . $NPDS_Prefix . "users_extend WHERE uid='" . $userinfo['uid'] . "'");
    list($C1, $C2, $C3, $C4, $C5, $C6, $C7, $C8, $M1, $M2, $T1, $T2, $B1) = sql_fetch_row($result);

    showimage();

    include("support/sform/extend-user/mod_extend-user.php");
    include("themes/default/footer.php");
}

function saveuser($uid, $name, $uname, $email, $femail, $url, $pass, $vpass, $bio, $user_avatar, $user_occ, $user_from, $user_intrest, $user_sig, $user_viewemail, $attach, $usend_email, $uis_visible, $user_lnl, $C1, $C2, $C3, $C4, $C5, $C6, $C7, $C8, $M1, $M2, $T1, $T2, $B1, $MAX_FILE_SIZE, $raz_avatar)
{
    global $NPDS_Prefix, $user, $userinfo;

    $cookie = cookie::cookiedecode($user);
    $check = $cookie[1];

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT uid, email FROM " . $NPDS_Prefix . "users WHERE uname='$check'");
    list($vuid, $vemail) = sql_fetch_row($result);

    if (($check == $uname) and ($uid == $vuid)) {
        if ((isset($pass)) && ("$pass" != "$vpass")) {
            message_error('<i class="fa fa-exclamation me-2"></i>' . __d('two_users', 'Les mots de passe sont différents. Ils doivent être identiques.') . '<br />', '');
        } elseif (($pass != '') && (strlen($pass) < Config::get('npds.minpass'))) {
            message_error('<i class="fa fa-exclamation me-2"></i>' . __d('two_users', 'Désolé, votre mot de passe doit faire au moins') . ' <strong>' . Config::get('npds.minpass') . '</strong> ' . __d('two_users', 'caractères') . '<br />', '');
        } else {
            $stop = userCheck('edituser', $email);

            if (!$stop) {
                $contents = '';
                $filename = "storage/users_private/usersbadmail.txt";
                $handle = fopen($filename, "r");
                
                if (filesize($filename) > 0) {
                    $contents = fread($handle, filesize($filename));
                }

                fclose($handle);

                $re = '/#' . $uid . '\|(\d+)/m';
                $maj = preg_replace($re, '', $contents);
                $file = fopen("storage/users_private/usersbadmail.txt", 'w');
                fwrite($file, $maj);
                fclose($file);

                if ($bio) {
                    $bio = str::FixQuotes(strip_tags($bio));
                }

                $t = $attach ? 1 : 0;
                $a = $user_viewemail ? 1 : 0;
                $u = $usend_email ? 1 : 0;
                $v = $uis_visible ? 0 : 1;
                $w = $user_lnl ? 1 : 0;

                include_once("modules/upload/config/upload.conf.php");

                global $avatar_size;
                if (!$avatar_size) {
                    $avatar_size = '80*100';
                }

                $avatar_limit = explode("*", $avatar_size);
                $rep = $DOCUMENTROOT != '' ? $DOCUMENTROOT : $_SERVER['DOCUMENT_ROOT'];

                if ($B1 != 'none') {
                    global $language;

                    include_once("modules/upload/language/upload.lang-$language.php");
                    include_once("modules/upload/library/clsUpload.php");

                    $upload = new Upload();
                    $upload->maxupload_size = $MAX_FILE_SIZE;
                    $field1_filename = trim($upload->getFileName("B1"));
                    $suffix = strtoLower(substr(strrchr($field1_filename, '.'), 1));

                    if (($suffix == 'gif') or ($suffix == 'jpg') or ($suffix == 'png') or ($suffix == 'jpeg')) {
                        $field1_filename = hack::removeHack(preg_replace('#[/\\\:\*\?"<>|]#i', '', rawurldecode($field1_filename)));
                        $field1_filename = preg_replace('#\.{2}|config.php|/etc#i', '', $field1_filename);

                        if ($field1_filename) {
                            if ($autorise_upload_p) {
                                $user_dir = $racine . '/storage/users_private/' . $uname . '/';

                                if (!is_dir($rep . $user_dir)) {
                                    @umask(0000);

                                    if (@mkdir($rep . $user_dir, 0777)) {
                                        $fp = fopen($rep . $user_dir . 'index.html', 'w');
                                        fclose($fp);
                                    } else {
                                        $user_dir = $racine . '/storage/users_private/';
                                    }
                                }
                            } else {
                                $user_dir = $racine . '/storage/users_private/';
                            }

                            if ($upload->saveAs($uname . '.' . $suffix, $rep . $user_dir, 'B1', true)) {
                                $old_user_avatar = $user_avatar;
                                $user_avatar = $user_dir . $uname . '.' . $suffix;
                                $img_size = @getimagesize($rep . $user_avatar);

                                if (($img_size[0] > $avatar_limit[0]) or ($img_size[1] > $avatar_limit[1])) {
                                    $raz_avatar = true;
                                }

                                if ($racine == '') {
                                    $user_avatar = substr($user_avatar, 1);
                                }
                            }
                        }
                    }
                }

                if ($raz_avatar) {
                    if (strstr($user_avatar, '/users_private')) {
                        @unlink($rep . $user_avatar);
                        @unlink($rep . $old_user_avatar);
                    }
                    $user_avatar = 'blank.gif';
                }

                if ($pass != '') {
                    cookie::cookiedecode($user);
                    $AlgoCrypt = PASSWORD_BCRYPT;
                    $min_ms = 100;
                    $options = ['cost' => users::getOptimalBcryptCostParameter($pass, $AlgoCrypt, $min_ms),];
                    $hashpass = password_hash($pass, PASSWORD_BCRYPT, $options);
                    $pass = crypt($pass, $hashpass);

                    //DB::table('')->where('', )->update(array(
                    //    ''       => ,
                    //));

                    sql_query("UPDATE " . $NPDS_Prefix . "users SET name='$name', email='$email', femail='" . hack::removeHack($femail) . "', url='" . hack::removeHack($url) . "', pass='$pass', hashkey='1', bio='" . hack::removeHack($bio) . "', user_avatar='$user_avatar', user_occ='" . hack:: removeHack($user_occ) . "', user_from='" . hack::removeHack($user_from) . "', user_intrest='" . hack::removeHack($user_intrest) . "', user_sig='" . hack::removeHack($user_sig) . "', user_viewemail='$a', send_email='$u', is_visible='$v', user_lnl='$w' WHERE uid='$uid'");
                    
                    // = DB::table('')->select()->where('', )->orderBy('')->get();
                    
                    $result = sql_query("SELECT uid, uname, pass, storynum, umode, uorder, thold, noscore, ublockon, theme FROM " . $NPDS_Prefix . "users WHERE uname='$uname' AND pass='$pass'");
                    
                    if (sql_num_rows($result) == 1) {
                        $userinfo = sql_fetch_assoc($result);
                        docookie($userinfo['uid'], $userinfo['uname'], $userinfo['pass'], $userinfo['storynum'], $userinfo['umode'], $userinfo['uorder'], $userinfo['thold'], $userinfo['noscore'], $userinfo['ublockon'], $userinfo['theme'], $userinfo['commentmax'], "", $skin);
                    }

                } else {

                    //DB::table('')->where('', )->update(array(
                    //    ''       => ,
                    //));

                    sql_query("UPDATE " . $NPDS_Prefix . "users SET name='$name', email='$email', femail='" . hack::removeHack($femail) . "', url='" . hack::removeHack($url) . "', bio='" . hack::removeHack($bio) . "', user_avatar='$user_avatar', user_occ='" . hack:: removeHack($user_occ) . "', user_from='" . hack::removeHack($user_from) . "', user_intrest='" . hack::removeHack($user_intrest) . "', user_sig='" . hack::removeHack($user_sig) . "', user_viewemail='$a', send_email='$u', is_visible='$v', user_lnl='$w' WHERE uid='$uid'");
                }

                //DB::table('')->where('', )->update(array(
                //    ''       => ,
                //));

                sql_query("UPDATE " . $NPDS_Prefix . "users_status SET attachsig='$t' WHERE uid='$uid'");

                // = DB::table('')->select()->where('', )->orderBy('')->get();

                $result = sql_query("SELECT uid FROM " . $NPDS_Prefix . "users_extend WHERE uid='$uid'");

                if (sql_num_rows($result) == 1) {

                    //DB::table('')->where('', )->update(array(
                    //    ''       => ,
                    //));

                    sql_query("UPDATE " . $NPDS_Prefix . "users_extend SET C1='" . hack::removeHack($C1) . "', C2='" . hack::removeHack($C2) . "', C3='" . hack::removeHack($C3) . "', C4='" . hack::removeHack($C4) . "', C5='" . hack::removeHack($C5) . "', C6='" . hack::removeHack($C6) . "', C7='" . hack::removeHack($C7) . "', C8='" . hack::removeHack($C8) . "', M1='" . hack::removeHack($M1) . "', M2='" . hack::removeHack($M2) . "', T1='" . hack::removeHack($T1) . "', T2='" . hack::removeHack($T2) . "', B1='$B1' WHERE uid='$uid'");
                } else {

                    // = DB::table('')->select()->where('', )->orderBy('')->get();

                    $result = sql_query("INSERT INTO " . $NPDS_Prefix . "users_extend VALUES ('$uid','" . hack::removeHack($C1) . "', '" . hack::removeHack($C2) . "', '" . hack::removeHack($C3) . "', '" . hack::removeHack($C4) . "', '" . hack::removeHack($C5) . "', '" . hack::removeHack($C6) . "', '" . hack::removeHack($C7) . "', '" . hack::removeHack($C8) . "', '" . hack::removeHack($M1) . "', '" . hack::removeHack($M2) . "', '" . hack::removeHack($T1) . "', '" . hack::removeHack($T2) . "', '$B1')");
                }

                if ($pass != '') {
                    logout();
                } else {
                    header('location: '. site_url('user.php?op=edituser'));
                }
            } else {
                message_error($stop, '');
            }
        }
    } else {
        Header('Location: '. site_url('index.php'));
    }
}

function edithome()
{
    global $user;

    include("themes/default/header.php");

    $userinfo = users::getusrinfo($user);
    users::member_menu($userinfo['mns'], $userinfo['uname']);

    $Default_Theme = Config::get('npds.Default_Theme');
    $Default_Skin = Config::get('npds.Default_Skin');

    if ($userinfo['theme'] == '') {
        $userinfo['theme'] = "$Default_Theme+$Default_Skin";
    }

    echo '
    <h2 class="mb-3">' . __d('two_users', 'Editer votre page principale') . '</h2>
    <form id="changehome" action="'. site_url('user.php') .'" method="post">
    <div class="mb-3 row">
        <label class="col-form-label col-sm-7" for="storynum">' . __d('two_users', 'Nombre d\'articles sur la page principale') . ' (max. 127) :</label>
        <div class="col-sm-5">
            <input class="form-control" type="text" min="0" max="127" id="storynum" name="storynum" maxlength="3" value="' . $userinfo['storynum'] . '" />
        </div>
    </div>';

    $sel = $userinfo['ublockon'] == 1 ? 'checked="checked"' : '';

    echo '
    <div class="mb-3 row">
        <div class="col-sm-10">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="ublockon" name="ublockon" value="1" ' . $sel . ' />
                <label class="form-check-label" for="ublockon">' . __d('two_users', 'Activer votre menu personnel') . '</label>
            </div>
        </div>
    </div>
    <ul>
        <li>' . __d('two_users', 'Validez cette option et le texte suivant apparaîtra sur votre page d\'accueil') . '</li>
        <li>' . __d('two_users', 'Vous pouvez utiliser du code html, pour créer un lien par exemple') . '</li>
    </ul>
    <div class="mb-3 row">
        <div class="col-sm-12">
            <textarea class="form-control" rows="20" name="ublock">' . $userinfo['ublock'] . '</textarea>
        </div>
    </div>
        <div class="mb-3 row">
            <input type="hidden" name="theme" value="' . $userinfo['theme'] . '" />
            <input type="hidden" name="uname" value="' . $userinfo['uname'] . '" />
            <input type="hidden" name="uid" value="' . $userinfo['uid'] . '" />
            <input type="hidden" name="op" value="savehome" />
            <div class="col-sm-12">
                <input class="btn btn-primary" type="submit" value="' . __d('two_users', 'Sauver les modifications') . '" />
            </div>
        </div>
    </form>';

    $fv_parametres = '
    storynum: {
        validators: {
            regexp: {
                regexp:/^[1-9](\d{0,2})$/,
                message: "0-9"
            },
            between: {
                min: 1,
                max: 127,
                message: "1 ... 127"
            }
        }
    },';

    $arg1 = '
    var formulid=["changehome"];';

    css::adminfoot('fv', $fv_parametres, $arg1, 'foo');
}

function savehome($uid, $uname, $theme, $storynum, $ublockon, $ublock)
{
    global $NPDS_Prefix, $user;

    $cookie = cookie::cookiedecode($user);
    $check = $cookie[1];

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT uid FROM " . $NPDS_Prefix . "users WHERE uname='$check'");
    list($vuid) = sql_fetch_row($result);

    if (($check == $uname) and ($uid == $vuid)) {
        $ublockon = $ublockon ? 1 : 0;
        $ublock = hack::removeHack(str::FixQuotes($ublock));

        //DB::table('')->where('', )->update(array(
        //    ''       => ,
        //));

        sql_query("UPDATE " . $NPDS_Prefix . "users SET storynum='$storynum', ublockon='$ublockon', ublock='$ublock' WHERE uid='$uid'");

        $userinfo = users::getusrinfo($user);

        docookie($userinfo['uid'], $userinfo['uname'], $userinfo['pass'], $userinfo['storynum'], $userinfo['umode'], $userinfo['uorder'], $userinfo['thold'], $userinfo['noscore'], $userinfo['ublockon'], $userinfo['theme'], $userinfo['commentmax'], '');
        
        // Include cache manager for purge cache Page
        $cache_obj = new cacheManager();
        $cache_obj->UsercacheCleanup();
        Header('Location: '. site_url('user.php?op=edithome'));
    } else {
        Header('Location: '. site_url('index.php'));
    }
}

function chgtheme()
{
    global $user;

    include("themes/default/header.php");

    $userinfo = users::getusrinfo($user);
    $ibid = explode('+', $userinfo['theme']);
    $theme = $ibid[0];

    if (array_key_exists(1, $ibid)) { 
        $skin = $ibid[1];
    } else {   
        $skin = '';
    }

    users::member_menu($userinfo['mns'], $userinfo['uname']);

    echo '
    <h2 class="mb-3">' . __d('two_users', 'Changer le thème') . '</h2>
    <form action="'. site_url('user.php') .'" method="post">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3 form-floating">
                <select class="form-select" id="theme_local" name="theme_local">';

    //include("themes/list.php");
    $themelist = theme::themeLists(true);
    $themelist = explode(' ', $themelist);
    $thl = sizeof($themelist);

    for ($i = 0; $i < $thl; $i++) {
        if ($themelist[$i] != '') {
            echo '<option value="' . $themelist[$i] . '" ';
            
            if ((($theme == '') && ($themelist[$i] == Config::get('npds.Default_Theme'))) || ($theme == $themelist[$i])) {
                echo 'selected="selected"';
            }

            echo '>' . $themelist[$i] . '</option>';
        }
    }

    echo '
                </select>
                <label for="theme_local">' . __d('two_users', 'Sélectionnez un thème d\'affichage') . '</label>
                </div>
                <p class="help-block mb-4">
                <span>' . __d('two_users', 'Cette option changera l\'aspect du site.') . '</span> 
                <span>' . __d('two_users', 'Les modifications seront seulement valides pour vous.') . '</span> 
                <span>' . __d('two_users', 'Chaque utilisateur peut voir le site avec un thème graphique différent.') . '</span>
                </p>';

    $handle = opendir('themes/_skins');
    while (false !== ($file = readdir($handle))) {
        if (($file[0] !== '_') 
        and (!strstr($file, '.')) 
        and (!strstr($file, 'assets')) 
        and (!strstr($file, 'fonts'))) {
            $skins[] = array('name' => $file, 
                             'description' => '', 
                             'thumbnail' => $file . '/thumbnail', 
                             'preview' => $file . '/', 
                             'css' => $file . '/bootstrap.css', 
                             'cssMin' => $file . '/bootstrap.min.css', 
                             'cssxtra' => $file . '/extra.css', 
                             'scss' => $file . '/_bootswatch.scss', 
                             'scssVariables' => $file . '/_variables.scss');
        }
    }

    closedir($handle);
    asort($skins);

    echo '<div class="mb-3 form-floating" id="skin_choice">
            <select class="form-select" id="skins" name="skins">';

    foreach ($skins as $k => $v) {
        echo '<option value="' . $skins[$k]['name'] . '" ';
        
        if ($skins[$k]['name'] == $skin) {
            echo 'selected="selected"';
        } elseif ($skin == '' and $skins[$k]['name'] == 'default') { 
            echo 'selected="selected"';
        }

        echo '>' . $skins[$k]['name'] . '</option>';
    }

    echo '
                </select>
                <label for="skins">' . __d('two_users', 'Choisir une charte graphique') . '</label>
                </div>
            </div>
            <div class="col-md-6">
                <div id="skin_thumbnail"></div>
            </div>
        </div>
        <input type="hidden" name="uname" value="' . $userinfo['uname'] . '" />
        <input type="hidden" name="uid" value="' . $userinfo['uid'] . '" />
        <input type="hidden" name="op" value="savetheme" />
        <input class="btn btn-primary my-3" type="submit" value="' . __d('two_users', 'Sauver les modifications') . '" />
    </form>
    <script type="text/javascript">
    //<![CDATA[
    $(function () {
        $("#theme_local").change(function () {
            sk = $("#theme_local option:selected").text().substr(-3);
            if(sk=="_sk") {
                $("#skin_choice").removeClass("collapse");
                $("#skins").change(function () {
                sl = $("#skins option:selected").text();
                $("#skin_thumbnail").html(\'<a href="themes/_skins/\'+sl+\'" class="btn btn-outline-primary"><img class="img-fluid img-thumbnail" src="themes/_skins/\'+sl+\'/thumbnail.png" /></a>\');
                }).change();
            } else {
                $("#skin_choice").addClass("collapse");
                $("#skin_thumbnail").html(\'\');
            }
        })
        .change();
    });
    //]]
    </script>';

    include("themes/default/footer.php");;
}

function savetheme($uid, $theme)
{
    global $NPDS_Prefix, $user;

    $cookie = cookie::cookiedecode($user);

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT uid FROM " . $NPDS_Prefix . "users WHERE uname='$cookie[1]'");

    list($vuid) = sql_fetch_row($result);
    if ($uid == $vuid) {

        //DB::table('')->where('', )->update(array(
        //    ''       => ,
        //));

        sql_query("UPDATE " . $NPDS_Prefix . "users SET theme='$theme' WHERE uid='$uid'");
        $userinfo = users::getusrinfo($user);
        
        docookie($userinfo['uid'], $userinfo['uname'], $userinfo['pass'], $userinfo['storynum'], $userinfo['umode'], $userinfo['uorder'], $userinfo['thold'], $userinfo['noscore'], $userinfo['ublockon'], $theme, $userinfo['commentmax'], '');
        
        // Include cache manager for purge cache Page
        $cache_obj = new cacheManager();
        $cache_obj->UsercacheCleanup();
        Header('Location: '. site_url('user.php'));
    } else {
        Header('Location: '. site_url('index.php'));
    }
}

function editjournal()
{
    global $user;

    include("themes/default/header.php");

    $userinfo = users::getusrinfo($user);
    users::member_menu($userinfo['mns'], $userinfo['uname']);

    echo '
    <h2 class="mb-3">' . __d('two_users', 'Editer votre journal') . '</h2>
    <form action="'. site_url('user.php') .'" method="post" name="adminForm">
        <div class="mb-3 row">
            <div class="col-sm-12">
                <textarea class="tin form-control" rows="25" name="journal">' . $userinfo['user_journal'] . '</textarea>'
        . editeur::aff_editeur('journal', '') . '
            </div>
        </div>
        <input type="hidden" name="uname" value="' . $userinfo['uname'] . '" />
        <input type="hidden" name="uid" value="' . $userinfo['uid'] . '" />
        <input type="hidden" name="op" value="savejournal" />
        <div class="mb-3 row">
            <div class="col-12">
                <div class="form-check">
                <input class="form-check-input" type="checkbox" id="datetime" name="datetime" value="1" />
                <label class="form-check-label" for="datetime">' . __d('two_users', 'Ajouter la date et l\'heure') . '</label>
                </div>
            </div>
        </div>
        <div class="mb-3 row">
            <div class="col-12">
                <input class="btn btn-primary" type="submit" value="' . __d('two_users', 'Sauvez votre journal') . '" />
            </div>
        </div>
    </form>';

    include("themes/default/footer.php");
}

function savejournal($uid, $journal, $datetime)
{
    global $NPDS_Prefix, $user;

    $cookie = cookie::cookiedecode($user);

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT uid FROM " . $NPDS_Prefix . "users WHERE uname='$cookie[1]'");
    list($vuid) = sql_fetch_row($result);

    if ($uid == $vuid) {
        include("modules/upload/config/upload.conf.php");

        if ($DOCUMENTROOT == '') {
            global $DOCUMENT_ROOT;
            $DOCUMENTROOT = ($DOCUMENT_ROOT) ? $DOCUMENT_ROOT : $_SERVER['DOCUMENT_ROOT'];
        }

        $user_dir = $DOCUMENTROOT . $racine . "/storage/users_private/" . $cookie[1];

        if (!is_dir($user_dir)) {
            mkdir($user_dir, 0777);
            $fp = fopen($user_dir . '/index.html', 'w');
            fclose($fp);
            chmod($user_dir . '/index.html', 0644);
        }

        $journal = image::dataimagetofileurl($journal, 'storage/users_private/' . $cookie[1] . '/jou'); //
        $journal = hack::removeHack(stripslashes(FixQuotes($journal)));

        if ($datetime) {
            $journalentry = $journal;
            $journalentry .= '<br /><br />';

            global $gmt;
            $journalentry .= date(__d('two_users', 'dateinternal'), time() + ((int)$gmt * 3600));

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            sql_query("UPDATE " . $NPDS_Prefix . "users SET user_journal='$journalentry' WHERE uid='$uid'");
        } else {

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            sql_query("UPDATE " . $NPDS_Prefix . "users SET user_journal='$journal' WHERE uid='$uid'");
        }

        Header('Location: '. site_url('user.php'));
    } else {
        Header('Location: '. site_url('index.php'));
    }
}

settype($op, 'string');

switch ($op) {
    case 'logout':
        logout();
        break;

    case 'new user':
        // CheckBox
        settype($user_viewemail, 'integer');
        settype($user_lnl, 'integer');
        settype($pass, 'string');
        settype($vpass, 'string');

        confirmNewUser($uname, $name, $email, $user_avatar, $user_occ, $user_from, $user_intrest, $user_sig, $user_viewemail, $pass, $vpass, $user_lnl, $C1, $C2, $C3, $C4, $C5, $C6, $C7, $C8, $M1, $M2, $T1, $T2, $B1);
        break;

    case 'finish':
        finishNewUser($uname, $name, $email, $user_avatar, $user_occ, $user_from, $user_intrest, $user_sig, $user_viewemail, $pass, $user_lnl, $C1, $C2, $C3, $C4, $C5, $C6, $C7, $C8, $M1, $M2, $T1, $T2, $B1);
        break;

    case 'forgetpassword':
        ForgetPassword();
        break;

    case "mailpasswd":
        if ($uname != '' and $code != '') {
            if (strlen($code) >= Config::get('npds.minpass')) {
                mail_password($uname, $code);
            } else {
                message_error("<i class=\"fa fa-exclamation\"></i>&nbsp;" . __d('two_users', 'Mot de passe erroné, refaites un essai.') . "<br /><br />", "");
            }
        } else {
            main($user);
        }
        break;

    case 'validpasswd':
        if ($code != '') {
            valid_password($code);
        } else {
            main($user);
        }
        break;

    case 'updatepasswd':
        if ($code != '' and $passwd != '') {
            update_password($code, $passwd);
        } else {
            main($user);
        }
        break;

    case 'userinfo':
        if (($member_list == 1) and ((!isset($user)) and (!isset($admin)))) {
            Header('Location: '. site_url('index.php'));
        }

        if ($uname != '') {
            userinfo($uname);
        } else {
            main($user);
        }
        break;

    case 'login':
        login($uname, $pass);
        break;

    case 'edituser':
        if ($user) {
            edituser();
        }  else {
            Header('Location: '. site_url('index.php'));
        }
        break;

    case 'saveuser':
        $past = time() - 300;
        
        DB::table('session')->where('time', '<', $past)->delete();

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $result = sql_query("SELECT time FROM " . $NPDS_Prefix . "session WHERE username='$cookie[1]'");
        
        if (sql_num_rows($result) == 1) {
            // CheckBox
            settype($attach, 'integer');
            settype($user_viewemail, 'integer');
            settype($usend_email, 'integer');
            settype($uis_visible, 'integer');
            settype($user_lnl, 'integer');
            settype($raz_avatar, 'integer');
            saveuser($uid, $name, $uname, $email, $femail, $url, $pass, $vpass, $bio, $user_avatar, $user_occ, $user_from, $user_intrest, $user_sig, $user_viewemail, $attach, $usend_email, $uis_visible, $user_lnl, $C1, $C2, $C3, $C4, $C5, $C6, $C7, $C8, $M1, $M2, $T1, $T2, $B1, $MAX_FILE_SIZE, $raz_avatar);
        } else {
            Header('Location: '. site_url('user.php'));
        }
        break;

    case 'edithome':
        if ($user) {
            edithome();
        } else {
            Header('Location: '. site_url('index.php'));
        }
        break;

    case 'savehome':
        settype($ublockon, 'integer');
        savehome($uid, $uname, $theme, $storynum, $ublockon, $ublock);
        break;

    case 'chgtheme':
        if ($user) {
            chgtheme();
        } else {
            Header('Location: i'. site_url('ndex.php'));
        }
        break;

    case 'savetheme':
        $theme = substr($theme_local, -3) != "_sk" ? $theme_local : $theme_local . "+" . $skins;
        savetheme($uid, $theme);
        break;

    case 'editjournal':
        if ($user) {
            editjournal();
        } else {
            Header('Location: '. site_url('index.php'));
        }
        break;

    case 'savejournal':
        settype($datetime, 'integer');
        savejournal($uid, $journal, $datetime);
        break;

    case 'only_newuser':
        global $CloseRegUser;

        if ($CloseRegUser == 0) {
            Only_NewUser();
        } else {
            include("themes/default/header.php");

            if (file_exists("storage/static/closed.txt")) {
                include("storage/static/closed.txt");
            }

            include("themes/default/footer.php");
        }
        break;

    case 'askforgroupe':
        if ($user) {
            $userdata = explode(':', base64_decode($user));

            if (!file_exists('storage/users_private/groupe/ask4group_' . $userdata[0] . '_' . $askedgroup . '_.txt')) { 
                fopen('storage/users_private/groupe/ask4group_' . $userdata[0] . '_' . $askedgroup . '_.txt', 'w');
            }

            Header('Location: '. site_url('index.php'));
        } else {
            Header('Location: '. site_url('index.php'));
        }
        break;

    default:
        if (!users::AutoReg()) {
            unset($user);
        }
        main($user);
        break;
}
