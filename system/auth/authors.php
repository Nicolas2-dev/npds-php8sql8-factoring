<?php

declare(strict_types=1);

namespace npds\system\auth;

class authors
{
 
    /**
     * Phpnuke compatibility functions
     *
     * @param   string  $xadmin  [$xadmin description]
     *
     * @return  bool
     */    
    public static function is_admin(string $xadmin): bool
    {
        global $admin;
        
        if (isset($admin) and ($admin != '')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *  Affiche URL et Email d'un auteur
     *
     * @param   string  $aid  [$aid description]
     *
     * @return  void
     */
    public static function formatAidHeader(string $aid): void
    {
        global $NPDS_Prefix;
        
        $holder = sql_query("SELECT url, email FROM " . $NPDS_Prefix . "authors WHERE aid='$aid'");
        
        if ($holder) {
            list($url, $email) = sql_fetch_row($holder);
            
            if (isset($url)) {
                echo '<a href="' . $url . '" >' . $aid . '</a>';
            } elseif (isset($email)) {
                echo '<a href="mailto:' . $email . '" >' . $aid . '</a>';
            } else {
                echo $aid;
            }
        }
    }
}
