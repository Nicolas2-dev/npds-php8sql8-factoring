<?php

/************************************************************************/
/* SFORM Extender for NPDS USER                                         */
/* ===========================                                          */
/* NPDS Copyright (c) 2002-2022 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
/* Dont modify this file if you dont know what you do                   */
/************************************************************************/
//declare(strict_types=1);

use App\Support\Assets\Css;
use App\Support\Ttheme\Theme;
use App\Support\Mail\Mailler;
use App\Support\Language\Language;
use Npds\Config\Config;
use Npds\Support\Facades\DB;
use Npds\Support\Facades\Sform;


Sform::add_title(__d('two_users', 'Utilisateur'));
Sform::add_mess(__d('two_users', '* Désigne un champ obligatoire'));
//Sform::add_form_field_size(50);

Sform::add_field('name', __d('two_users', 'Votre véritable identité') . ' ' . __d('two_users', '(optionnel)'), $userinfo['name'], 'text', false, 60, '', '');
Sform::add_extender('name', '', '<span class="help-block"><span class="float-end" id="countcar_name"></span></span>');

Sform::add_field('email', __d('two_users', 'Véritable adresse Email'), $userinfo['email'], 'email', true, 60, '', '');
Sform::add_extender('email', '', '<span class="help-block">' . __d('two_users', '(Cette adresse Email ne sera pas divulguée, mais elle nous servira à vous envoyer votre Mot de Passe si vous le perdez)') . '<span class="float-end" id="countcar_email"></span></span>');

Sform::add_field('femail', __d('two_users', 'Votre adresse mèl \'truquée\''), $userinfo['femail'], 'email', false, 60, "", "");
Sform::add_extender('femail', '', '<span class="help-block">' . __d('two_users', '(Cette adresse Email sera publique. Vous pouvez saisir ce que vous voulez mais attention au Spam)') . '<span class="float-end" id="countcar_femail"></span></span>');

if ($userinfo['user_viewemail']) {
    $checked = true;
} else {
    $checked = false;
}

Sform::add_checkbox('user_viewemail', __d('two_users', 'Autoriser les autres utilisateurs à voir mon Email'), 1, false, $checked);

Sform::add_field('url', __d('two_users', 'Votre page Web'), $userinfo['url'], 'url', false, 100, '', '');
Sform::add_extender('url', '', '<span class="help-block"><span class="float-end" id="countcar_url"></span></span>');

// ---- SUBSCRIBE and INVISIBLE

if (Config::get('npds.subscribe')) {
    //proto
    if (Mailler::isbadmailuser($userinfo['uid']) === false) { 
        if ($userinfo['send_email'] == 1) {
            $checked = true;
        } else {
            $checked = false;
        }
        Sform::add_checkbox('usend_email', __d('two_users', 'M\'envoyer un Email lorsqu\'un message interne arrive'), 1, false, $checked);
    }
}   

if (Config::get('npds.member_invisible')) {
    if ($userinfo['is_visible'] == 1) {
        $checked = false;
    } else {
        $checked = true;
    }
    Sform::add_checkbox('uis_visible', __d('two_users', 'Membre invisible') . " (" . __d('two_users', 'pas affiché dans l\'annuaire, message à un membre, ...') . ")", 1, false, $checked);
}
// ---- SUBSCRIBE and INVISIBLE

// LNL
if (mailler::isbadmailuser($userinfo['uid']) === false) { //proto
    if ($userinfo['user_lnl']) {
        $checked = true;
    } else {
        $checked = false;
    }
    Sform::add_checkbox('user_lnl', __d('two_users', 'S\'inscrire à la liste de diffusion du site'), 1, false, $checked);
}
// LNL

