<?php

use Two\Support\Facades\DB;
use Two\Support\Facades\Crypt;
use Two\Support\Facades\Config;
use Modules\TwoUsers\Support\Facades\User;
use Modules\TwoBlocks\Support\Facades\Block;
use Modules\TwoThemes\Support\Facades\Theme;
use Modules\TwoAuthors\Support\Facades\Author;
use Two\Support\Facades\Cache;


if (! function_exists('makeChatBox'))
{
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
    function makeChatBox(string $pour): void
    {
        $auto = (array) Block::autorisation_block('params#' . $pour);
        $dimauto = count($auto);

        $long_chain = Theme::getConfig('config.long_chain');

        if (!$long_chain) {
            $long_chain = 12;
        }

        $thing = '';
        $une_ligne = false;

        if ($dimauto <= 1) {
            $counter = DB::table('chatbox')
                        ->select('message')
                        ->where('id', $auto[0])
                        ->get();

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
                    if (isset($chatbox->username)) {
                        if ($chatbox->dbname == 1) {

                            $user = User::getUser();
                            $admin = Author::getAdmin();

                            $thing .= ((!$user) and (Config::get('two_core::config.member_list') == 1) and (!$admin)) ?
                                '<span class="">'. substr($chatbox->username, 0, 8) .'.</span>' :
                                "<a href=\"". site_url('user.php?op=userinfo&amp;uname='. $chatbox->username) ."\">". substr($chatbox->username, 0, 8) .".</a>";
                        } else {
                            $thing .= '<span class="">'. substr($chatbox->username, 0, 8) .'.</span>';
                        }
                    }

                    $une_ligne = true;
                    // $thing .= ((strlen($chatbox->message) > $long_chain)  
                    //     ? "&gt;&nbsp;<span>". Forum::smilie(stripslashes(substr($chatbox->message, 0, $long_chain))) ." </span><br />\n" 
                    //     : "&gt;&nbsp;<span>". Forum::smilie(stripslashes($chatbox->message)) ." </span><br />\n"
                    // );

                    $thing .= ((strlen($chatbox->message) > $long_chain)  
                        ? "&gt;&nbsp;<span>". stripslashes(substr($chatbox->message, 0, $long_chain)) ." </span><br />\n" 
                        : "&gt;&nbsp;<span>". stripslashes($chatbox->message) ." </span><br />\n"
                    );
                }
            }

            $PopUp = JavaPopUp(site_url('chat.php?id='. $auto[0] .'&amp;auto='. Crypt::encrypt(serialize($auto[0]))), "chat" . $auto[0], 380, 480);
            
            if ($une_ligne) {
                $thing .= '<hr />';
            }

            $numofchatters = DB::table('chatbox')
                                ->distinct()
                                ->select('ip')
                                ->where('id', $auto[0])
                                ->where('date', '>=', (time() - (60 * 2)))
                                ->count();

            $thing .= (($numofchatters > 0) 
                ? '<div class="d-flex">
                    <a id="'. $pour .'_encours" class="fs-4" href="javascript:void(0);" onclick="window.open('. $PopUp .');" title="'. __d('two_chat', 'Cliquez ici pour entrer') .' '. $pour .'" data-bs-toggle="tooltip" data-bs-placement="right">
                        <i class="fa fa-comments fa-2x nav-link faa-pulse animated faa-slow"></i>
                    </a>
                    <span class="badge rounded-pill bg-primary ms-auto align-self-center" title="' . __d('two_chat', 'personne connectée.') . '" data-bs-toggle="tooltip">
                        ' . $numofchatters . '</span>
                    </div>'
                 
                : '<div>
                    <a id="'. $pour .'" href="javascript:void(0);" onclick="window.open('. $PopUp .');" title="'. __d('two_chat', 'Cliquez ici pour entrer') .'" data-bs-toggle="tooltip" data-bs-placement="right">
                        <i class="fa fa-comments fa-2x "></i>
                    </a>
                </div>
                '
                );
        } else {
            if (count($auto) > 1) {
                $numofchatters = 0;
                $thing .= '<ul>';
                
                foreach ($auto as $autovalue) {

                    $autovalueX = Cache::remember('groupes_chat', Config::get('two_Chat::config.cache.groupes_chat'), function () use ($autovalue) {
                        return DB::table('groupes')
                            ->select('groupe_id', 'groupe_name')
                            ->where('groupe_id', $autovalue)
                            ->get();
                    });

                    $PopUp = JavaPopUp(site_url('chat.php?id='. $autovalueX->groupe_id .'&auto='. Crypt::encrypt(serialize($autovalueX->groupe_id))), "chat" . $autovalueX->groupe_id, 380, 480);
                    $thing .= "<li><a href=\"javascript:void(0);\" onclick=\"window.open($PopUp);\">". $autovalueX->groupe_name ."</a>";
                    
                    $numofchatters = DB::table('chatbox')
                                        ->distinct()
                                        ->select('ip')
                                        ->where('id', $autovalueX->groupe_id)
                                        ->where('date', '>=', (time() - (60 * 3)))
                                        ->count();

                    if ($numofchatters) {
                        $thing .= '&nbsp;(<span class="text-danger"><b>'. $numofchatters .'</b></span>)';
                    }

                    echo '</li>';
                }
                $thing .= '</ul>';
            }
        }

        global $block_title;
        if ($block_title == '') {
            $block_title = __d('two_chat', 'Bloc Chat');
        }

        Theme::themesidebox($block_title, $thing);
    }
}
