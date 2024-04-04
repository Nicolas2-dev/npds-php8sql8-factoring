<?php

/************************************************************************/
/* DUNE by NPDS - admin prototype                                       */
/* ===========================                                          */
/*                                                                      */
/*                                                                      */
/* NPDS Copyright (c) 2002-2022 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\system\assets\js;
use npds\system\logs\logs;
use npds\system\assets\css;
use npds\system\auth\users;
use npds\system\mail\mailler;
use npds\system\config\Config;
use npds\system\support\facades\DB;

if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'mod_authors';
$f_titre = adm_translate("Administrateurs");

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

if ($radminsuper != 1) {
    Header("Location: die.php");
}

/**
 * [listdroitsmodulo description]
 *
 * @return  array
 */
function listdroitsmodulo(): array 
{
    $listdroits = '';
    $listdroitsmodulo = '';

    $R = DB::table('fonctions')->select('fid', 'fnom', 'fnom_affich', 'fcategorie')->where('finterface', 1)->where('fcategorie', '<', 7)->orderBy('fcategorie')->get();

    foreach($R as $func) {
        if ($func['fcategorie'] == 6) {
            $listdroitsmodulo .= '
                <div class="col-md-4 col-sm-6">
                    <div class="form-check">
                    <input class="ckbm form-check-input" id="ad_d_m_' . $func['fnom'] . '" type="checkbox" name="ad_d_m_' . $func['fnom'] . '" value="' . $func['fid'] . '" />
                    <label class="form-check-label" for="ad_d_m_' . $func['fnom'] . '">' . $func['fnom_affich'] . '</label>
                    </div>
                </div>';
        } else {
            if ($func['fid'] != 12) {
                $listdroits .= '
                <div class="col-md-4 col-sm-6">
                    <div class="form-check">
                    <input class="ckbf form-check-input" id="ad_d_' . $func['fid'] . '" type="checkbox" name="ad_d_' . $func['fid'] . '" value="' . $func['fid'] . '" />
                    <label class="form-check-label" for="ad_d_' . $func['fid'] . '">' . adm_translate($func['fnom_affich']) . '</label>
                    </div>
                </div>';
            }
        }
    }

    return array($listdroitsmodulo, $listdroits);
}

/**
 * [scri_check description]
 *
 * @return  string
 */
function scri_check(): string
{
    return '
        <script type="text/javascript">
        //<![CDATA[
        $(function () {
            check = $("#cb_radminsuper").is(":checked");
            if(check) {
                $("#adm_droi_f, #adm_droi_m").addClass("collapse");
            }
        });
        $("#cb_radminsuper").on("click", function(){
            check = $("#cb_radminsuper").is(":checked");
            if(check) {
                $("#adm_droi_f, #adm_droi_m").toggleClass("collapse","collapse show");
                $(".ckbf, .ckbm, #ckball_f, #ckball_m").prop("checked", false);
            } else {
                $("#adm_droi_f, #adm_droi_m").toggleClass("collapse","collapse show");
            }
        }); 
        $(document).ready(function(){ 
            $("#ckball_f").change(function(){
                check_a_f = $("#ckball_f").is(":checked");
                if(check_a_f) {
                    $("#ckb_status_f").text("' . html_entity_decode(adm_translate("Tout décocher"), ENT_COMPAT | ENT_HTML401, 'utf-8') . '");
                } else {
                    $("#ckb_status_f").text("' . adm_translate("Tout cocher") . '");
                }
                $(".ckbf").prop("checked", $(this).prop("checked"));
            });
            
            $("#ckball_m").change(function(){
                check_a_m = $("#ckball_m").is(":checked");
                if(check_a_m) {
                    $("#ckb_status_m").text("' . html_entity_decode(adm_translate("Tout décocher"), ENT_COMPAT | ENT_HTML401, 'utf-8') . '");
                } else {
                    $("#ckb_status_m").text("' . adm_translate("Tout cocher") . '");
                }
                $(".ckbm").prop("checked", $(this).prop("checked"));
            });
        });
        //]]>
        </script>';
}

