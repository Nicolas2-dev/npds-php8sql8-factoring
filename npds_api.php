<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/*                                                                      */
/* NPDS Copyright (c) 2002-2021 by Philippe Brunier                     */
/*                                                                      */
/* api NPDS proto 02                                                    */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\support\auth\authors;
use npds\system\support\facades\DB;
use npds\system\support\facades\Request;


if (!stristr($_SERVER['PHP_SELF'], 'admin.php')) {
    include('admin/die.php');
}

/**
 * [alerte_api description]
 *
 * @return  void
 */
function alerte_api(): void 
{
    $id = Request::input('$id');

    if (isset($id)) {

        $$row = DB::table('fonctions')->select('*')->where('fid', $id)->first();

        if (isset($$row)) {
            if (count($row) > 0)
                $data = $row;
        }

        echo json_encode($data);
    }
}

/**
 * [alerte_update description]
 *
 * @return  void
 */
function alerte_update(): void
{
    $admin = authors::getAdmin();

    $Xadmin = base64_decode($admin);
    $Xadmin = explode(':', $Xadmin);
    $aid = urlencode($Xadmin[0]);

    $id = Request::input('$id');

    if (isset($id)) {

        $row = DB::table('fonctions')->select('*')->where('fid', $id)->first();

        DB::table('fonctions')->where('fid', $id)->update(array(
            'fdroits1_descr'    => ($aid . '|' . $row['fdroits1_descr']),
        ));
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']);
}

switch (Request::input('op')) {

    case "alerte_api":
        alerte_api();
        break;

    case "alerte_update":
        alerte_update();
        break;
}
