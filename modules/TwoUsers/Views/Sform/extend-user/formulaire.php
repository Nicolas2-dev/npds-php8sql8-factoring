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
/* Dont modify this file if you dont know what you do                   */
/************************************************************************/
declare(strict_types=1);

use App\Support\assets\js;
use App\Support\theme\theme;
use npds\system\config\Config;
use npds\support\language\language;
use Npds\Support\Facades\Sform;


Sform::add_title(__d('two_users', 'Inscription'));
Sform::add_mess(__d('two_users', '* Désigne un champ obligatoire'));

Sform::add_field('uname', __d('two_users', 'ID utilisateur (pseudo)'), $uname, 'text', true, 25, '', '');
Sform::add_field('name', __d('two_users', 'Votre véritable identité'), $name, 'text', false, 60, '', '');
Sform::add_extender('name', '', '<span class="help-block"><span class="float-end" id="countcar_name"></span></span>');
Sform::add_field('email', __d('two_users', 'Véritable adresse Email'), $email, 'email', true, 60, '', '');
Sform::add_extender('email', '', '<span class="help-block">' . __d('two_users', '(Cette adresse Email ne sera pas divulguée, mais elle nous servira à vous envoyer votre Mot de Passe si vous le perdez)') . '<span class="float-end" id="countcar_email"></span></span>');
Sform::add_checkbox('user_viewemail', __d('two_users', 'Autoriser les autres utilisateurs à voir mon Email'), "1", false, false);

// ---- AVATAR
if (Config::get('npds.smilies')) {
    $theme = theme::getTheme();
    
    $direktori = "assets/images/forum/avatar";
    if (function_exists("theme_image")) {
        if (theme::theme_image("forum/avatar/blank.gif")) {
            $direktori = "themes/$theme/images/forum/avatar";
        }
    }

    $handle = opendir($direktori);
    while (false !== ($file = readdir($handle))) {
        $filelist[] = $file;
    }

    asort($filelist);
    foreach ($filelist as $key => $file) {
        if (!preg_match('#\.gif|\.jpg|\.png$#i', $file)) {
            continue;
        }

        $tmp_tempo[$file]['en'] = $file;
        $tmp_tempo[$file]['selected'] = false;

        if ($file == 'blank.gif') {
            $tmp_tempo[$file]['selected'] = true;
        }
    }

    Sform::add_select('user_avatar', __d('two_users', 'Votre Avatar'), $tmp_tempo, false, '', false);
    Sform::add_extender('user_avatar', 'onkeyup="showimage();" onchange="showimage();"', '<img class="img-thumbnail n-ava mt-3" src="' . $direktori . '/blank.gif" name="avatar" alt="avatar" />');
    Sform::add_field('B1', 'B1', '', 'hidden', false);
}
// ---- AVATAR

Sform::add_field('user_from', __d('two_users', 'Votre situation géographique'), StripSlashes($user_from), 'text', false, 100, '', '');
Sform::add_extender('user_from', '', '<span class="help-block"><span class="float-end" id="countcar_user_from"></span></span>');

Sform::add_field('user_occ', __d('two_users', 'Votre activité'), StripSlashes($user_occ), 'text', false, 100, '', '');
Sform::add_extender('user_occ', '', '<span class="help-block"><span class="float-end" id="countcar_user_occ"></span></span>');

Sform::add_field('user_intrest', __d('two_users', 'Vos centres d\'intérêt'), StripSlashes($user_intrest), 'text', false, 150, '', '');
Sform::add_extender('user_intrest', '', '<span class="help-block"><span class="float-end" id="countcar_user_intrest"></span></span>');

Sform::add_field('user_sig', __d('two_users', 'Signature'), StripSlashes($user_sig), 'textarea', false, 255, '7', '');
Sform::add_extender('user_sig', '', '<span class="help-block">' . __d('two_users', '(255 characters max. Type your signature with HTML coding)') . '<span class="float-end" id="countcar_user_sig"></span></span>');

