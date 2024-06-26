<?php

namespace Modules\TwoReseauxSociaux\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class ReseauxSociaux extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'social';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'reseaux-sociaux';

        $this->f_titre = __d('two_reseaux_sociaux', 'Reseaux Sociaux');

        parent::initialize($request);
    }

    /**
     * 
     */
    public function index(Request $request)
    {

        return $this->createView()
            ->shares('title', __d('two_', ''));
    }

    function ListReseaux($ModPath, $ModStart, $f_meta_nom, $f_titre)
    {
        if (file_exists("modules/$ModPath/config/reseaux-sociaux.conf.php")) {
            include("modules/$ModPath/config/reseaux-sociaux.conf.php");
        }
    
        adminhead($f_meta_nom, $f_titre);
    
        echo '
        <hr />
        <h3><a href="admin.php?op=Extend-Admin-SubModule&amp;ModPath=' . $ModPath . '&amp;ModStart=' . $ModStart . '&amp;subop=AddReseaux"><i class="fa fa-plus-square"></i></a>&nbsp;' . __d('two_reseaux_sociaux', 'Ajouter') . '</h3>
        <table id ="lst_rs_adm" data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-mobile-responsive="true" data-buttons-class="outline-secondary" data-icons-prefix="fa" data-icons="icons">
            <thead>
                <tr>
                    <th class="n-t-col-xs-3" data-sortable="true" data-halign="center" data-align="right">' . __d('two_reseaux_sociaux', 'Nom') . '</th>
                    <th class="n-t-col-xs-5" data-sortable="true" data-halign="center">' . __d('two_reseaux_sociaux', 'URL') . '</th>
                    <th class="n-t-col-xs-1" data-halign="center" data-align="center">' . __d('two_reseaux_sociaux', 'Icône') . '</th>
                    <th class="n-t-col-xs-2" data-halign="center" data-align="center">' . __d('two_reseaux_sociaux', 'Fonctions') . '</th>
                </tr>
            </thead>
            <tbody>';
    
        foreach ($rs as $v1) {
            echo '
                <tr>
                    <td>' . $v1[0] . '</td>
                    <td>' . $v1[1] . '</td>
                    <td><i class="fab fa-' . $v1[2] . ' fa-2x text-muted align-middle"></i></td>
                    <td>
                    <a href="admin.php?op=Extend-Admin-SubModule&amp;ModPath=' . $ModPath . '&amp;ModStart=' . $ModStart . '&amp;subop=EditReseaux&amp;rs_id=' . urlencode($v1[0]) . '&amp;rs_url=' . urlencode($v1[1]) . '&amp;rs_ico=' . urlencode($v1[2]) . '" ><i class="fa fa-edit fa-lg me-2 align-middle" title="' . __d('two_reseaux_sociaux', 'Editer') . '" data-bs-toggle="tooltip" data-bs-placement="left"></i></a>
                    <a href="admin.php?op=Extend-Admin-SubModule&amp;ModPath=' . $ModPath . '&amp;ModStart=' . $ModStart . '&amp;subop=DeleteReseaux&amp;rs_id=' . urlencode($v1[0]) . '&amp;rs_url=' . urlencode($v1[1]) . '&amp;rs_ico=' . urlencode($v1[2]) . '" ><i class="fas fa-trash fa-lg text-danger align-middle" title="' . __d('two_reseaux_sociaux', 'Effacer') . '" data-bs-toggle="tooltip"></i></a>
                    </td>
                </tr>';
        }
    
        echo '
            </tbody>
        </table>';
    
        css::adminfoot('', '', '', '');
    }
    
    function EditReseaux($ModPath, $ModStart, $f_meta_nom, $f_titre, $rs_id, $rs_url, $rs_ico, $subop, $old_id)
    {
        if (file_exists("modules/$ModPath/config/reseaux-sociaux.conf.php")) {
            include("modules/$ModPath/config/reseaux-sociaux.conf.php");
        }
    
        adminhead($f_meta_nom, $f_titre);
    
        if ($subop == 'AddReseaux')
            echo '
        <hr />
        <h3 class="mb-3">' . __d('two_reseaux_sociaux', 'Ajouter') . '</h3>';
        else
            echo '
        <hr />
        <h3 class="mb-3">' . __d('two_reseaux_sociaux', 'Editer') . '</h3>';
    
        echo '
        <form id="reseauxadm" action="admin.php" method="post">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-3" for="rs_id">' . __d('two_reseaux_sociaux', 'Nom') . '</label>
                <div class="col-sm-9">
                    <input class="form-control" type="text" id="rs_id" name="rs_id"  maxlength="50"  placeholder="" value="' . urldecode($rs_id) . '" required="required" />
                    <span class="help-block text-end"><span id="countcar_rs_id"></span></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-3" for="rs_url">' . __d('two_reseaux_sociaux', 'URL') . '</label>
                <div class="col-sm-9">
                    <input class="form-control" type="url" id="rs_url" name="rs_url"  maxlength="100" placeholder="" value="' . urldecode($rs_url) . '" required="required" />
                    <span class="help-block text-end"><span id="countcar_rs_url"></span></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-3" for="rs_ico">' . __d('two_reseaux_sociaux', 'Icône') . '</label>
                <div class="col-sm-9">
                    <input class="form-control" type="text" id="rs_ico" name="rs_ico"  maxlength="40" placeholder="" value="' . stripcslashes(urldecode($rs_ico)) . '" required="required" />
                    <span class="help-block text-end"><span id="countcar_rs_ico"></span></span>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-9 ms-sm-auto">
                    <button class="btn btn-primary col-12" type="submit"><i class="fa fa-check-square fa-lg"></i>&nbsp;' . __d('two_reseaux_sociaux', 'Sauver') . '</button>
                    <input type="hidden" name="op" value="Extend-Admin-SubModule" />
                    <input type="hidden" name="ModPath" value="' . $ModPath . '" />
                    <input type="hidden" name="ModStart" value="' . $ModStart . '" />
                    <input type="hidden" name="subop" value="SaveSetReseaux" />
                    <input type="hidden" name="adm_img_mod" value="1" />
                    <input type="hidden" name="old_id" value="' . urldecode($rs_id) . '" />
                </div>
            </div>
        </form>';
        $arg1 = '
    
            var formulid = ["reseauxadm"];
            inpandfieldlen("rs_id",50);
            inpandfieldlen("rs_url",100);
            inpandfieldlen("rs_ico",40);';
    
        css::adminfoot('fv', '', $arg1, '');
    }
    
    function SaveSetReseaux($ModPath, $ModStart, $rs_id, $rs_url, $rs_ico, $subop, $old_id)
    {
        if (file_exists("modules/$ModPath/config/reseaux-sociaux.conf.php")) {
            include("modules/$ModPath/config/reseaux-sociaux.conf.php");
        }
    
        $newar = array($rs_id, $rs_url, $rs_ico);
        $newrs = array();
        $j = 0;
    
        foreach ($rs as $v1) {
            if (in_array($old_id, $v1, true)) unset($rs[$j]);
            $j++;
        }
    
        foreach ($rs as $v1) {
            if (!in_array($rs_id, $v1, true)) $newrs[] = $v1;
        }
    
        if ($subop !== 'DeleteReseaux') {
            $newrs[] = $newar;
        }
    
    
        $file = fopen("modules/$ModPath/config/reseaux-sociaux.conf.php", "w+");
        $content = "<?php \n";
        $content .= "/************************************************************************/\n";
        $content .= "/* DUNE by NPDS                                                         */\n";
        $content .= "/* ===========================                                          */\n";
        $content .= "/*                                                                      */\n";
        $content .= "/* Reseaux-sociaux Add-On ... ver. 1.0                                  */\n";
        $content .= "/*                                                                      */\n";
        $content .= "/* NPDS Copyright (c) 2002-" . date('Y') . " by Philippe Brunier        */\n";
        $content .= "/*                                                                      */\n";
        $content .= "/* This program is free software. You can redistribute it and/or modify */\n";
        $content .= "/* it under the terms of the GNU General Public License as published by */\n";
        $content .= "/* the Free Software Foundation; either version 2 of the License.       */\n";
        $content .= "/*                                                                      */\n";
        $content .= "/*                                                                      */\n";
        $content .= "/* reseaux-sociaux                                                      */\n";
        $content .= "/* reseaux-sociaux_conf 2016 by jpb                                     */\n";
        $content .= "/*                                                                      */\n";
        $content .= "/* version 1.0 17/02/2016                                               */\n";
        $content .= "/************************************************************************/\n";
        $content .= "// Do not change if you dont know what you do ;-)\n";
        $content .= "// \$rs=[['rs name','rs url',rs class fontawesome for rs icon],[...]]\n";
        $content .= "\$rs = [\n";
        $li = '';
    
        foreach ($newrs as $v1) {
            $li .= '[\'' . $v1[0] . '\',\'' . $v1[1] . '\',\'' . $v1[2] . '\'],' . "\n";
        }
    
        $li = substr_replace($li, '', -2, 1);
        $content .= $li;
        $content .= "];\n";
        $content .= "?>";
    
        fwrite($file, $content);
        fclose($file);
        @chmod("modules/$ModPath/config/reseaux-sociaux.conf.php", 0666);
    }

}