/**
 * [displayadmins description]
 *
 * @return  void
 */
function displayadmins(): void
{
    global $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('authors'));
    adminhead($f_meta_nom, $f_titre);

    $authors = DB::table('authors')->select('aid', 'name', 'url', 'email', 'radminsuper')->get();

    echo '
    <hr />
    <h3>' . adm_translate("Les administrateurs") . '</h3>
    <table id="tab_adm" data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-mobile-responsive="true" data-buttons-class="outline-secondary" data-show-export="true" data-icons="icons" data-icons-prefix="fa">
        <thead>
            <tr>
                <th data-sortable="true" data-halign="center">' . adm_translate('Nom') . '</th>
                <th data-sortable="true" data-halign="center">' . adm_translate('E-mail') . '</th>
                <th data-halign="center" data-align="right">' . adm_translate('Fonctions') . '</th>
            </tr>
        </thead>
        <tbody>';

    //while (list($a_aid, $name, $url, $email, $supadm) = sql_fetch_row($result)) {
    foreach($authors as $author) {   
        if ($author['radminsuper'] == 1) { 
            echo '<tr class="table-danger">';
        } else {
            echo '<tr>';
        }
        
        echo '
                <td>' . $author['aid'] . '</td>
                <td>' . $author['email'] . '</td>
                <td align="right" nowrap="nowrap">
                <a href="admin.php?op=modifyadmin&amp;chng_aid=' . $author['aid'] . '" class=""><i class="fa fa-edit fa-lg" title="' . adm_translate("Modifier l'information") . '" data-bs-toggle="tooltip"></i></a>&nbsp;
                <a href="mailto:' . $author['email'] . '"><i class="fa fa-at fa-lg" title="' . adm_translate("Envoyer un courriel à") . ' ' . $author['aid'] . '" data-bs-toggle="tooltip"></i></a>&nbsp;';
        
        if ($author['url'] != '') {
            echo 'a href="' . $author['url'] . '"><i class="fas fa-external-link-alt fa-lg" title="' . adm_translate("Visiter le site web") . '" data-bs-toggle="tooltip"></i></a>&nbsp;';
        }

        echo '
                <a href="admin.php?op=deladmin&amp;del_aid=' . $author['aid'] . '" ><i class="fas fa-trash fa-lg text-danger" title="' . adm_translate("Effacer l'Auteur") . '" data-bs-toggle="tooltip" ></i></a>
                </td>
            </tr>';
    }

    list($listdroitsmodulo, $listdroits) = listdroitsmodulo();

    echo '
        </tbody>
    </table>
    <hr />
    <h3>' . adm_translate("Nouvel administrateur") . '</h3>
    <form id="nou_adm" action="admin.php" method="post">
        <fieldset>
            <legend><img src="' . Config::get('npds.adminimg') . 'authors.' . Config::get('npds.admf_ext') . '" class="vam" border="0" width="24" height="24" alt="' . adm_translate("Informations") . '" /> ' . adm_translate("Informations") . ' </legend>
            <div class="form-floating mb-3 mt-3">
                <input id="add_aid" class="form-control" type="text" name="add_aid" maxlength="30" placeholder="' . adm_translate("Surnom") . '" required="required" />
                <label for="add_aid">' . adm_translate("Surnom") . ' <span class="text-danger">*</span></label>
                <span class="help-block text-end"><span id="countcar_add_aid"></span></span>
            </div>
            <div class="form-floating mb-3">
                <input id="add_name" class="form-control" type="text" name="add_name" maxlength="50" placeholder="' . adm_translate("Nom") . '" required="required" />
                <label for="add_name">' . adm_translate("Nom") . ' <span class="text-danger">*</span></label>
                <span class="help-block text-end"><span id="countcar_add_name"></span></span>
            </div>
            <div class="form-floating mb-3">
                <input id="add_email" class="form-control" type="email" name="add_email" maxlength="254" placeholder="' . adm_translate("E-mail") . '" required="required" />
                <label for="add_email">' . adm_translate("E-mail") . ' <span class="text-danger">*</span></label>
                <span class="help-block text-end"><span id="countcar_add_email"></span></span>
            </div>
            <div class="form-floating mb-3">
                <input id="add_url" class="form-control" type="url" name="add_url" maxlength="320" placeholder="' . adm_translate("URL") . '" />
                <label for="add_url">' . adm_translate("URL") . '</label>
                <span class="help-block text-end"><span id="countcar_add_url"></span></span>
            </div>
            <div class="form-floating mb-3">
                <input id="add_pwd" class="form-control" type="password" name="add_pwd" maxlength="20" placeholder="' . adm_translate("Mot de Passe") . '" required="required" />
                <label for="add_pwd">' . adm_translate("Mot de Passe") . ' <span class="text-danger">*</span></label>
                <span class="help-block text-end" id="countcar_add_pwd"></span>
                <div class="progress mt-2" style="height: 0.4rem;">
                <div id="passwordMeter_cont" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
                </div>
            </div>
            <div class="mb-3">
                <div class="form-check">
                <input id="cb_radminsuper" class="form-check-input" type="checkbox" name="add_radminsuper" value="1" />
                <label class="form-check-label text-danger" for="cb_radminsuper">' . adm_translate("Super administrateur") . '</label>
                </div>
                <span class="help-block">' . adm_translate("Si Super administrateur est coché, cet administrateur aura TOUS les droits.") . '</span>
            </div>
        </fieldset>
        <fieldset>
            <legend><img src="' . Config::get('npds.adminimg') . 'authors.' . Config::get('npds.admf_ext') . '" class="vam" border="0" width="24" height="24" alt="' . adm_translate("Droits") . '" /> ' . adm_translate("Droits") . ' </legend>
            <div id="adm_droi_f" class="container-fluid ">
                <div class="mb-3">
                <input type="checkbox" id="ckball_f" />&nbsp;<span class="small text-muted" id="ckb_status_f">' . adm_translate("Tout cocher") . '</span>
                </div>
                <div class="row">
                ' . $listdroits . '
                </div>
            </div>
        </fieldset>
        <fieldset>
            <legend><img src="' . Config::get('npds.adminimg') . 'authors.' . Config::get('npds.admf_ext') . '" class="vam" border="0" width="24" height="24" alt="' . adm_translate("Droits modules") . '" /> ' . adm_translate("Droits modules") . ' </legend>
            <div id="adm_droi_m" class="container-fluid">
                <div class="mb-3">
                <input type="checkbox" id="ckball_m" />&nbsp;<span class="small text-muted" id="ckb_status_m">' . adm_translate("Tout cocher") . '</span>
                </div>
                <div class="row">
                ' . $listdroitsmodulo . '
                </div>
            </div>
            <button class="btn btn-primary my-3" type="submit"><i class="fa fa-plus-square fa-lg me-2"></i>' . adm_translate("Ajouter un administrateur") . '</button>
            </div>
            <input type="hidden" name="op" value="AddAuthor" />
        </fieldset>
    </form>
    ' . scri_check();

    $arg1 = '
        var formulid = ["nou_adm"];
        ' . js::auto_complete('admin', 'aid', 'authors', '', '0') . '
        ' . js::auto_complete('adminname', 'name', 'authors', '', '0') . '
        inpandfieldlen("add_aid",30);
        inpandfieldlen("add_name",50);
        inpandfieldlen("add_email",254);
        inpandfieldlen("add_url",320);
        inpandfieldlen("add_pwd",20);';

    $fv_parametres = '
    add_aid: {
        validators: {
            callback: {
                message: "' . translate("Ce surnom n\'est pas disponible") . '",
                callback: function(input) {
                if($.inArray(btoa(input.value), admin) !== -1)
                    return false;
                else
                    return true;
                }
            }
        }
    },
    add_name: {
        validators: {
            callback: {
                message: "' . translate("Ce nom n\'est pas disponible") . '",
                callback: function(input) {
                if($.inArray(btoa(input.value), adminname) !== -1)
                    return false;
                else
                    return true;
                }
            }
        }
    },
    add_pwd: {
        validators: {
            checkPassword: {},
        }
    },';

    css::adminfoot('fv', $fv_parametres, $arg1, '');
}

