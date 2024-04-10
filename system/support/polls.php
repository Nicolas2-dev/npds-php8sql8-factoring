<?php

declare(strict_types=1);

namespace npds\system\support;

use npds\system\auth\users;
use npds\system\block\boxe;
use npds\system\support\facades\DB;

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
        $pollClose = '';
        $result = DB::table('poll_data')->select('pollType')->where('pollID', $pollID)->first();

        if ($result) {
            
            $pollClose = (($result['pollType'] / 128) >= 1 ? 1 : 0);
            $pollType = $result['pollType'] % 128;
            
            $user = users::getUser();
            
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
        // snipe : multi-poll evolution
        if ($id != 0) {
            
            list($ibid, $pollClose) = static::pollSecur($id);
            
            if ($ibid) {
                boxe::pollMain($ibid, $pollClose);
            }

        } elseif ($result = DB::table('poll_data')->select('pollID')->orderBy('pollID', 'asc')->limit(1)->first()) {
            
            list($ibid, $pollClose) = static::pollSecur($result['pollID']);
            
            if ($ibid) {
                boxe::pollMain($ibid, $pollClose);
            }
        }
    }
}