// ---- AVATAR
if (Config::get('npds.smilies')) {
    
    if (stristr($userinfo['user_avatar'], "users_private")) {
        Sform::add_field('user_avatar', __d('two_users', 'Votre Avatar'), $userinfo['user_avatar'], 'show-hidden', false, 30, '', '');
        Sform::add_extender('user_avatar', '', '<img class="img-thumbnail n-ava" src="' . $userinfo['user_avatar'] . '" name="avatar" alt="avatar" /><span class="ava-meca lead"><i class="fa fa-angle-right fa-lg text-muted mx-3"></i></span><img class="ava-meca img-thumbnail n-ava" id="ava_perso" src="#" alt="Your next avatar" />
    ');

    } else {
        $theme = Theme::getTheme();;
        
        $direktori = "assets/images/forum/avatar";
        if (function_exists("theme_image")) {
            if (Theme::theme_image("forum/avatar/blank.gif")) {
                $direktori = "themes/$theme/images/forum/avatar";
            }
        }

        $handle = opendir($direktori);
        while (false !== ($file = readdir($handle))) {
            $filelist[] = $file;
        }
        asort($filelist);

        foreach ($filelist as $key => $file) {
            if (!preg_match('#\.gif|\.jpg|\.jpeg|\.png$#i', $file)) {
                continue;
            }

            $tmp_tempo[$file]['en'] = $file;
            
            if ($userinfo['user_avatar'] == $file) {
                $tmp_tempo[$file]['selected'] = true;
            } else {
                $tmp_tempo[$file]['selected'] = false;
            }
        }

        Sform::add_select('user_avatar', __d('two_users', 'Votre Avatar'), $tmp_tempo, false, '', false);
        Sform::add_extender('user_avatar', 'onkeyup="showimage();$(\'#avatar,#tonewavatar\').show();" onchange="showimage();$(\'#avatar,#tonewavatar\').show();"', '<div class="help-block"><img class="img-thumbnail n-ava" src="' . $direktori . '/' . $userinfo['user_avatar'] . '" align="top" title="" /><span id="tonewavatar" class="lead"><i class="fa fa-angle-right fa-lg text-muted mx-3"></i></span><img class="img-thumbnail n-ava " src="' . $direktori . '/' . $userinfo['user_avatar'] . '" name="avatar" id="avatar" align="top" title="Your next avatar" data-bs-placement="right" data-bs-toggle="tooltip" /><span class="ava-meca lead"><i class="fa fa-angle-right fa-lg text-muted mx-3"></i></span><img class="ava-meca img-thumbnail n-ava" id="ava_perso" src="#" alt="your next avatar" title="Your next avatar" data-bs-placement="right" data-bs-toggle="tooltip" /></div>');
    }

    // Permet à l'utilisateur de télécharger un avatar (photo) personnel
    // - si vous mettez un // devant les deux lignes B1 et raz_avatar celà équivaut à ne pas autoriser cette fonction de NPDS
    // - le champ B1 est impératif ! La taille maxi du fichier téléchargeable peut-être changée (le dernier paramètre) et est en octets (par exemple 20480 = 20 Ko)
    // - on a une incohérence la dimension de l'image est fixé dans les préférences du site et son poids ici....

    $taille_fichier = 81920;

    if (!Config::get('npds.avatar_size')) {
        Config::set('npds.avatar_size', '80*100');
    }

    $avatar_wh = explode('*', Config::get('npds.avatar_size'));
    Sform::add_upload('B1', '', 30, $taille_fichier);
    Sform::add_extender('B1', '', '<span class="help-block text-end">Taille maximum du fichier image :&nbsp;=>&nbsp;<strong>' . $taille_fichier . '</strong> octets et <strong>' . Config::get('npds.avatar_size') . '</strong> pixels</span>');
    
    Sform::add_extra('<div id="avatarPreview" class="preview"></div>');
    Sform::add_checkbox('raz_avatar', __d('two_users', 'Revenir aux avatars standards'), 1, false, false);
    // ----------------------------------------------------------------------------------------------
}
// ---- AVATAR

Sform::add_field('user_from', __d('two_users', 'Votre situation géographique'), $userinfo['user_from'], 'text', false, 100, '', '');
Sform::add_extender('user_from', '', '<span class="help-block text-end" id="countcar_user_from"></span>');

Sform::add_field('user_occ', __d('two_users', 'Votre activité'), $userinfo['user_occ'], 'text', false, 100, '', '');
Sform::add_extender('user_occ', '', '<span class="help-block text-end" id="countcar_user_occ"></span>');

Sform::add_field('user_intrest', __d('two_users', 'Vos centres d\'intérêt'), $userinfo['user_intrest'], 'text', false, 150, '', '');
Sform::add_extender('user_intrest', '', '<span class="help-block text-end" id="countcar_user_intrest"></span>');

// ---- SIGNATURE


$users_status = DB::table('users_status')->select('attachsig')->where('uid', $userinfo['uid'])->first();

if ($users_status['attachsig'] == 1) {
    $checked = true;
} else {
    $checked = false;
}

Sform::add_checkbox('attach', __d('two_users', 'Afficher la signature'), 1, false, $checked);
Sform::add_field('user_sig', __d('two_users', 'Signature'), $userinfo['user_sig'], 'textarea', false, 255, 4, '', '');
Sform::add_extender('user_sig', '', '<span class="help-block">' . __d('two_users', '(255 caractères max. Entrez votre signature (mise en forme html))') . '<span class="float-end" id="countcar_user_sig"></span></span>');
// ---- SIGNATURE

Sform::add_field('bio', __d('two_users', 'Informations supplémentaires'), $userinfo['bio'], 'textarea', false, 255, 4, '', '');
Sform::add_extender('bio', '', '<span class="help-block">' . __d('two_users', '(255 caractères max). Précisez qui vous êtes, ou votre identification sur ce site)') . '<span class="float-end" id="countcar_bio"></span></span>');

Sform::add_field('pass', __d('two_users', 'Mot de passe'), '', 'password', false, 40, '', '');
Sform::add_extra('<div class="mb-3 row"><div class="col-sm-8 ms-sm-auto" ><div class="progress" style="height: 0.2rem;"><div id="passwordMeter_cont" class="progress-bar bg-danger" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div></div></div></div>');
Sform::add_extender('pass', '', '<span class="help-block text-end" id="countcar_pass"></span>');

Sform::add_field('vpass', __d('two_users', 'Entrez à nouveau votre mot de Passe'), '', 'password', false, 40, '', '');
Sform::add_extender('vpass', '', '<span class="help-block text-end" id="countcar_vpass"></span>');


// --- EXTENDER
if (file_exists("support/sform/extend-user/extender/formulaire.php")) {
    include("support/sform/extend-user/extender/formulaire.php");
}
// --- EXTENDER

// ----------------------------------------------------------------
// CES CHAMPS sont indispensables --- Don't remove these fields
// Champ Hidden
Sform::add_field('op', '', 'saveuser', 'hidden', false);
Sform::add_field('uname', '', $userinfo['uname'], 'hidden', false);
Sform::add_field('uid', '', $userinfo['uid'], 'hidden', false);

include_once('modules/geoloc/config/geoloc.conf');

// --- CONSENTEMENT
Sform::add_checkbox('consent', language::aff_langue('[fr]En soumettant ce formulaire j\'accepte que les informations saisies soient exploit&#xE9;es dans le cadre de l\'utilisation et du fonctionnement de ce site.[/fr][en]By submitting this form, I accept that the information entered will be used in the context of the use and operation of this website.[/en][es]Al enviar este formulario, acepto que la informaci&oacute;n ingresada se utilizar&aacute; en el contexto del uso y funcionamiento de este sitio web.[/es][de]Mit dem Absenden dieses Formulars erkl&auml;re ich mich damit einverstanden, dass die eingegebenen Informationen im Rahmen der Nutzung und des Betriebs dieser Website verwendet werden.[/de][zh]&#x63D0;&#x4EA4;&#x6B64;&#x8868;&#x683C;&#x5373;&#x8868;&#x793A;&#x6211;&#x63A5;&#x53D7;&#x6240;&#x8F93;&#x5165;&#x7684;&#x4FE1;&#x606F;&#x5C06;&#x5728;&#x672C;&#x7F51;&#x7AD9;&#x7684;&#x4F7F;&#x7528;&#x548C;&#x64CD;&#x4F5C;&#x8303;&#x56F4;&#x5185;&#x4F7F;&#x7528;&#x3002;[/zh]'), "1", true, false);
// --- CONSENTEMENT

Sform::add_extra('
        <div class="mb-3 row">
            <div class="col-sm-8 ms-sm-auto" >
                <button type="submit" class="btn btn-primary">' . __d('two_users', 'Valider') . '</button>
            </div>
        </div>
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
                inpandfieldlen("C2",40);
                inpandfieldlen("C1",100);
                inpandfieldlen("T1",40);
            });
            $(".ava-meca, #avatar, #tonewavatar").hide();
            function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $("#ava_perso").attr("src", e.target.result);
                    $(".ava-meca").show();
                }
                }
                reader.readAsDataURL(input.files[0]);
            }

            $("#B1").change(function() {
                readURL(this);
                $("#user_avatar option[value=\'' . $userinfo['user_avatar'] . '\']").prop("selected", true);
                $("#user_avatar").prop("disabled", "disabled");
                $("#avatar,#tonewavatar").hide();
                $("#avava .fv-plugins-message-container").removeClass("d-none").addClass("d-block");
            });

            window.reset2 = function (e,f) {
                e.wrap("<form>").closest("form").get(0).reset();
                e.unwrap();
                event.preventDefault();
                $("#B1").removeClass("is-valid is-invalid");
                $("#user_avatar option[value=\'' . $userinfo['user_avatar'] . '\']").prop("selected", true);
                $("#user_avatar").prop("disabled", false);
                $("#avava").removeClass("fv-plugins-icon-container has-success");
                $(".ava-meca").hide();
                $("#avava .fv-plugins-message-container").addClass("d-none").removeClass("d-block");
            };

        //]]>
        </script>
        ');

