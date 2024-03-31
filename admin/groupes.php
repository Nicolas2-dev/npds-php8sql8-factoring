<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* BIG mod by JPB for NPDS-WS                                           */
/*                                                                      */
/* NPDS Copyright (c) 2002-2023 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/


use npds\system\assets\js;
use npds\system\logs\logs;
use npds\system\assets\css;
use npds\system\theme\theme;
use npds\system\language\language;
use npds\system\support\facades\DB;

if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'groupes';
$f_titre = adm_translate('Gestion des groupes');

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

global $language, $adminimg, $admf_ext;
$hlpfile = "manuels/$language/groupes.html";

settype($al, 'string');

if ($al) {
    if (preg_match('#^mod#', $al)) {
        $al = explode('_', $al);
        $mes = adm_translate("Vous ne pouvez pas exclure") . ' ' . $al[1] . ' ' . adm_translate("car il est modérateur unique de forum. Oter ses droits de modération puis retirer le du groupe.");
    }
}

/**
 * [group_liste description]
 *
 * @return  void
 */
function group_liste(): void
{
    global $hlpfile, $al, $mes, $f_meta_nom, $f_titre, $adminimg;

    include("themes/default/header.php");

    GraphicAdmin($hlpfile);

    $one_gp = false;
    $tab_groupeII = array();
    $tab_groupeIII = array();

    $groupes = DB::table('groupes')->select('groupe_id')->orderBy('groupe_id', 'ASC')->get();

    foreach($groupes as $gl) {
        $tab_groupeII[$gl['groupe_id']] = '';
    }

    $users_status = DB::table('users_status')->select('uid', 'groupe')->where('groupe', '!=', '')->orderBy('uid', 'ASC')->get();

    foreach($users_status as $status) {    
        $one_gp = true;
        $tab_groupe = explode(',', $status['groupe']);

        if ($tab_groupe) {
            foreach ($tab_groupe as $groupevalue) {
                if ($groupevalue != '') {
                    $tab_groupeII[$groupevalue] .= $status['uid'] . ' ';
                    $tab_groupeIII[$groupevalue] = $groupevalue;
                }
            }
        }
    }

    adminhead($f_meta_nom, $f_titre, $adminimg);

    echo '<script type="text/javascript">
    //<![CDATA[';

    if ($al) {
        echo 'bootbox.alert("' . $mes . '")';
    }

    echo '
    tog(\'lst_gr\',\'show_lst_gr\',\'hide_lst_gr\');

    //==> choix moderateur
    function choisir_mod_forum(gp,gn,ar_user,ar_uid) {
        var user_json = ar_user.split(",");
        var uid_json = ar_uid.split(",");
        var choix_mod = prompt("' . html_entity_decode(adm_translate("Choisir un modérateur"), ENT_COMPAT | ENT_HTML401, 'utf-8') . ' : \n"+user_json);
        if (choix_mod) {
            for (i=0; i<user_json.length; i++) {
                if (user_json[i] == choix_mod) {var ind_uid=i;}
            }
            var xhr_object = null;
            if (window.XMLHttpRequest) // FF
                xhr_object = new XMLHttpRequest();
            else if(window.ActiveXObject) // IE
                xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
            xhr_object.open("GET", "admin.php?op=forum_groupe_create&groupe_id="+gp+"&groupe_name="+gn+"&moder="+uid_json[ind_uid], false);
            xhr_object.send(null);
            document.location.href="admin.php?op=groupes";
        }
    } 
    //<== choix moderateur

    //==> confirmation suppression tous les membres du groupe (done in xhr)
    function delete_AllMembersGroup(grp,ugp) {
        var xhr_object = null;
        if (window.XMLHttpRequest) // FF
            xhr_object = new XMLHttpRequest();
        else if(window.ActiveXObject) // IE
            xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
        if (confirm("' . adm_translate("Vous allez exclure TOUS les membres du groupe") . ' "+grp+" !")) {
            xhr_object.open("GET", location.href="admin.php?op=retiredugroupe_all&groupe_id="+grp+"&tab_groupe="+ugp, false);
        }
    }
    //<== confirmation suppression tous les membres du groupe (done in xhr)

    //==> confirmation suppression groupe (done in xhr)
    function confirm_deleteGroup(gr) {
        var xhr_object = null;
        if (window.XMLHttpRequest) // FF
            xhr_object = new XMLHttpRequest();
        else if(window.ActiveXObject) // IE
            xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
        if (confirm("' . adm_translate("Vous allez supprimer le groupe") . ' "+gr)) {
            xhr_object.open("GET", location.href="admin.php?op=groupe_maj&groupe_id="+gr+"&sub_op=' . adm_translate("Supprimer") . '", false);
        }
    }
    //<== confirmation suppression groupe (done in xhr)
    //]]>
    </script>';

    echo '
    <hr />
    <form action="admin.php" method="post" name="nouveaugroupe">
        <input type="hidden" name="op" value="groupe_add" />
        <a href="#" onclick="document.forms[\'nouveaugroupe\'].submit()" title="' . adm_translate("Ajouter un groupe") . '" data-bs-toggle="tooltip" data-bs-placement="right"><i class="fas fa-users fa-2x"></i><i class="fa fa-plus fa-lg me-1"></i></a> 
    </form>
    <hr />
    <h3 class="my-3"><a class="tog small" id="hide_lst_gr" title="' . adm_translate("Replier la liste") . '" ><i id="i_lst_gr" class="fa fa-caret-up fa-lg text-primary" ></i></a>&nbsp;' . adm_translate("Liste des groupes") . '</h3>
    <div id="lst_gr" class="row">
        <div id="gr_dat" class="p-3">';

    $lst_gr_json = '';
    if ($one_gp) {
        sort($tab_groupeIII);

        foreach ($tab_groupeIII as $bidon => $gp) {
            $lst_user_json = '';

            $result = DB::table('groupes')
                        ->select('groupe_id', 'groupe_name', 'groupe_description', 'groupe_forum', 'groupe_mns', 'groupe_chat', 'groupe_blocnote', 'groupe_pad')
                        ->where('groupe_id', $gp)
                        ->first();

            echo '
            <div id="bloc_gr_' . $gp . '" class="row border rounded ms-1 p-2 px-0 mb-2 w-100">
                <div class="col-lg-4 ">
                <span>' . $gp . '</span>
                <i class="fa fa-users fa-2x text-muted"></i><h4 class="my-2">' . language::aff_langue($result['groupe_name']) . '</h4><p>' . language::aff_langue($result['groupe_description']);
            
            if (file_exists('storage/users_private/groupe/' . $gp . '/groupe.png')) {
                echo '<img class="d-block my-2" src="storage/users_private/groupe/' . $gp . '/groupe.png" width="80" height="80" alt="logo_groupe" />';
            }
            
            echo '
                </div>
                <div class="col-lg-5">';

            $tab_groupe = explode(' ', ltrim($tab_groupeII[$gp]));
            $nb_mb = (count($tab_groupe)) - 1;

            echo '
                <a class="tog" id="show_lst_mb_' . $gp . '" title="' . adm_translate("Déplier la liste") . '"><i id="i_lst_mb_gr_' . $gp . '" class="fa fa-caret-down fa-lg text-primary" ></i></a>&nbsp;&nbsp;
                <i class="fa fa-user fa-2x text-muted"></i> <span class=" align-top badge bg-secondary">&nbsp;' . $nb_mb . '</span>&nbsp;&nbsp;';
            
            $lst_uid_json = '';
            //$lst_uidna_json = ''; // ??? not used

            //==> liste membres du groupe
            echo '<ul id="lst_mb_gr_' . $gp . '" style ="display:none; padding-left:0px; -webkit-padding-start: 0px;">';
            
            foreach ($tab_groupe as $bidon => $uidX) {
                if ($uidX) {
                    
                    $user = DB::table('users')
                        ->select('uname', 'user_avatar')
                        ->where('uid', $uidX)
                        ->first();

                    $lst_user_json .= $user['uname'] . ',';
                    $lst_uid_json .= $uidX . ',';
                    $lst_gr_json .= '\'mbgr_' . $gp . '\': { gp: \'' . $gp . '\'},';
                    
                    if (!$user['user_avatar']) {
                        $imgtmp = "assets/images/forum/avatar/blank.gif";
                    } else if (stristr($user['user_avatar'], "users_private")) {
                        $imgtmp = $user['user_avatar'];
                    } else {
                        if ($ibid = theme::theme_image("forum/avatar/".$user['user_avatar'])) {
                            $imgtmp = $ibid;
                        } else {
                            $imgtmp = "assets/images/forum/avatar/".$user['user_avatar'];
                        }

                        if (!file_exists($imgtmp)) {
                            $imgtmp = "assets/images/forum/avatar/blank.gif";
                        }
                    }

                    echo '
                <li id="' . $user['uname'] . $uidX . '_' . $gp . '" style="list-style-type:none;">
                    <div style="float:left;">
                        <a class="adm_tooltip"><em style="width:90px;"><img src="' . $imgtmp . '"  height="80" width="80" alt="avatar"/></em>- </a>
                    </div>
                    <div class="text-truncate" style="min-width:110px; width:110px; float:left;">
                        ' . $user['uname'] . '
                    </div>
                    <div>
                        <a href="admin.php?chng_uid=' . $uidX . '&amp;op=modifyUser" title="' . adm_translate("Editer les informations concernant") . ' ' . $user['uname'] . '" data-bs-toggle="tooltip"><i class="fa fa-edit fa-lg fa-fw me-1"></i></a>
                        <a href="admin.php?op=retiredugroupe&amp;uid=' . $uidX . '&amp;uname=' . $user['uname'] . '&amp;groupe_id=' . $gp . '" title="' . adm_translate("Exclure") . ' ' . $user['uname'] . ' ' . adm_translate("du groupe") . ' ' . $gp . '" data-bs-toggle="tooltip"><i class="fa fa-user-times fa-lg fa-fw text-danger me-1"></i></a>
                        <a href="" data-bs-toggle="collapse" data-bs-target="#moderation_' . $uidX . '_' . $gp . '" ><i class="fa fa-balance-scale fa-lg fa-fw" title="' . adm_translate("Modérateur") . '" data-bs-toggle="tooltip"></i></a>
                        <div id="moderation_' . $uidX . '_' . $gp . '" class="collapse">';
                    
                    //=>traitement moderateur
                    if ($result['groupe_forum'] == 1) {
                        $pat = '#\b' . $uidX . '\b#';
                        
                        $forums = DB::table('forums')->select('forum_id', 'forum_name', 'forum_moderator')->where('forum_pass', $gp)->get();

                        foreach ($forums as $row) {
                            $ar_moder = explode(',', $row[2]);
                            $tmp_moder = $ar_moder;
                            
                            if (preg_match($pat, $row[2])) {
                                unset($tmp_moder[array_search($uidX, $tmp_moder)]);
                                
                                $new_moder = implode(',', $tmp_moder);
                                
                                echo count($tmp_moder) != 0 ?
                                    '<a href="admin.php?op=moderateur_update&amp;forum_id=' . $row[0] . '&amp;forum_moderator=' . $new_moder . '" title="' . adm_translate("Oter") . ' ' . $user['uname'] . ' ' . adm_translate("des modérateurs du forum") . ' ' . $row[0] . '" data-bs-toggle="tooltip" data-bs-placement="right"><i class="fa fa-balance-scale fa-lg fa-fw text-danger me-1"></i></a>' :
                                    '<i class="fa fa-balance-scale fa-lg fa-fw me-1" title="' . adm_translate("Ce modérateur") . " (" . $user['uname'] . ") " . adm_translate("n'est pas modifiable tant qu'un autre n'est pas nommé pour ce forum") . ' ' . $row[0] . '" data-bs-toggle="tooltip" data-bs-placement="right" ></i>';
                            } else {
                                $tmp_moder[] = $uidX;
                                asort($tmp_moder);

                                $new_moder = implode(',', $tmp_moder);

                                echo '<a href="admin.php?op=moderateur_update&amp;forum_id=' . $row[0] . '&amp;forum_moderator=' . $new_moder . '" title="' . adm_translate("Nommer") . ' ' . $user['uname'] . ' ' . adm_translate("comme modérateur du forum") . ' ' . $row[1] . ' (' . $row[0] . ')" data-bs-toggle="tooltip" data-bs-placement="right" ><i class="fa fa-balance-scale fa-lg fa-fw me-1"></i></a>';
                            }
                        }
                    }

                    echo '
                        </div>
                    </div>
                    </li>';
                }
            }

            echo '
            </ul>';

            $lst_user_json = rtrim($lst_user_json, ',');
            $lst_uid_json = rtrim($lst_uid_json, ',');

            //==> pliage repliage listes membres groupes
            echo '
            <script type="text/javascript">
                //<![CDATA[
                tog(\'lst_mb_gr_' . $gp . '\',\'show_lst_mb_' . $gp . '\',\'hide_lst_mb_' . $gp . '\');
                //]]>
            </script>
            <i class="fa fa-user-times fa-lg text-danger" title="' . adm_translate('Exclure TOUS les membres du groupe') . ' ' . $gp . '" data-bs-toggle="tooltip" data-bs-placement="right" onclick="delete_AllMembersGroup(\'' . $gp . '\',\'' . $lst_uid_json . '\');"></i>';
            //<== liste membres du groupe

            //==> menu groupe
            echo '
            </div>
            <div class="col-lg-3 list-group-item px-0 mt-2 mt-md-0">
                <a class="btn btn-outline-secondary btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="admin.php?op=groupe_edit&amp;groupe_id=' . $gp . '" title="' . adm_translate("Editer groupe") . ' ' . $gp . '" data-bs-toggle="tooltip"  ><i class="fas fa-pencil-alt fa-lg"></i></a><a class="btn btn-outline-danger btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="javascript:void(0);" onclick="bootbox.alert(\'' . adm_translate("Avant de supprimer le groupe") . ' ' . $gp . ' ' . adm_translate("vous devez supprimer TOUS ses membres !") . '\');" title="' . adm_translate("Supprimer groupe") . ' ' . $gp . '" data-bs-toggle="tooltip"  ><i class="fas fa-trash fa-lg fa-fw"></i></a><a class="btn btn-outline-secondary btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="admin.php?op=membre_add&amp;groupe_id=' . $gp . '" title="' . adm_translate("Ajouter un ou des membres au groupe") . ' ' . $gp . '" data-bs-toggle="tooltip"  ><i class="fa fa-user-plus fa-lg fa-fw"></i></a><a class="btn btn-outline-secondary btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="admin.php?op=bloc_groupe_create&amp;groupe_id=' . $gp . '" title="' . adm_translate("Créer le bloc WS") . ' (' . $gp . ')" data-bs-toggle="tooltip"  ><i class="fa fa-clone fa-lg fa-fw"></i><i class="fa fa-plus"></i></a>';
            
            echo $result['groupe_pad'] == 1 
                ? '<a class="btn btn-outline-danger btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="admin.php?op=pad_remove&amp;groupe_id=' . $gp . '" title="' . adm_translate("Désactiver PAD du groupe") . ' ' . $gp . '" data-bs-toggle="tooltip"  ><i class="fa fa-edit fa-lg fa-fw"></i><i class="fa fa-minus"></i></a>' 
                : '<a class="btn btn-outline-secondary btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="admin.php?op=pad_create&amp;groupe_id=' . $gp . '" title="' . adm_translate("Activer PAD du groupe") . ' ' . $gp . '" data-bs-toggle="tooltip"  ><i class="fa fa-edit fa-lg fa-fw"></i><i class="fa fa-plus"></i></a>';
            
            echo $result['groupe_blocnote'] == 1 
                ? '<a class="btn btn-outline-danger btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="admin.php?op=note_remove&amp;groupe_id=' . $gp . '" title="' . adm_translate("Désactiver bloc-note du groupe") . ' ' . $gp . '" data-bs-toggle="tooltip"  ><i class="far fa-sticky-note fa-lg fa-fw"></i><i class="fa fa-minus"></i></a>' 
                : '<a class="btn btn-outline-secondary btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="admin.php?op=note_create&amp;groupe_id=' . $gp . '" title="' . adm_translate("Activer bloc-note du groupe") . ' ' . $gp . '" data-bs-toggle="tooltip"  ><i class="far fa-sticky-note fa-lg fa-fw"></i><i class="fa fa-plus"></i></a>';
            
            echo file_exists('modules/f-manager/config/groupe_' . $gp . '.conf.php') 
                ? '<a class="btn btn-outline-danger btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="admin.php?op=workspace_archive&amp;groupe_id=' . $gp . '" title="' . adm_translate("Désactiver gestionnaire de fichiers du groupe") . ' ' . $gp . '" data-bs-toggle="tooltip"  ><i class="far fa-folder fa-lg fa-fw"></i><i class="fa fa-minus"></i></a>' 
                : '<a class="btn btn-outline-secondary btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="admin.php?op=workspace_create&amp;groupe_id=' . $gp . '" title="' . adm_translate("Activer gestionnaire de fichiers du groupe") . ' ' . $gp . '" data-bs-toggle="tooltip"  ><i class="far fa-folder fa-lg fa-fw"></i><i class="fa fa-plus"></i></a>';
            
            echo $result['groupe_forum'] == 1 
                ? '<a class="btn btn-outline-danger btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="admin.php?op=forum_groupe_delete&amp;groupe_id=' . $gp . '" title="' . adm_translate("Supprimer forum du groupe") . ' ' . $gp . '" data-bs-toggle="tooltip"  ><i class="fa fa-list-alt fa-lg fa-fw"></i><i class="fa fa-minus"></i></a>' 
                : '<a class="btn btn-outline-secondary btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="javascript:void(0);" onclick="javascript:choisir_mod_forum(\'' . $gp . '\',\'' . $result['groupe_name'] . '\',\'' . $lst_user_json . '\',\'' . $lst_uid_json . '\');" title="' . adm_translate("Créer forum du groupe") . ' ' . $gp . '" data-bs-toggle="tooltip"  ><i class="fa fa-list-alt fa-lg fa-fw"></i> <i class="fa fa-plus"></i></a>';
            
            echo $result['groupe_mns'] == 1 
                ? '<a class="btn btn-outline-danger btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="admin.php?op=groupe_mns_delete&amp;groupe_id=' . $gp . '" title="' . adm_translate("Supprimer MiniSite du groupe") . ' ' . $gp . '" data-bs-toggle="tooltip"  ><i class="fa fa-desktop fa-lg fa-fw"></i><i class="fa fa-minus"></i></a>' 
                : '<a class="btn btn-outline-secondary btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="admin.php?op=groupe_mns_create&amp;groupe_id=' . $gp . '" title="' . adm_translate("Créer MiniSite du groupe") . ' ' . $gp . '" data-bs-toggle="tooltip"  ><i class="fa fa-desktop fa-lg fa-fw"></i><i class="fa fa-plus"></i></a>';
            
            echo $result['groupe_chat'] == 0 
                ? '<a class="btn btn-outline-secondary btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="admin.php?op=groupe_chat_create&amp;groupe_id=' . $gp . '" title="' . adm_translate("Activer chat du groupe") . ' ' . $gp . '" data-bs-toggle="tooltip"  ><i class="far fa-comments fa-lg fa-fw"></i><i class="fa fa-plus"></i></a>' 
                : '<a class="btn btn-outline-danger btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="admin.php?op=groupe_chat_delete&amp;groupe_id=' . $gp . '" title="' . adm_translate("Désactiver chat du groupe") . ' ' . $gp . '" data-bs-toggle="tooltip"  ><i class="far fa-comments fa-lg fa-fw"></i><i class="fa fa-minus"></i></a>';
            
            echo '
                </div>
            </div>';
            //<== menu groupe
        }
    }

    // groupes sans membre
    $groupes = DB::table('groupes')->select('groupe_id', 'groupe_name', 'groupe_description')->orderBy('groupe_id', 'ASC')->get();

    foreach ($groupes as $groupe) {
        
        $gp = $groupe['groupe_id'];

        $gpA = true;
        
        if ($tab_groupeIII) {
            foreach ($tab_groupeIII as $bidon => $gpU) {
                if ($gp == $gpU) {
                    $gpA = false;
                }
            }
        }

        if ($gpA) {
            $lst_gr_json .= '\'mbgr_' . $gp . '\': { gp: \'' . $gp . '\'},';
            
            echo '
            <div class="row border rounded ms-1 p-2 px-0 mb-2 w-100">
                <div id="bloc_gr_' . $gp . '" class="col-lg-5">
                <span class="text-danger">' . $gp . '</span>
                <i class="fa fa-users fa-2x text-muted"></i>
                <h4 class="my-2 text-muted">' . language::aff_langue($groupe['groupe_name']) . '</h4>
                <p class="text-muted">' . language::aff_langue($groupe['groupe_description']);

            if (file_exists('storage/users_private/groupe/' . $gp . '/groupe.png')) {
                echo '<img class="d-block my-2" src="storage/users_private/groupe/' . $gp . '/groupe.png" width="80" height="80" />';
            }
            
            echo '
                </p>
                </div>
                <div class="col-lg-4 ">
                <i class="fa fa-user-o fa-2x text-muted"></i><span class="align-top badge bg-secondary ms-1">0</span>
                </div>
                <div class="col-lg-3 list-group-item px-0 mt-2">
                <a class="btn btn-outline-secondary btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="admin.php?op=groupe_edit&amp;groupe_id=' . $gp . '" title="' . adm_translate("Editer groupe") . ' ' . $gp . '" data-bs-toggle="tooltip"  ><i class="fas fa-pencil-alt fa-lg"></i></a><a class="btn btn-outline-danger btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="#" onclick="confirm_deleteGroup(\'' . $gp . '\');" title="' . adm_translate("Supprimer groupe") . ' ' . $gp . '" data-bs-toggle="tooltip" ><i class="fas fa-trash fa-lg"></i></a><a class="btn btn-outline-secondary btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="admin.php?op=membre_add&amp;groupe_id=' . $gp . '" title="' . adm_translate("Ajouter un ou des membres au groupe") . ' ' . $gp . '" data-bs-toggle="tooltip" ><i class="fa fa-user-plus fa-lg"></i></a>
                </div>
            </div>';
        }
    }

    $lst_gr_json = rtrim($lst_gr_json, ',');

    echo '
        </div>
    </div>';

    css::adminfoot('', '', '', '');
}

// MEMBRE

/**
 * [membre_add description]
 *
 * @param   int   $gp  [$gp description]
 *
 * @return  void
 */
function membre_add(int $gp): void
{
    global $hlpfile, $f_meta_nom, $f_titre, $adminimg;

    include("themes/default/header.php");

    GraphicAdmin($hlpfile);
    adminhead($f_meta_nom, $f_titre, $adminimg);

    echo '
    <hr />
    <h3>' . adm_translate("Ajouter des membres") . ' / ' . adm_translate("Groupe") . ' : ' . $gp . '</h3>
    <form id="groupesaddmb" class="admform" action="admin.php" method="post">
        <fieldset>
            <legend><i class="fa fa-users fa-2x text-muted"></i></legend>
            <div class="mb-3">
                <label class="col-form-label" for="luname">' . adm_translate("Liste des membres") . '</label>
                <input type="text" class="form-control" id="luname" name="luname" maxlength="255" value="" required="required" />
                <span class="help-block text-end"><span id="countcar_luname"></span></span>
            </div>
            <input type="hidden" name="op" value="membre_add_finish" />
            <input type="hidden" name="groupe_id" value="' . $gp . '" />
            <div class="mb-3">
                <input class="btn btn-primary" type="submit" name="sub_op" value="' . adm_translate("Sauver les modifications") . '" />
            </div>
        </fieldset>
    </form>';

    $arg1 = '
    var formulid = ["groupesaddmb"];
    inpandfieldlen("luname",255);
    ';

    echo (mysqli_get_client_info() <= '8.0') 
        ? js::auto_complete_multi_query('membre', 'uname', 'luname', DB::table('users')->join('users_status', 'users.uid', '=', 'users_status.uid', 'inner')->where('users.uid', '<>', 1)->where('groupe', 'NOT REGEXP', '[[:<:]]' . $gp . '[[:>:]]')->get()) 
        : js::auto_complete_multi_query('membre', 'uname', 'luname', DB::table('users')->join('users_status', 'users.uid', '=', 'users_status.uid', 'inner')->where('users.uid', '<>', 1)->where('groupe', 'NOT REGEXP', '\\b' . $gp . '\\b\'')->get());
    
    css::adminfoot('fv', '', $arg1, '');
}

/**
 * [membre_add_finish description]
 *
 * @param   int     $groupe_id  [$groupe_id description]
 * @param   string  $luname     [$luname description]
 *
 * @return  void
 */
function membre_add_finish(int $groupe_id, string $luname): void
{
    include('powerpack_f.php');

    $image = '18.png';

    $groupes = DB::table('groupes')->select('groupe_name')->where('groupe_id', $groupe_id)->first();

    $gn = $groupes['groupe_name'];

    $luname = rtrim($luname, ', ');
    $luname = str_replace(' ', '', $luname);

    $list_membres = explode(',', $luname);
    $nbremembres = count($list_membres);

    $subject = adm_translate('Nouvelles du groupe') . ' ' . $gn;
    $message = adm_translate('Vous faites désormais partie des membres du groupe') . ' : ' . $gn . ' [' . $groupe_id . '].';

    $copie = '';
    $from_userid = 1;

    for ($j = 0; $j < $nbremembres; $j++) {
        $uname = $list_membres[$j];

        $ibid = DB::table('users')->select('uid')->where('uname', $uname)->first();

        if ($ibid['uid']) {
            $to_userid = $uname;

            $ibid2 = DB::table('users_status')->select('groupe')->where('uid', $ibid['uid'])->first();

            $lesgroupes = explode(',', $ibid2['groupe']);
            $nbregroupes = count($lesgroupes);

            $groupeexistedeja = false;
            for ($i = 0; $i < $nbregroupes; $i++) {
                if ($lesgroupes[$i] == $groupe_id) {
                    $groupeexistedeja = true;
                    break;
                }
            }

            if (!$groupeexistedeja) {
                if ($ibid2['groupe']) {
                    $groupesmodif = $ibid2['groupe'] . ',' . $groupe_id;
                } else {
                    $groupesmodif = $groupe_id;
                }

                DB::table('users_status')->where('uid', $ibid['uid'])->update(array(
                    'groupe'       => $$groupesmodif,
                ));
            }

            writeDB_private_message($to_userid, $image, $subject, $from_userid, $message, $copie);
        }
    }

    global $aid;
    logs::Ecr_Log('security', "AddMemberToGroup($groupe_id, $luname) by AID : $aid", '');

    Header("Location: admin.php?op=groupes");
}

/**
 * [retiredugroupe description]
 *
 * @param   int     $groupe_id  [$groupe_id description]
 * @param   int     $uid        [$uid description]
 * @param   string  $uname      [$uname description]
 *
 * @return  void
 */
function retiredugroupe(int $groupe_id, int $uid, string $uname): void
{
    include('powerpack_f.php');

    $image = '18.png';

    $gn = DB::table('groupes')->select('groupe_name')->where('groupe_id', $groupe_id)->first();

    $pat = '#^\b' . $uid . '\b$#';
    $mes_sys = '';
    $q = '';
    $ok = 0;

    $res = sql_query("SELECT f.forum_id, f.forum_name, f.forum_moderator FROM " . $NPDS_Prefix . "forums f WHERE f.forum_pass='$groupe_id' AND cat_id='-1'");
    
    $res = DB::table('forums')->select('forum_moderator')->where('forum_pass', $groupe_id)->where('cat_id', -1)->get();
    
    foreach($res as $row) {
        
        if (preg_match($pat, $row['forum_moderator'])) {
            $mes_sys = 'mod_' . $uname;
            $q = '&al=' . $mes_sys;
            $ok = 1;
        }
    }

    if ($ok == 0) {
        $pat = '#\b' . $uid . '\b#';

        $res = DB::table('forums')->select('forum_id', 'forum_moderator')->where('forum_pass', $groupe_id)->where('cat_id', -1)->get();

        foreach($res as $r) {
            $new_moder = preg_replace('#,,#', ',', trim(preg_replace($pat, '', $r['forum_moderator']), ','));
            
            DB::table('forums')->where('forum_id', $r['forum_id'])->update(array(
                'forum_moderator'   => $new_moder,
            ));
        }

        $subject = adm_translate('Nouvelles du groupe') . ' ' . $gn;
        $message = adm_translate('Vous ne faites plus partie des membres du groupe') . ' : ' . $gn . ' [' . $groupe_id . '].';

        $copie = '';
        $from_userid = 1;
        $to_userid = $uname;

        $valeurs = DB::table('users_status')->select('groupe')->where('uid', $uid)->first();

        $lesgroupes = explode(',', $valeurs['groupe']);
        $nbregroupes = count($lesgroupes);

        $groupesmodif = '';

        for ($i = 0; $i < $nbregroupes; $i++) {
            if ($lesgroupes[$i] != $groupe_id) {
                if ($groupesmodif == '') $groupesmodif .= $lesgroupes[$i];
                else $groupesmodif .= ',' . $lesgroupes[$i];
            }
        }

        DB::table('users_status')->where('uid', $uid)->update(array(
            'groupe'    => $groupesmodif,
        ));

        writeDB_private_message($to_userid, $image, $subject, $from_userid, $message, $copie);

        global $aid;
        logs::Ecr_Log('security', "DeleteMemberToGroup($groupe_id, $uname) by AID : $aid", '');
    }

    Header("Location: admin.php?op=groupes" . $q);
}

/**
 * [retiredugroupe_all description]
 *
 * @param   int     $groupe_id   [$groupe_id description]
 * @param   string  $tab_groupe  [$tab_groupe description]
 *
 * @return  void
 */
function retiredugroupe_all(int $groupe_id, string $tab_groupe): void
{
    $tab_groupe = explode(',', $tab_groupe);

    foreach ($tab_groupe as $bidon => $uidZ) {
        if ($uidZ) {
            // a rajouter enlever modérateur forum
            $valeurs = DB::table('users_status')->select('groupe')->where('uid', $uidZ)->first();

            $lesgroupes = explode(',', $valeurs['groupe']);
            $nbregroupes = count($lesgroupes);
            $groupesmodif = '';
            
            for ($i = 0; $i < $nbregroupes; $i++) {
                if ($lesgroupes[$i] != $groupe_id) {
                    if ($groupesmodif == '') $groupesmodif .= $lesgroupes[$i];
                    else $groupesmodif .= ',' . $lesgroupes[$i];
                }
            }

            DB::table('users_status')->where('uid', $uidZ)->update(array(
                'groupe'    => $groupesmodif,
            ));

            global $aid;
            logs::Ecr_Log('security', "DeleteAllMemberToGroup($groupe_id, $uidZ) by AID : $aid", '');
        }
    }

    Header("Location: admin.php?op=groupes");
}

// GROUPES

/**
 * [groupe_edit description]
 *
 * @param   int   $groupe_id  [$groupe_id description]
 *
 * @return  void
 */
function groupe_edit(int $groupe_id): void
{
    global $hlpfile, $f_meta_nom, $f_titre, $adminimg;

    include("themes/default/header.php");

    GraphicAdmin($hlpfile);
    adminhead($f_meta_nom, $f_titre, $adminimg);

    $result = DB::table('groupes')->select('groupe_name', 'groupe_description')->where('groupe_id', $groupe_id)->first();

    if ($groupe_id != 'groupe_add') {
        echo '
        <hr />
        <h3>' . adm_translate("Modifier le groupe") . ' : ' . $groupe_id . '</h3>';
    } else {
        echo '
        <hr />
        <h3>' . adm_translate("Créer un groupe.") . '</h3>';
    }  

    echo '
    <form class="admform" id="groupesaddmod" action="admin.php" method="post">
        <fieldset>
            <legend><i class="fas fa-users fa-2x text-muted"></i></legend>' . "\n";

    if ($groupe_id != 'groupe_add') {
        echo '<input type="hidden" name="groupe_id" value="' . $groupe_id . '" />';
    } else {
        echo '
            <div class="mb-3">
                <label for="inp_gr_id" class="admform">ID</label>
                <input id="inp_gr_id" type="number" min="2" max="126" class="form-control" name="groupe_id" value="" required="required"/><span class="help-block">(2...126)</span>
            </div>';
    }

    echo '
            <div class="mb-3">
                <label class="col-form-label" for="grname">' . adm_translate("Nom") . '</label>
                <input type="text" class="form-control" id="grname" name="groupe_name" maxlength="1000" value="';

    echo isset($result) ? $result['groupe_name'] : '';

    echo '" placeholder="' . adm_translate("Nom du groupe") . '" required="required" />
                <span class="help-block text-end"><span id="countcar_grname"></span></span>
            </div>
            <div class="mb-3">
                <label class="col-form-label" for="grdesc">' . adm_translate("Description") . '</label>
                <textarea class="form-control" name="groupe_description" id="grdesc" rows="11" placeholder="' . adm_translate("Description du groupe") . '" required="required">';
    
    echo isset($result) ? $result['groupe_description'] : '';

    echo '</textarea>
            </div>';

    if ($groupe_id != 'groupe_add') {
        echo '<input type="hidden" name="op" value="groupe_maj" />';
    } else {
        echo '<input type="hidden" name="op" value="groupe_add_finish" />';
    }

    echo '
            <div class="mb-3">
                <input class="btn btn-primary" type="submit" name="sub_op" value="' . adm_translate("Sauver les modifications") . '" />
            </div>
        </fieldset>
    </form>';

    $arg1 = '
    var formulid = ["groupesaddmod"];
    inpandfieldlen("grname",1000);
    ';

    css::adminfoot('fv', '', $arg1, '');
}

/**
 * [groupe_maj description]
 *
 * @param   string  $sub_op  [$sub_op description]
 *
 * @return  void
 */
function groupe_maj(string $sub_op): void
{
    global $groupe_id, $groupe_name, $groupe_description;

    if ($sub_op == adm_translate("Sauver les modifications")) {
        DB::table('groupes')->where('groupe_id', $groupe_id)->update(array(
            'groupe_name'           => $groupe_name,
            'groupe_description'    => $groupe_description,
        ));

        global $aid;
        logs::Ecr_Log("security", "UpdateGroup($groupe_id) by AID : $aid", '');
    }

    if ($sub_op == adm_translate("Supprimer")) {
        $maj_ok = true;
        
        $users_status = DB::table('users_status')->select('groupe')->where('groupe', '!=', '')->orderBy('uid', 'ASC')->get();

        foreach ($users_status as $status) {  
            $tab_groupe = explode(',', $status['groupe']);
            
            if ($tab_groupe) {
                foreach ($tab_groupe as $groupevalue) {
                    if ($groupevalue == $groupe_id) {
                        $maj_ok = false;
                        break;
                    }
                }
            }
        }

        if ($maj_ok) {
            groupe_delete($groupe_id);
        }
    }

    Header("Location: admin.php?op=groupes");
}

/**
 * [groupe_delete description]
 *
 * @param   int   $groupe_id  [$groupe_id description]
 *
 * @return  void
 */
function groupe_delete(int $groupe_id): void
{
    DB::table('lblocks')->where('member', $groupe_id)->delete();

    DB::table('rblocks')->where('member', $groupe_id)->delete();

    DB::table('groupes')->where('groupe_id', $groupe_id)->delete();

    DB::table('blocnotes')->where('bnid', md5("WS-BN" . $groupe_id))->delete();

    forum_groupe_delete($groupe_id);
    workspace_archive($groupe_id);
    groupe_mns_delete($groupe_id);

    global $aid;
    logs::Ecr_Log('security', "DeleteGroup($groupe_id) by AID : $aid", '');
}

// WORKSPACE

/**
 * [workspace_create description]
 *
 * @param   int   $groupe_id  [$groupe_id description]
 *
 * @return  void
 */
function workspace_create(int $groupe_id): void
{
    //==>creation fichier conf du groupe
    @copy('modules/f-manager/config/groupe.conf.php', 'modules/f-manager/config/groupe_' . $groupe_id . '.conf.php');
    
    $file = file('modules/f-manager/config/groupe_' . $groupe_id . '.conf.php');
    $file[29] = "   \$access_fma = \"$groupe_id\";\n";
    $fic = fopen('modules/f-manager/config/groupe_' . $groupe_id . '.conf.php', "w");
    
    foreach ($file as $n => $ligne) {
        fwrite($fic, $ligne);
    }

    fclose($fic);

    include("modules/upload/config/upload.conf.php");

    if ($DOCUMENTROOT == '') {
        global $DOCUMENT_ROOT;
        if ($DOCUMENT_ROOT) {
            $DOCUMENTROOT = $DOCUMENT_ROOT;
        } else {
            $DOCUMENTROOT = $_SERVER['DOCUMENT_ROOT'];
        }
    }

    $user_dir = $DOCUMENTROOT . $racine . '/storage/users_private/groupe/' . $groupe_id;

    // DOCUMENTS_GROUPE
    @mkdir('storage/users_private/groupe/' . $groupe_id . '/documents_groupe');

    $repertoire = $user_dir . '/documents_groupe';
    $directory = $racine . '/modules/groupe/matrice/documents_groupe';
    $handle = opendir($DOCUMENTROOT . $directory);

    while (false !== ($file = readdir($handle))) {
        $filelist[] = $file;
    }
    
    asort($filelist);

    foreach ($filelist as $key => $file) {
        if ($file <> '.' and $file <> '..') {
            @copy($DOCUMENTROOT . $directory . '/' . $file, $repertoire . '/' . $file);
        }
    }

    closedir($handle);
    unset($filelist);

    // IMAGES_GROUPE
    @mkdir('storage/users_private/groupe/' . $groupe_id . '/images_groupe');

    $repertoire = $user_dir . '/images_groupe';
    $directory = $racine . '/modules/groupe/matrice/images_groupe';
    $handle = opendir($DOCUMENTROOT . $directory);

    while (false !== ($file = readdir($handle))) {
        $filelist[] = $file;
    }

    asort($filelist);
    foreach ($filelist as $key => $file) {
        if ($file <> '.' and $file <> '..') {
            @copy($DOCUMENTROOT . $directory . '/' . $file, $repertoire . '/' . $file);
        }
    }
    closedir($handle);
    unset($filelist);

    @unlink('storage/users_private/groupe/' . $groupe_id . '/delete');

    global $aid;
    logs::Ecr_Log('security', "CreateWS($groupe_id) by AID : $aid", '');
}

// PAD

/**
 * [pad_create description]
 *
 * @param   int   $groupe_id  [$groupe_id description]
 *
 * @return  void
 */
function pad_create(int $groupe_id): void
{
    DB::table('groupes')->where('groupe_id', $groupe_id)->update(array(
        'groupe_pad'    => 1,
    ));

    global $aid;
    logs::Ecr_Log('security', "CreatePadWS($groupe_id) by AID : $aid", '');
}

/**
 * [pad_remove description]
 *
 * @param   int   $groupe_id  [$groupe_id description]
 *
 * @return  void
 */
function pad_remove(int $groupe_id): void
{
    DB::table('groupes')->where('groupe_id', $groupe_id)->update(array(
        'groupe_pad'    => 0,
    ));

    global $aid;
    logs::Ecr_Log('security', "DeletePadWS($groupe_id) by AID : $aid", '');
}

// BLOC-NOTE

/**
 * [note_create description]
 *
 * @param   int   $groupe_id  [$groupe_id description]
 *
 * @return  void
 */
function note_create(int $groupe_id): void
{
    $sql = "CREATE TABLE IF NOT EXISTS " . DB::getTablePrefix() . "blocnotes (
    bnid text COLLATE utf8mb4_unicode_ci NOT NULL,
    texte text COLLATE utf8mb4_unicode_ci,
    PRIMARY KEY (bnid(32))
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    DB::statement($sql);

    DB::table('groupes')->where('groupe_id', $groupe_id)->update(array(
        'groupe_blocnote'   => 1,
    ));

    global $aid;
    logs::Ecr_Log('security', "CreateBlocnoteWS($groupe_id) by AID : $aid", '');
}

/**
 * [note_remove description]
 *
 * @param   int   $groupe_id  [$groupe_id description]
 *
 * @return  void
 */
function note_remove(int $groupe_id): void
{
    DB::table('blocnotes')->where('bnid', md5("WS-BN" . $groupe_id))->delete();

    DB::table('groupes')->where('groupe_id', $groupe_id)->update(array(
        'groupe_blocnote'   => 0,
    ));

    global $aid;
    logs::Ecr_Log('security', "DeleteBlocnoteWS($groupe_id) by AID : $aid", '');
}

/**
 * [workspace_archive description]
 *
 * @param   int   $groupe_id  [$groupe_id description]
 *
 * @return  void
 */
function workspace_archive(int $groupe_id): void
{
    //=> archivage espace groupe
    $fp = fopen('storage/users_private/groupe/' . $groupe_id . '/delete', 'w');
    fclose($fp);

    //suppression fichier conf
    @unlink('modules/f-manager/config/groupe_' . $groupe_id . '.conf.php');

    global $aid;
    logs::Ecr_Log('security', "ArchiveWS($groupe_id) by AID : $aid", '');
}

// FORUMS

/**
 * [forum_groupe_create description]
 *
 * @param   int     $groupe_id    [$groupe_id description]
 * @param   string  $groupe_name  [$groupe_name description]
 * @param   string  $description  [$description description]
 * @param   int     $moder        [$moder description]
 *
 * @return  void
 */
function forum_groupe_create(int $groupe_id, string $groupe_name, string $description, int $moder): void
{
    // creation forum
    // creation catégorie forum_groupe
    $catagories = DB::table('catagories')->select('cat_id')->where('cat_id', -1)->first();

    if (!$catagories['cat_id']) {
        DB::table('catagories')->insert(array(
            'cat_id'       => -1,
            'cat_title'    => adm_translate("Groupe de travail"),
        ));

    }
    //==>creation forum

    //echo "$groupe_id, $groupe_name, $description, $moder";

    DB::table('forums')->insert(array(
        'forum_name'        => $groupe_name,
        'forum_desc'        => $description,
        'forum_access'      => 1,
        'forum_moderateur'  => $moder,
        'cat_id'            => -1,
        'forum_type'        => 7,
        'forum_pass'        => $groupe_id,
        'arbre'             => 0,
        'attachement'       => 0,
        'forum_index'       => 0,
    ));

    //=> ajout etat forum (1 ou 0) dans le groupe
    DB::table('groupes')->where('groupe_id', $groupe_id)->update(array(
        'groupe_forum'  => 1,
    ));

    global $aid;
    logs::Ecr_Log("security", "CreateForumWS($groupe_id) by AID : $aid", '');
}

/**
 * [moderateur_update description]
 *
 * @param   int     $forum_id         [$forum_id description]
 * @param   string  $forum_moderator  [$forum_moderator description]
 *
 * @return  void
 */
function moderateur_update(int $forum_id, string $forum_moderator): void
{
    DB::table('forums')->where('forum_id', $forum_id)->update(array(
        'forum_moderator'   => $forum_moderator,
    ));
}

/**
 * [forum_groupe_delete description]
 *
 * @param   int   $groupe_id  [$groupe_id description]
 *
 * @return  void
 */
function forum_groupe_delete(int $groupe_id): void
{
    $forum = DB::table('forums')->select('forum_id')->where('forum_pass', $groupe_id)->where('cat_id', -1)->first();

    // suppression des topics
    DB::table('forumtopics')->where('forum_id', $forum['forum_id'])->delete();

    // maj table lecture
    DB::table('forum_read')->where('forum_id', $forum['forum_id'])->delete();

    //=> suppression du forum
    DB::table('forums')->where('forum_id', $forum['forum_id'])->delete();

    // =>remise à 0 forum dans le groupe
    DB::table('groupes')->where('groupe_id', $groupe_id)->update(array(
        'groupe_forum'  => 0,
    ));

    global $aid;
    logs::Ecr_Log('security', "DeleteForumWS(". $forum['forum_id'] .") by AID : $aid", '');
}

// MNS

/**
 * [groupe_mns_create description]
 *
 * @param   int   $groupe_id  [$groupe_id description]
 *
 * @return  void
 */
function groupe_mns_create(int $groupe_id): void
{
    include("modules/upload/config/upload.conf.php");

    if ($DOCUMENTROOT == '') {
        global $DOCUMENT_ROOT;
        if ($DOCUMENT_ROOT) {
            $DOCUMENTROOT = $DOCUMENT_ROOT;
        } else {
            $DOCUMENTROOT = $_SERVER['DOCUMENT_ROOT'];
        }
    }

    $user_dir = $DOCUMENTROOT . $racine . '/storage/users_private/groupe/' . $groupe_id;
    $repertoire = $user_dir . '/mns';

    if (!is_dir($user_dir)) {
        @umask("0000");
        
        if (@mkdir($user_dir, 0777)) {
            $fp = fopen($user_dir . '/index.html', 'w');
            fclose($fp);
            @umask("0000");
            
            if (@mkdir($repertoire, 0777)) {
                $fp = fopen($repertoire . '/index.html', 'w');
                fclose($fp);
                $fp = fopen($repertoire . '/.htaccess', 'w');
                @fputs($fp, 'Deny from All');
                fclose($fp);
            }
        }
    } else {
        @umask("0000");
        if (@mkdir($repertoire, 0777)) {
            $fp = fopen($repertoire . '/index.html', 'w');
            fclose($fp);
            $fp = fopen($repertoire . '/.htaccess', 'w');
            @fputs($fp, 'Deny from All');
            fclose($fp);
        }
    }

    // copie de la matrice par défaut
    $directory = $racine . '/modules/groupe/matrice/mns_groupe';
    $handle = opendir($DOCUMENTROOT . $directory);

    while (false !== ($file = readdir($handle))) {
        $filelist[] = $file;
    }

    asort($filelist);
    
    foreach ($filelist as $key => $file) {
        if ($file <> '.' and $file <> '..') {
            @copy($DOCUMENTROOT . $directory . '/' . $file, $repertoire . '/' . $file);
        }
    }

    closedir($handle);
    unset($filelist);

    DB::table('groupes')->where('groupe_id', $groupe_id)->update(array(
        'groupe_mns'    => 1,
    ));

    global $aid;
    logs::Ecr_Log('security', "CreateMnsWS($groupe_id) by AID : $aid", '');
}

/**
 * [groupe_mns_delete description]
 *
 * @param   int   $groupe_id  [$groupe_id description]
 *
 * @return  void
 */
function groupe_mns_delete(int $groupe_id): void
{
    include("modules/upload/config/upload.conf.php");

    if ($DOCUMENTROOT == '') {
        global $DOCUMENT_ROOT;
        if ($DOCUMENT_ROOT) {
            $DOCUMENTROOT = $DOCUMENT_ROOT;
        } else {
            $DOCUMENTROOT = $_SERVER['DOCUMENT_ROOT'];
        }
    }

    $user_dir = $DOCUMENTROOT . $racine . '/storage/users_private/groupe/' . $groupe_id;

    // Supprimer son ministe s'il existe
    if (is_dir($user_dir . '/mns')) {
        $dir = opendir($user_dir . '/mns');
        
        while (false !== ($nom = readdir($dir))) {
            if ($nom != '.' && $nom != '..' && $nom != '') {
                @unlink($user_dir . '/mns/' . $nom);
            }
        }

        closedir($dir);
        @rmdir($user_dir . '/mns');
    }

    DB::table('groupes')->where('groupe_id', $groupe_id)->update(array(
        'groupe_mns'    => 0,
    ));

    global $aid;
    logs::Ecr_Log('security', "DeleteMnsWS($groupe_id) by AID : $aid", '');
}

// CHAT

/**
 * [groupe_chat_create description]
 *
 * @param   int   $groupe_id  [$groupe_id description]
 *
 * @return  void
 */
function groupe_chat_create(int $groupe_id): void
{
    DB::table('groupes')->where('groupe_id', $groupe_id)->update(array(
        'groupe_chat'   => 1,
    ));

    global $aid;
    logs::Ecr_Log('security', "ActivateChatWS($groupe_id) by AID : $aid", '');
}

/**
 * [groupe_chat_delete description]
 *
 * @param   int   $groupe_id  [$groupe_id description]
 *
 * @return  void
 */
function groupe_chat_delete(int $groupe_id): void
{
    DB::table('groupes')->where('groupe_id', $groupe_id)->update(array(
        'groupe_chat'    => 0,
    ));

    global $aid;
    logs::Ecr_Log('security', "DesactivateChatWS($groupe_id) by AID : $aid", '');
}

/**
 * [bloc_groupe_create description]
 *
 * @param   int   $groupe_id  [$groupe_id description]
 *
 * @return  void
 */
function bloc_groupe_create(int $groupe_id): void
{
    // Creation bloc espace de travail user
    // On créer le bloc s'il n'existe pas déjà
    $bloc = false;
    $menu_workspace = "function#bloc_espace_groupe\r\nparams#$groupe_id,1";

    $count_lblock = DB::table('lblocks')->where('content', $menu_workspace)->count('id');
    
    if ($count_lblock == 0) {
        $count_rblock = DB::table('rblocks')->where('content', $menu_workspace)->count('id');
        
        if ($count_rblock <> 0) { 
            $bloc = true;
        }
    } else {
        $bloc = true;
    }

    if ($bloc == false) {
        DB::table('lblocks')->insert(array(
            'title'     => '',
            'content'   => $menu_workspace,
            'member'    => $groupe_id,
            'Lindex'    => 3,
            'cache'     => 0,
            'actif'     => 1,
            'css'       => 0,
            'aide'      => NULL,
        ));
    }
}

/**
 * [groupe_member_ask description]
 *
 * @return  void
 */
function groupe_member_ask(): void 
{
    global $sub_op, $f_meta_nom, $f_titre, $adminimg, $myrow, $hlpfile, $groupe_asked, $user_asked;
    
    $directory = "storage/users_private/groupe";
    
    if (isset($sub_op)) {
        include_once('powerpack_f.php');

        $user = DB::table('users')->select('uname')->where('uid', $user_asked)->first();
        $uname = $user['uname'];

        $groupe = DB::table('groupes')->select('groupe_name')->where('groupe_id', $groupe_asked)->first();
        $gn = $groupe['groupe_name'];

        $subject = adm_translate('Nouvelles du groupe') . ' ' . $gn;
        $image = '18.png';

        if ($sub_op == adm_translate("Oui")) {
            $message = '✅ ' . adm_translate('Demande acceptée.') . ' ' . adm_translate('Vous faites désormais partie des membres du groupe') . ' : ' . $gn . ' [' . $groupe_asked . '].';
            
            unlink($directory . '/ask4group_' . $user_asked . '_' . $groupe_asked . '_.txt');
            
            $ibid2 = DB::table('users_status')->select('groupe')->where('uid', $user_asked)->first();

            $lesgroupes = explode(',', $ibid2['groupe']);
            $nbregroupes = count($lesgroupes);
            $groupeexistedeja = false;

            for ($i = 0; $i < $nbregroupes; $i++) {
                if ($lesgroupes[$i] == $groupe_asked) {
                    $groupeexistedeja = true;
                    break;
                }
            }

            if (!$groupeexistedeja) {
                $groupesmodif = $ibid2['groupe'] ? $ibid2['groupe'] . ',' . $groupe_asked : $groupe_asked;
                
                DB::table('users_status')->where('uid', $user_asked)->update(array(
                    'groupe'    => $groupesmodif,
                ));
            }

            writeDB_private_message($uname, $image, $subject, 1, $message, '');

            global $aid;
            logs::Ecr_Log('security', "AddMemberToGroup($groupe_asked, $uname) by AID : $aid", '');

            Header("Location: admin.php?op=groupes");
        }
        if ($sub_op == adm_translate("Non")) {
            $message = '🚫 ' . adm_translate('Demande refusée pour votre participation au groupe') . ' : ' . $gn . ' [' . $groupe_asked . '].';
            unlink($directory . '/ask4group_' . $user_asked . '_' . $groupe_asked . '_.txt');

            writeDB_private_message($uname, $image, $subject, 1, $message, '');

            Header("Location: admin.php?op=groupes");
        }
    }

    include("themes/default/header.php");

    GraphicAdmin($hlpfile);
    adminhead($f_meta_nom, $f_titre, $adminimg);

    $iterator = new DirectoryIterator($directory);
    $j = 0;

    foreach ($iterator as $fileinfo) {
        if ($fileinfo->isFile() and strpos($fileinfo->getFilename(), 'ask4group') !== false) {
            
            $us_gr = explode('_', $fileinfo->getFilename());
            $myrow = get_userdata_from_id($us_gr[1]);

            $r = DB::table('groupes')->select('groupe_name')->where('groupe_id', $us_gr[2])->first();
            $gn = $r['groupe_name'];

            echo '
            <form id="acceptmember_' . $us_gr[1] . '_' . $us_gr[2] . '" class="admform" action="admin.php" method="post">
                <div id="" class="">
                ' . adm_translate("Accepter") . ' ' . $myrow['uname'] . ' ' . adm_translate("dans le groupe") . ' ' . $us_gr[2] . ' : ' . $gn . ' ?
                </div>
                <input type="hidden" name="op" value="groupe_member_ask" />
                <input type="hidden" name="user_asked" value="' . $us_gr[1] . '" />
                <input type="hidden" name="groupe_asked" value="' . $us_gr[2] . '" />
                <div class="mb-3">
                    <input class="btn btn-primary btn-sm" type="submit" name="sub_op" value="' . adm_translate("Oui") . '" />
                    <input class="btn btn-primary btn-sm" type="submit" name="sub_op" value="' . adm_translate("Non") . '" />
                </div>
            </form>';
            $j++;
        }
    }
}

switch ($op) {
    case 'membre_add':
        membre_add($groupe_id);
        break;

    case 'membre_add_finish':
        membre_add_finish($groupe_id, $luname);
        break;

    case 'retiredugroupe':
        retiredugroupe($groupe_id, $uid, $uname);
        break;

    case 'retiredugroupe_all':
        retiredugroupe_all($groupe_id, $tab_groupe);
        break;

    case 'pad_create':
        pad_create($groupe_id);
        Header("Location: admin.php?op=groupes");
        break;

    case 'pad_remove':
        pad_remove($groupe_id);
        Header("Location: admin.php?op=groupes");
        break;

    case 'note_create':
        note_create($groupe_id);
        Header("Location: admin.php?op=groupes");
        break;

    case 'note_remove':
        note_remove($groupe_id);
        Header("Location: admin.php?op=groupes");
        break;

    case 'workspace_create':
        workspace_create($groupe_id);
        Header("Location: admin.php?op=groupes");
        break;

    case 'workspace_archive':
        workspace_archive($groupe_id);
        Header("Location: admin.php?op=groupes");
        break;

    case 'forum_groupe_create':
        forum_groupe_create($groupe_id, $groupe_name, $description, $moder);
        break;

    case 'moderateur_update':
        moderateur_update($forum_id, $forum_moderator);
        Header('location: admin.php?op=groupes');
        break;

    case 'forum_groupe_delete':
        forum_groupe_delete($groupe_id);
        Header('location: admin.php?op=groupes');
        break;

    case 'groupe_mns_create':
        groupe_mns_create($groupe_id);
        Header('location: admin.php?op=groupes');
        break;

    case 'groupe_mns_delete':
        groupe_mns_delete($groupe_id);
        Header('location: admin.php?op=groupes');
        break;

    case 'groupe_chat_create':
        groupe_chat_create($groupe_id);
        Header('location: admin.php?op=groupes');
        break;

    case 'groupe_chat_delete':
        groupe_chat_delete($groupe_id);
        Header('location: admin.php?op=groupes');
        break;

    case 'groupe_edit':
        groupe_edit($groupe_id);
        break;

    case 'groupe_maj':
        groupe_maj($sub_op);
        break;

    case 'groupe_add':
        groupe_edit("groupe_add");
        break;

    case 'bloc_groupe_create':
        bloc_groupe_create($groupe_id);
        Header('location: admin.php?op=groupes');
        break;

    case 'groupe_member_ask':
        groupe_member_ask();
        break;

    case 'groupe_add_finish':
        $ok_grp = false;
        if (($groupe_id == '') or ($groupe_id < 2) or ($groupe_id > 126)) {
            
            $row = DB::table('groupes')->select(DB::raw('MAX(groupe_id)'))->get();

            if ($row[0] < 126) {
                if ($row[0] == 0) $row[0] = 1;
                $groupe_id = $row[0] + 1;
                $ok_grp = true;
            }
        } else {
            $ok_grp = true;
        }
        
        if ($ok_grp) {
            DB::table('groupes')->insert(array(
                'groupe_id'             => $groupe_id,                
                'groupe_name'           => $groupe_name,
                'groupe_description'    => $groupe_description,
                'groupe_forum'          => 0,
                'groupe_mns'            => 0,
                'groupe_chat'           => 0,
                'groupe_blocnote'       => 0,
                'groupe_pad'            => 0,

            ));

            @mkdir('storage/users_private/groupe/' . $groupe_id);
            $fp = fopen('storage/users_private/groupe/' . $groupe_id . '/index.html', 'w');
            fclose($fp);

            @copy('modules/groupe/assets/images/groupe.png', 'storage/users_private/groupe/' . $groupe_id . '/groupe.png');
            @unlink('storage/users_private/groupe/' . $groupe_id . '/delete');

            global $aid;
            logs::Ecr_Log('security', "CreateGroupe($groupe_id, $groupe_name) by AID : $aid", '');
        }

    default:
        group_liste();
        break;
}