// --- MEMBER-PASS
if ($memberpass) {
    Sform::add_field('pass', __d('two_users', 'Mot de passe'), '', 'password', true, 40, '', '');
    Sform::add_extra('<div class="mb-3 row"><div class="col-sm-8 ms-sm-auto" ><div class="progress" style="height: 0.2rem;"><div id="passwordMeter_cont" class="progress-bar bg-danger" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div></div></div></div>');
    Sform::add_field('vpass', __d('two_users', 'Entrez à nouveau votre mot de Passe'), '', 'password', true, 40, '', '');
}

// --- MEMBER-PASS

Sform::add_checkbox('user_lnl', __d('two_users', 'S\'inscrire à la liste de diffusion du site'), "1", false, true);

// --- EXTENDER
if (file_exists("modules/sform/extend-user/extender/formulaire.php")) {
    include("modules/sform/extend-user/extender/formulaire.php");
}
// --- EXTENDER

// ----------------------------------------------------------------
// CES CHAMPS sont indispensables --- Don't remove these fields
// Champ Hidden
Sform::add_field('op', '', 'new user', 'hidden', false);

// --- CHARTE du SITE
Sform::add_checkbox('charte', '<a href="' . site_url('static.php?op=charte.html') .'" target="_blank">' . __d('two_users', 'Vous devez accepter la charte d\'utilisation du site') . '</a>', "1", true, false);
// --- CHARTE du SITE

// --- CONSENTEMENT
Sform::add_checkbox('consent', language::aff_langue('[fr]En soumettant ce formulaire j\'accepte que les informations saisies soient exploit&#xE9;es dans le cadre de l\'utilisation et du fonctionnement de ce site.[/fr][en]By submitting this form, I accept that the information entered will be used in the context of the use and operation of this website.[/en][es]Al enviar este formulario, acepto que la informaci&oacute;n ingresada se utilizar&aacute; en el contexto del uso y funcionamiento de este sitio web.[/es][de]Mit dem Absenden dieses Formulars erkl&auml;re ich mich damit einverstanden, dass die eingegebenen Informationen im Rahmen der Nutzung und des Betriebs dieser Website verwendet werden.[/de][zh]&#x63D0;&#x4EA4;&#x6B64;&#x8868;&#x683C;&#x5373;&#x8868;&#x793A;&#x6211;&#x63A5;&#x53D7;&#x6240;&#x8F93;&#x5165;&#x7684;&#x4FE1;&#x606F;&#x5C06;&#x5728;&#x672C;&#x7F51;&#x7AD9;&#x7684;&#x4F7F;&#x7528;&#x548C;&#x64CD;&#x4F5C;&#x8303;&#x56F4;&#x5185;&#x4F7F;&#x7528;&#x3002;[/zh]'), "1", true, false);
// --- CONSENTEMENT

