<?php

/************************************************************************/
/* DUNE by NPDS - admin prototype                                       */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* NPDS Copyright (c) 2002-2022 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

use npds\system\logs\logs;
use npds\system\assets\css;
use npds\system\support\str;
use npds\system\support\facades\DB;

if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'ablock';
$f_titre = adm_translate("Bloc Administration");

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

/**
 * [ablock description]
 *
 * @return  void
 */
function ablock(): void
{
    global $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('adminblock'));
    adminhead($f_meta_nom, $f_titre);

    echo '
        <hr />
        <h3 class="mb-3">' . adm_translate("Editer le Bloc Administration") . '</h3>';

    $block = DB::table('block')->select('title', 'content')->find(2);

    if (!empty($block)) {
        echo '
        <form id="adminblock" action="admin.php" method="post" class="needs-validation">
            <div class="form-floating mb-3">
            <textarea class="form-control" type="text" name="title" id="title" maxlength="1000" style="height:70px;">' . $block['title'] . '</textarea>
            <label for="title">' . adm_translate("Titre") . '</label>
            <span class="help-block text-end"><span id="countcar_title"></span></span>
            </div>
            <div class="form-floating mb-3">
            <textarea class="form-control" type="text" rows="25" name="content" id="content" style="height:170px;">' . $block['content'] . '</textarea>
            <label for="content">' . adm_translate("Contenu") . '</label>
            </div>
            <input type="hidden" name="op" value="changeablock" />
            <button class="btn btn-primary btn-block" type="submit">' . adm_translate("Valider") . '</button>
        </form>';

        $arg1 = '
        var formulid = ["adminblock"];
        inpandfieldlen("title",1000);';
    }

    css::adminfoot('fv', '', $arg1, '');
}

/**
 * [changeablock description]
 *
 * @param   string  $title    [$title description]
 * @param   string  $content  [$content description]
 *
 * @return  void
 */
function changeablock(string $title, string $content): void
{
    $title = stripslashes(str::FixQuotes($title));
    $content = stripslashes(str::FixQuotes($content));

    DB::table('block')->where('id', 2)->update(array(
        'title'     => $title,
        'content'   => $content,
    ));

    global $aid;
    logs::Ecr_Log('security', "ChangeAdminBlock() by AID : $aid", '');

    Header("Location: admin.php?op=adminMain");
}

switch ($op) {
    case 'ablock':
        ablock();
        break;

    case 'changeablock':
        changeablock($title, $content);
        break;
}
