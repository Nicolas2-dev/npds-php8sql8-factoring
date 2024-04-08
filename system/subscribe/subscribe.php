<?php

declare(strict_types=1);

namespace npds\system\subscribe;

use npds\system\mail\mailler;
use npds\system\config\Config;
use npds\system\support\facades\DB;

class subscribe
{

 
    /**
     * Assure l'envoi d'un mail pour un abonnement
     *
     * @param   string  $Xtype         [$Xtype description]
     * @param   int|string  $Xtopic    [$Xtopic description]
     * @param   int|string   $Xforum   [$Xforum description]
     * @param   string  $Xresume       [$Xresume description]
     * @param   string  $Xsauf         [$Xsauf description]
     *
     * @return  void
     */
    public static function subscribe_mail(string $Xtype, int|string $Xtopic, int|string $Xforum, string $Xresume, string $Xsauf): void
    {
        // $Xtype : topic, forum ... / $Xtopic clause WHERE / $Xforum id of forum / $Xresume Text passed / $Xsauf not this userid

        $nuke_url = Config::get('npds.nuke_url');

        if ($Xtype == 'topic') {
            $result  = DB::table('topics')
                        ->select('topictext')
                        ->where('topicid', $Xtopic)
                        ->first();

            $abo = $result['topictext'];

            $result = DB::table('subscribe')
                        ->select('uid')
                        ->where('topicid', $Xtopic)
                        ->get();
        }

        if ($Xtype == 'forum') {
            $result = DB::table('forums')
                        ->select('forum_name', 'arbre')
                        ->where('forum_id', $Xforum)
                        ->first();

            $abo = $result['forum_name']; 
            $arbre = $result['arbre'];

            if ($arbre) {
                $hrefX = 'viewtopicH.php';
            } else {
                $hrefX = 'viewtopic.php';
            }

            $resultZ = DB::table('forumtopics')
                        ->select('topic_title')
                        ->where('topic_id', $Xtopic)
                        ->first();
            
            $title_topic = $resultZ['topic_title'];

            $result = DB::table('subscribe')
                        ->select('uid')
                        ->where('forumid', $Xforum)
                        ->get();
        }

        include_once("language/multilangue.php");

        foreach($result as $res) {  
            
        if ($res['uid'] != $Xsauf) {
                $resultX = DB::table('users')
                                ->select('email', 'user_langue')
                                ->where('uid', $res['uid'])
                                ->first();

                $email = $resultX['email'];
                $user_langue = $resultX['user_langue'];

                if ($Xtype == 'topic') {
                    $entete = translate_ml($user_langue, "Vous recevez ce Mail car vous vous êtes abonné à : ") . translate_ml($user_langue, "Sujet") . " => " . strip_tags($abo) . "\n\n";
                    $resume = translate_ml($user_langue, "Le titre de la dernière publication est") . " => $Xresume\n\n";
                    $url = translate_ml($user_langue, "L'URL pour cet article est : ") . "<a href=\"$nuke_url/search.php?query=&topic=$Xtopic\">$nuke_url/search.php?query=&topic=$Xtopic</a>\n\n";
                }

                if ($Xtype == 'forum') {
                    $entete = translate_ml($user_langue, "Vous recevez ce Mail car vous vous êtes abonné à : ") . translate_ml($user_langue, "Forum") . " => " . strip_tags($abo) . "\n\n";
                    $url = translate_ml($user_langue, "L'URL pour cet article est : ") . "<a href=\"$nuke_url/$hrefX?topic=$Xtopic&forum=$Xforum&start=9999#lastpost\">$nuke_url/$hrefX?topic=$Xtopic&forum=$Xforum&start=9999</a>\n\n";
                    $resume = translate_ml($user_langue, "Le titre de la dernière publication est") . " => ";
                    
                    if ($Xresume != '') {
                        $resume .= $Xresume . "\n\n";
                    } else {
                        $resume .= $title_topic . "\n\n";
                    }
                }

                $subject = html_entity_decode(translate_ml($user_langue, "Abonnement"), ENT_COMPAT | ENT_HTML401, 'utf-8') . " / " . Config::get('npds.sitename');
                $message = $entete;
                $message .= $resume;
                $message .= $url;
                $message .= Config::get('signature.message');

                mailler::send_email($email, $subject, $message, '', true, 'html');
            }
        }
    }
 
    /**
     * Retourne true si le membre est abonné; à un topic ou forum
     *
     * @param   string  $Xuser  [$Xuser description]
     * @param   string  $Xtype  [$Xtype description]
     * @param   int     $Xclef  [$Xclef description]
     *
     * @return  bool
     */
    public static function subscribe_query(string $Xuser, string $Xtype, int $Xclef): bool
    {
        if ($Xtype == 'topic') {
            $result = DB::table('subscribe')
                            ->select('topicid')
                            ->where('uid', $Xuser)
                            ->where('topicid', $Xclef)
                            ->first();
            if ($result) {
                $Xtemp = $result['topicid'];
            } else {
                $Xtemp = '';
            }
        }

        if ($Xtype == 'forum') {
            $result = DB::table('subscribe')
                            ->select('forumid')
                            ->where('uid', $Xuser)
                            ->where('forumid', $Xclef)
                            ->first();

            if ($result) {
                $Xtemp = $result['forumid'];
            } else {
                $Xtemp = '';
            }
        }

        if ($Xtemp != '') {
            return true;
        } else {
            return false;
        }
    }
}
