<?php

declare(strict_types=1);

namespace npds\system\chat;

use npds\system\auth\users;
use npds\system\assets\java;
use npds\system\block\block;
use npds\system\cache\cache;
use npds\system\forum\forum;
use npds\system\support\str;
use npds\system\auth\authors;
use npds\system\config\Config;
use npds\system\security\hack;
use npds\system\utility\crypt;
use npds\system\support\facades\DB;

class chat
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
        $auto = block::autorisation_block("params#" . $pour);
        $dimauto = count( (array) $auto);
        $numofchatters = 0;

        if ($dimauto <= 1) {
            $numofchatters = DB::table('chatbox')->distinct()->select('ip')->where('id', $auto[0])->where('date', '>=', (time() - (60 * 3)))->count();
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
    public static function insertChat(string $username, string $message, int $dbname, int $id) : void
    {
        if ($message != '') {
            $username = hack::removeHack(stripslashes(str::FixQuotes(strip_tags(trim($username)))));
            $message =  hack::removeHack(stripslashes(str::FixQuotes(strip_tags(trim($message)))));

            $ip = getip();

            settype($id, 'integer');
            settype($dbname, 'integer');

            DB::table('chatbox')->insert(array(
                'username'  => $username,
                'ip'        => $ip,
                'message'   => $message,
                'date'      => time(),
                'ip'        => $id,
                'dbname'    => $dbname,
            ));
        }
    }

    /**
     * Bloc ChatBox
     * syntaxe : function#makeChatBox
     * params#chat_membres
     * 
     * le parametre doit être en accord avec l'autorisation donc (chat_membres, chat_tous, chat_admin, chat_anonyme)
     *
     * @param   string  $pour  [$pour description]
     *
     * @return  void
     */
    public static function makeChatBox(string $pour): void
    {
        $auto = (array) block::autorisation_block('params#' . $pour);
        $dimauto = count($auto);

        if (!Config::get('npds.theme.long_chain')) {
            Config::get('npds.theme.long_chain', 12);
        }

        $thing = '';
        $une_ligne = false;

        if ($dimauto <= 1) {
            $counter = DB::table('chatbox')->select('message')->where('id', $auto[0])->get();

            if ($counter < 0) {
                $counter = 0;
            }

            $result = DB::table('chatbox')
                        ->select('username', 'message', 'dbname')
                        ->where('id', $auto[0])
                        ->orderBy('date', 'asc')
                        ->limit(6)
                        ->offset($counter)
                        ->get();

            if ($result) {
                foreach($result as $chatbox) {  
                    if (isset($chatbox['username'])) {
                        if ($chatbox['dbname'] == 1) {

                            $user = users::getUser();
                            $admin = authors::getAdmin();

                            $thing .= ((!$user) and (Config::get('npds.member_list') == 1) and (!$admin)) ?
                                '<span class="">' . substr($chatbox['username'], 0, 8) . '.</span>' :
                                "<a href=\"user.php?op=userinfo&amp;uname=" . $chatbox['username'] ."\">" . substr($chatbox['username'], 0, 8) . ".</a>";
                        } else {
                            $thing .= '<span class="">' . substr($chatbox['username'], 0, 8) . '.</span>';
                        }
                    }

                    $une_ligne = true;
                    $thing .= (strlen($chatbox['message']) > Config::get('npds.theme.long_chain'))  ?
                        "&gt;&nbsp;<span>" . forum::smilie(stripslashes(substr($chatbox['message'], 0, Config::get('npds.theme.long_chain')))) . " </span><br />\n" :
                        "&gt;&nbsp;<span>" . forum::smilie(stripslashes($chatbox['message'])) . " </span><br />\n";
                }
            }

            $PopUp = java::JavaPopUp("chat.php?id=" . $auto[0] . "&amp;auto=" . crypt::encrypt(serialize($auto[0])), "chat" . $auto[0], 380, 480);
            
            if ($une_ligne) {
                $thing .= '<hr />';
            }

            $numofchatters = DB::table('chatbox')
                                ->distinct()
                                ->select('ip')
                                ->where('id', $auto[0])
                                ->where('date', '>=', (time() - (60 * 2)))
                                ->count();

            $thing .= $numofchatters > 0 ?
                '<div class="d-flex"><a id="' . $pour . '_encours" class="fs-4" href="javascript:void(0);" onclick="window.open(' . $PopUp . ');" title="' . translate("Cliquez ici pour entrer") . ' ' . $pour . '" data-bs-toggle="tooltip" data-bs-placement="right"><i class="fa fa-comments fa-2x nav-link faa-pulse animated faa-slow"></i></a><span class="badge rounded-pill bg-primary ms-auto align-self-center" title="' . translate("personne connectée.") . '" data-bs-toggle="tooltip">' . $numofchatters . '</span></div>' :
                '<div><a id="' . $pour . '" href="javascript:void(0);" onclick="window.open(' . $PopUp . ');" title="' . translate("Cliquez ici pour entrer") . '" data-bs-toggle="tooltip" data-bs-placement="right"><i class="fa fa-comments fa-2x "></i></a></div>';
        } else {
            if (count($auto) > 1) {
                $numofchatters = 0;
                $thing .= '<ul>';
                
                foreach ($auto as $autovalue) {

                    $autovalueX = cache::Q_select3(DB::table('groupes')->select('groupe_id', 'groupe_name')->where('groupe_id', $autovalue)->get(), 3600, 'groupe(groupr_id)');

                    $PopUp = java::JavaPopUp("chat.php?id=" . $autovalueX[0]['groupe_id'] . "&auto=" . crypt::encrypt(serialize($autovalueX[0]['groupe_id'])), "chat" . $autovalueX[0]['groupe_id'], 380, 480);
                    $thing .= "<li><a href=\"javascript:void(0);\" onclick=\"window.open($PopUp);\">" . $autovalueX[0]['groupe_name'] . "</a>";
                    
                    $numofchatters = DB::table('chatbox')->distinct()->select('ip')->where('id', $autovalueX[0]['groupe_id'])->where('date', '>=', (time() - (60 * 3)))->count();

                    if ($numofchatters) {
                        $thing .= '&nbsp;(<span class="text-danger"><b>' . $numofchatters . '</b></span>)';
                    }

                    echo '</li>';
                }
                $thing .= '</ul>';
            }
        }

        global $block_title;
        if ($block_title == '') {
            $block_title = translate("Bloc Chat");
        }

        themesidebox($block_title, $thing);
    }

}
