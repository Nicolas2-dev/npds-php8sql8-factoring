<?php

declare(strict_types=1);

namespace npds\system\messenger;

use npds\system\assets\java;
use npds\system\cache\cache;
use npds\system\forum\forum;
use npds\system\theme\theme;
use npds\system\mail\mailler;
use npds\system\config\Config;
use npds\system\security\hack;
use npds\system\support\online;
use npds\system\language\language;
use npds\system\support\facades\DB;

class messenger
{

    /**
     * Ouvre la page d'envoi d'un MI (Message Interne)
     *
     * @param   string  $to_userid  [$to_userid description]
     *
     * @return  void
     */
    public static function Form_instant_message(string $to_userid): void
    {
        include("themes/default/header.php");
        static::write_short_private_message(hack::removeHack($to_userid));
        include("themes/default/footer.php");;
    }

    /**
     * Insère un MI dans la base et le cas échéant envoi un mail
     *
     * @param   string  $to_userid    [$to_userid description]
     * @param   string  $image        [$image description]
     * @param   string  $subject      [$subject description]
     * @param   int     $from_userid  [$from_userid description]
     * @param   string  $message      [$message description]
     * @param   string  $copie        [$copie description]
     *
     * @return  void
     */
    public static function writeDB_private_message(string $to_userid, string $image, string $subject, int $from_userid, string $message, string $copie): void
    {
        $user = DB::table('users')
                    ->select('uid', 'user_langue')
                    ->where('uname', $to_userid)
                    ->first();

        if ($to_useridx == '') {
            forum::forumerror('0016');
        } else {
            $time = date(translate("dateinternal"), time() + ((int) Config::get('npds.gmt') * 3600));

            include_once("language/multilangue.php");

            $subject = hack::removeHack($subject);
            $message = str_replace("\n", "<br />", $message);
            $message = addslashes(hack::removeHack($message));

            $r = DB::table('priv_msgs')->insert(array(
                'msg_image'      => $image,
                'subject'        => $subject,
                'from_userid'    => $from_userid,
                'to_userid'      => $user['to_useridx'],
                'msg_time'       => $time,
                'msg_text'       => $message,
            ));

            if (!$r) {
                forum::forumerror('0020');
            }

            if ($copie) {
                $r = DB::table('priv_msgs')->insert(array(
                    'msg_image'      => $image,
                    'subject'        => $subject,
                    'from_userid'    => $from_userid,
                    'to_userid'      => $user['to_useridx'],
                    'msg_time'       => $time,
                    'msg_text'       => $message,
                    'type_msg'       => 1,
                    'read_msg'       => 1,
                ));

                if (!$r) {
                    forum::forumerror('0020');
                }
            }

            $nuke_url = Config::get('npds.nuke_url');
            
            if (Config::get('npds.subscribe')) {
                $sujet = html_entity_decode(translate_ml($user['user_langue'], "Notification message privé."), ENT_COMPAT | ENT_HTML401, 'utf-8') . '[' . $from_userid . '] / ' . Config::get('npds.sitename');
                $message = $time . '<br />' . translate_ml($user['user_langue'], "Bonjour") . '<br />' . translate_ml($user['user_langue'], "Vous avez un nouveau message.") . '<br /><br /><b>' . $subject . '</b><br /><br /><a href="' . $nuke_url . '/viewpmsg.php">' . translate_ml($user['user_langue'], "Cliquez ici pour lire votre nouveau message.") . '</a><br />';
                $message .= Config::get('signature.message');
                
                mailler::copy_to_email($user['to_useridx'], $sujet, stripslashes($message));
            }
        }
    }

    /**
     * Formulaire d'écriture d'un MI
     *
     * @param   string  $to_userid  [$to_userid description]
     *
     * @return  void
     */
    public static function write_short_private_message(string $to_userid): void
    {
        echo '
        <h2>' . translate("Message à un membre") . '</h2>
        <h3><i class="fa fa-at me-1"></i>' . $to_userid . '</h3>
        <form id="sh_priv_mess" action="powerpack.php" method="post">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="subject" >' . translate("Sujet") . '</label>
                <div class="col-sm-12">
                    <input class="form-control" type="text" id="subject" name="subject" maxlength="100" />
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="message" >' . translate("Message") . '</label>
                <div class="col-sm-12">
                    <textarea class="form-control"  id="message" name="message" rows="10"></textarea>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-12">
                    <div class="form-check" >
                    <input class="form-check-input" type="checkbox" id="copie" name="copie" />
                    <label class="form-check-label" for="copie">' . translate("Conserver une copie") . '</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 row">
                <input type="hidden" name="to_userid" value="' . $to_userid . '" />
                <input type="hidden" name="op" value="write_instant_message" />
                <div class="col-sm-12">
                    <input class="btn btn-primary" type="submit" name="submit" value="' . translate("Valider") . '" accesskey="s" />&nbsp;
                    <button class="btn btn-secondary" type="reset">' . translate("Annuler") . '</button>
                </div>
            </div>
        </form>';
    }

