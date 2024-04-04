<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* NPDS Copyright (c) 2002-2023 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\system\logs\logs;
use npds\system\support\str;
use npds\system\language\language;

if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'blocks';

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

manuel('rightblocks');

function makerblock($title, $content, $members, $Mmember, $Rindex, $Scache, $BRaide, $SHTML, $css)
{
    global $NPDS_Prefix;

    if (is_array($Mmember) and ($members == 1)) {
        $members = implode(',', $Mmember);
        if ($members == 0) { 
            $members = 1;
        }
    }

    if (empty($Rindex)) {
        $Rindex = 0;
    }

    $title = stripslashes(str::FixQuotes($title));
    $content = stripslashes(str::FixQuotes($content));

    if ($SHTML != 'ON') {
        $content = strip_tags(str_replace('<br />', "\n", $content));
    }

    sql_query("INSERT INTO " . $NPDS_Prefix . "rblocks VALUES (NULL,'$title','$content', '$members', '$Rindex', '$Scache', '1', '$css', '$BRaide')");

    global $aid;
    logs::Ecr_Log('security', "MakeRightBlock(" . language::aff_langue($title) . ") by AID : $aid", '');

    Header("Location: admin.php?op=blocks");
}

function changerblock($id, $title, $content, $members, $Mmember, $Rindex, $Scache, $Sactif, $BRaide, $css)
{
    global $NPDS_Prefix;

    if (is_array($Mmember) and ($members == 1)) {
        $members = implode(',', $Mmember);
        if ($members == 0) { 
            $members = 1;
        }
    }

    if (empty($Rindex)) { 
        $Rindex = 0;
    }

    $title = stripslashes(str::FixQuotes($title));

    if ($Sactif == 'ON') { 
        $Sactif = 1;
    } else {
        $Sactif = 0;
    }

    $content = stripslashes(str::FixQuotes($content));
    
    sql_query("UPDATE " . $NPDS_Prefix . "rblocks SET title='$title', content='$content', member='$members', Rindex='$Rindex', cache='$Scache', actif='$Sactif', css='$css', aide='$BRaide' WHERE id='$id'");

    global $aid;
    logs::Ecr_Log('security', "ChangeRightBlock(" . language::aff_langue($title) . " - $id) by AID : $aid", '');

    Header("Location: admin.php?op=blocks");
}

function changegaucherblock($id, $title, $content, $members, $Mmember, $Rindex, $Scache, $Sactif, $BRaide, $css)
{
    global $NPDS_Prefix;

    if (is_array($Mmember) and ($members == 1)) {
        $members = implode(',', $Mmember);
        if ($members == 0) {
            $members = 1;
        }
    }

    if (empty($Rindex)) {
        $Rindex = 0;
    }

    $title = stripslashes(str::FixQuotes($title));

    if ($Sactif == 'ON') { 
        $Sactif = 1;
    } else {
        $Sactif = 0;
    }

    $content = stripslashes(str::FixQuotes($content));

    sql_query("INSERT INTO " . $NPDS_Prefix . "lblocks VALUES (NULL,'$title','$content','$members', '$Rindex', '$Scache', '$Sactif', '$css', '$BRaide')");
    sql_query("DELETE FROM " . $NPDS_Prefix . "rblocks WHERE id='$id'");

    global $aid;
    logs::Ecr_Log('security', "MoveRightBlockToLeft(" . language::aff_langue($title) . " - $id) by AID : $aid", '');

    Header("Location: admin.php?op=blocks");
}

function deleterblock($id)
{
    global $NPDS_Prefix;

    sql_query("DELETE FROM " . $NPDS_Prefix . "rblocks WHERE id='$id'");

    global $aid;
    logs::Ecr_Log('security', "DeleteRightBlock($id) by AID : $aid", '');

    Header("Location: admin.php?op=blocks");
}

settype($css, 'integer');
settype($Sactif, 'string');
settype($SHTML, 'string');

$Mmember = isset($Mmember) ? $Mmember : '';

switch ($op) {
    case 'makerblock':
        makerblock($title, $xtext, $members, $Mmember, $index, $Scache, $Baide, $SHTML, $css);
        break;

    case 'deleterblock':
        deleterblock($id);
        break;

    case 'changerblock':
        changerblock($id, $title, $content, $members, $Mmember, $Rindex, $Scache, $Sactif, $BRaide, $css);
        break;

    case 'gaucherblock':
        changegaucherblock($id, $title, $content, $members, $Mmember, $Rindex, $Scache, $Sactif, $BRaide, $css);
        break;
}
