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

use npds\system\logs\logs;
use npds\system\support\str;
use npds\system\language\language;
use npds\system\support\facades\DB;

if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'blocks';

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

global $language;
$hlpfile = "manuels/$language/leftblocks.html";

/**
 * [makelblock description]
 *
 * @param   string  $title    [$title description]
 * @param   string  $content  [$content description]
 * @param   int     $members  [$members description]
 * @param   string  $Mmember  [$Mmember description]
 * @param   int     $Lindex   [$Lindex description]
 * @param   int     $Scache   [$Scache description]
 * @param   string  $BLaide   [$BLaide description]
 * @param   int     $SHTML    [$SHTML description]
 * @param   int     $css      [$css description]
 *
 * @return  void
 */
function makelblock(string $title, string $content, int $members, string $Mmember, int $Lindex, int $Scache, string $BLaide, int $SHTML, int $css): void
{
    if (is_array($Mmember) and ($members == 1)) {
        $members = implode(',', $Mmember);
        if ($members == 0) {
            $members = 1;
        }
    }

    if (empty($Lindex)) {
        $Lindex = 0;
    }

    $title = stripslashes(str::FixQuotes($title));
    $content = stripslashes(str::FixQuotes($content));

    if ($SHTML != 'ON') {
        $content = strip_tags(str_replace('<br />', '\n', $content));
    }

    DB::table('lblocks')->insert(array(
        'title'     => $title,
        'content'   => $content,
        'member'    => $members,
        'Lindex'    => $Lindex,
        'cache'     => $Scache,
        'actif'     => 1,
        'css'       => $css,
        'aide'      => $BLaide,
    ));

    global $aid;
    logs::Ecr_Log('security', "MakeLeftBlock(" . language::aff_langue($title) . ") by AID : $aid", "");

    Header("Location: admin.php?op=blocks");
}

/**
 * [changelblock description]
 *
 * @param   int     $id       [$id description]
 * @param   string  $title    [$title description]
 * @param   string  $content  [$content description]
 * @param   int     $members  [$members description]
 * @param   string  $Mmember  [$Mmember description]
 * @param   int     $Lindex   [$Lindex description]
 * @param   int     $Scache   [$Scache description]
 * @param   int     $Sactif   [$Sactif description]
 * @param   string  $BLaide   [$BLaide description]
 * @param   int     $css      [$css description]
 *
 * @return  void
 */
function changelblock(int $id, string $title, string $content, int $members, string $Mmember, int $Lindex, int $Scache, int $Sactif, string $BLaide, int $css): void
{
    if (is_array($Mmember) and ($members == 1)) {
        $members = implode(',', $Mmember);
        if ($members == 0) {
            $members = 1;
        }
    }

    if (empty($Lindex)) { 
        $Lindex = 0;
    }

    $title = stripslashes(str::FixQuotes($title));

    if ($Sactif == 'ON') { 
        $Sactif = 1;
    } else {
        $Sactif = 0;
    }

    if ($css) { 
        $css = 1;
    } else {
        $css = 0;
    }

    $content = stripslashes(str::FixQuotes($content));
    $BLaide = stripslashes(str::FixQuotes($BLaide));

    DB::table('lblocks')->where('id', $id)->update(array(
        'title'     => $title,
        'content'   => $content,
        'member'    => $members,
        'Lindex'    => $Lindex,
        'cache'     => $Scache,
        'actif'     => $Sactif,
        'aide'      => $BLaide,
        'css'       => $css,

    ));

    global $aid;
    logs::Ecr_Log('security', "ChangeLeftBlock(" . language::aff_langue($title) . " - $id) by AID : $aid", '');

    Header("Location: admin.php?op=blocks");
}

/**
 * [changedroitelblock description]
 *
 * @param   int     $id       [$id description]
 * @param   string  $title    [$title description]
 * @param   string  $content  [$content description]
 * @param   int     $members  [$members description]
 * @param   string  $Mmember  [$Mmember description]
 * @param   int     $Lindex   [$Lindex description]
 * @param   int     $Scache   [$Scache description]
 * @param   int     $Sactif   [$Sactif description]
 * @param   string  $BLaide   [$BLaide description]
 * @param   int     $css      [$css description]
 *
 * @return  void
 */
function changedroitelblock(int $id, string $title, string $content, int $members, string $Mmember, int $Lindex, int $Scache, int $Sactif, string $BLaide, int $css): void
{
    if (is_array($Mmember) and ($members == 1)) {
        $members = implode(',', $Mmember);
        if ($members == 0) {
            $members = 1;
        }
    }

    if (empty($Lindex)) {
        $Lindex = 0;
    }

    $title = stripslashes(str::FixQuotes($title));

    if ($Sactif == 'ON') {
        $Sactif = 1;
    } else {
        $Sactif = 0;
    }

    if ($css) {
        $css = 1;
    } else {
        $css = 0;
    }

    $content = stripslashes(str::FixQuotes($content));
    $BLaide = stripslashes(str::FixQuotes($BLaide));

    DB::table('rblocks')->insert(array(
        'title'     => $title,
        'content'   => $content,
        'member'    => $members,
        'Rindex'    => $Lindex,
        'cache'     => $Scache,
        'actif'     => $Sactif,
        'css'       => $css,
        'aide'      => $BLaide,
    ));

    DB::table('lblocks')->where('id', $id)->delete();

    global $aid;
    logs::Ecr_Log('security', "MoveLeftBlockToRight(" . language::aff_langue($title) . " - $id) by AID : $aid", '');

    Header("Location: admin.php?op=blocks");
}

/**
 * [deletelblock description]
 *
 * @param   int   $id  [$id description]
 *
 * @return  void
 */
function deletelblock(int $id): void
{
    DB::table('lblocks')->where('id', $id)->delete();

    global $aid;
    logs::Ecr_Log('security', "DeleteLeftBlock($id) by AID : $aid", '');

    Header("Location: admin.php?op=blocks");
}

settype($css, 'integer');
settype($Sactif, 'string');
settype($SHTML, 'string');

$Mmember = isset($Mmember) ? $Mmember : '';

switch ($op) {
    case 'makelblock':
        makelblock($title, $xtext, $members, $Mmember, $index, $Scache, $Baide, $SHTML, $css);
        break;

    case 'deletelblock':
        deletelblock($id);
        break;

    case 'changelblock':
        changelblock($id, $title, $content, $members, $Mmember, $Lindex, $Scache, $Sactif, $BLaide, $css);
        break;
        
    case 'droitelblock':
        changedroitelblock($id, $title, $content, $members, $Mmember, $Lindex, $Scache, $Sactif, $BLaide, $css);
        break;
}