Sform::add_extra(Language::aff_langue('
        <div class="mb-3 row">
            <div class="col-sm-8 ms-sm-auto small" >
    [fr]Pour conna&icirc;tre et exercer vos droits notamment de retrait de votre consentement &agrave; l\'utilisation des donn&eacute;es collect&eacute;es veuillez consulter notre <a href="' . site_url('static.php?op=politiqueconf.html&amp;npds=1&amp;metalang=1') .'">politique de confidentialit&eacute;</a>.[/fr][en]To know and exercise your rights, in particular to withdraw your consent to the use of the data collected, please consult our <a href="' . site_url('static.php?op=politiqueconf.html&amp;npds=1&amp;metalang=1') .'">privacy policy</a>.[/en][es]Para conocer y ejercer sus derechos, en particular para retirar su consentimiento para el uso de los datos recopilados, consulte nuestra <a href="' . site_url('static.php?op=politiqueconf.html&amp;npds=1&amp;metalang=1') .'">pol&iacute;tica de privacidad</a>.[/es][de]Um Ihre Rechte zu kennen und auszu&uuml;ben, insbesondere um Ihre Einwilligung zur Nutzung der erhobenen Daten zu widerrufen, konsultieren Sie bitte unsere <a href="' . site_url('static.php?op=politiqueconf.html&amp;npds=1&amp;metalang=1') .'">Datenschutzerkl&auml;rung</a>.[/de][zh]&#x8981;&#x4E86;&#x89E3;&#x5E76;&#x884C;&#x4F7F;&#x60A8;&#x7684;&#x6743;&#x5229;&#xFF0C;&#x5C24;&#x5176;&#x662F;&#x8981;&#x64A4;&#x56DE;&#x60A8;&#x5BF9;&#x6240;&#x6536;&#x96C6;&#x6570;&#x636E;&#x7684;&#x4F7F;&#x7528;&#x7684;&#x540C;&#x610F;&#xFF0C;&#x8BF7;&#x67E5;&#x9605;&#x6211;&#x4EEC;<a href="' . site_url('static.php?op=politiqueconf.html&#x26;npds=1&#x26;metalang=1') .'">&#x7684;&#x9690;&#x79C1;&#x653F;&#x7B56;</a>&#x3002;[/zh]
            </div>
        </div>'));

$arg1 = 'var formulid = ["register"];';

$fv_parametres = '

        B1: {
            validators: {
                file: {
                    extension: "jpeg,jpg,png,gif",
                    type: "image/jpeg,image/png,image/gif",
                    maxSize: ' . $taille_fichier . ',
                    message: "Type ou/et poids ou/et extension de fichier incorrect"
                },
                promise: {
                    promise: function (input) {
                        return new Promise(function(resolve, reject) {
                            const files = input.element.files
                            if (!files.length || typeof FileReader === "undefined") {
                                resolve({
                                    valid: true
                                });
                            }
                            const img = new Image();
                            img.addEventListener("load", function() {
                                const w = this.width;
                                const h = this.height;

                                resolve({
                                    valid: (w <= ' . $avatar_wh[0] . ' && h <= ' . $avatar_wh[1] . '),
                                    message: "Dimension(s) incorrecte(s) largeur > ' . $avatar_wh[0] . ' px ou/et hauteur > ' . $avatar_wh[1] . ' px !",
                                    meta: {
                                        source: img.src,    // We will use it later to show the preview
                                        width: w,
                                        height: h,
                                    },
                                });
                            });
                            img.addEventListener("error", function() {
                                reject({
                                    valid: false,
                                    message: "Please choose an image",
                                });
                            });
                            const reader = new FileReader();
                            reader.readAsDataURL(files[0]);
                            reader.addEventListener("loadend", function(e) {
                                img.src = e.target.result;
                            });
                        });
                    }
                },
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
                "locale": "' . Language::language_iso(1, '', '') . '",
            });
            ';

Sform::add_extra(Css::adminfoot('fv', $fv_parametres, $arg1, '1'));
