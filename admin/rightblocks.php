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
use npds\system\language\language;
use npds\system\support\facades\DB;

if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'blocks';

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

manuel('rightblocks');

/**
 * [makerblock description]
 *
 * @param   string  $title    [$title description]
 * @param   string  $content  [$content description]
 * @param   int     $members  [$members description]
 * @param   string  $Mmember  [$Mmember description]
 * @param   int     $Rindex   [$Rindex description]
 * @param   int     $Scache   [$Scache description]
 * @param   string  $BRaide   [$BRaide description]
 * @param   int     $SHTML    [$SHTML description]
 * @param   int     $css      [$css description]
 *
 * @return  void
 */
function makerblock(string $title, string $content, int $members, string $Mmember, int $Rindex, int $Scache, string $BRaide, int $SHTML, int $css): void
{
    if (is_array($Mmember) and ($members == 1)) {
        $members = implode(',', $Mmember);
        if ($members == 0) { 
            $members = 1;
        }
    }

    if (empty($Rindex)) {
        $Rindex = 0;
    }

    // $title = stripslashes(str::FixQuotes($title));
    // $content = stripslashes(str::FixQuotes($content));

    $title = stripslashes($title);
    $content = stripslashes($content);

    if ($SHTML != 'ON') {
        $content = strip_tags(str_replace('<br />', "\n", $content));
    }

    DB::table('rblocks')->insert(array(
        'title'       => $title,
        'content'     => $content,
        'memeber'     => $members,
        'Rindex'      => $Rindex,
        'cache'       => $Scache,
        'actif'       => 1,
        'css'         => $css,
        'aide'        => $BRaide
    ));

    global $aid;
    logs::Ecr_Log('security', "MakeRightBlock(". language::aff_langue($title) .") by AID : $aid", '');

    Header('Location: '. site_url('admin.php?op=blocks'));
}

/**
 * [changerblock description]
 *
 * @param   int     $id       [$id description]
 * @param   string  $title    [$title description]
 * @param   string  $content  [$content description]
 * @param   int     $members  [$members description]
 * @param   string  $Mmember  [$Mmember description]
 * @param   int     $Rindex   [$Rindex description]
 * @param   int     $Scache   [$Scache description]
 * @param   string  $Sactif   [$Sactif description]
 * @param   string  $BRaide   [$BRaide description]
 * @param   int     $css      [$css description]
 *
 * @return  void
 */
function changerblock(int $id, string $title, string $content, int $members, string $Mmember, int $Rindex, int $Scache, string $Sactif, string $BRaide, int $css): void
{
    if (is_array($Mmember) and ($members == 1)) {
        $members = implode(',', $Mmember);
        if ($members == 0) { 
            $members = 1;
        }
    }

    if (empty($Rindex)) { 
        $Rindex = 0;
    }

    //$title = stripslashes(str::FixQuotes($title));
    $title = stripslashes($title);

    if ($Sactif == 'ON') { 
        $Sactif = 1;
    } else {
        $Sactif = 0;
    }

    //$content = stripslashes(str::FixQuotes($content));
    $content = stripslashes($content);
    
    DB::table('rblocks')->where('id', $id)->update(array(
        'title'       => $title,
        'content'     => $content,
        'member'      => $members,
        'Rindex'      => $Rindex,
        'cache'       => $Scache,
        'actif'       => $Sactif,
        'css'         => $css,
        'aide'        => $BRaide
    ));

    global $aid;
    logs::Ecr_Log('security', "ChangeRightBlock(". language::aff_langue($title) ." - $id) by AID : $aid", '');

    Header('Location: '. site_url('admin.php?op=blocks'));
}

/**
 * [changegaucherblock description]
 *
 * @param   int     $id       [$id description]
 * @param   string  $title    [$title description]
 * @param   string  $content  [$content description]
 * @param   int     $members  [$members description]
 * @param   string  $Mmember  [$Mmember description]
 * @param   int     $Rindex   [$Rindex description]
 * @param   int     $Scache   [$Scache description]
 * @param   string  $Sactif   [$Sactif description]
 * @param   string  $BRaide   [$BRaide description]
 * @param   int     $css      [$css description]
 *
 * @return  void
 */
function changegaucherblock(int $id, string $title, string $content, int $members, string $Mmember, int $Rindex, int $Scache, string $Sactif, string $BRaide, int $css): void
{
    if (is_array($Mmember) and ($members == 1)) {
        $members = implode(',', $Mmember);
        if ($members == 0) {
            $members = 1;
        }
    }

    if (empty($Rindex)) {
        $Rindex = 0;
    }

    //$title = stripslashes(str::FixQuotes($title));
    $title = stripslashes($title);

    if ($Sactif == 'ON') { 
        $Sactif = 1;
    } else {
        $Sactif = 0;
    }

    //$content = stripslashes(str::FixQuotes($content));
    $content = stripslashes($content);

    DB::table('lblocks')->insert(array(
        'title'         => $title,
        'content'       => $content,
        'member'        => $members,
        'Lindex'        => $Rindex,
        'cache'         => $Scache,
        'actif'         => $Sactif,
        'css'           => $css,
        'aide'          => $BRaide
    ));

    DB::table('rblocks')->where('id', $id)->delete();

    global $aid;
    logs::Ecr_Log('security', "MoveRightBlockToLeft(". language::aff_langue($title) ." - $id) by AID : $aid", '');

    Header('Location: '. site_url('admin.php?op=blocks'));
}

/**
 * [deleterblock description]
 *
 * @param   int   $id  [$id description]
 *
 * @return  void
 */
function deleterblock(int $id): void
{
    DB::table('rblocks')->where('id', $id)->delete();

    global $aid;
    logs::Ecr_Log('security', "DeleteRightBlock($id) by AID : $aid", '');

    Header('Location: '. site_url('admin.php?op=blocks'));
}

settype($css, 'integer');
settype($Sactif, 'string');
settype($SHTML, 'string');

$Mmember = isset($Mmember) ? $Mmember : '';

switch ($op) {
    case 'makerblock':

        settype($title, 'string');
        settype($content, 'string');
        settype($members, 'int');
        settype($Mmember, 'string');
        settype($index, 'int');
        settype($Scache, 'int');
        settype($Baide, 'string');
        settype($SHTML, 'int');
        settype($css, 'int');

        makerblock($title, $xtext, $members, $Mmember, $index, $Scache, $Baide, $SHTML, $css);
        break;

    case 'deleterblock':

        settype($id, 'int');

        deleterblock($id);
        break;

    case 'changerblock':

        settype($id, 'int');
        settype($title, 'string');
        settype($content, 'string');
        settype($members, 'int');
        settype($Mmember, 'string');
        settype($Rindex, 'int');
        settype($Scache, 'int');
        settype($Sactif, 'string');
        settype($BRaide, 'string');
        settype($css, 'int');

        changerblock($id, $title, $content, $members, $Mmember, $Rindex, $Scache, $Sactif, $BRaide, $css);
        break;

    case 'gaucherblock':

        settype($id, 'int');
        settype($title, 'string');
        settype($content, 'string');
        settype($members, 'int');
        settype($Mmember, 'string');
        settype($Rindex, 'int');
        settype($Scache, 'int');
        settype($Sactif, 'string');
        settype($BRaide, 'string');
        settype($css, 'int');

        changegaucherblock($id, $title, $content, $members, $Mmember, $Rindex, $Scache, $Sactif, $BRaide, $css);
        break;
}
