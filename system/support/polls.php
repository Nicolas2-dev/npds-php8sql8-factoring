<?php

declare(strict_types=1);

namespace npds\system\support;

use npds\system\block\boxe;

class polls
{

    /**
     * Assure la gestion des sondages membres
     *
     * @param   string    $pollID  [$pollID description]
     *
     * @return  array
     */
    public static function pollSecur(string|int $pollID): array
    {
        global $NPDS_Prefix, $user;

        $pollClose = '';
        $result = sql_query("SELECT pollType FROM " . $NPDS_Prefix . "poll_data WHERE pollID='$pollID'");

        if (sql_num_rows($result)) {
            list($pollType) = sql_fetch_row($result);
            
            $pollClose = (($pollType / 128) >= 1 ? 1 : 0);
            $pollType = $pollType % 128;
            
            if (($pollType == 1) and !isset($user)) {
                $pollClose = 99;
            }
        }

        return array($pollID, $pollClose);
    }

    /**
     * Bloc Sondage
     *
     * syntaxe   : function#pollnewest 
     * arguments : params#ID_du_sondage OU vide (dernier sondage créé)
     * 
     * @param   int  $id  [$id description]
     *
     * @return  void
     */
    public static function PollNewest(?int $id = null): void
    {
        global $NPDS_Prefix;

        // snipe : multi-poll evolution
        if ($id != 0) {
            
            list($ibid, $pollClose) = static::pollSecur($id);
            
            if ($ibid) {
                boxe::pollMain($ibid, $pollClose);
            }

        } elseif ($result = sql_query("SELECT pollID FROM " . $NPDS_Prefix . "poll_data ORDER BY pollID DESC LIMIT 1")) {
            list($pollID) = sql_fetch_row($result);
            
            list($ibid, $pollClose) = static::pollSecur($pollID);
            
            if ($ibid) {
                boxe::pollMain($ibid, $pollClose);
            }
        }
    }
}
