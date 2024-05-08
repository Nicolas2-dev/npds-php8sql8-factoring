<?php

/************************************************************************/
/* SFORM Extender for NPDS USER                                         */
/* ===========================                                          */
/* NPDS Copyright (c) 2002-2020 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
/* Dont modify this file if you dont know what you make                 */
/************************************************************************/
declare(strict_types=1);

use App\Support\Theme\Theme;
use Npds\Support\Facades\Sform;


Sform::add_form_field_size(50);

settype($op, 'string');

if ($op != 'userinfo') {
    $theme = Theme::getTheme();
    
    $direktori = "assets/images/forum/avatar";
    if (function_exists("theme_image")) {
        if (Theme::theme_image("forum/avatar/blank.gif")) {
            $direktori = "themes/$theme/images/forum/avatar";
        }
    }

    Sform::add_extra('<img class="img-thumbnail n-ava mb-2" src="' . $direktori . '/' . $user_avatar . '" align="top" title="" />');
}

if (($op == 'userinfo') and ($user)) {
    global $act_uname;
    $act_uname = "<a href='". site_url('powerpack.php?op=instant_message&amp;to_userid='.$uname) ."' title='" . __d('two_users', 'Envoyer un message interne') . "'>$uname</a>";
    
    Sform::add_field('act_uname', __d('two_users', 'ID utilisateur (pseudo)'), $act_uname, 'text', true, 25, '', '');
} else {
    Sform::add_field('uname', __d('two_users', 'ID utilisateur (pseudo)'), $uname, 'text', true, 25, '', '');
}

if ($name != '') {
    Sform::add_field('name', __d('two_users', 'Identité'), $name, 'text', false, 60, '', '');
}

if ($email != '') {
    Sform::add_field('email', __d('two_users', 'Véritable adresse Email'), $email, 'text', true, 60, '', '');
}

// if ($user_viewemail===1) {
//     $checked = true;
// } else {
//     $checked = false;
// }
// Sform::add_checkbox('user_viewemail',__d('two_users', 'Allow other users to view my email address'), 1, false, $checked);


settype($url, 'string');

if ($url != '') {
    $url = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
    Sform::add_field('url',  __d('two_users', 'Page d\'accueil'), $url, 'text', false, 100, '', '');
}

if ($user_from != '') {
    Sform::add_field('user_from', __d('two_users', 'Localisation'), $user_from, 'text', false, 100, '', '');
}

if ($user_occ != '') {
    Sform::add_field('user_occ', __d('two_users', 'Votre activité'), $user_occ, 'text', false, 100, '', '');
}

if ($user_intrest != '') {
    Sform::add_field('user_intrest', __d('two_users', 'Centres d\'interêt'), $user_intrest, 'text', false, 150, '', '');
}

if ($op == 'userinfo' and $bio != '') {
    Sform::add_field('bio', __d('two_users', 'Informations supplémentaires'), $bio, 'textarea', false, 255, 7, '', '');
}

if ($op != "userinfo") {
    if ($user_sig != '') {
        Sform::add_field('user_sig', __d('two_users', 'Signature'), StripSlashes($user_sig), 'textarea', false, 255, '', '');
    }
}


// !!! à revoir !! pour prise en compte du champ choisi dans user_extend
settype($C7, 'float');
settype($C8, 'float');

if ($C7 != '') {
    Sform::add_field('C7', 'Latitude', $C7, 'text', false, 100, '', '', '');
}

if ($C8 != '') {
    Sform::add_field('C8', 'Longitude', $C8, 'text', false, 100, '', '', '');
}

// !!! à revoir !! pour prise en compte du champ choisi dans user_extend