/**
 * [modifyadmin description]
 *
 * @param   string  $chng_aid  [$chng_aid description]
 *
 * @return  void
 */
function modifyadmin(string $chng_aid): void
{
    global $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('authors'));
    adminhead($f_meta_nom, $f_titre);

    echo '
    <hr />
    <h3>' . adm_translate("Actualiser l'administrateur") . ' : <span class="text-muted">' . $chng_aid . '</span></h3>';

    $author = DB::table('authors')->select('aid', 'name', 'url', 'email', 'pwd', 'radminsuper')->where('aid', $chng_aid)->first();

    $supadm_inp = (($author['radminsuper'] == 1) ? ' checked="checked"' : '');

    //==> construction des check-box des droits
    $listdroits = '';
    $listdroitsmodulo = '';

    $droits = DB::table('droits')->select('d_fon_fid')->where('d_aut_aid', $author['aid'])->get();

    $datas = array();
    foreach ($droits as $key => $value) {
        $datas[] = $value['d_fon_fid'];
    }

    $R = DB::table('fonctions')->select('fid', 'fnom', 'fnom_affich', 'fcategorie')->where('finterface', 1)->where('fcategorie', '<', 7)->orderBy('fcategorie')->get();

    foreach($R as $func) {
        
        $chec = ((in_array($func['fid'], $datas)) ? 'checked="checked"' : '');

        if ($func['fcategorie'] == 6) {
            $listdroitsmodulo .= '
            <div class="col-md-4 col-sm-6">
                <div class="form-check">
                <input class="ckbm form-check-input" id="ad_d_m_' . $func['fnom'] . '" type="checkbox" ' . $chec . ' name="ad_d_m_' . $func['fnom'] . '" value="' . $func['fid'] . '" />
                <label class="form-check-label" for="ad_d_m_' . $func['fnom'] . '">' . $func['fnom_affich'] . '</label>
                </div>
            </div>';
        } else {
            if ($func['fid'] != 12) {
                $listdroits .= '
                <div class="col-md-4 col-sm-6">
                    <div class="form-check">
                    <input class="ckbf form-check-input" id="ad_d_' . $func['fid'] . '" type="checkbox" ' . $chec . ' name="ad_d_' . $func['fid'] . '" value="' . $func['fid'] . '" />
                    <label class="form-check-label" for="ad_d_' . $func['fid'] . '">' . adm_translate($func['fnom_affich']) . '</label>
                    </div>
                </div>';
            }
        }
    }

    //<== construction des check-box des droits
    echo '
    <form id="mod_adm" class="" action="admin.php" method="post">
        <fieldset>
            <legend><img src="' . Config::get('npds.adminimg') . 'authors.' . Config::get('npds.admf_ext') . '" class="vam" border="0" width="24" height="24" alt="' . adm_translate("Informations") . '" title="' . $author['aid'] . '" /> ' . adm_translate("Informations") . '</legend>
            <div class="form-floating mb-3 mt-3">
                <input id="chng_name" class="form-control" type="text" name="chng_name" value="' . $author['name'] . '" maxlength="30" placeholder="' . adm_translate("Nom") . '" required="required" />
                <label for="chng_name">' . adm_translate("Nom") . ' <span class="text-danger">*</span></label>
                <span class="help-block text-end"><span id="countcar_chng_name"></span></span>
            </div>
            <div class="form-floating mb-3">
                <input id="chng_email" class="form-control" type="text" name="chng_email" value="' . $author['email'] . '" maxlength="254" placeholder="' . adm_translate("E-mail") . '" required="required" />
                <label for="chng_email">' . adm_translate("E-mail") . ' <span class="text-danger">*</span></label>
                <span class="help-block text-end"><span id="countcar_chng_email"></span></span>
            </div>
            <div class="form-floating mb-3">
                <input id="chng_url" class="form-control" type="url" name="chng_url" value="' . $author['url'] . '" maxlength="320" placeholder="' . adm_translate("URL") . '" />
                <label for="chng_url">' . adm_translate("URL") . '</label>
                <span class="help-block text-end"><span id="countcar_chng_url"></span></span>
            </div>
            <div class="form-floating mb-3">
                <input id="chng_pwd" class="form-control" type="password" name="chng_pwd" maxlength="20" placeholder="' . adm_translate("Mot de Passe") . '" title="' . adm_translate("Entrez votre nouveau Mot de Passe") . '" />
                <label for="chng_pwd">' . adm_translate("Mot de Passe") . ' <span class="text-danger">*</span></label>
                <span class="help-block text-end" id="countcar_chng_pwd"></span>
                <div class="progress" style="height: 0.4rem;">
                <div id="passwordMeter_cont" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
                </div>
            </div>
            <div class="form-floating mb-3">
                <input id="chng_pwd2" class="form-control" type="password" name="chng_pwd2" maxlength="20" placeholder="' . adm_translate("Mot de Passe") . '" title="' . adm_translate("Entrez votre nouveau Mot de Passe") . '" />
                <label for="chng_pwd2">' . adm_translate("Mot de Passe") . ' <span class="text-danger">*</span></label>
                <span class="help-block text-end"><span id="countcar_chng_pwd2"></span></span>
            </div>
            <div class="mb-3">
                <div class="form-check">
                <input id="cb_radminsuper" class="form-check-input" type="checkbox" name="chng_radminsuper" value="1" ' . $supadm_inp . ' />
                <label class="form-check-label text-danger" for="cb_radminsuper">' . adm_translate("Super administrateur") . '</label>
                </div>
                <span class="help-block">' . adm_translate("Si Super administrateur est coché, cet administrateur aura TOUS les droits.") . '</span>
            </div>
            <input type="hidden" name="chng_aid" value="' . $author['aid'] . '" />
        </fieldset>
        <fieldset>
            <legend><img src="' . Config::get('npds.adminimg') . 'authors.' . Config::get('npds.admf_ext') . '" class="vam" border="0" width="24" height="24" alt="' . adm_translate("Droits") . '" /> ' . adm_translate("Droits") . ' </legend>
            <div id="adm_droi_f" class="container-fluid ">
                <div class="mb-3">
                <input type="checkbox" id="ckball_f" />&nbsp;<span class="small text-muted" id="ckb_status_f">' . adm_translate("Tout cocher") . '</span>
                </div>
                <div class="row">
                ' . $listdroits . '
                </div>
            </div>
        </fieldset>
        <fieldset>
            <legend><img src="' . Config::get('npds.adminimg') . 'authors.' . Config::get('npds.admf_ext') . '" class="vam" border="0" width="24" height="24" alt="' . adm_translate("Droits modules") . '" /> ' . adm_translate("Droits modules") . ' </legend>
            <div id="adm_droi_m" class="container-fluid ">
                <div class="mb-3">
                <input type="checkbox" id="ckball_m" />&nbsp;<span class="small text-muted" id="ckb_status_m">' . adm_translate("Tout cocher") . '</span>
                </div>
                <div class="row">
                ' . $listdroitsmodulo . '
                </div>
            </div>
            <input type="hidden" name="old_pwd" value="' . $author['pwd'] . '" />
            <input type="hidden" name="op" value="UpdateAuthor" />
            <button class="btn btn-primary my-3" type="submit"><i class="fa fa-check fa-lg me-2"></i>' . adm_translate("Actualiser l'administrateur") . '</button>
        </fieldset>
    </form>';

    echo scri_check();

    $arg1 = '
        var formulid = ["mod_adm"]
            inpandfieldlen("chng_name",50);
            inpandfieldlen("chng_email",254);
            inpandfieldlen("chng_url",320);
            inpandfieldlen("chng_pwd",20);
            inpandfieldlen("chng_pwd2",20);';

    $fv_parametres = '
    chng_pwd: {
        validators: {
            checkPassword: {},
        }
    },
    chng_pwd2: {
        validators: {
            identical: {
                compare: function() {
                return mod_adm.querySelector(\'[name="chng_pwd"]\').value;
                },
            }
        }
    },
    !###!
    mod_adm.querySelector(\'[name="chng_pwd"]\').addEventListener("input", function() {
        fvitem.revalidateField("chng_pwd2");
    });';

    css::adminfoot('fv', $fv_parametres, $arg1, '');
}

