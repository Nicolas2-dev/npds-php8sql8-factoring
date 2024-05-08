<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* NPDS Copyright (c) 2002-2023 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\support\chat\chat;
use npds\support\assets\css;
use npds\support\auth\users;
use npds\support\forum\forum;
use npds\support\theme\theme;
use npds\system\config\Config;
use npds\support\utility\crypt;
use npds\system\support\facades\Request;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

// chatbox avec salon privatif - on utilise id pour filtrer les messages -> id = l'id du groupe au sens autorisation de NPDS (-127,-1,0,1,2...126))
$id = Request::input('id'); 
$auto = Request::input('auto');

if ($id === '' || unserialize(crypt::decrypt($auto)) != $id) { 
    die();
}

// Savoir si le 'connecté' a le droit à ce chat ?
// le problème c'est que tous les groupes qui existent on le droit au chat ... donc il faut trouver une solution pour pouvoir l'interdire
// soit on vient d'un bloc qui par définition autorise en fabricant l'interface
// soit on viens de WS et là ....

if (!users::autorisation($id)) {
    die();
}

Config::set('npds.Titlesitename', 'NPDS Chat');
include("storage/meta/meta.php");

echo css::import_css(theme::getTheme(), Config::get('npds.language'), theme::getSkin(), basename($_SERVER['PHP_SELF']), '');

include("assets/formhelp.java.php");
    
$cookie = users::cookieUser(1);

if (!isset($cookie)) {
    $name = Request::input('name');
    
    $pseudo = ((isset($name)) ? ($name) : urldecode(Request::getIp()));
} else {
    $pseudo = $cookie;
}

// cookie chat_info (1 par groupe)
echo '</head>
    <script type="text/javascript" src="assets/js/cookies.js"></script>
    <body id="chat" onload="setCookie(\'chat_info_'.$id.'\', \'1\', \'\');" onUnload="deleteCookie(\'chat_info_'.$id.'\');">
        <script type="text/javascript" src="assets/js/jquery.min.js"></script>
        <script type="text/javascript" src="assets/shared/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
        <link rel="stylesheet" href="assets/shared/font-awesome/css/all.min.css">
        <form name="coolsus" action="'. site_url('chatinput.php') .'" method="post">
        <input type="hidden" name="op" value="set" />
        <input type="hidden" name="id" value="' . $id . '" />
        <input type="hidden" name="auto" value="' . $auto . '" />
        '. __d('two_chat', 'Vous êtes connecté en tant que :') .' <strong>' . $pseudo . '</strong>&nbsp;
        <input type="hidden" name="name" value="' . $pseudo . '" />
        <textarea id="chatarea" class="form-control my-3" type="text" rows="2" name="message" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" onfocus="storeForm(this)" placeholder="🖋"></textarea>
            <div class="float-end">';
                forum::putitems("chatarea");
echo '
            </div>
            <input class="btn btn-primary btn-sm" type="submit" tabindex="1" value="' . __d('two_chat', 'Valider') . '" />
        </form>
            <script src="assets/js/npds_adapt.js"></script>
            <script type="text/javascript">
            //<![CDATA[
                document.coolsus.message.focus();
            //]]>
        </script>
    </body>
</html>';

switch (Request::input('op')) {
    case 'set':
        chat::insertChat();
        break;
}
