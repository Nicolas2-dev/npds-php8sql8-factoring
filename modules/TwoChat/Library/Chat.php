<?php

declare(strict_types=1);

namespace Modules\TwoChat\Library;

use Predis\Command\Traits\DB;
use Two\Support\Facades\Request;
use Modules\TwoCore\Support\Security;
use Modules\TwoUsers\Support\Facades\User;
use Modules\TwoBlocks\Support\Facades\Block;


class Chat
{

    /**
     * Retourne le nombre de connecté au Chat
     *
     * @param   string  $pour  [$pour description]
     *
     * @return  string
     */
    public static function if_chat(string $pour): string 
    {
        $auto = Block::autorisation_block("params#" . $pour);
        $dimauto = count( (array) $auto);
        $numofchatters = 0;

        if ($dimauto <= 1) {
            $numofchatters = DB::table('chatbox')
                                ->distinct()
                                ->select('ip')
                                ->where('id', $auto[0])
                                ->where('date', '>=', (time() - (60 * 3)))
                                ->count();
        }

        return $numofchatters;
    }

    /**
     * Insère un record dans la table Chat / on utilise id pour filtrer les messages - id = l'id du groupe
     *
     * @param   string  $username  [$username description]
     * @param   string  $message   [$message description]
     * @param   int     $dbname    [$dbname description]
     * @param   int     $id        [$id description]
     *
     * @return  void
     */
    public static function insertChat() : void
    {
        $cookie = User::cookieUser(1);
        $name   = Request::input('name');

        if (!isset($cookie) && isset($name)) {
            $username = $name;
            $dbname = 0;
        } else {
            $username = $cookie;
            $dbname = 1;
        }

        if ($message = Request::input('message')) {
            $message =  Security::remove(stripslashes(strip_tags(trim($message))));
            $message =  Security::remove(stripslashes(strip_tags(trim($message))));

            DB::table('chatbox')->insert(array(
                'username'  => $username,
                'ip'        => Request::getIp(),
                'message'   => $message,
                'date'      => time(),
                'id'        => Request::input('id'),
                'dbname'    => $dbname,
            ));
        }
    }

}
