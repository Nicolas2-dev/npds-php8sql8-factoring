<?php

declare(strict_types=1);

namespace Modules\TwoNews\Support;

use Two\Support\Facades\DB;
use Two\Support\Facades\Config;
use Modules\TwoCore\Support\Facades\Mailer;


class SubscribeNew
{

 
    /**
     * Assure l'envoi d'un mail pour un abonnement
     *
     * @param   int|string   $Xtopic    [$Xtopic description]
     * @param   int|string   $Xforum   [$Xforum description]
     * @param   string       $Xresume       [$Xresume description]
     * @param   int          $Xsauf         [$Xsauf description]
     *
     * @return  void
     */
    public static function subscribe_mail(int|string $Xtopic, int|string $Xforum, string $Xresume, string|int $Xsauf): void
    {
        $result  = DB::table('topics')
                    ->select('topictext')
                    ->where('topicid', $Xtopic)
                    ->first();

        $abo = $result->topictext;

        $result = DB::table('subscribe')
                    ->select('uid')
                    ->where('topicid', $Xtopic)
                    ->get();

        // Note : a revoir multilangue
        include_once("language/multilangue.php");

        foreach($result as $res) {  
            
            if ($res['uid'] != $Xsauf) {
                $resultX = DB::table('users')
                                ->select('email', 'user_langue')
                                ->where('uid', $res->uid)
                                ->first();

                $email       = $resultX->email;
                $user_langue = $resultX->user_langue;

                $entete = translate_ml($user_langue, "Vous recevez ce Mail car vous vous êtes abonné à : ") . translate_ml($user_langue, "Sujet") . " => " . strip_tags($abo) . "\n\n";
                $resume = translate_ml($user_langue, "Le titre de la dernière publication est") . " => $Xresume\n\n";
                $url    = translate_ml($user_langue, "L'URL pour cet article est : ") . "<a href=\"". site_url('search.php?query=&topic='. $Xtopic) ."\">". site_url('search.php?query=&topic='. $Xtopic) ."</a>\n\n";

                $subject = html_entity_decode(translate_ml($user_langue, "Abonnement"), ENT_COMPAT | ENT_HTML401, 'utf-8') ." / ". Config::get('two_core::config.sitename');
                
                $message  = $entete;
                $message .= $resume;
                $message .= $url;
                $message .= Config::get('two_core::signature.message');

                Mailer::send_email($email, $subject, $message, '', true, 'html');
            }
        }
    }
 
    /**
     * Retourne true si le membre est abonné; à un topic ou forum
     *
     * @param   int  $Xuser  [$Xuser description]
     * @param   string     $Xclef  [$Xclef description]
     *
     * @return  bool
     */
    public static function subscribe_query(int $Xuser, string $Xclef): bool
    {
        $result = DB::table('subscribe')
                    ->select('topicid')
                    ->where('uid', $Xuser)
                    ->where('topicid', $Xclef)
                    ->first();
        if ($result) {
            $Xtemp = $result->topicid;
        } else {
            $Xtemp = '';
        }

        if ($Xtemp != '') {
            return true;
        } else {
            return false;
        }
    }

}
