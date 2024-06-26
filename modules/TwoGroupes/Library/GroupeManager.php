<?php

declare(strict_types=1);

namespace Modules\TwoGroupes\Library;

use Two\Support\Facades\DB;
use Two\Support\Facades\Cache;
use Two\Support\Facades\Crypt;
use Two\Foundation\Application;
use Two\Support\Facades\Config;
use Modules\TwoCore\Support\Spam;
use Modules\TwoCore\Support\Sanitize;
use Modules\TwoThemes\Library\ThemeManager;
use Modules\TwoUser\Support\Facades\Online;
use Modules\TwoCore\Support\Facades\Language;
use Modules\TwoUsers\Library\User\UserManager;



class GroupeManager
{

    /**
     * The Application Instance.
     *
     * @var \Two\Application\Application
     */
    public $app;

    /**
     * The User Instance.
     *
     * @var \Modules\TwoUsers\Library\User\UserManager
     */
    public $user;

    /**
     * The Theme Instance.
     *
     * @var \Modules\TwoThemes\Library\ThemeManager
     */
    public $theme;


    /**
     * Mailer constructor.
     *
     * @param string $theme
     */
    public function __construct(Application $app, UserManager $user, ThemeManager $theme)
    {
        $this->app = $app;

        $this->user = $user;

        $this->theme = $theme;
    }


    /**
     * Retourne un tableau contenant la liste des groupes d'appartenance d'un membre
     *
     * @param   string  $xuser  [$xuser description]
     *
     * @return  array
     */
    public function valid_group(string $xuser): array
    {
        if ($xuser) {
            $userdata = explode(':', base64_decode($xuser));

            $user_temp = Cache::remember('valid_group', Config::get('two_groupes::config.cache.valid_group'), function () use ($userdata) {
                return DB::table('users_status')
                            ->select('groupe')
                            ->where('uid', $userdata[0])
                            ->get();
            });

            $tab_groupe = explode(',', $user_temp[0]['groupe']);
        } else {
            $tab_groupe = [];
        }

        return $tab_groupe;
    }

    /**
     * Retourne une liste des groupes disponibles dans un tableau
     *
     * @return  array
     */
    public function liste_group(): array
    {  
        $groupes = DB::table('groupes')->select('groupe_id', 'groupe_name')->orderBy('groupe_id', 'asc')->get();
        
        $tmp_groupe[0] = '-> ' . __d('two_groupes', 'Supprimer') . '/' . __d('two_groupes', 'Choisir un groupe') . ' <-';
        
        foreach ($groupes as $groupe) {
            $tmp_groupe[$groupe['groupe_id']] = Language::aff_langue($groupe['groupe_name']);
        }

        return $tmp_groupe;
    }
 
    /**
     * Retourne true ou false en fonction de l'autorisation d'un membre sur 1 (ou x) forum de type groupe
     *
     * @param   string  $forum_groupeX  [$forum_groupeX description]
     * @param   array   $tab_groupeX    [$tab_groupeX description]
     *
     * @return  bool
     */
    public function groupe_forum(string $forum_groupeX, array $tab_groupeX): bool
    {
        $ok_affich = $this->groupe_autorisation($forum_groupeX, $tab_groupeX);

        return $ok_affich;
    }
 
    /**
     * Retourne true ou false en fonction de l'autorisation d'un membre sur 1 (ou x) groupe
     *
     * @param   string  $groupeX      [$groupeX description]
     * @param   array   $tab_groupeX  [$tab_groupeX description]
     *
     * @return  bool
     */
    public function groupe_autorisation(string $groupeX, array $tab_groupeX): bool
    {
        $tab_groupe = explode(',', $groupeX);
        $ok = false;
        
        if ($tab_groupeX) {
            foreach ($tab_groupe as $groupe) {
                foreach ($tab_groupeX as $groupevalue) {
                    if ($groupe == $groupevalue) {
                        $ok = true;
                        break;
                    }
                }

                if ($ok) {
                    break;
                }
            }
        }

        return $ok;
    }