/**
 * [deletedroits description]
 *
 * @param   int   $del_dr_aid  [$del_dr_aid description]
 *
 * @return  void
 */
function deletedroits(int $del_dr_aid): void
{
    DB::table('droits')->where('d_aut_aid', $del_dr_aid)->delete();
}

/**
 * [updatedroits description]
 *
 * @param   string  $chng_aid  [$chng_aid description]
 *
 * @return  void
 */
function updatedroits(string $chng_aid): void
{
    foreach ($_POST as $y => $w) {
        if (stristr("$y", 'ad_d_')) {
            DB::table('droits')->insert(array(
                'd_aut_aid' => $chng_aid,
                'd_fon_fid' => $w,
                'd_droits'  => 11111,
            ));
        }
    }
}

/**
 * [updateadmin description]
 *
 * @param   string  $chng_aid          [$chng_aid description]
 * @param   string  $chng_name         [$chng_name description]
 * @param   string  $chng_email        [$chng_email description]
 * @param   string  $chng_url          [$chng_url description]
 * @param   int     $chng_radminsuper  [$chng_radminsuper description]
 * @param   string  $chng_pwd          [$chng_pwd description]
 * @param   string  $chng_pwd2         [$chng_pwd2 description]
 * @param   string  $ad_d_27           [$ad_d_27 description]
 * @param   string  $old_pwd           [$old_pwd description]
 *
 * @return  void
 */
