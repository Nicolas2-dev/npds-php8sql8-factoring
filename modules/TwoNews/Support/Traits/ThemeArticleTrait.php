<?php

namespace Modules\TwoNews\Support\Traits;

use Two\Support\Facades\View;
use Two\Support\Facades\Config;
use Modules\TwoCore\Support\Facades\Language;
use Modules\TwoCore\Support\Facades\Metalang;


trait ThemeArticleTrait 
{

    /**
     * [themearticle description]
     *
     * @param   [type]  $aid           [$aid description]
     * @param   [type]  $informant     [$informant description]
     * @param   [type]  $time          [$time description]
     * @param   [type]  $title         [$title description]
     * @param   [type]  $thetext       [$thetext description]
     * @param   [type]  $topic         [$topic description]
     * @param   [type]  $topicname     [$topicname description]
     * @param   [type]  $topicimage    [$topicimage description]
     * @param   [type]  $topictext     [$topictext description]
     * @param   [type]  $id            [$id description]
     * @param   [type]  $previous_sid  [$previous_sid description]
     * @param   [type]  $next_sid      [$next_sid description]
     * @param   [type]  $archive       [$archive description]
     *
     * @return  [type]                 [return description]
     */
    function themearticle($aid, $informant, $time, $title, $thetext, $topic, $topicname, $topicimage, $topictext, $id, $previous_sid, $next_sid, $archive)
    {
        global $counter, $boxtitle, $boxstuff;
    
        $view = false;
    
        $theme = $this->getName();
    
        if (!$view) {
            if (View::exists('Themes/'.$theme.'::Partials/News/Detail_News')) {
                $view = View::fetch('Themes/'.$theme.'::Partials/News/Detail_News');
            } elseif (View::exists('Themes/TwoNews::Partials/News/Detail_News')) {
                $view = View::fetch('Themes/TwoNews::Partials/News/Detail_News');
            } else {
                echo 'Themes/'.$theme.'::partials/News/Detail_News manquant or Themes/TwoNews::Partials/News/Detail_News (.php or .tpl) / not find !<br />';
                die();
            }
        }

        $H_var = $this->local_var($thetext);
    
        if ($H_var != '') {
            ${$H_var} = true;
            $thetext = str_replace("!var!$H_var", '', $thetext);
        }
    
        ob_start();
            include ($view);
            $Xcontent = ob_get_contents();
        ob_end_clean();
    
        if ($previous_sid) {
            $prevArt = '<a href="'. site_url('article.php?sid=' . $previous_sid . '&amp;archive=' . $archive) . '" >
                    <i class="fa fa-chevron-left fa-lg me-2" title="' . translate("Précédent") . '" data-bs-toggle="tooltip"></i>
                    <span class="d-none d-sm-inline">' . translate("Précédent") . '</span>
                </a>';
        } else {
            $prevArt = '';
        }
    
        if ($next_sid) {
            $nextArt = '<a href="'. site_url('article.php?sid=' . $next_sid . '&amp;archive=' . $archive) . '" >
                    <span class="d-none d-sm-inline">' . translate("Suivant") . '</span>
                    <i class="fa fa-chevron-right fa-lg ms-2" title="' . translate("Suivant") . '" data-bs-toggle="tooltip"></i>
                </a>';
        } else {
            $nextArt = '';
        }
    
        $printP = '<a href="'. site_url('print.php?sid=' . $id) . '" title="' . translate("Page spéciale pour impression") . '" data-bs-toggle="tooltip">
                    <i class="fa fa-2x fa-print"></i>
                </a>';

        $sendF = '<a href="'. site_url('friend.php?op=FriendSend&amp;sid=' . $id) . '" title="' . translate("Envoyer cet article à un ami") . '" data-bs-toggle="tooltip">
                    <i class="fa fa-2x fa-at"></i>
                </a>';
    
        if (!$imgtmp = $this->theme_image('topics/' . $topicimage)) {
            $imgtmp = Config::get('two_core::config.tipath') . $topicimage;
        }
    
        $timage = $imgtmp;
    
        $npds_METALANG_words = array(
            "'!N_publicateur!'i"        => $aid,
            "'!N_emetteur!'i"           => $this->userpopover($informant, 40, 2) . '<a href="'. site_url('user.php?op=userinfo&amp;uname=' . $informant) . '"><span class="">' . $informant . '</span></a>',
            "'!N_date!'i"               => formatTimestamp($time),
            //"'!N_date!'i"             => str_ftime("%A %e %B %Y @ %X", $time, getLocale()),   
            "'!N_date_y!'i"             => substr($time, 0, 4),
            //"'!N_date_m!'i"           => strftime("%B", mktime(0,0,0, substr($time,5,2),1,2000)),
            "'!N_date_m!'i"             => \PHP81_BC\strftime("%B", $time, Language::getLocale()),
            "'!N_date_d!'i"             => substr($time, 8, 2),
            "'!N_date_h!'i"             => substr($time, 11),
            "'!N_print!'i"              => $printP,
            "'!N_friend!'i"             => $sendF,
            "'!N_boxrel_title!'i"       => $boxtitle,
            "'!N_boxrel_stuff!'i"       => $boxstuff,
            "'!N_titre!'i"              => $title,
            "'!N_id!'i"                 => $id,
            "'!N_previous_article!'i"   => $prevArt,
            "'!N_next_article!'i"       => $nextArt,
            "'!N_sujet!'i"              => '<a href="'. site_url('search.php?query=&amp;topic=' . $topic) . '"><img class="img-fluid" src="' . $timage . '" alt="' . translate("Rechercher dans") . '&nbsp;' . $topictext . '" /></a>',
            "'!N_texte!'i"              => $thetext,
            "'!N_nb_lecture!'i"         => $counter
        );
    
        echo Metalang::meta_lang(
            Language::aff_langue(
                preg_replace(
                    array_keys($npds_METALANG_words),
                    array_values($npds_METALANG_words), 
                    $Xcontent)
                )
        );
    }

}
