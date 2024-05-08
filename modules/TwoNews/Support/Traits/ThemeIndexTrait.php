<?php

namespace Modules\TwoNews\Support\Traits;

use Two\Support\Facades\View;
use Two\Support\Facades\Config;
use Modules\TwoCore\Support\Facades\Language;
use Modules\TwoCore\Support\Facades\Metalang;


trait ThemeIndexTrait 
{

    /**
     * [themeindex description]
     *
     * @param   [type]  $aid         [$aid description]
     * @param   [type]  $informant   [$informant description]
     * @param   [type]  $time        [$time description]
     * @param   [type]  $title       [$title description]
     * @param   [type]  $counter     [$counter description]
     * @param   [type]  $topic       [$topic description]
     * @param   [type]  $thetext     [$thetext description]
     * @param   [type]  $notes       [$notes description]
     * @param   [type]  $morelink    [$morelink description]
     * @param   [type]  $topicname   [$topicname description]
     * @param   [type]  $topicimage  [$topicimage description]
     * @param   [type]  $topictext   [$topictext description]
     * @param   [type]  $id          [$id description]
     *
     * @return  [type]               [return description]
     */
    function themeindex($aid, $informant, $time, $title, $counter, $topic, $thetext, $notes, $morelink, $topicname, $topicimage, $topictext, $id)
    {
        $view = false;
    
        $theme = $this->getName();
    
        if ($notes != '') {
            $notes = '<div class="note">' . __d('two_news', 'Note') . ' : ' . $notes . '</div>';
        }

        if (!$view) {
            if (View::exists('Themes/'.$theme.'::Partials/News/Index_News')) {
                $view = View::fetch('Themes/'.$theme.'::Partials/News/Index_News', compact('notes'));
            } elseif (View::exists('Themes/TwoNews::Partials/News/Index_News')) {
                $view = View::fetch('Themes/TwoNews::Partials/News/Index_News', compact('notes'));
            } else {
                echo 'Themes/'.$theme.'::partials/News/Index_News manquant or Themes/TwoNews::Partials/News/Index_News (.php or .tpl) / not find !<br />';
                die();
            }
        }

        $H_var = $this->local_var($thetext);
    
        if ($H_var != '') {
            ${$H_var} = true;
            $thetext = str_replace("!var!$H_var", "", $thetext);
        }

        ob_start();
            echo $view;
            $Xcontent = ob_get_contents();
        ob_end_clean();
    
        $lire_la_suite = '';
    
        if ($morelink[0]) {
            $lire_la_suite = $morelink[1] . ' ' . $morelink[0] . ' | ';
        }
    
        $commentaire = '';
    
        if ($morelink[2]) {
            $commentaire = $morelink[2] . ' ' . $morelink[3] . ' | ';
        } else {
            $commentaire = $morelink[3] . ' | ';
        }
    
        $categorie = '';
    
        if ($morelink[6]) {
            $categorie = ' : ' . $morelink[6];
        }
    
        $morel = $lire_la_suite . $commentaire . $morelink[4] . ' ' . $morelink[5] . $categorie;
    
        $Xsujet = '';
        if ($topicimage != '') {
            if (!$imgtmp = $this->theme_image('topics/' . $topicimage)) {
                $imgtmp = Config::get('two_core::config.tipath') . $topicimage;
            }
    
            $Xsujet = '<a href="'. site_url('search.php?query=&amp;topic=' . $topic) . '">
                    <img class="img-fluid" src="' . $this->asset_theme($imgtmp) . '" alt="' . __d('two_news', 'Rechercher dans') . ' : ' . $topicname . '" title="' . __d('two_news', 'Rechercher dans') . ' : ' . $topicname . '<hr />' . $topictext . '" data-bs-toggle="tooltip" data-bs-html="true" />
                </a>';
        } else {
            $Xsujet = '<a href="'. site_url('search.php?query=&amp;topic=' . $topic) . '">
                    <span class="badge bg-secondary h1" title="' . __d('two_news', 'Rechercher dans') . ' : ' . $topicname . '<hr />' . $topictext . '" data-bs-toggle="tooltip" data-bs-html="true">' . $topicname . '</span>
                </a>';
        }
    
        $npds_METALANG_words = array(
            "'!N_publicateur!'i"        => $aid,
            "'!N_emetteur!'i"           => $this->userpopover($informant, 40, 2) . '<a href="'. site_url('user.php?op=userinfo&amp;uname=' . $informant) . '">' . $informant . '</a>',
            "'!N_date!'i"               => formatTimestamp($time),
            //"'!N_date!'i"             => ucfirst(htmlentities(str_ftime(__d('two_news', 'datestring'), $time, getLocale()), ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML401, 'utf-8')),
            "'!N_date_y!'i"             => substr($time, 0, 4),
            //"'!N_date_m!'i"           => strftime("%B", mktime(0,0,0, substr($time,5,2),1,2000)),
            "'!N_date_m!'i"             => \PHP81_BC\strftime("%B", $time, Language::getLocale()),
            "'!N_date_d!'i"             => substr($time, 8, 2),
            "'!N_date_h!'i"             => substr($time, 11),
            "'!N_print!'i"              => $morelink[4],
            "'!N_friend!'i"             => $morelink[5],
            "'!N_nb_carac!'i"           => $morelink[0],
            "'!N_read_more!'i"          => $morelink[1],
            "'!N_nb_comment!'i"         => $morelink[2],
            "'!N_link_comment!'i"       => $morelink[3],
            "'!N_categorie!'i"          => $morelink[6],
            "'!N_titre!'i"              => $title,
            "'!N_texte!'i"              => $thetext,
            "'!N_id!'i"                 => $id,
            "'!N_sujet!'i"              => $Xsujet,
            "'!N_note!'i"               => $notes,
            "'!N_nb_lecture!'i"         => $counter,
            "'!N_suite!'i"              => $morel
        );
    
        return Metalang::meta_lang(
            Language::aff_langue(
                preg_replace(
                    array_keys($npds_METALANG_words), 
                    array_values($npds_METALANG_words), 
                    $Xcontent
                )
            )
        );
    }

}
