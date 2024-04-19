<?php
declare(strict_types=1);

use npds\support\polls;
use npds\support\download;
use npds\support\chat\chat;
use npds\support\logs\logs;
use npds\support\block\boxe;
use npds\support\cache\cache;
use npds\support\forum\forum;
use npds\support\news\gzfile;
use npds\support\theme\theme;
use npds\support\auth\authors;
use npds\support\news\zipfile;
use npds\system\config\Config;
use npds\support\metalang\metalang;
use npds\support\messenger\messenger;

// boxe.php

if (! function_exists('Site_Activ'))
{
    /**
     * [Site_Activ description]
     *
     * @return  [type]  [return description]
     */
    function Site_Activ()
    {
        return boxe::Site_Activ();
    }
}

if (! function_exists('online'))
{
    /**
     * [online description]
     *
     * @return  [type]  [return description]
     */
    function online()
    {
        return boxe::online();
    }
}

if (! function_exists('lnlbox'))
{
    /**
     * [lnlbox description]
     *
     * @return  [type]  [return description]
     */
    function lnlbox()
    {
        return boxe::lnlbox();
    }
}

if (! function_exists('searchbox'))
{
    /**
     * [searchbox description]
     *
     * @return  [type]  [return description]
     */
    function searchbox()
    {
        return boxe::searchbox();
    }
}

if (! function_exists('adminblock'))
{
    /**
     * [adminblock description]
     *
     * @return  [type]  [return description]
     */
    function adminblock()
    {
        return boxe::adminblock();
    }
}

if (! function_exists('mainblock'))
{
    /**
     * [mainblock description]
     *
     * @return  [type]  [return description]
     */
    function mainblock()
    {
        return boxe::mainblock();
    }
}

if (! function_exists('ephemblock'))
{
    /**
     * [ephemblock description]
     *
     * @return  [type]  [return description]
     */
    function ephemblock()
    {
        return boxe::ephemblock();
    }
}

if (! function_exists('loginbox'))
{
    /**
     * [loginbox description]
     *
     * @return  [type]  [return description]
     */
    function loginbox()
    {
        return boxe::loginbox();
    }
}

if (! function_exists('userblock'))
{
    /**
     * [userblock description]
     *
     * @return  [type]  [return description]
     */
    function userblock()
    {
        return boxe::userblock();
    }
}

if (! function_exists('topdownload'))
{
    /**
     * [topdownload description]
     *
     * @return  [type]  [return description]
     */
    function topdownload()
    {
        return boxe::topdownload();
    }
}

if (! function_exists('lastdownload'))
{
    /**
     * [lastdownload description]
     *
     * @return  [type]  [return description]
     */
    function lastdownload()
    {
        return boxe::lastdownload();
    }
}

if (! function_exists('ldNews'))
{
    /**
     * [oldNews description]
     *
     * @param   [type]  $storynum  [$storynum description]
     * @param   [type]  $typ_aff   [$typ_aff description]
     *
     * @return  [type]             [return description]
     */
    function oldNews($storynum, $typ_aff = '')
    {
        return boxe::oldNews($storynum, $typ_aff);
    }
}

if (! function_exists('bigstory'))
{
    /**
     * [bigstory description]
     *
     * @return  [type]  [return description]
     */
    function bigstory()
    {
        return boxe::bigstory();
    }
}

if (! function_exists('category'))
{
    /**
     * [category description]
     *
     * @return  [type]  [return description]
     */
    function category()
    {
        return boxe::category();
    }
}

if (! function_exists('headlines'))
{
    /**
     * [headlines description]
     *
     * @param   [type]  $hid    [$hid description]
     * @param   [type]  $block  [$block description]
     *
     * @return  [type]          [return description]
     */
    function headlines(string $hid = '', string|bool $block = true)
    {
        return boxe::headlines($hid, $block);
    }
}

if (! function_exists('bloc_rubrique'))
{
    /**
     * [bloc_rubrique description]
     *
     * @return  [type]  [return description]
     */
    function bloc_rubrique()
    {
        return boxe::bloc_rubrique();
    }
}

