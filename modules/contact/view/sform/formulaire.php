<?php
/************************************************************************/
/* SFORM Extender since NPDS SABLE Contact Example                      */
/* ===========================                                          */
/* NPDS Copyright (c) 2002-2022 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

settype($nom,'string');
settype($ad1,'string');
settype($ville,'string');
settype($dpt,'string');
settype($cpt,'string');
settype($tel,'string');
settype($fax,'string');
settype($mob,'string');
settype($email,'string');
settype($act,'string');
settype($des,'string');
settype($subok,'string');
global $nuke_url;

$m->add_title("[fr]Contactez-nous[/fr][en]Contact us[/en][es]Cont&aacute;ctenos[/es][de]Melden Sie sich[/de][zh]&#x8054;&#x7CFB;&#x6211;&#x4EEC;[/zh]");
$m->add_field('nom', "[fr]Nom / Raison Sociale[/fr][en]Name/Corporate name[/en][es]Nombre/ Raz&oacute;n Social[/es][de]Name / Name der Firma oder Organisation[/de][zh]&#x540D;&#x79F0;/&#x516C;&#x53F8;&#x540D;&#x79F0;[/zh]",$nom,'text',true,150,'','');
$m->add_extender('nom', '', '<span class="help-block text-end" id="countcar_nom"></span>');
$m->add_field('ad1', "[fr]Adresse[/fr][en]Address[/en][es]Direcci&oacute;n[/es][de]Anschrift[/de][zh]&#x5730;&#x5740;[/zh]",$ad1,'text',true,150,'','');
$m->add_extender('ad1', '', '<span class="help-block text-end" id="countcar_ad1"></span>');
$m->add_field('ville', "[fr]Ville[/fr][en]City[/en][es]Ciudad[/es][de]Stadt[/de][zh]&#x57CE;&#x5E02;[/zh]",$ville,'text',false,150,'','');
$m->add_extender('ville', '', '<span class="help-block text-end" id="countcar_ville"></span>');
$m->add_field('dpt', "[fr]D&#xE9;partement[/fr][en]Department[/en][es]Provincia[/es][de]Department[/de][zh]&#x5730;&#x533A;[/zh]",$dpt,'text',true,50,'','');
$m->add_extender('dpt', '', '<span class="help-block text-end" id="countcar_dpt"></span>');
$m->add_field('cpt', "[fr]Code Postal[/fr][en]Postal code[/en][es]C&oacute;digo postal[/es][de]Postleitzahl[/de][zh]&#x90AE;&#x7F16;[/zh]",$cpt,'text',true,5,'','');
$m->add_extender('cpt', '', '<span class="help-block text-end" id="countcar_cpt"></span>');
$m->add_field('tel', "[fr]Tel[/fr][en]Phone[/en][es]Tel&eacute;fono[/es][de]Telefon[/de][zh]&#x7535;&#x8BDD;[/zh]",$tel,'text',true,25,'',"0-9extend");
$m->add_extender('tel', '', '<span class="help-block text-end" id="countcar_tel"></span>');
$m->add_field('fax', "[fr]Fax[/fr][en]Fax[/en][es]Fax[/es][de]Fax[/de][zh]Fax[/zh]",$fax,'text',false,25,'',"0-9extend");
$m->add_extender('fax', '', '<span class="help-block text-end" id="countcar_fax"></span>');
$m->add_field('mob', "[fr]Mobile[/fr][en]Gsm[/en][es]Celular[/es][de]Gsm[/de][zh]&#x624B;&#x673A;[/zh]",$mob,'text',false,25,'',"0-9extend");
$m->add_extender('mob', '', '<span class="help-block text-end" id="countcar_mob"></span>');
$m->add_field('email', "[fr]Adresse de messagerie[/fr][en]Email address[/en][es]Direcci&oacute;n de Email[/es][de]E-Mail-Adresse[/de][zh]&#x7535;&#x5B50;&#x90AE;&#x4EF6;&#x5730;&#x5740;[/zh]",$email,'text',true,255,'','email');
$m->add_extender('email', '', '<span class="help-block text-end" id="countcar_email"></span>');
$m->add_field('act', "[fr]Activit&#xE9;[/fr][en]Activity[/en][es]Actividad[/es][de]T&auml;tigkeit[/de][zh]&#x6D3B;&#x52A8;[/zh]",$act,'text',true,150,'','');
$m->add_extender('act', '', '<span class="help-block text-end" id="countcar_act"></span>');
$m->add_field('des', "[fr]Description de votre demande[/fr][en]Your request[/en][es]Descripci&oacute;n de su solicitud[/es][de]Beschreibung Ihres Antrags[/de][zh]&#x5E94;&#x7528;&#x7A0B;&#x5E8F;&#x7684;&#x8BF4;&#x660E;[/zh]",$des,'textarea',true,430,10,'');
$m->add_extender('des', '', '<span class="help-block text-end" id="countcar_des"></span>');

// ----------------------------------------------------------------
// CES CHAMPS sont indispensables --- Don't remove these fields
// Anti-Spam
$m->add_Qspam();

// --- CONSENTEMENT
$m->add_checkbox('consent',aff_langue('[fr]En soumettant ce formulaire j\'accepte que les informations saisies soient exploit&#xE9;es dans le cadre de l\'utilisation et du fonctionnement de ce site.[/fr][en]By submitting this form, I accept that the information entered will be used in the context of the use and operation of this website.[/en][es]Al enviar este formulario, acepto que la informaci&oacute;n ingresada se utilizar&aacute; en el contexto del uso y funcionamiento de este sitio web.[/es][de]Mit dem Absenden dieses Formulars erkl&auml;re ich mich damit einverstanden, dass die eingegebenen Informationen im Rahmen der Nutzung und des Betriebs dieser Website verwendet werden.[/de][zh]&#x63D0;&#x4EA4;&#x6B64;&#x8868;&#x683C;&#x5373;&#x8868;&#x793A;&#x6211;&#x63A5;&#x53D7;&#x6240;&#x8F93;&#x5165;&#x7684;&#x4FE1;&#x606F;&#x5C06;&#x5728;&#x672C;&#x7F51;&#x7AD9;&#x7684;&#x4F7F;&#x7528;&#x548C;&#x64CD;&#x4F5C;&#x8303;&#x56F4;&#x5185;&#x4F7F;&#x7528;&#x3002;[/zh]'), "1", true, false);
// --- CONSENTEMENT
$m->add_extra('
      <div class="mb-3 row">
         <div class="col-sm-8 ms-sm-auto" >');
$m->add_field('reset','',translate("Annuler"),'reset',false);
$m->add_extra('&nbsp;');
$m->add_field('','',"[fr]Soumettre[/fr][en]Submit[/en][es]Enviar[/es][de]Sendet[/de][zh]&#x53D1;&#x9001;[/zh]",'submit',false);
$m->add_extra('
         </div>
      </div>');
$m->add_extra(aff_langue('
      <div class="mb-3 row">
         <div class="col-sm-8 ms-sm-auto small" >
[fr]Pour conna&icirc;tre et exercer vos droits notamment de retrait de votre consentement &agrave; l\'utilisation des donn&eacute;es collect&eacute;es veuillez consulter notre <a href="'.$nuke_url.'/static.php?op=politiqueconf.html&amp;npds=1&amp;metalang=1">politique de confidentialit&eacute;</a>.[/fr][en]To know and exercise your rights, in particular to withdraw your consent to the use of the data collected, please consult our <a href="'.$nuke_url.'/static.php?op=politiqueconf.html&amp;npds=1&amp;metalang=1">privacy policy</a>.[/en][es]Para conocer y ejercer sus derechos, en particular para retirar su consentimiento para el uso de los datos recopilados, consulte nuestra <a href="'.$nuke_url.'/static.php?op=politiqueconf.html&amp;npds=1&amp;metalang=1">pol&iacute;tica de privacidad</a>.[/es][de]Um Ihre Rechte zu kennen und auszu&uuml;ben, insbesondere um Ihre Einwilligung zur Nutzung der erhobenen Daten zu widerrufen, konsultieren Sie bitte unsere <a href="'.$nuke_url.'/static.php?op=politiqueconf.html&amp;npds=1&amp;metalang=1">Datenschutzerkl&auml;rung</a>.[/de][zh]&#x8981;&#x4E86;&#x89E3;&#x5E76;&#x884C;&#x4F7F;&#x60A8;&#x7684;&#x6743;&#x5229;&#xFF0C;&#x5C24;&#x5176;&#x662F;&#x8981;&#x64A4;&#x56DE;&#x60A8;&#x5BF9;&#x6240;&#x6536;&#x96C6;&#x6570;&#x636E;&#x7684;&#x4F7F;&#x7528;&#x7684;&#x540C;&#x610F;&#xFF0C;&#x8BF7;&#x67E5;&#x9605;&#x6211;&#x4EEC;<a href="'.$nuke_url.'/static.php?op=politiqueconf.html&#x26;npds=1&#x26;metalang=1">&#x7684;&#x9690;&#x79C1;&#x653F;&#x7B56;</a>&#x3002;[/zh]
         </div>
      </div>'));
$m->add_extra('
      <script type="text/javascript">
      //<![CDATA[
         $(document).ready(function() {
            inpandfieldlen("nom",150);
            inpandfieldlen("ad1",150);
            inpandfieldlen("ville",150);
            inpandfieldlen("dpt",50);
            inpandfieldlen("cpt",5);
            inpandfieldlen("tel",25);
            inpandfieldlen("fax",25);
            inpandfieldlen("mob",25);
            inpandfieldlen("email",255);
            inpandfieldlen("act",150);
            inpandfieldlen("des",1024);
         });
      //]]>
      </script>');
// ----------------------------------------------------------------
?>