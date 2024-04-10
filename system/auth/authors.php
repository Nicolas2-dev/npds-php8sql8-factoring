<?php

declare(strict_types=1);

namespace npds\system\auth;

use npds\system\cookie\cookie;
use npds\system\security\protect;
use npds\system\support\facades\DB;

class authors
{
 
    /**
     * 
     *
     * @return  string
     */
    public static function extractAdmin(): string 
    {
        $admin = cookie::extratCookie('admin');

        if (isset($admin)) {
            $ibid = explode(':', base64_decode($admin));
            array_walk($ibid, [protect::class, 'url']);
            $admin = base64_encode(str_replace("%3A", ":", urlencode(base64_decode($admin))));
        }

        return $admin;
    }

    /**
     * 
     *
     * @return  string
     */
    public static function getAdmin(): string
    {
        return static::extractAdmin();
    }

    /**
     * 
     *
     * @return  array
     */
    public static function cookieAdmin():array|bool
    {
        $admin = static::extractAdmin();

        if (isset($admin)) {
            return cookie::cookiedecode($admin);
        }

        return false;
    }

    /**
     * Phpnuke compatibility functions
     *
     * @param   string  $xadmin  [$xadmin description]
     *
     * @return  bool
     */    
    public static function is_admin(string $xadmin): bool
    {
        $admin = static::getAdmin();
        
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
        $author = DB::table('authors')->select('url', 'email')->where('aid', $aid)->first();

        if ($author) {
            
            if (isset($author['url'])) {
                echo '<a href="' . $author['url'] . '" >' . $aid . '</a>';
            } elseif (isset($author['email'])) {
                echo '<a href="mailto:' . $author['email'] . '" >' . $aid . '</a>';
            } else {
                echo $aid;
            }
        }
    }
}