if (! function_exists('bloc_espace_groupe'))
{
    /**
     * [bloc_espace_groupe description]
     *
     * @param   [type]  $gr    [$gr description]
     * @param   [type]  $i_gr  [$i_gr description]
     *
     * @return  [type]         [return description]
     */
    function bloc_espace_groupe($gr, $i_gr)
    {
        return boxe::bloc_espace_groupe($gr, $i_gr);
    }
}

if (! function_exists('bloc_groupes'))
{
    /**
     * [bloc_groupes description]
     *
     * @param   [type]  $im  [$im description]
     *
     * @return  [type]       [return description]
     */
    function bloc_groupes($im)
    {
        return boxe::bloc_groupes($im);
    }
}

if (! function_exists('bloc_langue'))
{
    /**
     * [bloc_langue description]
     *
     * @return  [type]  [return description]
     */
    function bloc_langue()
    {
        return boxe::bloc_langue();
    }
}

if (! function_exists('blockSkin'))
{
    /**
     * [blockSkin description]
     *
     * @return  [type]  [return description]
     */
    function blockSkin()
    {
        return boxe::blockSkin();
    }
}

if (! function_exists('pollMain'))
{
    /**
     * [pollMain description]
     *
     * @param   [type]  $pollID     [$pollID description]
     * @param   [type]  $pollClose  [$pollClose description]
     *
     * @return  [type]              [return description]
     */
    function pollMain($pollID, $pollClose)
    {
        return boxe::pollMain($pollID, $pollClose);
    }
}

// cache.php

if (! function_exists('SC_infos'))
{
    /**
     * [SC_infos description]
     *
     * @return  [type]  [return description]
     */
    function SC_infos()
    {
        return cache::SC_infos();
    }
}

if (! function_exists('cacheManagerStart'))
{
    /**
     * [cacheManagerStart description]
     *
     * @return  [type]  [return description]
     */
    function cacheManagerStart()
    {
        return cache::cacheManagerStart();
    }
}

if (! function_exists('cacheManagerEnd'))
{
    /**
     * [cacheManagerEnd description]
     *
     * @return  [type]  [return description]
     */
    function cacheManagerEnd()
    {
        return cache::cacheManagerEnd();
    }
}

// download.php

if (! function_exists('topdownload_data'))
{
    /**
     * [topdownload_data description]
     *
     * @param   [type]  $form   [$form description]
     * @param   [type]  $ordre  [$ordre description]
     *
     * @return  [type]          [return description]
     */
    function topdownload_data($form, $ordre)
    {
        return download::topdownload_data($form, $ordre);
    }
}

// polls.php

if (! function_exists('PollNewest'))
{
    /**
     * [PollNewest description]
     *
     * @param   int  $id  [$id description]
     *
     * @return  [type]    [return description]
     */
    function PollNewest(?int $id = null)
    {
        return polls::PollNewest($id);
    }
}
// debug

if (! function_exists('vd'))
{
    /**
     * [vd description]
     *
     * @return  [type]  [return description]
     */
    function vd() {
        array_map(function($value)
        {
            echo '<pre>'.var_dump($value).'</pre>';
        }, func_get_args());
    }
}

if (! function_exists('dd'))
{
    /**
     * [dd description]
     *
     * @return  [type]  [return description]
     */
    function dd() {
        array_map(function($value)
        {
            echo '<pre>'.var_dump($value).'</pre>';
        }, func_get_args());
        die();
    }
}

// chat

if (! function_exists('makeChatBox'))
{
    /**
     * [makeChatBox description]
     *
     * @param   [type]  $pour  [$pour description]
     *
     * @return  [type]         [return description]
     */
    function makeChatBox($pour) {
        return  chat::makeChatBox($pour);
    }
}

// messenger

if (! function_exists('instant_members_message'))
{
    /**
     * [instant_members_message description]
     *
     * @return  [type]  [return description]
     */
    function instant_members_message() {
        return  messenger::instant_members_message();
    }
}

