<?php

use Two\Support\Facades\DB;
use Two\Support\Facades\Config;
use Two\Support\Facades\Mailer;
use Modules\TwoCore\Support\Security;


if (! function_exists('Form_instant_message'))
{
    /**
     * Ouvre la page d'envoi d'un MI (Message Interne)
     *
     * @param   string  $to_userid  [$to_userid description]
     *
     * @return  void
     */
    function Form_instant_message(string $to_userid): void
    {
        include("themes/default/header.php");

        write_short_private_message(Security::remove($to_userid));

        include("themes/default/footer.php");;
    }
}

if (! function_exists('writeDB_private_message'))
{
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
    function writeDB_private_message(string $to_userid, string $image, string $subject, int $from_userid, string $message, string $copie): void
    {
        $user = DB::table('users')
                    ->select('uid', 'user_langue')
                    ->where('uname', $to_userid)
                    ->first();

        if ($to_userid == '') {
            Forum::forumerror('0016');
        } else {
            $time = date(translate("dateinternal"), time() + ((int) Config::get('two_core::config.gmt') * 3600));

            include_once("language/multilangue.php");

            $subject = Security::remove($subject);
            $message = str_replace("\n", "<br />", $message);
            $message = addslashes(Security::remove($message));

            $r = DB::table('priv_msgs')->insert(array(
                'msg_image'      => $image,
                'subject'        => $subject,
                'from_userid'    => $from_userid,
                'to_userid'      => $user['to_useridx'],
                'msg_time'       => $time,
                'msg_text'       => $message,
            ));

            if (!$r) {
                Forum::forumerror('0020');
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
                    Forum::forumerror('0020');
                }
            }

            if (Config::get('two_core::config.subscribe')) {
                $sujet = html_entity_decode(translate_ml($user['user_langue'], "Notification message privé."), ENT_COMPAT | ENT_HTML401, 'utf-8') .'['. $from_userid .'] / '. Config::get('npds.sitename');
                $message = $time .'<br />'. translate_ml($user['user_langue'], "Bonjour") .'<br />'. translate_ml($user['user_langue'], "Vous avez un nouveau message.") .'<br /><br /><b>'. $subject .'</b><br /><br /><a href="'. site_url('viewpmsg.php') .'/">' . translate_ml($user['user_langue'], "Cliquez ici pour lire votre nouveau message.") .'</a><br />';
                
                $message .= Config::get('two_core::signature.message');
                
                Mailer::copy_to_email($user['to_useridx'], $sujet, stripslashes($message));
            }
        }
    }
}

if (! function_exists('write_short_private_message'))
{
    /**
     * Formulaire d'écriture d'un MI
     *
     * @param   string  $to_userid  [$to_userid description]
     *
     * @return  void
     */
    function write_short_private_message(string $to_userid): void
    {
        echo '
        <h2>'. translate("Message à un membre") .'</h2>
        <h3><i class="fa fa-at me-1"></i>'. $to_userid .'</h3>
        <form id="sh_priv_mess" action="'. site_url('powerpack.php') .'" method="post">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="subject" >'. translate("Sujet") .'</label>
                <div class="col-sm-12">
                    <input class="form-control" type="text" id="subject" name="subject" maxlength="100" />
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="message" >'. translate("Message") .'</label>
                <div class="col-sm-12">
                    <textarea class="form-control"  id="message" name="message" rows="10"></textarea>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-12">
                    <div class="form-check" >
                    <input class="form-check-input" type="checkbox" id="copie" name="copie" />
                    <label class="form-check-label" for="copie">'. translate("Conserver une copie") .'</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 row">
                <input type="hidden" name="to_userid" value="'. $to_userid .'" />
                <input type="hidden" name="op" value="write_instant_message" />
                <div class="col-sm-12">
                    <input class="btn btn-primary" type="submit" name="submit" value="'. translate("Valider") .'" accesskey="s" />&nbsp;
                    <button class="btn btn-secondary" type="reset">'. translate("Annuler") .'</button>
                </div>
            </div>
        </form>';
    }
}
