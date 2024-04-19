<?php

declare(strict_types=1);

namespace npds\support\auth;

use npds\support\auth\groupe;
use npds\system\config\Config;
use npds\support\cookie\cookie;
use npds\support\security\protect;
use npds\system\support\facades\DB;
use npds\system\support\facades\Request;

class users
{

    /**
     * 
     *
     * @return  string|null
     */
    public static function extractUser(): string|null  
    {
        $user = cookie::extratCookie('user');

        if (isset($user)) {
            $ibid = explode(':', base64_decode($user));
            array_walk($ibid, [protect::class, 'url']);
            return base64_encode(str_replace("%3A", ":", urlencode(base64_decode($user))));
        }

        return null;
    }

    /**
     * 
     *
     * @return  string|null
     */
    public static function getUser(): string|null
    {
        return static::extractUser();
    }

    /**
     * 
     *
     * @return  string|array|bool|int
     */
    public static function cookieUser($arg = null): string|array|bool|int|null
    {
        $user = static::extractUser();

        if (isset($user)) {
            $cookie = cookie::cookiedecode($user);

            if(is_null($arg)) {
                return $cookie;
            } elseif (is_numeric($arg)) {
                if (array_key_exists($arg, $cookie)) {
                    return $cookie[$arg];
                }
                
                return null;
            }
        }

        return null;
    }

    /**
     * Phpnuke compatibility functions
     *
     * @param   string  $xuser  [$xuser description]
     *
     * @return  bool
     */
    public static function is_user(string $xuser): bool
    {
        $user = static::getUser();

        if (isset($user) and ($user != '')) {
            return true;
        } else {
            return false;
        }
    }
 
    /**
     * Renvoi le contenu de la table users pour le user uname
     *
     * @param   string  $user  [$user description]
     *
     * @return  array
     */
    public static function getusrinfo(string $user): array
    {
        $cookie = explode(':', base64_decode($user));
        
        $user = DB::table('users')
                    ->select('pass')
                    ->where('uname', $cookie[1])
                    ->first();

        $userinfo = '';
        
        if (($cookie[2] == md5($user['pass'])) and ($user['pass'] != '')) {
            
            $result = DB::table('users')
                ->select('uid', 'name', 'uname', 'email', 'femail', 'url', 'user_avatar', 'user_occ', 'user_from', 'user_intrest', 'user_sig', 'user_viewemail', 'user_theme', 'pass', 'storynum', 'umode', 'uorder', 'thold', 'noscore', 'bio', 'ublockon', 'ublock', 'theme', 'commentmax', 'user_journal', 'send_email', 'is_visible', 'mns', 'user_lnl')
                ->where('uname', $cookie[1])
                ->first();

            if ($result) {
                $userinfo = $result;
            } else {
                echo '<strong>'. translate("Un problème est survenu") .'.</strong>';
            }
        }

        return $userinfo;
    }

