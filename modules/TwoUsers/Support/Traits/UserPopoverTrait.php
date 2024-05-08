<?php

namespace Modules\TwoUsers\Support\Traits;

use Two\Support\Facades\DB;
use Two\Support\Facades\Config;
use Modules\TwoCore\Support\Spam;
use Modules\TwoForum\Library\Forum;
use Modules\TwoUsers\Support\Facades\User;
use Modules\TwoThemes\Support\Facades\Theme;


trait UserPopoverTrait 
{

    #autodoc userpopover($who, $dim, $avpop) : à partir du nom de l'utilisateur ($who) $avpop à 1 : affiche son avatar (ou avatar defaut) au dimension ($dim qui défini la class n-ava-$dim)<br /> $avpop à 2 : l'avatar affiché commande un popover contenant diverses info de cet utilisateur et liens associés
    function userpopover($who, $dim, $avpop)
    {
        if (DB::table('users')
            ->select('uname')
            ->where('uname', $who)
            ->first()) 
        {
            $temp_user = Forum::get_userdata($who);

            $socialnetworks = array();
            $posterdata_extend = array();
            $res_id = array();

            $my_rs = '';

            if (!Config::get('two_core::config.short_user')) {
                if ($temp_user['uid'] != 1) {
                    $posterdata_extend = Forum::get_userdata_extend_from_id($temp_user['uid']);

                    include('modules/reseaux-sociaux/reseaux-sociaux.conf.php');
                    include('modules/geoloc/config/geoloc.conf');

                    if (User::getUser() or User::autorisation(-127)) {
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
                                        $my_rs .= '<a class="me-2 " href="';
                                        
                                        if ($v1[2] == 'skype') {
                                            $my_rs .= $v1[1] . $y1[1] . '?chat';
                                        } else {
                                            $my_rs .= $v1[1] . $y1[1];
                                        }
                                        
                                        $my_rs .= '" target="_blank"><i class="fab fa-' . $v1[2] . ' fa-lg fa-fw mb-2"></i></a> ';
                                        break;
                                    } else {
                                        $my_rs .= '';
                                    }
                                }
                            }
                        }
                    }
                }
            }

            settype($ch_lat, 'string');

            $useroutils = '';
            if (User::getUser() or User::autorisation(-127)) {
                if ($temp_user['uid'] != 1 and $temp_user['uid'] != '') {
                    $useroutils .= '<li><a class="dropdown-item text-center text-md-start" href="user.php?op=userinfo&amp;uname=' . $temp_user['uname'] . '" target="_blank" title="' . __d('two_users', 'Profil') . '" ><i class="fa fa-lg fa-user align-middle fa-fw"></i><span class="ms-2 d-none d-md-inline">' . __d('two_users', 'Profil') . '</span></a></li>';
                }

                if ($temp_user['uid'] != 1 and $temp_user['uid'] != '') {
                    $useroutils .= '<li><a class="dropdown-item text-center text-md-start" href="powerpack.php?op=instant_message&amp;to_userid=' . urlencode($temp_user['uname']) . '" title="' . __d('two_users', 'Envoyer un message interne') . '" ><i class="far fa-lg fa-envelope align-middle fa-fw"></i><span class="ms-2 d-none d-md-inline">' . __d('two_users', 'Message') . '</span></a></li>';
                }

                if ($temp_user['femail'] != '') {
                    $useroutils .= '<li><a class="dropdown-item  text-center text-md-start" href="mailto:' . Spam::anti_spam($temp_user['femail'], 1) . '" target="_blank" title="' . __d('two_users', 'Email') . '" ><i class="fa fa-at fa-lg align-middle fa-fw"></i><span class="ms-2 d-none d-md-inline">' . __d('two_users', 'Email') . '</span></a></li>';
                }

                if ($temp_user['uid'] != 1 and array_key_exists($ch_lat, $posterdata_extend)) {
                    if ($posterdata_extend[$ch_lat] != '') {
                        $useroutils .= '<li><a class="dropdown-item text-center text-md-start" href="modules.php?ModPath=geoloc&amp;ModStart=geoloc&op=u' . $temp_user['uid'] . '" title="' . __d('two_users', 'Localisation') . '" ><i class="fas fa-map-marker-alt fa-lg align-middle fa-fw">&nbsp;</i><span class="ms-2 d-none d-md-inline">' . __d('two_users', 'Localisation') . '</span></a></li>';
                    }
                }
            }

            if ($temp_user['url'] != '') {
                $useroutils .= '<li><a class="dropdown-item text-center text-md-start" href="' . $temp_user['url'] . '" target="_blank" title="' . __d('two_users', 'Visiter ce site web') . '"><i class="fas fa-external-link-alt fa-lg align-middle fa-fw"></i><span class="ms-2 d-none d-md-inline">' . __d('two_users', 'Visiter ce site web') . '</span></a></li>';
            }

            if ($temp_user['mns']) {
                $useroutils .= '<li><a class="dropdown-item text-center text-md-start" href="minisite.php?op=' . $temp_user['uname'] . '" target="_blank" target="_blank" title="' . __d('two_users', 'Visitez le minisite') . '" ><i class="fa fa-lg fa-desktop align-middle fa-fw"></i><span class="ms-2 d-none d-md-inline">' . __d('two_users', 'Visitez le minisite') . '</span></a></li>';
            }

            if (stristr($temp_user['user_avatar'], 'users_private')){
                $imgtmp = $temp_user['user_avatar'];
            } else {
                if ($ibid = $this->theme_image('forum/avatar/' . $temp_user['user_avatar'])) {
                    $imgtmp = $ibid;
                } else {
                    $imgtmp = 'images/forum/avatar/' . $temp_user['user_avatar'];
                }
            }

            $userpop = $avpop == 1 ?
                '<img class="btn-outline-primary img-thumbnail img-fluid n-ava-' . $dim . ' me-2" src="' . $this->asset_theme($imgtmp) . '" alt="' . $temp_user['uname'] . '" loading="lazy" />' :
                //         '<a tabindex="0" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-html="true" data-bs-title="'.$temp_user['uname'].'" data-bs-content=\'<div class="list-group mb-3 text-center">'.$useroutils.'</div><div class="mx-auto text-center" style="max-width:170px;">'.$my_rs.'</div>\'></i><img data-bs-html="true" class="btn-outline-primary img-thumbnail img-fluid n-ava-'.$dim.' me-2" src="'.$imgtmp.'" alt="'.$temp_user['uname'].'" loading="lazy" /></a>' ;

                '
                <div class="dropdown d-inline-block me-4 dropend">
                    <a class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                        <img class=" btn-outline-primary img-fluid n-ava-' . $dim . ' me-0" src="' . $this->asset_theme($imgtmp) . '" alt="' . $temp_user['uname'] . '" />
                    </a>
                    <ul class="dropdown-menu bg-light">
                        <li><span class="dropdown-item-text text-center py-0 my-0">' . Theme::userpopover($who, 64, 1) . '</span></li>
                        <li><h6 class="dropdown-header text-center py-0 my-0">' . $who . '</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        ' . $useroutils . '
                        <li><hr class="dropdown-divider"></li>
                        <li><div class="mx-auto text-center" style="max-width:170px;">' . $my_rs . '</div>
                    </ul>
                </div>';

            return $userpop;
        }
    }

}