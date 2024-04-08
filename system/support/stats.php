<?php

declare(strict_types=1);

namespace npds\system\support;

use npds\system\support\facades\DB;

class stats
{
 
    /**
     * Retourne un tableau contenant les nombres pour les statistiques du site (stats.php)
     *
     * @return  array   [return description]
     */
    public static function req_stat(): array
    {
        // Les membres
        $count = DB::table('users')->select('uid')->count();
        $xtab[0] = $count ? ($count - 1) : '0';

        // Les Nouvelles (News)
        $count = DB::table('stories')->select('sid')->count();
        $xtab[1] = $count ? $count : '0';

        // Les Critiques (Reviews))
        $count = DB::table('reviews')->select('id')->count();
        $xtab[2] = $count ? $count : '0';

        // Les Forums
        $count = DB::table('forums')->select('forum_id')->count();
        $xtab[3] = $count ? $count : '0';

        // Les Sujets (topics)
        $count = DB::table('topics')->select('topicid')->count();
        $xtab[4] = $count ? $count : '0';

        // Nombre de pages vues
        $count = DB::table('counter')->select('count')->where('type', 'total')->count();
        if ($count) {
            list($totalz) = $count;
        }

        $totalz++;
        $xtab[5] = $totalz++;
        
        return $xtab;
    }
}
