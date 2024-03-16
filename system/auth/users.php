<?php

declare(strict_types=1);

namespace npds\system\auth;

use npds\system\auth\groupe;

class users
{

    /**
     * Phpnuke compatibility functions
     *
     * @param   string  $xuser  [$xuser description]
     *
     * @return  bool
     */
    public static function is_user(string $xuser): bool
    {
        global $user;

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
        global $NPDS_Prefix;
        
        $cookie = explode(':', base64_decode($user));
        $result = sql_query("SELECT pass FROM " . $NPDS_Prefix . "users WHERE uname='$cookie[1]'");
        list($pass) = sql_fetch_row($result);
        
        $userinfo = '';
        
        if (($cookie[2] == md5($pass)) and ($pass != '')) {
            $result = sql_query("SELECT uid, name, uname, email, femail, url, user_avatar, user_occ, user_from, user_intrest, user_sig, user_viewemail, user_theme, pass, storynum, umode, uorder, thold, noscore, bio, ublockon, ublock, theme, commentmax, user_journal, send_email, is_visible, mns, user_lnl FROM " . $NPDS_Prefix . "users WHERE uname='$cookie[1]'");
            
            if (sql_num_rows($result) == 1) {
                $userinfo = sql_fetch_assoc($result);
            } else {
                echo '<strong>' . translate("Un problème est survenu") . '.</strong>';
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
        global $NPDS_Prefix, $AutoRegUser, $user;

        if (!$AutoRegUser) {
            if (isset($user)) {
                $cookie = explode(':', base64_decode($user));
                list($test) = sql_fetch_row(sql_query("SELECT open FROM " . $NPDS_Prefix . "users_status WHERE uid='$cookie[0]'"));
                
                if (!$test) {
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
        global $user, $admin;

        switch ($sec_type) {
            case 'member':
                if (isset($user)) {
                    return true;
                } else {
                    return false;
                }
            break;
            
            case 'admin':
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
        global $user, $admin;

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

        if (($auto == -127) and ($admin)) { 
            $affich = true;
        }

        return $affich;
    }

}
