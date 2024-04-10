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
declare(strict_types=1);

use npds\system\support\facades\Sform;

Sform::add_form_title('Register');
Sform::add_form_id('Register');
Sform::add_form_method('post');
Sform::add_form_check('false');
Sform::add_url(site_url('admin.php'));

include('support/sform/extend-user/adm_formulaire.php');

echo Sform::print_form('');
