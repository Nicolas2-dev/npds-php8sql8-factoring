<?php

/************************************************************************/
/* SFORM Extender for Dune comments.                                    */
/* ===========================                                          */
/*                                                                      */
/* P. Brunier 2002 - 2019                                               */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
/* Dont modify this file is you dont know what you make                 */
/************************************************************************/

use npds\system\language\language;
use npds\system\sform\form_handler;

global $m;
$m = new form_handler();
//********************
$m->add_form_title("coolsus");
$m->add_form_method("post");
$m->add_form_check("false");
$m->add_mess("[fr]* désigne un champ obligatoire[/fr][en]* required field[/en]");
$m->add_submit_value("submitS");
$m->add_url("modules.php");

/************************************************/
include("modules/comments/support/sform/$formulaire");
/************************************************/

if (!isset($GLOBALS["submitS"]))
    echo language::aff_langue($m->print_form(''));
else
    $message = language::aff_langue($m->aff_response('', "not_echo", ''));
