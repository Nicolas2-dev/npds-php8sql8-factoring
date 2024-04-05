<?php

/************************************************************************/
/* SFORM Extender for NPDS V Forum .                                    */
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


Sform::add_form_title("Bugs_Report");
Sform::add_form_method("post");
Sform::add_form_check("false");
Sform::add_mess(" * d&eacute;signe un champ obligatoire ");
Sform::add_submit_value("submitS");
Sform::add_url("newtopic.php");

include("support/sform/forum/$formulaire");

if (!$submitS) {
    echo Sform::print_form('');
} else {
    $message = Sform::aff_response('', 'not_echo', '');
}
