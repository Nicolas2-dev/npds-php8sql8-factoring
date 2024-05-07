<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* BLOC-NOTES engine for NPDS - Philippe Brunier & Arnaud Latourrette   */
/*                                                                      */
/* NPDS Copyright (c) 2002-2023 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

use Npds\Support\Facades\DB;
use Npds\Support\Facades\Request;

$bnid = Request::query('bnid');

if (strstr($bnid, '..') 
    || strstr($bnid, './') 
    || stristr($bnid, 'script') 
    || stristr($bnid, 'cookie') 
    || stristr($bnid, 'iframe') 
    || stristr($bnid, 'applet') 
    || stristr($bnid, 'object') 
    || stristr($bnid, 'meta')) 
{
    die();
}

if (DB::table('blocnotes')
        ->select('texte')
        ->where('bnid', $bnid)
        ->first()) 
{
    $bloc_note_text = str_replace(
        ["\'", chr(13) . chr(10), '"'], 
        ["'", "\\n", '\\"'], 
        stripslashes($blocnotes['texte'])
    );

    echo '$(function(){ $("#texteBlocNote_' . $bnid . '").val(unescape("' . $bloc_note_text . '")); })';
}
