<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
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
declare(strict_types=1);

use npds\support\logs\logs;
use npds\support\assets\css;
use npds\support\str;
use npds\support\language\language;
use npds\system\support\facades\DB;

if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'mblock';
$f_titre = __d('two_blocks', 'Bloc Principal');

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

/**
 * [mblock description]
 *
 * @return  void
 */
function mblock():  void
{
    global $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('mainblock'));
    adminhead($f_meta_nom, $f_titre);

    echo '
    <hr />
    <h3>'. __d('two_blocks', 'Edition du Bloc Principal') .'</h3>';

    $block = DB::table('block')->select('title', 'content')->find(1);

    if (!empty($block)) {

        echo '
        <form id="fad_mblock" action="'. site_url('admin.php') .'" method="post">
            <div class="form-floating mb-3">
                <textarea class="form-control" type="text" id="title" name="title" maxlength="1000" placeholder="'. __d('two_blocks', 'Titre :') .'" style="height:70px;">'. $block['title'] .'</textarea>
                <label for="title">'. __d('two_blocks', 'Titre') .'</label>
                <span class="help-block text-end"><span id="countcar_title"></span></span>
            </div>
            <div class="form-floating mb-3">
                <textarea class="form-control" id="content" name="content" style="height:170px;">'. $block['content'] .'</textarea>
                <label for="content">'. __d('two_blocks', 'Contenu') .'</label>
            </div>
            <input type="hidden" name="op" value="changemblock" />
            <button class="btn btn-primary btn-block" type="submit">'. __d('two_blocks', 'Valider') .'</button>
        </form>
        <script type="text/javascript">
            //<![CDATA[
                $(document).ready(function() {
                inpandfieldlen("title",1000);
                });
            //]]>
        </script>';
    }

    css::adminfoot('fv', '', '', '');
}

/**
 * [changemblock description]
 *
 * @param   string  $title    [$title description]
 * @param   string  $content  [$content description]
 *
 * @return  void              [return description]
 */
function changemblock(string $title, string $content): void
{
    $title = stripslashes(str::FixQuotes($title));
    $content = stripslashes(str::FixQuotes($content));

    DB::table('block')->where('id', 1)->update(array(
        'title'     => $title,
        'content'   => $content,
    ));

    global $aid;
    logs::Ecr_Log('security', "ChangeMainBlock(" . language::aff_langue($title) . ") by AID : $aid", '');

    Header('Location: '. site_url('admin.php?op=adminMain'));
}

switch ($op) {
    case 'mblock':
        mblock();
        break;
        
    case 'changemblock':
        changemblock($title, $content);
        break;
}