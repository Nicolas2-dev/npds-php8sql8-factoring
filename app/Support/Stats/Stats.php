<?php

declare(strict_types=1);

namespace App\Support\Stats;

use Npds\Support\Facades\DB;


class Stats
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
        $counter = DB::table('counter')->select('count')->where('type', 'total')->first();
        if ($counter['count']) {
            $totalz = $counter['count'];
        }

        //$totalz++;
        $xtab[5] = $totalz; //++;
        
        return $xtab;
    }
}
