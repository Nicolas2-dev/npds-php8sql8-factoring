<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* NPDS Copyright (c) 2002-2022 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

use npds\system\assets\css;
use npds\system\auth\users;
use npds\system\forum\forum;
use npds\system\support\str;
use npds\system\theme\theme;
use npds\system\config\Config;
use npds\system\security\hack;
use npds\system\utility\crypt;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

// chatbox avec salon privatif - on utilise id pour filtrer les messages -> id = l'id du groupe au sens autorisation de NPDS (-127,-1,0,1,2...126))
settype($id, 'integer');

if ($id === '' || unserialize(crypt::decrypt($auto)) != $id) {
    die();
}

settype($repere, 'integer');
settype($aff_entetes, 'integer');
settype($connectes, 'integer');

// Savoir si le 'connecté' a le droit à ce chat ?
if (!users::autorisation($id)) {
    die();
}

global $NPDS_Prefix;

$result = sql_query("SELECT username, message, dbname, date FROM " . $NPDS_Prefix . "chatbox WHERE id='$id' AND date>'$repere' ORDER BY date ASC");

$thing = '';

if ($result) {
    include("themes/themes-dynamic/theme.php");

    while (list($username, $message, $dbname, $date_message) = sql_fetch_row($result)) {
        $thing .= "<div class='chatmessage'><div class='chatheure'>" . date(translate("Chatdate"), $date_message + ((int) config::get('app.gmt') * 3600)) . "</div>";
        
        if ($dbname == 1) {
            global $user;
            if ((!$user) and (Config::get('app.member_list') == 1) and (!$admin)) {
                $thing .= "<div class='chatnom'>$username</div>";
            } else {
                $thing .= "<div class='chatnom'><div class='float-start'> " . str_replace('"', '\"', userpopover($username, 36, 1)) . "</div> <a href='user.php?op=userinfo&amp;uname=$username' target='_blank'>$username</a></div>";
            }
        } else {
            $thing .= "<div class='chatnom'>$username</div>";
        }

        $message = forum::smilie($message);
        $chat_forbidden_words = array(
            "'\"'i" => '&quot;',
            "'OxOA'i" => '',
            "'OxOD'i" => '',
            "'\n'i" => '',
            "'\r'i" => '',
            "'\t'i" => ''
        );

        $message = preg_replace(array_keys($chat_forbidden_words), array_values($chat_forbidden_words), $message);
        $message = str_replace('"', '\"', forum::make_clickable($message));
        $thing .= "<div class='chattexte'>" . hack::removeHack($message) . "</div></div>";
        $repere = $date_message;
    }

    $thing = "\"" . $thing . "\"";
}

if ($aff_entetes == '1') {
    $meta_op = true;

    settype($Xthing, 'string');

    include("storage/meta/meta.php");

    $Xthing .= $l_meta;
    $Xthing .= str_replace("\n", "", css::import_css_javascript(theme::getTheme(), Config::get('app.language'), theme::getSkin(), basename($_SERVER['PHP_SELF']), ""));
    $Xthing .= "</head><body id='chat'>";
    $Xthing = "\"" . str_replace("'", "\'", $Xthing) . "\"";
}

$result = sql_query("SELECT DISTINCT ip FROM " . $NPDS_Prefix . "chatbox WHERE id='$id' and date >= " . (time() - (60 * 2)) . "");
$numofchatters = sql_num_rows($result);

$rafraich_connectes = 0;

if (intval($connectes) != $numofchatters) {
    $rafraich_connectes = 1;
    
    if (($numofchatters == 1) or ($numofchatters == 0)) {
        $nbre_connectes = "'" . $numofchatters . " " . str::utf8_java(html_entity_decode(translate("personne connectée."), ENT_QUOTES | ENT_HTML401, 'UTF-8')) . " GP [$id]'";
    } else {
        $nbre_connectes = "'" . $numofchatters . " " . str::utf8_java(html_entity_decode(translate("personnes connectées."), ENT_QUOTES | ENT_HTML401, 'UTF-8')) . " GP [$id]'";
    }
}

$commande = "self.location='chatrafraich.php?repere=$repere&aff_entetes=0&connectes=$numofchatters&id=$id&auto=$auto'";

include("storage/meta/meta.php");

echo "</head>\n<body id='chat'>
    <script type='text/javascript'>
    //<![CDATA[
    function scroll_messages() {
        if (typeof(scrollBy) != 'undefined') {
            parent.frames[1].scrollBy(0, 20000);
            parent.frames[1].scrollBy(0, 20000);
        }
        else if (typeof(scroll) != 'undefined') {
            parent.frames[1].scroll(0, 20000);
            parent.frames[1].scroll(0, 20000);
        }
    }

    function rafraichir() {
        $commande;
    }

    function sur_chargement() {
        setTimeout(\"rafraichir();\", 5000);";

if ($aff_entetes == "1") {
    echo "parent.frames[1].document.write($Xthing);";
}

if ($thing != "\"\"") {
    echo "parent.frames[1].document.write($thing);
                setTimeout(\"scroll_messages();\", 300);
                ";
}

if ($rafraich_connectes == 1) {
    echo "top.document.title=$nbre_connectes;";
}

echo "}
    window.onload=sur_chargement();
    //]]>
    </script>
    </body>
    </html>";
