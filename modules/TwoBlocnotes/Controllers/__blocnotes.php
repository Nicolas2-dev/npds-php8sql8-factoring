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

use App\Support\Security\Hack;
use Npds\Support\Facades\Request;


global $NPDS_Prefix;

$uriBlocNote = Request::input('uriBlocNote');
$typeBlocNote = Request::input('typeBlocNote');
$nomBlocNote = Request::input('nomBlocNote');
$supBlocNote = Request::input('supBlocNote');
$texteBlocNote = Request::input('texteBlocNote');

if ($uriBlocNote) {
    if ($typeBlocNote == "shared") {
        $bnid = md5($nomBlocNote);
    } elseif ($typeBlocNote == "context") {
        
        if ($nomBlocNote == "\$username") {
            global $cookie, $admin;
            
            $nomBlocNote = $cookie[1];
            $cur_admin = explode(':', base64_decode($admin));
            
            if ($cur_admin) {
                $nomBlocNote = $cur_admin[0];
            }
        }

        if (stristr(urldecode($uriBlocNote), "article.php")) {
            $bnid = md5($nomBlocNote . substr(urldecode($uriBlocNote), 0, strpos(urldecode($uriBlocNote), "&")));
        } else {
            $bnid = md5($nomBlocNote . urldecode($uriBlocNote));
        }
    } else {
        $bnid = '';
    }

    if ($bnid) {
        if ($supBlocNote == 'RAZ') {

            // DB::table('')->where('', )->delete();

            sql_query("DELETE FROM " . $NPDS_Prefix . "blocnotes WHERE bnid='$bnid'");
        } else {
            sql_query("LOCK TABLES " . $NPDS_Prefix . "blocnotes WRITE");

            // = DB::table('')->select()->where('', )->orderBy('')->get();

            $result = sql_query("SELECT texte FROM " . $NPDS_Prefix . "blocnotes WHERE bnid='$bnid'");
            if (sql_num_rows($result) > 0) {
                if ($texteBlocNote != '') {

                    //DB::table('')->where('', )->update(array(
                    //    ''       => ,
                    //));

                    sql_query("UPDATE " . $NPDS_Prefix . "blocnotes SET texte='" . Hack::removeHack($texteBlocNote) . "' WHERE bnid='$bnid'");
                } else {

                    // DB::table('')->where('', )->delete();

                    sql_query("DELETE FROM " . $NPDS_Prefix . "blocnotes WHERE bnid='$bnid'");
                }
            } else {
                if ($texteBlocNote != '') {

                    //DB::table('')->insert(array(
                    //    ''       => ,
                    //));

                    sql_query("INSERT INTO " . $NPDS_Prefix . "blocnotes (bnid, texte) VALUES ('$bnid', '" . Hack::removeHack($texteBlocNote) . "')");
                }
            }
            sql_query("UNLOCK TABLES");
        }
    }
    
    header('location: '. site_url(urldecode($uriBlocNote)));
} else {
    header('location: '. site_url('index.php'));
}