function updateadmin(string $chng_aid, string $chng_name, string $chng_email, string $chng_url, int $chng_radminsuper, string $chng_pwd, string $chng_pwd2, string $ad_d_27, string $old_pwd): void
{
    if (!($chng_aid && $chng_name && $chng_email)) {
        Header("Location: admin.php?op=mod_authors");
    }

    if (mailler::checkdnsmail($chng_email) === false) {
        include("themes/default/header.php");
        
        GraphicAdmin(manuel('authors'));
        
        echo error_handler(adm_translate("ERREUR : DNS ou serveur de mail incorrect") . '<br />');
        
        include("themes/default/footer.php");
        return;
    }

    $author = DB::table('authors')->select('radminsuper')->where('aid', $chng_aid)->first();

    if (!$author['radminsuper'] and $chng_radminsuper) {
        @copy("modules/f-manager/config/modele.admin.conf.php", "modules/f-manager/config/" . strtolower($chng_aid) . ".conf.php");
        
        deletedroits($chng_aid);
    }

    if ($author['radminsuper'] and !$chng_radminsuper) {
        @unlink("modules/f-manager/config/" . strtolower($chng_aid) . ".conf.php");
        
        updatedroits($chng_aid);
    }

    if (file_exists("modules/f-managerconfigs/" . strtolower($chng_aid) . ".conf.php") and $ad_d_27 != '27') {
        @unlink("modules/f-manager/config/" . strtolower($chng_aid) . ".conf.php");
    }
    
    if (($chng_radminsuper or $ad_d_27 != '') and !file_exists("modules/f-manager/config/" . strtolower($chng_aid) . ".conf.php")) {
        @copy("modules/f-manager/config/modele.admin.conf.php", "modules/f-manager/config/" . strtolower($chng_aid) . ".conf.php");
    }

    if ($chng_pwd2 != '') {
        if ($chng_pwd != $chng_pwd2) {
            include("themes/default/header.php");
            
            GraphicAdmin(manuel('authors'));
            
            echo error_handler(adm_translate("Désolé, les nouveaux Mots de Passe ne correspondent pas. Cliquez sur retour et recommencez") . '<br />');
            
            include("themes/default/footer.php");
            exit;
        }

        $AlgoCrypt = PASSWORD_BCRYPT;
        $min_ms = 100;
        $options = ['cost' => users::getOptimalBcryptCostParameter($chng_pwd, $AlgoCrypt, $min_ms)];
        $hashpass = password_hash($chng_pwd, $AlgoCrypt, $options);
        $chng_pwd = crypt($chng_pwd, $hashpass);

        if ($old_pwd) {
            global $admin;

            $Xadmin = base64_decode($admin);
            $Xadmin = explode(':', $Xadmin);
            $aid = urlencode($Xadmin[0]);
            $AIpwd = $Xadmin[1];

            if ($aid == $chng_aid) {
                if (md5($old_pwd) == $AIpwd and $chng_pwd != '') {
                    $admin = base64_encode("$aid:" . md5($chng_pwd));
                    
                    $admin_cook_duration = Config::get('npds.admin_cook_duration');

                    if ($admin_cook_duration <= 0) {
                        $admin_cook_duration = 1;
                    }

                    $timeX = time() + (3600 * $admin_cook_duration);

                    setcookie('admin', $admin, $timeX);
                    setcookie('adm_exp', $timeX, $timeX);
                }
            }
        }

        if($chng_radminsuper == 1) { 
            DB::table('authors')->where('aid', $chng_aid)->update(array(
                'name'          => $chng_name,
                'email'         => $chng_email,
                'url'           => $chng_url,
                'radminsuper'   => $chng_radminsuper,
                'pwd'           => $chng_pwd,
                'hashkey'       => 1,
            ));
        } else {
            DB::table('authors')->where('aid', $chng_aid)->update(array(
                'name'          => $chng_name,
                'email'         => $chng_email,
                'url'           => $chng_url,
                'radminsuper'   => 0,
                'pwd'           => $chng_pwd,
                'hashkey'       => 1,
            ));
        }
    } else {
        if ($chng_radminsuper == 1) {
            DB::table('authors')->where('aid', $chng_aid)->update(array(
                'name'          => $chng_name,
                'email'         => $chng_email,
                'url'           => $chng_url,
                'radminsuper'   => $chng_radminsuper,
            ));

            deletedroits($chng_aid);
        } else {
            DB::table('authors')->where('aid', $chng_aid)->update(array(
                'name'          => $chng_name,
                'email'         => $chng_email,
                'url'           => $chng_url,
                'radminsuper'   => 0,
            ));

            deletedroits($chng_aid);
            updatedroits($chng_aid);
        }
    }

    global $aid;
    logs::Ecr_Log('security', "ModifyAuthor($chng_name) by AID : $aid", '');

    Header("Location: admin.php?op=mod_authors");
}

