<?php

declare(strict_types=1);

namespace npds\system\support;

class stats
{
 
    /**
     * Retourne un tableau contenant les nombres pour les statistiques du site (stats.php)
     *
     * @return  array   [return description]
     */
    public static function req_stat(): array
    {
        global $NPDS_Prefix;

        // Les membres
        $result = sql_query("SELECT uid FROM " . $NPDS_Prefix . "users");
        $xtab[0] = $result ? (sql_num_rows($result) - 1) : '0';

        // Les Nouvelles (News)
        $result = sql_query("SELECT sid FROM " . $NPDS_Prefix . "stories");
        $xtab[1] = $result ? sql_num_rows($result) : '0';

        // Les Critiques (Reviews))
        $result = sql_query("SELECT id FROM " . $NPDS_Prefix . "reviews");
        $xtab[2] = $result ? sql_num_rows($result) : '0';

        // Les Forums
        $result = sql_query("SELECT forum_id FROM " . $NPDS_Prefix . "forums");
        $xtab[3] = $result ? sql_num_rows($result) : '0';

        // Les Sujets (topics)
        $result = sql_query("SELECT topicid FROM " . $NPDS_Prefix . "topics");
        $xtab[4] = $result ? sql_num_rows($result) : '0';

        // Nombre de pages vues
        $result = sql_query("SELECT count FROM " . $NPDS_Prefix . "counter WHERE type='total'");
        if ($result) {
            list($totalz) = sql_fetch_row($result);
        }

        $totalz++;
        $xtab[5] = $totalz++;
        
        sql_free_result($result);

        return $xtab;
    }
}