// forum

if (! function_exists('RecentForumPosts'))
{
    /**
     * [RecentForumPosts description]
     *
     * @param   string  $title          [$title description]
     * @param   int     $maxforums      [$maxforums description]
     * @param   int     $maxtopics      [$maxtopics description]
     * @param   bool    $displayposter  [$displayposter description]
     * @param   false                   [ description]
     * @param   int     $topicmaxchars  [$topicmaxchars description]
     * @param   bool    $hr             [$hr description]
     * @param   false                   [ description]
     * @param   string  $decoration     [$decoration description]
     *
     * @return  [type]                  [return description]
     */
    function RecentForumPosts(string $title, int $maxforums, int $maxtopics, bool $displayposter = false, int $topicmaxchars = 15, bool $hr = false, string $decoration = '') {
        return forum::RecentForumPosts($title, $maxforums, $maxtopics, $displayposter, $topicmaxchars, $hr, $decoration);
    }
}

if (! function_exists('RecentForumPosts_fab'))
{
    /**
     * [RecentForumPosts_fab description]
     *
     * @param   string  $title          [$title description]
     * @param   int     $maxforums      [$maxforums description]
     * @param   int     $maxtopics      [$maxtopics description]
     * @param   bool    $displayposter  [$displayposter description]
     * @param   int     $topicmaxchars  [$topicmaxchars description]
     * @param   bool    $hr             [$hr description]
     * @param   string  $decoration     [$decoration description]
     *
     * @return  [type]                  [return description]
     */
    function RecentForumPosts_fab(string $title, int $maxforums, int $maxtopics, bool $displayposter,int $topicmaxchars, bool $hr, string $decoration) {
        return forum::RecentForumPosts_fab($title, $maxforums, $maxtopics, $displayposter, $topicmaxchars, $hr, $decoration);
    }
}
// // .php

/**
 * 
 */
if (! function_exists('manuel'))
{
    /**
     * [manuel description]
     *
     * @param   [type]  $manuel  [$manuel description]
     *
     * @return  [type]
     */
    function manuel($manuel)  
    {
        return 'modules/manuels/view/'. Config::get('npds.language') .'/'. $manuel .'.html';
    }
}

if (! function_exists('access_denied'))
{
    /**
     * [access_denied description]
     *
     * @return  [type]  [return description]
     */
    function access_denied()
    {
        include("admin/die.php");
    }
}

// note A revoir !!!
if (! function_exists('MM_img'))
{
    /**
     * Cette fonction est utilisée pour intégrer des smilies et comme service pour theme_img()
     *
     * @param   string  $ibid  [$ibid description]
     *
     * @return  string
     */
    function MM_img(string $ibid): string 
    {
        $ibid = metalang::arg_filter($ibid);
        $ibidX = theme::theme_image($ibid);
        
        if ($ibidX) {
            $ret = "<img src=\"$ibidX\" border=\"0\" alt=\"\" />";
        } else {
            if (@file_exists("assets/images/$ibid")) {
                $ret = "<img src=\"assets/images/$ibid\" border=\"0\" alt=\"\" />";
            } else {
                $ret = false;
            }
        }

        return $ret;
    }
}

if (! function_exists('site_url'))
{
    /**
     * 
     *
     * @param   string  $url  [$url description]
     *
     * @return  string
     */
    function site_url(string $url): string
    {
        $url = ltrim($url, '/');

        return Config::get('npds.nuke_url') .'/'. $url;
    }
}

if (! function_exists('module_url'))
{
    /**
     * [module_url description]
     *
     * @param   string  $ModPath   [$ModPath description]
     * @param   string  $ModStart  [$ModStart description]
     * @param   string  $url       [$url description]
     *
     * @return  string
     */
    function module_url(string $ModPath, string $ModStart, string $url): string
    {
        $url = ltrim($url, '/');

        return Config::get('npds.nuke_url') .'/modules.php?ModPath='. $ModPath .'&ModStart='. $ModStart .'&'. $url;
    }
}

