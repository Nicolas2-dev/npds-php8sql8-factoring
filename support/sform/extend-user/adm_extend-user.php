<?php

/************************************************************************/
/* SFORM Extender for NPDS USER                                         */
/* ===========================                                          */
/*                                                                      */
/* NPDS Copyright (c) 2002-2019 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
/* Dont modify this file is you dont know what you make                 */
/************************************************************************/

use npds\system\sform\form_handler;

$sform_path = 'support/sform/';

global $m;
$m = new form_handler();
//********************
$m->add_form_title('Register');
$m->add_form_id('Register');
$m->add_form_method('post');
$m->add_form_check('false');
$m->add_url('admin.php');

/************************************************/
include($sform_path . 'extend-user/adm_formulaire.php');
/************************************************/
echo $m->print_form('');
