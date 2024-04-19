<?php

/************************************************************************/
/* SFORM Extender for NPDS USER                                         */
/* ===========================                                          */
/*                                                                      */
/* NPDS Copyright (c) 2002-2022 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

/************************************************************************/
/* Dont modify this file if you dont know what you make                 */
/************************************************************************/
/* Utilise une table complémentaire de la table user : users_extend
    C1  varchar(255)
    C2  varchar(255)
    C3  varchar(255)
    C4  varchar(255)
    C5  varchar(255)
    C6  varchar(255)
    C7  varchar(255)
    C8  varchar(255)

    M1  mediumtext
    M2  mediumtext // utilisé pour les réseaux sociaux

    T1  varchar(10) date standard
    T2  varchar(14) peut stocker un TimeStamp

    B1  BLOB peut stocker des fichiers (gif, exe ...)

    ==> Le nom des champs (C1, C2, M1, T1 ...) est IMPERATIF
    ==> un formulaire valide doit contenir au moins C1 ou M1 ou T1
    */
declare(strict_types=1);

use npds\support\language\language;
use npds\system\support\facades\Sform;

if (!isset($C1)) $C1 = '';
if (!isset($C2)) $C2 = '';
if (!isset($C3)) $C3 = '';
if (!isset($C4)) $C4 = '';
if (!isset($C5)) $C5 = '';
if (!isset($C6)) $C6 = '';
if (!isset($C7)) $C7 = '';
if (!isset($C8)) $C8 = '';
if (!isset($T1)) $T1 = '';
if (!isset($T1)) $T2 = '';
if (!isset($M2)) $M2 = '';

Sform::add_comment(language::aff_langue('<div class="row"><p class="lead">[fr]En savoir plus[/fr][en]More[/en][es]M&#xE1;s[/es][de]Mehr[/de]</p></div>'));

Sform::add_field('C1', language::aff_langue('[fr]Activit&#x00E9; professionnelle[/fr][en]Professional activity[/en][es]Actividad profesional[/es][de]Berufliche T&#xE4;tigkeit[/de]'), $C1, 'text', false, 100, '', '');
Sform::add_extender('C1', '', '<span class="help-block text-end" id="countcar_C1"></span>');

Sform::add_field('C2', language::aff_langue('[fr]Code postal[/fr][en]Postal code[/en][es]C&#xF3;digo postal[/es][de]Postleitzahl[/de]'), $C2, 'text', false, 5, '', '');
Sform::add_extender('C2', '', '<span class="help-block text-end" id="countcar_C2"></span>');

Sform::add_date('T1', language::aff_langue('[fr]Date de naissance[/fr][en]Birth date[/en][es]Fecha de nacimiento[/es][de]Geburtsdatum[/de]'), $T1, 'text', '', false, 20);
Sform::add_extender('T1', '', '<span class="help-block">JJ/MM/AAAA</span>');

Sform::add_field('M2', "R&#x00E9;seaux sociaux", $M2, 'hidden', false);

include('modules/geoloc/config/geoloc.conf');

Sform::add_comment(language::aff_langue('<div class="row"><p class="lead"><a href="' . site_url('modules.php?ModPath=geoloc&amp;ModStart=geoloc') .'"><i class="fas fa-map-marker-alt fa-2x" title="[fr]Modifier ou d&#xE9;finir votre position[/fr][en]Define or change your geolocation[/en][zh]Define or change your geolocation[/zh][es]Definir o cambiar la geolocalizaci&#243;n[/es][de]Definieren oder &#xE4;ndern Sie Ihre Geolokalisierung[/de]" data-bs-toggle="tooltip" data-bs-placement="right"></i></a>&nbsp;[fr]G&#xE9;olocalisation[/fr][en]Geolocation[/en][zh]&#x5730;&#x7406;&#x5B9A;&#x4F4D;[/zh][es]Geolocalizaci&#243;n[/es][de]Geolokalisierung[/de]</p></div>'));
Sform::add_field($ch_lat, language::aff_langue('[fr]Latitude[/fr][en]Latitude[/en][zh]&#x7ECF;&#x5EA6;[/zh][es]Latitud[/es][de]Breitengrad[/de]'), $$ch_lat, 'text', false, '', '', 'lat');
Sform::add_field($ch_lon, language::aff_langue('[fr]Longitude[/fr][en]Longitude[/en][zh]&#x7EAC;&#x5EA6;[/zh][es]Longitud[/es][de]L&#228;ngengrad[/de]'), $$ch_lon, 'text', false, '', '', 'long');

// Les champ B1 et M2 sont utilisé par NPDS dans le cadre des fonctions USERs
// Si vous avez besoin d'un ou de champs ci-dessous - le(s) définir selon vos besoins et l'(les) enlever du tableau $fielddispo
$fielddispo = array('C3', 'C4', 'C5', 'C6', 'C7', 'C8', 'M1', 'T2');
$geofield = array($ch_lat, $ch_lon);
$fieldrest = array_diff($fielddispo, $geofield);

//reset($fieldrest);
foreach ($fieldrest as $k => $v) {
    Sform::add_field($v, $v, '', 'hidden', false);
}

Sform::add_extra('
        <script type="text/javascript" src="assets/shared/flatpickr/dist/flatpickr.min.js"></script>
        <script type="text/javascript" src="assets/shared/flatpickr/dist/l10n/' . language::language_iso(1, '', '') . '.js"></script>
        <script type="text/javascript">
        //<![CDATA[
            $(document).ready(function() {
                $("<link>").appendTo("head").attr({type: "text/css", rel: "stylesheet",href: "assets/shared/flatpickr/dist/themes/npds.css"});
            })
        //]]>
        </script>');
