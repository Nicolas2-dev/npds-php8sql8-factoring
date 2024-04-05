<?php

/************************************************************************/
/* SFORM Extender for NPDS Contact Example .                            */
/* ===========================                                          */
/*                                                                      */
/* NPDS Copyright (c) 2002-2023 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
/* Dont modify this file if you dont know what you make                 */
/************************************************************************/

use npds\system\logs\logs;
use npds\system\assets\css;
use npds\system\mail\mailler;
use npds\system\utility\spam;
use npds\system\language\language;
use npds\system\support\facades\Sform;

global $ModPath, $ModStart;

Sform::add_form_title('contact');
Sform::add_form_id('formcontact');
Sform::add_form_method('post');
Sform::add_form_check('false');
Sform::add_url('modules.php');
Sform::add_field('ModStart', '', $ModStart, 'hidden', false);
Sform::add_field('ModPath', '', $ModPath, 'hidden', false);
Sform::add_submit_value('subok');
Sform::add_field('subok', '', 'Submit', 'hidden', false);

/************************************************/
include('support/sform/contact/formulaire.php');

css::adminfoot('fv', '', 'var formulid = ["' . Sform::$form_id . '"];', '1');
/************************************************/
// Manage the <form>
switch ($subok) {
    case 'Submit':
        settype($message, 'string');
        settype($sformret, 'string');
        if (!$sformret) {
            Sform::make_response();
            //anti_spambot
            if (!spam::R_spambot($asb_question, $asb_reponse, $message)) {
                logs::Ecr_Log('security', 'Contact', '');
                $subok = '';
            } else {
                $message = Sform::aff_response('', 'not_echo', '');
                global $notify_email;
                mailler::send_email($notify_email, "Contact site", language::aff_langue($message), '', '', "html", '');
                echo '
                <div class="alert alert-success">
                ' . language::aff_langue("[fr]Votre demande est prise en compte. Nous y r&eacute;pondrons au plus vite[/fr][en]Your request is taken into account. We will answer it as fast as possible.[/en][zh]&#24744;&#30340;&#35831;&#27714;&#24050;&#34987;&#32771;&#34385;&#22312;&#20869;&#12290; &#25105;&#20204;&#20250;&#23613;&#24555;&#22238;&#22797;[/zh][es]Su solicitud es tenida en cuenta. Le responderemos lo m&aacute;s r&aacute;pido posible.[/es][de]Ihre Anfrage wird ber&uuml;cksichtigt. Wir werden so schnell wie m&ouml;glich antworten[/de]") . '
                </div>';
                break;
            }
        } else
            $subok = '';

    default:
        echo language::aff_langue(Sform::print_form(''));
        break;
}