    /**
     * [fab_espace_groupe description]
     *
     * @param   string  $gr    [$gr description]
     * @param   string  $t_gr  [$t_gr description]
     * @param   string  $i_gr  [$i_gr description]
     *
     * @return  string
     */
    public function fab_espace_groupe(string $gr, string $t_gr, string $i_gr): string
    {
        $content = '
        <script type="text/javascript">
        //<![CDATA[
        //==> chargement css
        if (!document.getElementById(\'bloc_ws_css\')) {
           var l_css = document.createElement(\'link\');
           l_css.href = "modules/groupe/bloc_ws.css";
           l_css.rel = "stylesheet";
           l_css.id = "bloc_ws_css";
           l_css.type = "text/css";
           document.getElementsByTagName("head")[0].appendChild(l_css);
        }
        //]]>
        </script>';

        $content .= '
        <div id="bloc_ws_' . $gr . '" class="">' . "\n";
        
        $rsql = DB::table('groupes')
                ->select('groupe_id', 'groupe_name', 'groupe_description', 'groupe_forum', 'groupe_mns', 'groupe_chat', 'groupe_blocnote', 'groupe_pad')
                ->where('groupe_id', $gr)
                ->first();

        if ($t_gr == 1) {
            $content .= '<span style="font-size: 120%; font-weight:bolder;">' . Language::aff_langue($rsql['groupe_name']) . '</span>' . "\n";
        }

        $content .= '<p>' . Language::aff_langue($rsql['groupe_description']) . '</p>' . "\n";
        
        if (file_exists('storage/users_private/groupe/' . $gr . '/groupe.png') and ($i_gr == 1)){
            $content .= '<img src="storage/users_private/groupe/' . $gr . '/groupe.png" class="img-fluid mx-auto d-block rounded" alt="' . __d('two_groupes', 'Groupe') . '" loading="lazy" />';
        }
        
        //=> liste des membres
        $li_mb = '';
        $li_ic = '';

        $result = DB::table('users_status')->select('uid', 'groupe')->where('groupe', 'REGEXP', $gr)->orderBy('uid', 'asc')->get();
        $nb_mb = count($result);

        $count = 0;

        $li_mb .= '
           <div class="my-4">
              <a data-bs-toggle="collapse" data-bs-target="#lst_mb_ws_' . $gr . '" class="text-primary" id="show_lst_mb_ws_' . $gr . '" title="' . __d('two_groupes', 'Déplier la liste') . '"><i id="i_lst_mb_ws_' . $gr . '" class="toggle-icon fa fa-caret-down fa-2x" >&nbsp;</i></a><i class="fa fa-users fa-2x text-muted ms-3 align-middle" title="' . __d('two_groupes', 'Liste des membres du groupe.') . '" data-bs-toggle="tooltip"></i>&nbsp;<a href="memberslist.php?gr_from_ws=' . $gr . '" class="text-uppercase">' . __d('two_groupes', 'Membres') . '</a><span class="badge bg-secondary float-end">' . $nb_mb . '</span>';
        
        $tab = Online::online_members();

        $li_mb .= '
              <ul id="lst_mb_ws_' . $gr . '" class="list-group ul_bloc_ws collapse">';

        foreach ($result as $groupe) {

            $uid = $groupe['uid'];

            $socialnetworks = array();
            $posterdata_extend = array();
            $res_id = array();
            $my_rs = '';

            if (!Config::get('two_core::config.short_user')) {

                $posterdata_extend = Forum::get_userdata_extend_from_id($uid);

                include('modules/reseaux-sociaux/reseaux-sociaux.conf.php');

                if ($posterdata_extend['M2'] != '') {
                    $socialnetworks = explode(';', $posterdata_extend['M2']);

                    foreach ($socialnetworks as $socialnetwork) {
                        $res_id[] = explode('|', $socialnetwork);
                    }

                    sort($res_id);
                    sort($rs);

                    foreach ($rs as $v1) {
                        foreach ($res_id as $y1) {
                            $k = array_search($y1[0], $v1);

                            if (false !== $k) {
                                $my_rs .= '<a class="me-2" href="';

                                if ($v1[2] == 'skype') {
                                    $my_rs .= $v1[1] . $y1[1] . '?chat';
                                } else {
                                    $my_rs .= $v1[1] . $y1[1];
                                }

                                $my_rs .= '" target="_blank"><i class="fab fa-' . $v1[2] . ' fa-lg fa-fw mb-2"></i></a> ';
                                break;
                            } else $my_rs .= '';
                        }
                    }
                    $my_rsos[] = $my_rs;
                } else {
                    $my_rsos[] = '';
                }
            }

            $user = DB::table('users')
                    ->select('uname', 'user_avatar', 'mns', 'url', 'femail')
                    ->where('uid', $uid)->first();

            include('modules/geoloc/config/geoloc.conf');

            settype($ch_lat, 'string');
            $useroutils = '';
            
            if ($uid != 1 and $uid != '') {
                $useroutils .= '<a class="list-group-item text-primary" href="'. site_url('user.php?op=userinfo&amp;uname='. $user['uname']) .'" target="_blank" title="' . __d('two_groupes', 'Profil') . '" data-bs-toggle="tooltip"><i class="fa fa-2x fa-user align-middle fa-fw"></i><span class="ms-2 d-none d-sm-inline">' . __d('two_groupes', 'Profil') . '</span></a>';
            }

            if ($uid != 1) {
                $useroutils .= '<a class="list-group-item text-primary" href="'. site_url('powerpack.php?op=instant_message&amp;to_userid='. $user['uname']) .'" title="' . __d('two_groupes', 'Envoyer un message interne') . '" data-bs-toggle="tooltip"><i class="far fa-2x fa-envelope align-middle fa-fw"></i><span class="ms-2 d-none d-sm-inline">' . __d('two_groupes', 'Message') . '</span></a>';
            }

            if ($user['femail'] != '') {
                $useroutils .= '<a class="list-group-item text-primary" href="mailto:' . Spam::anti_spam($user['femail'], 1) . '" target="_blank" title="' . __d('two_groupes', 'Email') . '" data-bs-toggle="tooltip"><i class="fas fa-at fa-2x align-middle fa-fw"></i><span class="ms-2 d-none d-sm-inline">' . __d('two_groupes', 'Email') . '</span></a>';
            }

            if ($user['url'] != '') {
                $useroutils .= '<a class="list-group-item text-primary" href="' . $user['url'] . '" target="_blank" title="' . __d('two_groupes', 'Visiter ce site web') . '" data-bs-toggle="tooltip"><i class="fas fa-2x fa-external-link-alt align-middle fa-fw"></i><span class="ms-2 d-none d-sm-inline">' . __d('two_groupes', 'Visiter ce site web') . '</span></a>';
            }

            if ($user['mns']) {
                $useroutils .= '<a class="list-group-item text-primary" href="'. site_url('minisite.php?op='. $user['uname']) .'" target="_blank" target="_blank" title="' . __d('two_groupes', 'Visitez le minisite') . '" data-bs-toggle="tooltip"><i class="fa fa-2x fa-desktop align-middle fa-fw"></i><span class="ms-2 d-none d-sm-inline">' . __d('two_groupes', 'Visitez le minisite') . '</span></a>';
            }

            if (!Config::get('two_core::config.short_user')) {
                if ($posterdata_extend[$ch_lat] != '') {
                    $useroutils .= '<a class="list-group-item text-primary" href="'. site_url('modules.php?ModPath=geoloc&amp;ModStart=geoloc&op=u'. $uid) .'" title="' . __d('two_groupes', 'Localisation') . '" ><i class="fas fa-map-marker-alt fa-2x align-middle fa-fw"></i><span class="ms-2 d-none d-sm-inline">' . __d('two_groupes', 'Localisation') . '</span></a>';
                }
            }

            $conn = '<i class="fa fa-plug text-muted" title="' . $user['uname'] . ' ' . __d('two_groupes', 'n\'est pas connecté') . '" data-bs-toggle="tooltip" ></i>';
            
            if (!$user['user_avatar']) {
                $imgtmp = "assets/images/forum/avatar/blank.gif";
            } else if (stristr($user['user_avatar'], "users_private")) {
                $imgtmp = $user['user_avatar'];
            } else {
                if ($ibid = $this->theme->theme_image("forum/avatar/". $user['user_avatar'])) {
                    $imgtmp = $ibid;
                } else {
                    $imgtmp = "assets/images/forum/avatar/". $user['user_avatar'];
                }

                if (!file_exists($imgtmp)) {
                    $imgtmp = "assets/images/forum/avatar/blank.gif";
                }
            }

            $timex = false;
            for ($i = 1; $i <= $tab[0]; $i++) {
                if ($tab[$i]['username'] == $user['uname']) {
                    $timex = time() - $tab[$i]['time'];
                }
            }
            
            if (($timex !== false) and ($timex < 60)) {
                $conn = '<i class="fa fa-plug faa-flash animated text-primary" title="' . $user['uname'] . ' ' . __d('two_groupes', 'est connecté') . '" data-bs-toggle="tooltip" ></i>';
            }
            
            $li_ic .= '<img class="n-smil" src="' . $imgtmp . '" alt="avatar" loading="lazy" />';
            $li_mb .= '
                 <li class="list-group-item list-group-item-action d-flex flex-row p-2">
                    <div id="li_mb_' . $user['uname'] . '_' . $gr . '" class="n-ellipses">
                       ' . $conn . '<a class="ms-2" tabindex="0" data-bs-title="' . $user['uname'] . '" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-html="true" data-bs-content=\'<div class="list-group mb-3">' . $useroutils . '</div><div class="mx-auto text-center" style="max-width:170px;">';
            
            if (!Config::get('two_core::config.short_user')) {
                $li_mb .= $my_rsos[$count];
            }
            
            $li_mb .= '</div>\'>
           <img class=" btn-outline-primary img-thumbnail img-fluid n-ava-small " src="' . $imgtmp . '" alt="avatar" title="' . $user['uname'] . '" loading="lazy" /></a>
           <span class="ms-2">' . $user['uname'] . '</span>
     
                    </div>
                 </li>';
            $count++;
        }

        $li_mb .= '
              <li style="clear:left;line-height:6px; background:none;">&nbsp;</li>
              <li class="list-group-item" style="clear:left;line-height:24px;padding:6px; margin-top:0px;">' . $li_ic . '</li>
           </ul>
        </div>';
        $content .= $li_mb;
        //<== liste des membres

        //=> Forum
        $lst_for = '';
        $lst_for_tog = '';
        $nb_for_gr = '';
        
        if ($rsql['groupe_forum'] == 1) {
            $res_forum = DB::table('forums')
                            ->select('forum_id', 'forum_name')
                            ->where('forum_pass', 'REGEXP', $gr)
                            ->get();

            $nb_foru = count($res_forum);

            if ($nb_foru >= 1) {
                $lst_for_tog = '<a data-bs-toggle="collapse" data-bs-target="#lst_for_gr_' . $gr . '" class="text-primary" id="show_lst_for_' . $gr . '" title="' . __d('two_groupes', 'Déplier la liste') . '" ><i id="i_lst_for_gr_' . $gr . '" class="toggle-icon fa fa-caret-down fa-2x" >&nbsp;</i></a>';
                $lst_for .= '<ul id="lst_for_gr_' . $gr . '" class="list-group ul_bloc_ws collapse" style ="list-style-type:none;">';
                $nb_for_gr = '  <span class="badge bg-secondary float-end">' . $nb_foru . '</span>';
                
                foreach ($res_forum as $forum) {   
                    $lst_for .= '<li class="list-group-item list-group-item-action"><a href="'. site_url('viewforum.php?forum='. $forum['forum_id']) .'">' . $forum['forum_name'] . '</a></li>';
                }

                $lst_for .= '</ul>';
            }

            $content .= '
           <hr /><div class="">' . $lst_for_tog . '<i class="fa fa-list-alt fa-2x text-muted ms-3 align-middle" title="' . __d('two_groupes', 'Groupe') . '(' . $gr . '): ' . __d('two_groupes', 'forum') . '." data-bs-toggle="tooltip" ></i>&nbsp;<a class="text-uppercase" href="forum.php">' . __d('two_groupes', 'Forum') . '</a>' . $nb_for_gr . $lst_for . '</div>' . "\n";
        }

        //=> wspad
        if ($rsql['groupe_pad'] == 1) {
            settype($lst_doc, 'string');
            settype($nb_doc_gr, 'string');
            settype($lst_doc_tog, 'string');
            
            include("modules/wspad/config/config.php");

            $wspad = DB::table('wspad')
                ->select('page', 'editedby', 'modtime', 'ranq')
                ->where('member', $gr)
                ->groupeBy('page')
                ->orderBy('page', 'asc')
                ->get();

            $nb_doc = count($wspad);

            if ($nb_doc >= 1) {
                $lst_doc_tog = '<a data-bs-toggle="collapse" data-bs-target="#lst_doc_gr_' . $gr . '" class="text-primary" id="show_lst_doc_' . $gr . '" title="' . __d('two_groupes', 'Déplier la liste') . '"><i id="i_lst_doc_gr_' . $gr . '" class="toggle-icon fa fa-caret-down fa-2x" >&nbsp;</i></a>';
                
                $lst_doc .= '<ul id="lst_doc_gr_' . $gr . '" class="list-group ul_bloc_ws mt-3 collapse">';
                $nb_doc_gr = '  <span class="badge bg-secondary float-end">' . $nb_doc . '</span>';
                
                foreach($wspad as $pad) {
                    $surlignage = $couleur[Sanitize::hexfromchr($pad['e'])];
                    $lst_doc .= '<li class="list-group-item list-group-item-action px-1 py-3" style="line-height:14px;">
                    <div id="last_editor_' . $pad['p'] . '" data-bs-toggle="tooltip" data-bs-placement="right" title="' . __d('two_groupes', 'Dernier éditeur') . ' : ' . $pad['e'] . ' ' . date(__d('two_groupes', 'dateinternal'), $pad['m']) . '" style="float:left; width:1rem; height:1rem; background-color:' . $surlignage . '"></div><i class="fa fa-edit text-muted mx-1" data-bs-toggle="tooltip" title="' . __d('two_groupes', 'Document co-rédigé') . '." ></i><a href="'. site_url('modules.php?ModPath=wspad&amp;ModStart=wspad&amp;op=relo&amp;page='. $pad['p'] .'&amp;member='. $gr .'&amp;ranq='. $pad['r']) .'">' . $pad['p'] . '</a></li>';
                }

                $lst_doc .= '
              </ul>';
            }

            $content .= '
           <hr /><div class="">' . $lst_doc_tog . '<i class="fa fa-edit fa-2x text-muted ms-3 align-middle" title="' . __d('two_groupes', 'Co-rédaction') . '" data-bs-toggle="tooltip" data-bs-placement="right"></i>&nbsp;<a class="text-uppercase" href="'. site_url('modules.php?ModPath=wspad&ModStart=wspad&member='. $gr) .'" >' . __d('two_groupes', 'Co-rédaction') . '</a>' . $nb_doc_gr . $lst_doc . '</div>' . "\n";
        }
        //<= wspad

        //=> bloc-notes
        if ($rsql['groupe_blocnote'] == 1) {
            settype($lst_blocnote_tog, 'string');
            settype($lst_blocnote, 'string');
            
            include_once("modules/BlocNotes/http/bloc-notes.php");
            
            $lst_blocnote_tog = '<a data-bs-toggle="collapse" data-bs-target="#lst_blocnote_' . $gr . '" class="text-primary" id="show_lst_blocnote" title="' . __d('two_groupes', 'Déplier la liste') . '"><i id="i_lst_blocnote" class="toggle-icon fa fa-caret-down fa-2x" >&nbsp;</i></a><i class="far fa-sticky-note fa-2x text-muted ms-3 align-middle"></i>&nbsp;<span class="text-uppercase">Bloc note</span>';
            $lst_blocnote = '
           <div id="lst_blocnote_' . $gr . '" class="mt-3 collapse">
           ' . blocnotes("shared", 'WS-BN' . $gr, '', '7', 'bg-dark text-light', false) . '
           </div>';
            $content .= '
           <hr />
           <div class="mb-2">' . $lst_blocnote_tog . $lst_blocnote . '</div>';
        }
        //<= bloc-notes

        $content .= '<div class="px-1 card card-body d-flex flex-row mt-3 flex-wrap text-center">';
        
        //=> Filemanager
        if (file_exists('modules/f-manager/config/groupe_' . $gr . '.conf.php')) {
            $content .= '<a class="mx-2" href="'. site_url('modules.php?ModPath=f-manager&amp;ModStart=f-manager&amp;FmaRep=groupe_'. $gr .'') .'" title="' . __d('two_groupes', 'Gestionnaire fichiers') . '" data-bs-toggle="tooltip" data-bs-placement="right"><i class="fa fa-folder fa-2x"></i></a>' . "\n";
        }

        //=> Minisite
        if ($rsql['groupe_mns'] == 1) {
            $content .= '<a class="mx-2" href="'. site_url('minisite.php?op=groupe/'. $gr) .'" target="_blank" title= "' . __d('two_groupes', 'MiniSite') . '" data-bs-toggle="tooltip" data-bs-placement="right"><i class="fa fa-desktop fa-2x"></i></a>';
        }

        //=> Chat
        settype($chat_img, 'string');
        
        if ($rsql['groupe_chat'] == 1) {
            $PopUp = JavaPopUp(site_url('chat.php?id='. $gr .'&amp;auto='. Crypt::encrypt(serialize($gr))), "chat" . $gr, 380, 480);
            
            if (array_key_exists('chat_info_' . $gr, $_COOKIE)) {
                if ($_COOKIE['chat_info_' . $gr]) {
                    $chat_img = 'faa-pulse animated faa-slow';
                }
            }
            
            $content .= '<a class="mx-2" href="javascript:void(0);" onclick="window.open(' . $PopUp . ');" title="' . __d('two_groupes', 'Ouvrir un salon de chat pour le groupe.') . '" data-bs-toggle="tooltip" data-bs-placement="right" ><i class="fa fa-comments fa-2x ' . $chat_img . '"></i></a>';
        }
        //=> admin

        if ($this->user->autorisation(-127)) {
            $content .= '<a class="mx-2" href="'. site_url('admin.php?op=groupes') .'" ><i title="' . __d('two_groupes', 'Gestion des groupes.') . '" data-bs-toggle="tooltip" class="fa fa-cogs fa-2x"></i></a>';
        }

        $content .= '</div>
        </div>';
        
        return $content;
    }

    /**
     * [fab_groupes_bloc description]
     *
     * @param   string  $user  [$user description]
     * @param   string  $im    [$im description]
     *
     * @return  string
     */
    public function fab_groupes_bloc(string $user, string $im): string
    {
        $lstgr = array();
        $userdata = explode(':', base64_decode( (string) $user));

        $result = DB::table('users_status')
                    ->distinct()
                    ->select('groupe')
                    ->where('groupe', '>', 1)
                    ->get();

        foreach ($result as $groupe) {

            $pos = strpos($groupe['groupe'], ',');
            
            if ($pos === false) {
                $lstgr[] = $groupe['groupe'];
            } else {
                $arg = explode(',', $groupe['groupe']);
                foreach ($arg as $v) {
                    if (!in_array($v, $lstgr, true)) {
                        $lstgr[] = $v;
                    }
                }
            }
        }

        $ids_gr = join("','", $lstgr);
        
        $result = DB::table('groupes')
                        ->select('groupe_id', 'groupe_name', 'groupe_description')
                        ->where('groupe_id', 'in', $ids_gr)
                        ->get();

        $nb_groupes = count($result);

        $content = '
           <div id="bloc_groupes" class="">
              <ul id="lst_groupes" class="list-group list-group-flush mb-3">
                 <li class="list-group-item d-flex justify-content-between align-items-start px-0">
                    <div class="me-auto">
                       <div class="fw-bold"><i class="fa fa-users fa-2x text-muted me-2"></i>' . __d('two_groupes', 'Groupes') . '</div>';
        $content .= $nb_groupes > 0 ? __d('two_groupes', 'Groupe ouvert') : __d('two_groupes', 'Pas de groupe ouvert');
        $content .= '
                    </div>
                    <span class="badge bg-primary rounded-pill">' . $nb_groupes . '</span>
                 </li>';
        
        foreach ($result as $groupe) {   
            $content .= '
                 <li class="list-group-item px-0">' . $groupe['groupe_name'] . '<div class="small">' . $groupe['groupe_description'] . '</div>';
            $content .= $im == 1 ? '<div class="text-center my-2"><img class="img-fluid" src="storage/users_private/groupe/' . $groupe['groupe_id'] . '/groupe.png" loading="lazy"></div>' : '';
            
            if (!file_exists('storage/users_private/groupe/ask4group_' . $userdata[0] . '_' . $groupe['groupe_id'] . '_.txt') and !$this->user->autorisation($groupe['groupe_id'])) {
                if (!$this->user->autorisation(-1)) {
                    $content .= '<div class="text-end small"><a href="'. site_url('user.php?op=askforgroupe&amp;askedgroup='. $groupe['groupe_id']) .'" title="' . __d('two_groupes', 'Envoi une demande aux administrateurs pour rejoindre ce groupe. Un message privé vous informera du résultat de votre demande.') . '" data-bs-toggle="tooltip">' . __d('two_groupes', 'Rejoindre ce groupe') . '</a></div>';
                }
            }

            $content .= '</li>';
        }

        $content .= '
              </ul>';
        
        if ($this->user->autorisation(-127)) {
            $content .= '
              <div class="text-end"><a class="mx-2" href="'. site_url('admin.php?op=groupes') .'" ><i title="' . __d('two_groupes', 'Gestion des groupes.') . '" data-bs-toggle="tooltip" data-bs-placement="left" class="fa fa-cogs fa-lg"></i></a></div>';
        }

        $content .= '
           </div>';

        return $content;
    }

    /**
     * [groupe description]
     *
     * @param   string  $groupe  [$groupe description]
     *
     * @return  string
     */
    public function groupe(string $groupe): string
    {
        $les_groupes = explode(',', $groupe);

        $mX = $this->liste_group();
        
        $nbg = 0;
        $str = '';

        foreach ($mX as $groupe_id => $groupe_name) {
            $selectionne = 0;

            if ($les_groupes) {
                foreach ($les_groupes as $groupevalue) {
                    if (($groupe_id == $groupevalue) and ($groupe_id != 0)) {
                        $selectionne = 1;
                    }
                }
            }

            if ($selectionne == 1) {
                $str .= '<option value="' . $groupe_id . '" selected="selected">' . $groupe_name . '</option>';
            } else {
                $str .= '<option value="' . $groupe_id . '">' . $groupe_name . '</option>';
            }

            $nbg++;
        }

        if ($nbg > 5) {
            $nbg = 5;
        }
        
        return ('
        <select multiple="multiple" class="form-control" name="Mmember[]" size="' . $nbg . '">
        ' . $str . '
        </select>');
    }

    /**
     * [droits description]
     *
     * @param   string   $member  [$member description]
     *
     * @return  void
     */
    public function droits(string $member): void
    {
        echo '
        <fieldset>
        <legend>' . __d('two_groupes', 'Droits') . '</legend>
        <div class="mb-3">
            <div class="form-check form-check-inline">';
    
        if ($member == -127) { 
            $checked = ' checked="checked"';
        } else {
            $checked = '';
        }
    
        echo '
                <input class="form-check-input" type="radio" id="adm" name="members" value="-127" ' . $checked . ' />
                <label class="form-check-label" for="adm">' . __d('two_groupes', 'Administrateurs') . '</label>
            </div>
            <div class="form-check form-check-inline">';
    
        if ($member == -1) {
            $checked = ' checked="checked"';
        } else {
            $checked = '';
        }
    
        echo '
                <input class="form-check-input" type="radio" id="ano" name="members" value="-1" ' . $checked . ' />
                <label class="form-check-label" for="ano">' . __d('two_groupes', 'Anonymes') . '</label>
            </div>
            <div class="form-check form-check-inline">';
        
        if ($member > 0) {
            echo '
                    <input class="form-check-input" type="radio" id="mem" name="members" value="1" checked="checked" />
                    <label class="form-check-label" for="mem">' . __d('two_groupes', 'Membres') . '</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="tous" name="members" value="0" />
                    <label class="form-check-label" for="tous">' . __d('two_groupes', 'Tous') . '</label>
                </div>
            </div>
            <div class="mb-3">
                <label class="col-form-label" for="Mmember[]">' . __d('two_groupes', 'Groupes') . '</label>';
           
            echo $this->groupe($member) . '</div>';
        } else {
            if ($member == 0) { 
                $checked = ' checked="checked"';
            } else {
                $checked = '';
            }
    
            echo '
                    <input class="form-check-input" type="radio" id="mem" name="members" value="1" />
                    <label class="form-check-label" for="mem">' . __d('two_groupes', 'Membres') . '</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="tous" name="members" value="0"' . $checked . ' />
                    <label class="form-check-label" for="tous">' . __d('two_groupes', 'Tous') . '</label>
                </div>
            </div>
            <div class="mb-3">
                <label class="col-form-label" for="Mmember[]">' . __d('two_groupes', 'Groupes') . '</label>';
                
                echo $this->groupe($member) . '
                </div>
            </fieldset>';
        }
    }

}
