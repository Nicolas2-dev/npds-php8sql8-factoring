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

class messenger
{

    #autodoc Form_instant_message($to_userid) : Ouvre la page d'envoi d'un MI (Message Interne)
    public static function Form_instant_message($to_userid)
    {
        include("themes/default/header.php");
        static::write_short_private_message(hack::removeHack($to_userid));
        include("themes/default/footer.php");;
    }

    #autodoc writeDB_private_message($to_userid,$image,$subject,$from_userid,$message, $copie) : Insère un MI dans la base et le cas échéant envoi un mail
    public static function writeDB_private_message($to_userid, $image, $subject, $from_userid, $message, $copie)
    {
        global $NPDS_Prefix;

        $res = sql_query("SELECT uid, user_langue FROM " . $NPDS_Prefix . "users WHERE uname='$to_userid'");
        list($to_useridx, $user_languex) = sql_fetch_row($res);

        if ($to_useridx == '') {
            forum::forumerror('0016');
        } else {
            global $gmt;
            $time = date(translate("dateinternal"), time() + ((int)$gmt * 3600));

            include_once("language/multilangue.php");

            $subject = hack::removeHack($subject);
            $message = str_replace("\n", "<br />", $message);
            $message = addslashes(hack::removeHack($message));

            $sql = "INSERT INTO " . $NPDS_Prefix . "priv_msgs (msg_image, subject, from_userid, to_userid, msg_time, msg_text) ";
            $sql .= "VALUES ('$image', '$subject', '$from_userid', '$to_useridx', '$time', '$message')";

            if (!$result = sql_query($sql)) {
                forum::forumerror('0020');
            }

            if ($copie) {
                $sql = "INSERT INTO " . $NPDS_Prefix . "priv_msgs (msg_image, subject, from_userid, to_userid, msg_time, msg_text, type_msg, read_msg) ";
                $sql .= "VALUES ('$image', '$subject', '$from_userid', '$to_useridx', '$time', '$message', '1', '1')";
                
                if (!$result = sql_query($sql)) {
                    forum::forumerror('0020');
                }
            }

            global $subscribe; 

            $nuke_url = Config::get('app.nuke_url');
            
            if ($subscribe) {
                $sujet = html_entity_decode(translate_ml($user_languex, "Notification message privé."), ENT_COMPAT | ENT_HTML401, 'utf-8') . '[' . $from_userid . '] / ' . Config::get('app.sitename');
                $message = $time . '<br />' . translate_ml($user_languex, "Bonjour") . '<br />' . translate_ml($user_languex, "Vous avez un nouveau message.") . '<br /><br /><b>' . $subject . '</b><br /><br /><a href="' . $nuke_url . '/viewpmsg.php">' . translate_ml($user_languex, "Cliquez ici pour lire votre nouveau message.") . '</a><br />';
                $message .= Config::get('signature.message');
                
                mailler::copy_to_email($to_useridx, $sujet, stripslashes($message));
            }
        }
    }

    #autodoc write_short_private_message($to_userid) : Formulaire d'écriture d'un MI
    public static function write_short_private_message($to_userid)
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

    #autodoc <span class="text-success">BLOCS NPDS</span>:
    #autodoc instant_members_message() : Bloc MI (Message Interne) <br />=> syntaxe : function#instant_members_message
    public static function instant_members_message()
    {
        global $user, $admin, $NPDS_Prefix;

        settype($boxstuff, 'string');

        if (!Config::get('app.theme.long_chain')) {
            Config::set('app.theme.long_chain', 13);
        }

        global $block_title;
        if ($block_title == '') {
            $block_title = translate("M2M bloc");
        }

        if ($user) {
            global $cookie;

            $boxstuff = '
            <ul class="">';

            $ibid = online::online_members();

            $rank1 = '';
            for ($i = 1; $i <= $ibid[0]; $i++) {
                $timex = time() - $ibid[$i]['time'];
                
                if ($timex >= 60) {
                    $timex = '<i class="fa fa-plug text-muted" title="' . $ibid[$i]['username'] . ' ' . translate("n'est pas connecté") . '" data-bs-toggle="tooltip" data-bs-placement="right"></i>&nbsp;';
                } else {
                    $timex = '<i class="fa fa-plug faa-flash animated text-primary" title="' . $ibid[$i]['username'] . ' ' . translate("est connecté") . '" data-bs-toggle="tooltip" data-bs-placement="right" ></i>&nbsp;';
                }

                global $member_invisible;
                if ($member_invisible) {
                    if ($admin) {
                        $and = '';
                    } else {
                        if ($ibid[$i]['username'] == $cookie[1]) {
                            $and = '';
                        } else {
                            $and = "AND is_visible=1";}
                    }
                } else {
                    $and = '';
                }

                $result = sql_query("SELECT uid FROM " . $NPDS_Prefix . "users WHERE uname='" . $ibid[$i]['username'] . "' $and");
                list($userid) = sql_fetch_row($result);

                if ($userid) {
                    $rowQ1 = cache::Q_Select("SELECT rang FROM " . $NPDS_Prefix . "users_status WHERE uid='$userid'", 3600);
                    $myrow = $rowQ1[0];
                    $rank = $myrow['rang'];
                    $tmpR = '';

                    if ($rank) {
                        if ($rank1 == '') {
                            if ($rowQ2 = cache::Q_Select("SELECT rank1, rank2, rank3, rank4, rank5 FROM " . $NPDS_Prefix . "config", 86400)) {
                                $myrow = $rowQ2[0];
                                $rank1 = $myrow['rank1'];
                                $rank2 = $myrow['rank2'];
                                $rank3 = $myrow['rank3'];
                                $rank4 = $myrow['rank4'];
                                $rank5 = $myrow['rank5'];
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

                    $new_messages = sql_num_rows(sql_query("SELECT msg_id FROM " . $NPDS_Prefix . "priv_msgs WHERE to_userid = '$userid' AND read_msg='0' AND type_msg='0'"));
                    
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
                        $messages = sql_num_rows(sql_query("SELECT msg_id FROM " . $NPDS_Prefix . "priv_msgs WHERE to_userid = '$userid' AND type_msg='0' AND dossier='...'"));
                        
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

                    if (strlen($N) > Config::get('app.theme.long_chain')) {
                        $M = substr($N, 0, Config::get('app.theme.long_chain')) . '.';
                    } else {
                        $M = $N;
                    }

                    $boxstuff .= '
                <li class="">' . $timex . '&nbsp;<a href="powerpack.php?op=instant_message&amp;to_userid=' . $N . '" title="' . translate("Envoyer un message interne") . '" data-bs-toggle="tooltip" >' . $M . '</a><span class="float-end">' . $icon . '</span></li>';
                } //suppression temporaire ... rank  '.$tmpR.'
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
                        $M = ((strlen($N) > Config::get('app.theme.long_chain')) 
                            ? substr($N, 0, Config::get('app.theme.long_chain')) . '.' 
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
