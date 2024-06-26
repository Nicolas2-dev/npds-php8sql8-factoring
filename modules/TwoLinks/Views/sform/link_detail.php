<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* This version name NPDS Copyright (c) 2001-2020 by Philippe Brunier   */
/*                                                                      */
/* New Links.php Module with SFROM extentions                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

use npds\support\routing\url;
use npds\support\language\language;
use npds\system\sform\sform;

global $ModPath, $ModStart;
$pos = strpos($ModPath, '/admin');
if ($pos > 0) $ModPathX = substr($ModPath, 0, $pos);
else $ModPathX = $ModPath;
global $sform_path;
$sform_path = 'support/sform/';
//********************
global $m;
$m = new sform();
//********************
$m->add_form_title($ModPathX);
$m->add_field($ModPathX . '_id', $ModPathX . '_id', '', 'text', true, 11, '', 'a-9');
$m->add_key($ModPathX . '_id');
$m->add_submit_value('link_fiche_detail');
$m->add_url("modules.php?ModStart=$ModStart&ModPath=$ModPath");

/************************************************/
include_once($sform_path . $ModPathX . '/formulaire.php');
/************************************************/

// Fabrique le formulaire et assure sa gestion
switch ($link_fiche_detail) {

    case 'fiche_detail':
        if ($m->sform_read_mysql($browse_key)) {
            $m->add_extra("<tr><td colspan=\"2\" align=\"center\">");
            $m->add_extra('<a href="javascript: history.go(-1)" class="btn btn-primary">' . __d('two_links', 'Retour en arrière') . '</a>');
            $m->add_extra("</td></tr>");
            $m->key_lock("close");
            echo language::aff_langue($m->print_form("class=\"ligna\""));
        } else
            url::redirect_url($m->url);
        break;

    default:
        if ($m->sform_read_mysql($browse_key))
            echo '<a class="me-3" href="modules.php?ModStart=' . $ModStart . '&amp;ModPath=' . $ModPath . '&amp;op=fiche_detail&amp;lid=' . $browse_key . '" ><i class="fa fa-info fa-lg" title="' . __d('two_links', 'Détails supplémentaires') . '" data-bs-toggle="tooltip"></i></a>';
        break;
}