    /**
     * Si AutoRegUser=true et que le user ne dispose pas du droit de connexion
     * RAZ du cookie NPDS retourne False ou True
     *
     * @return  bool
     */
    public static function AutoReg(): bool
    {
        if (!Config::get('npds.AutoRegUser')) {

            $user = static::getUser();

            if (isset($user)) {
                $cookie = explode(':', base64_decode($user));
                
                $test = DB::table('users_status')
                            ->select('open')
                            ->where('uid', $cookie[0])
                            ->first();

                if (!$test['uid']) {
                    setcookie('user', '', 0);
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        } else {
            return true;
        }
    }
  
    /**
     * permet de calculer le coût algorythmique optimum pour la procédure de hashage ($AlgoCrypt)
     * d'un mot de pass ($pass) avec un temps minimum alloué ($min_ms)
     *
     * @param   string  $pass       [$pass description]
     * @param   string  $AlgoCrypt  [$AlgoCrypt description]
     * @param   int     $min_ms     [$min_ms description]
     *
     * @return  int
     */
    public static function getOptimalBcryptCostParameter(string $pass, string $AlgoCrypt, int $min_ms = 100): int
    {
        for ($i = 8; $i < 13; $i++) {
            $calculCost = ['cost' => $i];
            $time_start = microtime(true);
            
            password_hash($pass, $AlgoCrypt, $calculCost);
            $time_end = microtime(true);
            
            if (($time_end - $time_start) * 1000 > $min_ms)
                return $i;
        }
    }
 
    /**
     * Pour savoir si le visiteur est un : membre ou admin (static.php et banners.php par exemple)
     *
     * @param   string  $sec_type  [$sec_type description]
     *
     * @return  bool
     */
    public static function secur_static(string $sec_type): bool
    {
        switch ($sec_type) {
            case 'member':
                $user = static::getUser();
                if (isset($user)) {
                    return true;
                } else {
                    return false;
                }
            break;
            
            case 'admin':
                $admin = authors::getAdmin();
                if (isset($admin)) {
                    return true;
                } else {
                    return false;
                }
            break;
        }
    }
 
    /**
     * Retourne true ou false en fonction des paramètres d'autorisation de NPDS (Administrateur, anonyme, Membre, Groupe de Membre, Tous)
     *
     * @param   int|string  $auto  [$auto description]
     *
     * @return  bool
     */
    public static function autorisation(int|string $auto): bool
    {
        $user   = static::getUser();
        
        $affich = false;
        if (($auto == -1) and (!$user)) {
            $affich = true;
        }

        if (($auto == 1) and (isset($user))) {
            $affich = true;
        }

        if ($auto > 1) {
            $tab_groupe = groupe::valid_group($user);
            
            if ($tab_groupe) {
                foreach ($tab_groupe as $groupevalue) {
                    if ($groupevalue == $auto) {
                        $affich = true;
                        break;
                    }
                }
            }
        }

        if ($auto == 0) {
            $affich = true;
        }

        $admin  = authors::getAdmin();

        if (($auto == -127) and ($admin)) { 
            $affich = true;
        }

        return $affich;
    }

    /**
     * retourne un menu utilisateur 
     *
     * @param   int     $mns  [$mns description]
     * @param   string  $qui  [$qui description]
     *
     * @return  void
     */
    public static function member_menu(int $mns, string $qui): void
    {
        $op = Request::query('op');

        echo '
        <ul class="nav nav-tabs d-flex flex-wrap"> 
            <li class="nav-item">
                <a class="nav-link '. ($_SERVER['REQUEST_URI'] == '/user.php' ? 'active' : '') .'" href="'. site_url('user.php') .'" title="'. translate("Votre compte") .'" data-bs-toggle="tooltip" >
                    <i class="fas fa-user fa-2x d-xl-none"></i><span class="d-none d-xl-inline"><i class="fas fa-user fa-lg"></i></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link '. ($op == 'edituser' ? 'active' : '') .'" href="'. site_url('user.php?op=edituser') .'" title="'. translate("Vous") .'" data-bs-toggle="tooltip" >
                    <i class="fas fa-user-edit fa-2x d-xl-none"></i><span class="d-none d-xl-inline">&nbsp;'. translate("Vous") .'</span>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle tooltipbyclass '. (($op == 'editjournal' or $op == 'edithome') ? 'active' : '') .'" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false" data-bs-html="true" title="'. translate("Editer votre journal") .'<br />'. translate("Editer votre page principale") .'">
                    <i class="fas fa-edit fa-2x d-xl-none me-2"></i><span class="d-none d-xl-inline">Editer</span>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item ' . ($op == 'editjournal' ? 'active' : '') . '" href="'. site_url('user.php?op=editjournal') .'" title="'. translate("Editer votre journal") .'" data-bs-toggle="tooltip">
                            '. translate("Journal") .'
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item ' . ($op == 'edithome' ? 'active' : '') . '" href="'. site_url('user.php?op=edithome') .'" title="'. translate("Editer votre page principale") .'" data-bs-toggle="tooltip">
                            '. translate("Page") .'
                        </a>
                    </li>
                </ul>
            </li>';

        include("modules/upload/config/upload.conf.php");

        if (($mns) and ($autorise_upload_p)) {
            include_once("modules/blog/http/upload_minisite.php");

            $PopUp = win_upload("popup");

            echo '
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle tooltipbyclass" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false" title="'. translate("Gérer votre miniSite") .'">
                    <i class="fas fa-desktop fa-2x d-xl-none me-2"></i><span class="d-none d-xl-inline">'. translate("MiniSite") .'</span>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="'. site_url('minisite.php?op='. $qui) .'" target="_blank">
                            '. translate("MiniSite") .'
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" onclick="window.open('. $PopUp .')" >
                            '. translate("Gérer votre miniSite") .'
                        </a>
                    </li>
                </ul>
            </li>';
        }
        
        $cl_rs = ($_SERVER['QUERY_STRING'] == 'ModPath=reseaux-sociaux&ModStart=reseaux-sociaux' or  $_SERVER['QUERY_STRING'] == 'ModPath=reseaux-sociaux&ModStart=reseaux-sociaux&op=EditReseaux' 
            ? 'active' 
            : ''
        );
        
        echo '
            <li class="nav-item">
                <a class="nav-link '. ($op == 'chgtheme' ? 'active' : '') .'" href="'. site_url('user.php?op=chgtheme') .'" title="'. translate("Changer le thème") .'"  data-bs-toggle="tooltip" >
                    <i class="fas fa-paint-brush fa-2x d-xl-none"></i><span class="d-none d-xl-inline">&nbsp;'. translate("Thème") .'</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link '. $cl_rs .'" href="'. site_url('modules.php?ModPath=reseaux-sociaux&amp;ModStart=reseaux-sociaux') .'" title="'. translate("Réseaux sociaux") .'"  data-bs-toggle="tooltip" >
                    <i class="fas fa-share-alt-square fa-2x d-xl-none"></i><span class="d-none d-xl-inline">&nbsp;'. translate("Réseaux sociaux") .'</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link '. (strstr($_SERVER['REQUEST_URI'], '/viewpmsg.php') ? 'active' : '') .' " href="'. site_url('viewpmsg.php') .'" title="'. translate("Message personnel") .'"  data-bs-toggle="tooltip" ><i class="far fa-envelope fa-2x d-xl-none"></i>
                    <span class="d-none d-xl-inline">&nbsp;' . translate("Message") . '</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="'. site_url('user.php?op=logout') .'" title="'. translate("Déconnexion") .'" data-bs-toggle="tooltip" >
                    <i class="fas fa-sign-out-alt fa-2x text-danger d-xl-none"></i><span class="d-none d-xl-inline text-danger">&nbsp;'. translate("Déconnexion") .'</span>
                </a>
            </li>
        </ul>
        <div class="mt-3"></div>';
    }

}