/**
 * [error_handler description]
 *
 * @param   string  $ibid  [$ibid description]
 *
 * @return  void
 */
function error_handler(string $ibid): void
{
    echo '
    <div class="alert alert-danger mb-3">
    ' . adm_translate("Merci d'entrer l'information en fonction des spécifications") . '<br />' . $ibid . '
    </div>
    <a class="btn btn-outline-secondary" href="admin.php?op=mod_authors" >' . adm_translate("Retour en arrière") . '</a>';
}

switch ($op) {
    case 'mod_authors':
        displayadmins();
        break;

    case 'modifyadmin':
        modifyadmin($chng_aid);
        break;

    case 'UpdateAuthor':
        settype($chng_radminsuper, 'int');
        settype($ad_d_27, 'int');

        updateadmin($chng_aid, $chng_name, $chng_email, $chng_url, $chng_radminsuper, $chng_pwd, $chng_pwd2, $ad_d_27, $old_pwd);
        break;

    case 'AddAuthor':
        settype($add_radminsuper, 'int');

        if (!($add_aid && $add_name && $add_email && $add_pwd)) {
            include("themes/default/header.php");

            GraphicAdmin(manuel('authors'));

            echo error_handler(adm_translate("Vous devez remplir tous les Champs") . '<br />');

            include("themes/default/footer.php");
            return;
        }

        if (mailler::checkdnsmail($add_email) === false) {
            include("themes/default/header.php");

            GraphicAdmin(manuel('authors'));

            echo error_handler(adm_translate("ERREUR : DNS ou serveur de mail incorrect") . '<br />');

            include("themes/default/footer.php");
            return;
        }

        $AlgoCrypt = PASSWORD_BCRYPT;
        $min_ms = 100;
        $options = ['cost' => users::getOptimalBcryptCostParameter($add_pwd, $AlgoCrypt, $min_ms)];
        $hashpass = password_hash($add_pwd, $AlgoCrypt, $options);
        $add_pwdX = crypt($add_pwd, $hashpass);

        DB::table('authors')->insert(array(
            'aid'           => $add_aid,
            'name'          => $add_name,
            'url'           => $add_url,
            'email'         => $add_email,
            'pwd'           => $add_pwdX,
            'hashkey'       => 1,
            'counter'       => 0,
            'radminsuper'   => $add_radminsuper,
        ));

        updatedroits($add_aid);

        // Copie du fichier pour filemanager
        if ($add_radminsuper or isset($ad_d_27)) { // $ad_d_27 pas là ?
            @copy("modules/f-manager/config/modele.admin.conf.php", "modules/f-manager/config/" . strtolower($add_aid) . ".conf.php");
        }

        global $aid;
        logs::Ecr_Log('security', "AddAuthor($add_aid) by AID : $aid", '');

        Header("Location: admin.php?op=mod_authors");
        break;

    case 'deladmin':
        include("themes/default/header.php");

        GraphicAdmin(manuel('authors'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <h3>' . adm_translate("Effacer l'Administrateur") . ' : <span class="text-muted">' . $del_aid . '</span></h3>
        <div class="alert alert-danger">
        <p><strong>' . adm_translate("Etes-vous sûr de vouloir effacer") . ' ' . $del_aid . ' ? </strong></p>
        <a href="admin.php?op=deladminconf&amp;del_aid=' . $del_aid . '" class="btn btn-danger btn-sm">' . adm_translate("Oui") . '</a>&nbsp;<a href="admin.php?op=mod_authors" class="btn btn-secondary btn-sm">' . adm_translate("Non") . '</a>
        </div>';

        css::adminfoot('', '', '', '');
        break;

    case 'deladminconf':
        DB::table('authors')->where('aid', $del_aid)->delete();

        deletedroits($chng_aid = $del_aid);

        DB::table('publisujet')->where('aid', $del_aid)->delete();

        // Supression du fichier pour filemanager
        @unlink("modules/f-manager/config/" . strtolower($del_aid) . ".conf.php");

        global $aid;
        logs::Ecr_Log('security', "DeleteAuthor($del_aid) by AID : $aid", '');

        Header("Location: admin.php?op=mod_authors");
        break;
}
