<?php

namespace Modules\TwoGroupes\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class Groupes extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'groupes';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'groupes';

        $this->f_titre = __d('two_groupes', 'Gestion des groupes');

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

    /**
     * [group_liste description]
     *
     * @return  void
     */
    function group_liste(string $al): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('groupes'));
        adminhead($f_meta_nom, $f_titre);

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
                        $tab_groupeII[$groupevalue] .= $status['uid']  .' ';
                        $tab_groupeIII[$groupevalue] = $groupevalue;
                    }
                }
            }
        }

        echo '<script type="text/javascript">
        //<![CDATA[';

        if ($al) {
            if (preg_match('#^mod#', $al)) {
                $al = explode('_', $al);
                $mes = __d('two_groupes', 'Vous ne pouvez pas exclure')  .' '. $al[1]  .' '. __d('two_groupes', 'car il est modérateur unique de forum. Oter ses droits de modération puis retirer le du groupe.');
            }
        }

        if ($al) {
            echo 'bootbox.alert("'. $mes  .'")';
        }

        echo '
        tog(\'lst_gr\',\'show_lst_gr\',\'hide_lst_gr\');

        //==> choix moderateur
        function choisir_mod_forum(gp,gn,ar_user,ar_uid) {
            var user_json = ar_user.split(",");
            var uid_json = ar_uid.split(",");
            var choix_mod = prompt("'. html_entity_decode(__d('two_groupes', 'Choisir un modérateur'), ENT_COMPAT | ENT_HTML401, 'utf-8')  .' : \n"+user_json);
            if (choix_mod) {
                for (i=0; i<user_json.length; i++) {
                    if (user_json[i] == choix_mod) {var ind_uid=i;}
                }
                var xhr_object = null;
                if (window.XMLHttpRequest) // FF
                    xhr_object = new XMLHttpRequest();
                else if(window.ActiveXObject) // IE
                    xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
                xhr_object.open("GET", "' . site_url('admin.php?op=forum_groupe_create&groupe_id=') .'"+gp+"&groupe_name="+gn+"&moder="+uid_json[ind_uid], false);
                xhr_object.send(null);
                document.location.href="' . site_url('admin.php?op=groupes') .'";
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
            if (confirm("'. __d('two_groupes', 'Vous allez exclure TOUS les membres du groupe')  .' "+grp+" !")) {
                xhr_object.open("GET", location.href="' . site_url('admin.php?op=retiredugroupe_all&groupe_id=') .'"+grp+"&tab_groupe="+ugp, false);
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
            if (confirm("'. __d('two_groupes', 'Vous allez supprimer le groupe')  .' "+gr)) {
                xhr_object.open("GET", location.href="' . site_url('admin.php?op=groupe_maj&groupe_id=') .'"+gr+"&sub_op='. __d('two_groupes', 'Supprimer')  .'", false);
            }
        }
        //<== confirmation suppression groupe (done in xhr)
        //]]>
        </script>';

        echo '
        <hr />
        <form action="' . site_url('admin.php') .'" method="post" name="nouveaugroupe">
            <input type="hidden" name="op" value="groupe_add" />
            <a href="#" onclick="document.forms[\'nouveaugroupe\'].submit()" title="'. __d('two_groupes', 'Ajouter un groupe')  .'" data-bs-toggle="tooltip" data-bs-placement="right"><i class="fas fa-users fa-2x"></i><i class="fa fa-plus fa-lg me-1"></i></a> 
        </form>
        <hr />
        <h3 class="my-3"><a class="tog small" id="hide_lst_gr" title="'. __d('two_groupes', 'Replier la liste')  .'" ><i id="i_lst_gr" class="fa fa-caret-up fa-lg text-primary" ></i></a>&nbsp;'. __d('two_groupes', 'Liste des groupes')  .'</h3>
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
                <div id="bloc_gr_'. $gp  .'" class="row border rounded ms-1 p-2 px-0 mb-2 w-100">
                    <div class="col-lg-4 ">
                    <span>'. $gp  .'</span>
                    <i class="fa fa-users fa-2x text-muted"></i><h4 class="my-2">'. language::aff_langue($result['groupe_name'])  .'</h4><p>'. language::aff_langue($result['groupe_description']);
                
                if (file_exists('storage/users_private/groupe/'. $gp  .'/groupe.png')) {
                    echo '<img class="d-block my-2" src="storage/users_private/groupe/'. $gp  .'/groupe.png" width="80" height="80" alt="logo_groupe" />';
                }
                
                echo '
                    </div>
                    <div class="col-lg-5">';

                $tab_groupe = explode(' ', ltrim($tab_groupeII[$gp]));
                $nb_mb = (count($tab_groupe)) - 1;

                echo '
                    <a class="tog" id="show_lst_mb_'. $gp  .'" title="'. __d('two_groupes', 'Déplier la liste')  .'"><i id="i_lst_mb_gr_'. $gp  .'" class="fa fa-caret-down fa-lg text-primary" ></i></a>&nbsp;&nbsp;
                    <i class="fa fa-user fa-2x text-muted"></i> <span class=" align-top badge bg-secondary">&nbsp;'. $nb_mb  .'</span>&nbsp;&nbsp;';
                
                $lst_uid_json = '';
                //$lst_uidna_json = ''; // ??? not used

                //==> liste membres du groupe
                echo '<ul id="lst_mb_gr_'. $gp  .'" style ="display:none; padding-left:0px; -webkit-padding-start: 0px;">';
                
                foreach ($tab_groupe as $bidon => $uidX) {
                    if ($uidX) {
                        
                        $user = DB::table('users')
                            ->select('uname', 'user_avatar')
                            ->where('uid', $uidX)
                            ->first();

                        $lst_user_json .= $user['uname']  .',';
                        $lst_uid_json .= $uidX  .',';
                        $lst_gr_json .= '\'mbgr_'. $gp  .'\': { gp: \''. $gp  .'\'},';
                        
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
                    <li id="'. $user['uname'] . $uidX  .'_'. $gp  .'" style="list-style-type:none;">
                        <div style="float:left;">
                            <a class="adm_tooltip"><em style="width:90px;"><img src="'. $imgtmp  .'"  height="80" width="80" alt="avatar"/></em>- </a>
                        </div>
                        <div class="text-truncate" style="min-width:110px; width:110px; float:left;">
                            '. $user['uname']  .'
                        </div>
                        <div>
                            <a href="' . site_url('admin.php?chng_uid='. $uidX  .'&amp;op=modifyUser') .'" title="'. __d('two_groupes', 'Editer les informations concernant')  .' '. $user['uname']  .'" data-bs-toggle="tooltip"><i class="fa fa-edit fa-lg fa-fw me-1"></i></a>
                            <a href="' . site_url('admin.php?op=retiredugroupe&amp;uid='. $uidX  .'&amp;uname='. $user['uname']  .'&amp;groupe_id='. $gp) .'" title="'. __d('two_groupes', 'Exclure')  .' '. $user['uname']  .' '. __d('two_groupes', 'du groupe')  .' '. $gp  .'" data-bs-toggle="tooltip"><i class="fa fa-user-times fa-lg fa-fw text-danger me-1"></i></a>
                            <a href="" data-bs-toggle="collapse" data-bs-target="#moderation_'. $uidX  .'_'. $gp  .'" ><i class="fa fa-balance-scale fa-lg fa-fw" title="'. __d('two_groupes', 'Modérateur')  .'" data-bs-toggle="tooltip"></i></a>
                            <div id="moderation_'. $uidX  .'_'. $gp  .'" class="collapse">';
                        
                        //=>traitement moderateur
                        if ($result['groupe_forum'] == 1) {
                            $pat = '#\b'. $uidX  .'\b#';
                            
                            $forums = DB::table('forums')->select('forum_id', 'forum_name', 'forum_moderator')->where('forum_pass', $gp)->get();

                            foreach ($forums as $row) {
                                $ar_moder = explode(',', $row[2]);
                                $tmp_moder = $ar_moder;
                                
                                if (preg_match($pat, $row[2])) {
                                    unset($tmp_moder[array_search($uidX, $tmp_moder)]);
                                    
                                    $new_moder = implode(',', $tmp_moder);
                                    
                                    echo count($tmp_moder) != 0 ?
                                        '<a href="' . site_url('admin.php?op=moderateur_update&amp;forum_id='. $row[0]  .'&amp;forum_moderator='. $new_moder) .'" title="'. __d('two_groupes', 'Oter')  .' '. $user['uname']  .' '. __d('two_groupes', 'des modérateurs du forum')  .' '. $row[0]  .'" data-bs-toggle="tooltip" data-bs-placement="right"><i class="fa fa-balance-scale fa-lg fa-fw text-danger me-1"></i></a>' :
                                        '<i class="fa fa-balance-scale fa-lg fa-fw me-1" title="'. __d('two_groupes', 'Ce modérateur') . " (" . $user['uname'] . ") " . __d('two_groupes', 'n\'est pas modifiable tant qu\'un autre n\'est pas nommé pour ce forum')  .' '. $row[0]  .'" data-bs-toggle="tooltip" data-bs-placement="right" ></i>';
                                } else {
                                    $tmp_moder[] = $uidX;
                                    asort($tmp_moder);

                                    $new_moder = implode(',', $tmp_moder);

                                    echo '<a href="' . site_url('admin.php?op=moderateur_update&amp;forum_id='. $row[0]  .'&amp;forum_moderator='. $new_moder) .'" title="'. __d('two_groupes', 'Nommer')  .' '. $user['uname']  .' '. __d('two_groupes', 'comme modérateur du forum')  .' '. $row[1]  .' ('. $row[0]  .')" data-bs-toggle="tooltip" data-bs-placement="right" ><i class="fa fa-balance-scale fa-lg fa-fw me-1"></i></a>';
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
                    tog(\'lst_mb_gr_'. $gp  .'\',\'show_lst_mb_'. $gp  .'\',\'hide_lst_mb_'. $gp  .'\');
                    //]]>
                </script>
                <i class="fa fa-user-times fa-lg text-danger" title="'. __d('two_groupes', 'Exclure TOUS les membres du groupe')  .' '. $gp  .'" data-bs-toggle="tooltip" data-bs-placement="right" onclick="delete_AllMembersGroup(\''. $gp  .'\',\''. $lst_uid_json  .'\');"></i>';
                //<== liste membres du groupe

                //==> menu groupe
                echo '
                </div>
                <div class="col-lg-3 list-group-item px-0 mt-2 mt-md-0">
                    <a class="btn btn-outline-secondary btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="' . site_url('admin.php?op=groupe_edit&amp;groupe_id='. $gp) .'" title="'. __d('two_groupes', 'Editer groupe')  .' '. $gp  .'" data-bs-toggle="tooltip"  >
                        <i class="fas fa-pencil-alt fa-lg"></i>
                    </a>
                    <a class="btn btn-outline-danger btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="javascript:void(0);" onclick="bootbox.alert(\''. __d('two_groupes', 'Avant de supprimer le groupe')  .' '. $gp  .' '. __d('two_groupes', 'vous devez supprimer TOUS ses membres !')  .'\');" title="'. __d('two_groupes', 'Supprimer groupe')  .' '. $gp  .'" data-bs-toggle="tooltip"  >
                        <i class="fas fa-trash fa-lg fa-fw"></i></a><a class="btn btn-outline-secondary btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="' . site_url('admin.php?op=membre_add&amp;groupe_id='. $gp) .'" title="'. __d('two_groupes', 'Ajouter un ou des membres au groupe')  .' '. $gp  .'" data-bs-toggle="tooltip"  >
                        <i class="fa fa-user-plus fa-lg fa-fw"></i></a><a class="btn btn-outline-secondary btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="' . site_url('admin.php?op=bloc_groupe_create&amp;groupe_id='. $gp) .'" title="'. __d('two_groupes', 'Créer le bloc WS')  .' ('. $gp  .')" data-bs-toggle="tooltip"  ><i class="fa fa-clone fa-lg fa-fw"></i><i class="fa fa-plus"></i>
                    </a>';
                
                echo $result['groupe_pad'] == 1 
                    ? '<a class="btn btn-outline-danger btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="' . site_url('admin.php?op=pad_remove&amp;groupe_id='. $gp) .'" title="'. __d('two_groupes', 'Désactiver PAD du groupe')  .' '. $gp  .'" data-bs-toggle="tooltip"  ><i class="fa fa-edit fa-lg fa-fw"></i><i class="fa fa-minus"></i></a>' 
                    : '<a class="btn btn-outline-secondary btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="' . site_url('admin.php?op=pad_create&amp;groupe_id='. $gp) .'" title="'. __d('two_groupes', 'Activer PAD du groupe')  .' '. $gp  .'" data-bs-toggle="tooltip"  ><i class="fa fa-edit fa-lg fa-fw"></i><i class="fa fa-plus"></i></a>';
                
                echo $result['groupe_blocnote'] == 1 
                    ? '<a class="btn btn-outline-danger btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="' . site_url('admin.php?op=note_remove&amp;groupe_id='. $gp) .'" title="'. __d('two_groupes', 'Désactiver bloc-note du groupe')  .' '. $gp  .'" data-bs-toggle="tooltip"  ><i class="far fa-sticky-note fa-lg fa-fw"></i><i class="fa fa-minus"></i></a>' 
                    : '<a class="btn btn-outline-secondary btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="' . site_url('admin.php?op=note_create&amp;groupe_id='. $gp) .'" title="'. __d('two_groupes', 'Activer bloc-note du groupe')  .' '. $gp  .'" data-bs-toggle="tooltip"  ><i class="far fa-sticky-note fa-lg fa-fw"></i><i class="fa fa-plus"></i></a>';
                
                echo file_exists('modules/f-manager/config/groupe_'. $gp  .'.conf.php') 
                    ? '<a class="btn btn-outline-danger btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="' . site_url('admin.php?op=workspace_archive&amp;groupe_id='. $gp) .'" title="'. __d('two_groupes', 'Désactiver gestionnaire de fichiers du groupe')  .' '. $gp  .'" data-bs-toggle="tooltip"  ><i class="far fa-folder fa-lg fa-fw"></i><i class="fa fa-minus"></i></a>' 
                    : '<a class="btn btn-outline-secondary btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="' . site_url('admin.php?op=workspace_create&amp;groupe_id='. $gp) .'" title="'. __d('two_groupes', 'Activer gestionnaire de fichiers du groupe')  .' '. $gp  .'" data-bs-toggle="tooltip"  ><i class="far fa-folder fa-lg fa-fw"></i><i class="fa fa-plus"></i></a>';
                
                echo $result['groupe_forum'] == 1 
                    ? '<a class="btn btn-outline-danger btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="' . site_url('admin.php?op=forum_groupe_delete&amp;groupe_id='. $gp  .'') .'" title="'. __d('two_groupes', 'Supprimer forum du groupe')  .' '. $gp  .'" data-bs-toggle="tooltip"  ><i class="fa fa-list-alt fa-lg fa-fw"></i><i class="fa fa-minus"></i></a>' 
                    : '<a class="btn btn-outline-secondary btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="javascript:void(0);" onclick="javascript:choisir_mod_forum(\''. $gp  .'\',\''. $result['groupe_name']  .'\',\''. $lst_user_json  .'\',\''. $lst_uid_json  .'\');" title="'. __d('two_groupes', 'Créer forum du groupe')  .' '. $gp  .'" data-bs-toggle="tooltip"  ><i class="fa fa-list-alt fa-lg fa-fw"></i> <i class="fa fa-plus"></i></a>';
                
                echo $result['groupe_mns'] == 1 
                    ? '<a class="btn btn-outline-danger btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="' . site_url('admin.php?op=groupe_mns_delete&amp;groupe_id='. $gp) .'" title="'. __d('two_groupes', 'Supprimer MiniSite du groupe')  .' '. $gp  .'" data-bs-toggle="tooltip"  ><i class="fa fa-desktop fa-lg fa-fw"></i><i class="fa fa-minus"></i></a>' 
                    : '<a class="btn btn-outline-secondary btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="' . site_url('admin.php?op=groupe_mns_create&amp;groupe_id='. $gp) .'" title="'. __d('two_groupes', 'Créer MiniSite du groupe')  .' '. $gp  .'" data-bs-toggle="tooltip"  ><i class="fa fa-desktop fa-lg fa-fw"></i><i class="fa fa-plus"></i></a>';
                
                echo $result['groupe_chat'] == 0 
                    ? '<a class="btn btn-outline-secondary btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="' . site_url('admin.php?op=groupe_chat_create&amp;groupe_id='. $gp) .'" title="'. __d('two_groupes', 'Activer chat du groupe')  .' '. $gp  .'" data-bs-toggle="tooltip"  ><i class="far fa-comments fa-lg fa-fw"></i><i class="fa fa-plus"></i></a>' 
                    : '<a class="btn btn-outline-danger btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="' . site_url('admin.php?op=groupe_chat_delete&amp;groupe_id='. $gp) .'" title="'. __d('two_groupes', 'Désactiver chat du groupe')  .' '. $gp  .'" data-bs-toggle="tooltip"  ><i class="far fa-comments fa-lg fa-fw"></i><i class="fa fa-minus"></i></a>';
                
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
                $lst_gr_json .= '\'mbgr_'. $gp  .'\': { gp: \''. $gp  .'\'},';
                
                echo '
                <div class="row border rounded ms-1 p-2 px-0 mb-2 w-100">
                    <div id="bloc_gr_'. $gp  .'" class="col-lg-5">
                    <span class="text-danger">'. $gp  .'</span>
                    <i class="fa fa-users fa-2x text-muted"></i>
                    <h4 class="my-2 text-muted">'. language::aff_langue($groupe['groupe_name'])  .'</h4>
                    <p class="text-muted">'. language::aff_langue($groupe['groupe_description']);

                if (file_exists('storage/users_private/groupe/'. $gp  .'/groupe.png')) {
                    echo '<img class="d-block my-2" src="storage/users_private/groupe/'. $gp  .'/groupe.png" width="80" height="80" />';
                }
                
                echo '
                    </p>
                    </div>
                    <div class="col-lg-4 ">
                    <i class="fa fa-user-o fa-2x text-muted"></i><span class="align-top badge bg-secondary ms-1">0</span>
                    </div>
                    <div class="col-lg-3 list-group-item px-0 mt-2">
                    <a class="btn btn-outline-secondary btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="' . site_url('admin.php?op=groupe_edit&amp;groupe_id='. $gp) .'" title="'. __d('two_groupes', 'Editer groupe')  .' '. $gp  .'" data-bs-toggle="tooltip"  ><i class="fas fa-pencil-alt fa-lg"></i></a><a class="btn btn-outline-danger btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="#" onclick="confirm_deleteGroup(\''. $gp  .'\');" title="'. __d('two_groupes', 'Supprimer groupe')  .' '. $gp  .'" data-bs-toggle="tooltip" ><i class="fas fa-trash fa-lg"></i></a><a class="btn btn-outline-secondary btn-sm col-lg-6 col-md-1 col-sm-2 col-3 mb-1 border-0" href="' . site_url('admin.php?op=membre_add&amp;groupe_id='. $gp) .'" title="'. __d('two_groupes', 'Ajouter un ou des membres au groupe')  .' '. $gp  .'" data-bs-toggle="tooltip" ><i class="fa fa-user-plus fa-lg"></i></a>
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

    /**
     * [groupe_edit description]
     *
     * @param   string   $groupe_id  [$groupe_id description]
     *
     * @return  void
     */
    function groupe_edit(string $groupe_id): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('groupes'));
        adminhead($f_meta_nom, $f_titre);

        $result = DB::table('groupes')->select('groupe_name', 'groupe_description')->where('groupe_id', $groupe_id)->first();

        if ($groupe_id != 'groupe_add') {
            echo '
            <hr />
            <h3>'. __d('two_groupes', 'Modifier le groupe')  .' : '. $groupe_id  .'</h3>';
        } else {
            echo '
            <hr />
            <h3>'. __d('two_groupes', 'Créer un groupe.')  .'</h3>';
        }  

        echo '
        <form class="admform" id="groupesaddmod" action="' . site_url('admin.php') .'" method="post">
            <fieldset>
                <legend><i class="fas fa-users fa-2x text-muted"></i></legend>'. "\n";

        if ($groupe_id != 'groupe_add') {
            echo '<input type="hidden" name="groupe_id" value="'. $groupe_id  .'" />';
        } else {
            echo '
                <div class="mb-3">
                    <label for="inp_gr_id" class="admform">ID</label>
                    <input id="inp_gr_id" type="number" min="2" max="126" class="form-control" name="groupe_id" value="" required="required"/><span class="help-block">(2...126)</span>
                </div>';
        }

        echo '
                <div class="mb-3">
                    <label class="col-form-label" for="grname">'. __d('two_groupes', 'Nom')  .'</label>
                    <input type="text" class="form-control" id="grname" name="groupe_name" maxlength="1000" value="';

        echo isset($result) ? $result['groupe_name'] : '';

        echo '" placeholder="'. __d('two_groupes', 'Nom du groupe')  .'" required="required" />
                    <span class="help-block text-end"><span id="countcar_grname"></span></span>
                </div>
                <div class="mb-3">
                    <label class="col-form-label" for="grdesc">'. __d('two_groupes', 'Description')  .'</label>
                    <textarea class="form-control" name="groupe_description" id="grdesc" rows="11" placeholder="'. __d('two_groupes', 'Description du groupe')  .'" required="required">';
        
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
                    <input class="btn btn-primary" type="submit" name="sub_op" value="'. __d('two_groupes', 'Sauver les modifications')  .'" />
                </div>
            </fieldset>
        </form>';

        $arg1 = '
        var formulid = ["groupesaddmod"];
        inpandfieldlen("grname",1000);';

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

        if ($sub_op == __d('two_groupes', 'Sauver les modifications')) {
            DB::table('groupes')->where('groupe_id', $groupe_id)->update(array(
                'groupe_name'           => $groupe_name,
                'groupe_description'    => $groupe_description,
            ));

            global $aid;
            logs::Ecr_Log("security", "UpdateGroup($groupe_id) by AID : $aid", '');
        }

        if ($sub_op == __d('two_groupes', 'Supprimer')) {
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

        Header('Location: ' . site_url('admin.php?op=groupes'));
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

    /**
     * [groupe_member_ask description]
     *
     * @return  void
     */
    function groupe_member_ask(): void 
    {
        global $sub_op, $f_meta_nom, $f_titre, $myrow, $groupe_asked, $user_asked;
        
        $directory = "storage/users_private/groupe";
        
        if (isset($sub_op)) {
            
            $user = DB::table('users')->select('uname')->where('uid', $user_asked)->first();
            $uname = $user['uname'];

            $groupe = DB::table('groupes')->select('groupe_name')->where('groupe_id', $groupe_asked)->first();
            $gn = $groupe['groupe_name'];

            $subject = __d('two_groupes', 'Nouvelles du groupe')  .' '. $gn;
            $image = '18.png';

            if ($sub_op == __d('two_groupes', 'Oui')) {
                $message = '✅ '. __d('two_groupes', 'Demande acceptée.')  .' '. __d('two_groupes', 'Vous faites désormais partie des membres du groupe')  .' : '. $gn  .' ['. $groupe_asked  .'].';
                
                unlink($directory  .'/ask4group_'. $user_asked  .'_'. $groupe_asked  .'_.txt');
                
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
                    $groupesmodif = $ibid2['groupe'] ? $ibid2['groupe']  .','. $groupe_asked : $groupe_asked;
                    
                    DB::table('users_status')->where('uid', $user_asked)->update(array(
                        'groupe'    => $groupesmodif,
                    ));
                }

                messenger::writeDB_private_message($uname, $image, $subject, 1, $message, '');

                global $aid;
                logs::Ecr_Log('security', "AddMemberToGroup($groupe_asked, $uname) by AID : $aid", '');

                Header('Location: ' . site_url('admin.php?op=groupes'));
            }
            if ($sub_op == __d('two_groupes', 'Non')) {
                $message = '🚫 '. __d('two_groupes', 'Demande refusée pour votre participation au groupe')  .' : '. $gn  .' ['. $groupe_asked  .'].';
                unlink($directory  .'/ask4group_'. $user_asked  .'_'. $groupe_asked  .'_.txt');

                messenger::writeDB_private_message($uname, $image, $subject, 1, $message, '');

                Header('Location: ' . site_url('admin.php?op=groupes'));
            }
        }

        include("themes/default/header.php");

        GraphicAdmin(manuel('groupes'));
        adminhead($f_meta_nom, $f_titre);

        $iterator = new DirectoryIterator($directory);
        $j = 0;

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile() and strpos($fileinfo->getFilename(), 'ask4group') !== false) {
                
                $us_gr = explode('_', $fileinfo->getFilename());
                $myrow = forum::get_userdata_from_id($us_gr[1]);

                $r = DB::table('groupes')->select('groupe_name')->where('groupe_id', $us_gr[2])->first();
                $gn = $r['groupe_name'];

                echo '
                <form id="acceptmember_'. $us_gr[1]  .'_'. $us_gr[2]  .'" class="admform" action="' . site_url('admin.php') .'" method="post">
                    <div id="" class="">
                    '. __d('two_groupes', 'Accepter')  .' '. $myrow['uname']  .' '. __d('two_groupes', 'dans le groupe')  .' '. $us_gr[2]  .' : '. $gn  .' ?
                    </div>
                    <input type="hidden" name="op" value="groupe_member_ask" />
                    <input type="hidden" name="user_asked" value="'. $us_gr[1]  .'" />
                    <input type="hidden" name="groupe_asked" value="'. $us_gr[2]  .'" />
                    <div class="mb-3">
                        <input class="btn btn-primary btn-sm" type="submit" name="sub_op" value="'. __d('two_groupes', 'Oui')  .'" />
                        <input class="btn btn-primary btn-sm" type="submit" name="sub_op" value="'. __d('two_groupes', 'Non')  .'" />
                    </div>
                </form>';
                $j++;
            }
        }
    }

}