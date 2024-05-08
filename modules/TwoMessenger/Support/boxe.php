<?php

use Two\Support\Facades\DB;
use Two\Support\Facades\Config;
use Modules\TwoUsers\Support\Facades\User;
use Modules\TwoThemes\Support\Facades\Theme;
use Modules\TwoUsers\Support\Facades\Online;
use Modules\TwoCore\Support\Facades\Language;
use Modules\TwoAuthors\Support\Facades\Author;


if (! function_exists('instant_members_message'))
{
    /**
     * Bloc MI (Message Interne)
     * syntaxe : function#instant_members_message
     *
     * @return  void
     */
    function instant_members_message(): void
    {
        //global $user, $admin, $cookie;

        settype($boxstuff, 'string');

        $long_chain = Theme::getConfig('config.long_chain');

        if (!$long_chain) {
            $long_chain = 13;
        }

        global $block_title;
        if ($block_title == '') {
            $block_title = __d('two_messenger', 'M2M bloc');
        }

        $user = User::getUser();
        
        if ($user) {

            $boxstuff = '<ul class="">';

            $ibid = Online::online_members();

            $rank1 = '';
            for ($i = 1; $i <= $ibid[0]; $i++) {
                $timex = time() - $ibid[$i]['time'];
                
                if ($timex >= 60) {
                    $timex = '<i class="fa fa-plug text-muted" title="'. $ibid[$i]['username'] .' '. __d('two_messenger', 'n\'est pas connecté') .'" data-bs-toggle="tooltip" data-bs-placement="right"></i>&nbsp;';
                } else {
                    $timex = '<i class="fa fa-plug faa-flash animated text-primary" title="'. $ibid[$i]['username'] .' '. __d('two_messenger', 'est connecté') .'" data-bs-toggle="tooltip" data-bs-placement="right" ></i>&nbsp;';
                }

                $query = DB::table('users')->select('uid');

                if (!Config::get('two_core::config.member_invisible')) {
                    
                    $admin = Author::getAdmin();
                
                    if (!$admin) {
                        if (!$ibid[$i]['username'] == User::cookieUser(1)) {
                            $query->where('is_visible', 1);
                        }
                    }
                }

                $userid = $query->where('uname', $ibid[$i]['username'])->first();

                if ($userid) {

                    $rowQ1 = Cache::Q_Select(
                        DB::table('users_status')
                            ->select('rang')
                            ->where('uid', $userid['uid'])
                            ->get(), 3600, Crypt::encrypt('users_status(rang)')
                    );

                    $rank = $rowQ1[0]['rang'];

                    if ($rank) {
                        if ($rank1 == '') {

                            if ($myrow = Cache::Q_Select(
                                    DB::table('config')
                                    ->select('rank1', 'rank2', 'rank3', 'rank4', 'rank5')
                                    ->get(), 86400, Crypt::encrypt('config(rank)'))) 
                            {
                                
                                $rank1 = $myrow[0]['rank1'];
                                $rank2 = $myrow[0]['rank2'];
                                $rank3 = $myrow[0]['rank3'];
                                $rank4 = $myrow[0]['rank4'];
                                $rank5 = $myrow[0]['rank5'];
                            }
                        }

                        if ($ibidR = Theme::theme_image("forum/rank/" . $rank . ".gif")) {
                            $imgtmpA = $ibidR;
                        } else {
                            $imgtmpA = "assets/images/forum/rank/" . $rank . ".gif";
                        }

                        $messR = 'rank' . $rank;
                        $tmpR = '<img src="'. $imgtmpA .'" border="0" alt="'. Language::aff_langue($$messR) .'" title="'. Language::aff_langue($$messR) .'" loading="lazy" />';
                    } else {
                        $tmpR = '&nbsp;';
                    }

                    $new_messages = DB::table('priv_msgs')
                                        ->select('msg_id')
                                        ->where('to_userid', $userid['uid'])
                                        ->where('read_msg', 0)
                                        ->where('type_msg', 0)
                                        ->count();

                    if ($new_messages > 0) {
                        $PopUp = JavaPopUp(site_url('readpmsg_imm.php?op=new_msg'), "IMM", 600, 500);
                        $PopUp = '<a href="javascript:void(0);" onclick="window.open($PopUp);">';
                        
                        if ($ibid[$i]['username'] == User::cookieUser(1)) {
                            $icon = $PopUp;
                        } else {
                            $icon = "";
                        }

                        $icon .= '<i class="fa fa-envelope fa-lg faa-shake animated" title="'. __d('two_messenger', 'Nouveau') .'<span class=\'rounded-pill bg-danger ms-2\'>'. $new_messages .'</span>" data-bs-html="true" data-bs-toggle="tooltip"></i>';
                        
                        if ($ibid[$i]['username'] == User::cookieUser(1)) {
                            $icon .= '</a>';
                        }
                    } else {
                        $messages = DB::table('priv_msgs')
                                        ->select('msg_id')
                                        ->where('to_userid', $userid['uid'])
                                        ->where('type_msg', 0)
                                        ->where('dossier', '...')
                                        ->count();

                        if ($messages > 0) {
                            $PopUp = JavaPopUp(site_url('readpmsg_imm.php?op=msg'), "IMM", 600, 500);
                            $PopUp = "<a href=\"javascript:void(0);\" onclick=\"window.open($PopUp);\">";
                            
                            if ($ibid[$i]['username'] == User::cookieUser(1)) {
                                $icon = $PopUp;
                            } else {
                                $icon = '';
                            }

                            $icon .= '<i class="far fa-envelope-open fa-lg " title="'. __d('two_messenger', 'Nouveau') .' : '. $new_messages .'" data-bs-toggle="tooltip"></i></a>';
                        } else {
                            $icon = '&nbsp;';
                        }
                    }

                    $N = $ibid[$i]['username'];

                    if (strlen($N) > $long_chain) {
                        $M = substr($N, 0, $long_chain) . '.';
                    } else {
                        $M = $N;
                    }

                    $boxstuff .= '<li class="">'. $timex .'&nbsp;<a href="'. site_url('powerpack.php?op=instant_message&amp;to_userid='. $N) .'" title="'. __d('two_messenger', 'Envoyer un message interne') .'" data-bs-toggle="tooltip">'. $M .'</a><span class="float-end">'. $icon .'</span></li>';
                } 
            }

            $boxstuff .= '</ul>';

            Theme::themesidebox($block_title, $boxstuff);
        } else {
            $admin = Author::getAdmin();
            
            if ($admin) {
                $ibid = Online::online_members();

                if ($ibid[0]) {
                    for ($i = 1; $i <= $ibid[0]; $i++) {
                        $N = $ibid[$i]['username'];
                        $M = ((strlen($N) > $long_chain) 
                            ? substr($N, 0, $long_chain) .'.' 
                            : $N
                        );
                        
                        $boxstuff .= $M . '<br />';
                    }

                    Theme::themesidebox('<i>' . $block_title . '</i>', $boxstuff);
                }
            }
        }
    }
}