Sform::add_extra('
        <div class="mb-3 row">
            <div class="col-sm-8 ms-sm-auto" >
                <button class="btn btn-primary" type="submit">' . __d('two_users', 'Valider') . '</button>
            </div>
        </div>');

Sform::add_extra(language::aff_langue('
        <div class="mb-3 row">
            <div class="col-sm-8 ms-sm-auto small" >
    [fr]Pour conna&icirc;tre et exercer vos droits notamment de retrait de votre consentement &agrave; l\'utilisation des donn&eacute;es collect&eacute;es veuillez consulter notre <a href="' . site_url('static.php?op=politiqueconf.html&amp;npds=1&amp;metalang=1') .'">politique de confidentialit&eacute;</a>.[/fr][en]To know and exercise your rights, in particular to withdraw your consent to the use of the data collected, please consult our <a href="' . site_url('static.php?op=politiqueconf.html&amp;npds=1&amp;metalang=1') .'">privacy policy</a>.[/en][es]Para conocer y ejercer sus derechos, en particular para retirar su consentimiento para el uso de los datos recopilados, consulte nuestra <a href="' . site_url('static.php?op=politiqueconf.html&amp;npds=1&amp;metalang=1') .'">pol&iacute;tica de privacidad</a>.[/es][de]Um Ihre Rechte zu kennen und auszu&uuml;ben, insbesondere um Ihre Einwilligung zur Nutzung der erhobenen Daten zu widerrufen, konsultieren Sie bitte unsere <a href="' . site_url('static.php?op=politiqueconf.html&amp;npds=1&amp;metalang=1') .'">Datenschutzerkl&auml;rung</a>.[/de][zh]&#x8981;&#x4E86;&#x89E3;&#x5E76;&#x884C;&#x4F7F;&#x60A8;&#x7684;&#x6743;&#x5229;&#xFF0C;&#x5C24;&#x5176;&#x662F;&#x8981;&#x64A4;&#x56DE;&#x60A8;&#x5BF9;&#x6240;&#x6536;&#x96C6;&#x6570;&#x636E;&#x7684;&#x4F7F;&#x7528;&#x7684;&#x540C;&#x610F;&#xFF0C;&#x8BF7;&#x67E5;&#x9605;&#x6211;&#x4EEC;<a href="' . site_url('static.php?op=politiqueconf.html&#x26;npds=1&#x26;metalang=1') .'">&#x7684;&#x9690;&#x79C1;&#x653F;&#x7B56;</a>&#x3002;[/zh]
            </div>
        </div>'));

Sform::add_extra('
        <script type="text/javascript">
        //<![CDATA[
            $(document).ready(function() {
                inpandfieldlen("name",60);
                inpandfieldlen("email",60);
                inpandfieldlen("femail",60);
                inpandfieldlen("url",100);
                inpandfieldlen("user_from",100);
                inpandfieldlen("user_occ",100);
                inpandfieldlen("user_intrest",150);
                inpandfieldlen("bio",255);
                inpandfieldlen("user_sig",255);
                inpandfieldlen("pass",40);
                inpandfieldlen("vpass",40);
                inpandfieldlen("C2",5);
                inpandfieldlen("C1",100);
                inpandfieldlen("T1",40);
            })
        //]]>
        </script>');
/*
test encodage de l'input : btoa(input.value) dans la recherche dans tableau is ok from IE9
encodé en php dans la fonction autocomplete du mainfile ...
*/

$fv_parametres = '
            uname: {
                validators: {
                callback: {
                    message: "Ce surnom n\'est pas disponible",
                    callback: function(input) {
                        if($.inArray(btoa(input.value), aruser) !== -1)
                            return false;
                        else
                            return true;
                    }
                }
                }
            },
            pass: {
                validators: {
                checkPassword: {
                    message: "Le mot de passe est trop simple."
                },
                }
            },
            vpass: {
                validators: {
                    identical: {
                    compare: function() {
                    return register.querySelector(\'[name="pass"]\').value;
                    },
                    }
                }
            },
            ' . $ch_lat . ': {
                validators: {
                regexp: {
                    regexp: /^[-]?([1-8]?\d(\.\d+)?|90(\.0+)?)$/,
                    message: "La latitude doit être entre -90.0 and 90.0"
                },
                numeric: {
                    thousandsSeparator: "",
                    decimalSeparator: "."
                },
                between: {
                    min: -90,
                    max: 90,
                    message: "La latitude doit être entre -90.0 and 90.0"
                }
                }
            },
            ' . $ch_lon . ': {
                validators: {
                regexp: {
                    regexp: /^[-]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/,
                    message: "La longitude doit être entre -180.0 and 180.0"
                },
                numeric: {
                    thousandsSeparator: "",
                    decimalSeparator: "."
                },
                between: {
                    min: -180,
                    max: 180,
                    message: "La longitude doit être entre -180.0 and 180.0"
                }
                }
            },
            !###!
            register.querySelector(\'[name="pass"]\').addEventListener("input", function() {
                fvitem.revalidateField("vpass");
            });
            flatpickr("#T1", {
                altInput: true,
                altFormat: "l j F Y",
                maxDate:"today",
                minDate:"' . date("Y-m-d", (time() - 3784320000)) . '",
                dateFormat:"d/m/Y",
                "locale": "' . language::language_iso(1, '', '') . '",
            });
            ';

$arg1 = 'var formulid = ["register"];
            ' . js::auto_complete('aruser', 'uname', 'users', '', 0);