if (! function_exists('Admin_alert'))
{
    function Admin_alert($motif)
    {
        $admin = authors::getAdmin();

        setcookie('admin', '', 0);
        unset($admin);

        logs::Ecr_Log('security', 'auth.inc.php/Admin_alert : ' . $motif, '');
        
        if (file_exists("storage/meta/meta.php")) {
            $Titlesitename = 'NPDS';
            include("storage/meta/meta.php");
        }

        echo '
            </head>
            <body>
                <br /><br /><br />
                <p style="font-size: 24px; font-family: Tahoma, Arial; color: red; text-align:center;"><strong>.: ' . translate("Votre adresse Ip est enregistrée") . ' :.</strong></p>
            </body>
        </html>';
        die();
    }
}

if (! function_exists('get_os'))
{
    /**
     * retourne true si l'OS de la station cliente est Windows sinon false
     *
     * @return  [type]  [return description]
     */
    function get_os()
    {
        $client = getenv("HTTP_USER_AGENT");
        
        if (preg_match('#(\(|; )(Win)#', $client, $regs)) {
            if ($regs[2] == "Win") {
                $MSos = true;
            } else {
                $MSos = false;
            }
        } else {
            $MSos = false;
        }

        return $MSos;
    }
}

if (! function_exists('send_file'))
{
    /**
     * compresse et télécharge un fichier
     *
     * @param   [type]  $line       le flux
     * @param   [type]  $filename   
     * @param   [type]  $extension  le fichier
     * @param   [type]  $MSos       (voir fonction get_os)
     *
     * @return  [type]              [return description]
     */    
    function send_file($line, $filename, $extension, $MSos)
    {
        $compressed = false;
        if (file_exists("system/news/archive.php")) {
            if (function_exists("gzcompress")) {
                $compressed = true;
            }
        }

        if ($compressed) {
            if ($MSos) {
                $arc = new zipfile();
                $filez = $filename . ".zip";
            } else {
                $arc = new gzfile();
                $filez = $filename . ".gz";
            }

            $arc->addfile($line, $filename . "." . $extension, "");
            $arc->arc_getdata();
            $arc->filedownload($filez);
        } else {
            if ($MSos) {
                header("Content-Type: application/octetstream");
            } else {
                header("Content-Type: application/octet-stream");
            }

            header("Content-Disposition: attachment; filename=\"$filename." . "$extension\"");
            header("Pragma: no-cache");
            header("Expires: 0");

            echo $line;
        }
    }
}

if (! function_exists('send_tofile'))
{
    /**
     * compresse et enregistre un fichier
     *
     * @param   [type]  $line        le flux
     * @param   [type]  $repertoire  
     * @param   [type]  $filename    
     * @param   [type]  $extension   
     * @param   [type]  $MSos        (voir fonction get_os)
     *
     * @return  [type]               [return description]
     */
    function send_tofile($line, $repertoire, $filename, $extension, $MSos)
    {
        $compressed = false;

        if (file_exists("system/news/archive.php")) {
            if (function_exists("gzcompress")) {
                $compressed = true;
            }
        }

        if ($compressed) {
            if ($MSos) {
                $arc = new zipfile();
                $filez = $filename . ".zip";
            } else {
                $arc = new gzfile();
                $filez = $filename . ".gz";
            }

            $arc->addfile($line, $filename . "." . $extension, "");
            $arc->arc_getdata();

            if (file_exists($repertoire . "/" . $filez)) {
                unlink($repertoire . "/" . $filez);
            }

            $arc->filewrite($repertoire . "/" . $filez, $perms = null);
        } else {
            if ($MSos) {
                header("Content-Type: application/octetstream");
            } else {
                header("Content-Type: application/octet-stream");
            }

            header("Content-Disposition: attachment; filename=\"$filename." . "$extension\"");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            echo $line;
        }
    }
}

// if (! function_exists(''))
// {
// function  {
//    return  ::;
// }
// }