    /**
     * Bloc MI (Message Interne)
     * syntaxe : function#instant_members_message
     *
     * @return  void
     */
    public static function instant_members_message(): void
    {
        global $user, $admin, $cookie;

        settype($boxstuff, 'string');

        if (!Config::get('npds.theme.long_chain')) {
            Config::set('npds.theme.long_chain', 13);
        }

        global $block_title;
        if ($block_title == '') {
            $block_title = translate("M2M bloc");
        }

        if ($user) {

            $boxstuff = '<ul class="">';

            $ibid = online::online_members();

            $rank1 = '';
            for ($i = 1; $i <= $ibid[0]; $i++) {
                $timex = time() - $ibid[$i]['time'];
                
                if ($timex >= 60) {
                    $timex = '<i class="fa fa-plug text-muted" title="' . $ibid[$i]['username'] . ' ' . translate("n'est pas connecté") . '" data-bs-toggle="tooltip" data-bs-placement="right"></i>&nbsp;';
                } else {
                    $timex = '<i class="fa fa-plug faa-flash animated text-primary" title="' . $ibid[$i]['username'] . ' ' . translate("est connecté") . '" data-bs-toggle="tooltip" data-bs-placement="right" ></i>&nbsp;';
                }

                $query = DB::table('users')->select('uid');

                if (!Config::get('npds.member_invisible')) {
                    if (!$admin) {
                        if (!$ibid[$i]['username'] == $cookie[1]) {
                            $query->where('is_visible', 1);
                        }
                    }
                }

                $userid = $query->where('uname', $ibid[$i]['username'])->first();

                if ($userid) {

                    $rowQ1 = cache::Q_Select3(DB::table('users_status')->select('rang')->where('uid', $userid['uid'])->get(), 3600, 'users_status(rang)');

                    $rank = $rowQ1[0]['rang'];
                    $tmpR = '';

                    if ($rank) {
                        if ($rank1 == '') {

                            if ($myrow = cache::Q_Select3(DB::table('config')->select('rank1', 'rank2', 'rank3', 'rank4', 'rank5')->get(), 86400, 'config(rank)')) {
                                
                                $rank1 = $myrow[0]['rank1'];
                                $rank2 = $myrow[0]['rank2'];
                                $rank3 = $myrow[0]['rank3'];
                                $rank4 = $myrow[0]['rank4'];
                                $rank5 = $myrow[0]['rank5'];
                            }
                        }

                        if ($ibidR = theme::theme_image("forum/rank/" . $rank . ".gif")) {
                            $imgtmpA = $ibidR;
                        } else {
                            $imgtmpA = "assets/images/forum/rank/" . $rank . ".gif";
                        }

                        $messR = 'rank' . $rank;
                        $tmpR = "<img src=\"" . $imgtmpA . "\" border=\"0\" alt=\"" . language::aff_langue($$messR) . "\" title=\"" . language::aff_langue($$messR) . "\" loading=\"lazy\" />";
                    } else {
                        $tmpR = '&nbsp;';
                    }

                    $new_messages = DB::table('priv_msgs')->select('msg_id')->where('to_userid', $userid['uid'])->where('read_msg', 0)->where('type_msg', 0)->count();

                    if ($new_messages > 0) {
                        $PopUp = java::JavaPopUp("readpmsg_imm.php?op=new_msg", "IMM", 600, 500);
                        $PopUp = "<a href=\"javascript:void(0);\" onclick=\"window.open($PopUp);\">";
                        
                        if ($ibid[$i]['username'] == $cookie[1]) {
                            $icon = $PopUp;
                        } else {
                            $icon = "";
                        }

                        $icon .= '<i class="fa fa-envelope fa-lg faa-shake animated" title="' . translate("Nouveau") . '<span class=\'rounded-pill bg-danger ms-2\'>' . $new_messages . '</span>" data-bs-html="true" data-bs-toggle="tooltip"></i>';
                        
                        if ($ibid[$i]['username'] == $cookie[1]) {
                            $icon .= '</a>';
                        }
                    } else {
                        $messages = DB::table('priv_msgs')->select('msg_id')->where('to_userid', $userid['uid'])->where('type_msg', 0)->where('dossier', '...')->count();


                        if ($messages > 0) {
                            $PopUp = java::JavaPopUp("readpmsg_imm.php?op=msg", "IMM", 600, 500);
                            $PopUp = '<a href="javascript:void(0);" onclick="window.open(' . $PopUp . ');">';
                            
                            if ($ibid[$i]['username'] == $cookie[1]) {
                                $icon = $PopUp;
                            } else {
                                $icon = '';
                            }

                            $icon .= '<i class="far fa-envelope-open fa-lg " title="' . translate("Nouveau") . ' : ' . $new_messages . '" data-bs-toggle="tooltip"></i></a>';
                        } else {
                            $icon = '&nbsp;';
                        }
                    }

                    $N = $ibid[$i]['username'];

                    if (strlen($N) > Config::get('npds.theme.long_chain')) {
                        $M = substr($N, 0, Config::get('npds.theme.long_chain')) . '.';
                    } else {
                        $M = $N;
                    }

                    $boxstuff .= '<li class="">' . $timex . '&nbsp;<a href="powerpack.php?op=instant_message&amp;to_userid=' . $N . '" title="' . translate("Envoyer un message interne") . '" data-bs-toggle="tooltip" >' . $M . '</a><span class="float-end">' . $icon . '</span></li>';
                } 
            }

            $boxstuff .= '
            </ul>';

            themesidebox($block_title, $boxstuff);
        } else {
            if ($admin) {
                $ibid = online::online_members();

                if ($ibid[0]) {
                    for ($i = 1; $i <= $ibid[0]; $i++) {
                        $N = $ibid[$i]['username'];
                        $M = ((strlen($N) > Config::get('npds.theme.long_chain')) 
                            ? substr($N, 0, Config::get('npds.theme.long_chain')) . '.' 
                            : $N
                        );
                        
                        $boxstuff .= $M . '<br />';
                    }

                    themesidebox('<i>' . $block_title . '</i>', $boxstuff);
                }
            }
        }
    }

}
