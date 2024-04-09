<?php

declare(strict_types=1);

namespace npds\system\language;

use npds\system\news\news;
use npds\system\block\boxe;
use npds\system\auth\groupe;
use npds\system\block\block;
use npds\system\cache\cache;
use npds\system\forum\forum;
use npds\system\support\str;
use npds\system\theme\theme;
use npds\system\mail\mailler;
use npds\system\utility\spam;
use npds\system\config\Config;
use npds\system\support\edito;
use npds\system\support\online;
use npds\system\language\language;
use npds\system\language\metalang;
use npds\system\support\facades\DB;
use npds\system\utility\crypt;

class metafunction
{

    /**
     * The metafunction instance
     *
     * @var metafunction
     */
    private static ?metafunction $instance = null;


    /**
     * [__construct description]
     * @param Container $container [description]
     */
    public function __construct()
    {        
    }

    /**
     * 
     *
     * @return  metafunction
     */
    public static function instance(): metafunction
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;  
    }

    /**
     * Get singleton instance
     *
     * @return metafunction
     */
    public static function getInstance(): metafunction
    {
        return static::$instance;
    }

    /**
     * 
     *
     * @return  string
     */
    public function MM_sc_infos(): string 
    {
        return cache::sc_infos();
    }
    
    /**
     * 
     *
     * @param   string  $opex      [$opex description]
     * @param   int     $premier   [$premier description]
     * @param   int     $deuxieme  [$deuxieme description]
     *
     * @return  string
     */
    public function MM_Scalcul(string $opex, int $premier, int $deuxieme): string 
    {
        if ($opex == "+") {
            $tmp = $premier + $deuxieme;
        }
        
        if ($opex == "-") {
            $tmp = $premier - $deuxieme;
        }
    
        if ($opex == "*") {
            $tmp = $premier * $deuxieme;
        }
        
        if ($opex == "/") {
            if ($deuxieme == 0) {
                $tmp = "Division by zero !";
            } else {
                $tmp = $premier / $deuxieme;
            }
        }
    
        return $tmp;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_anti_spam(string $arg): string 
    {
        return ("<a href=\"mailto:" . spam::anti_spam($arg, 1) . "\" target=\"_blank\">" . spam::anti_spam($arg, 0) . "</a>");
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_msg_foot(): string
    {
        if ($foot1 = Config::get('npds.foot1')) {
            $MT_foot = stripslashes($foot1) . "<br />";
        }
    
        if ($foot2 = Config::get('npds.foot2')) {
            $MT_foot .= stripslashes($foot2) . "<br />";
        }
    
        if ($foot3 = Config::get('npds.foot3')) {
            $MT_foot .= stripslashes($foot3) . "<br />";
        }
    
        if ($foot4 = Config::get('npds.foot4')) {
            $MT_foot .= stripslashes($foot4);
        }
    
        return language::aff_langue($MT_foot);
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_date(): string 
    {
        $locale = language::getLocale();
    
        return ucfirst(htmlentities(\PHP81_BC\strftime(translate("daydate"), time(), $locale), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'utf-8'));
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_banner(): string 
    {
        global $hlpfile;
    
        if ((Config::get('npds.banners')) and (!$hlpfile)) {
            ob_start();
                include("banners.php");
                $MT_banner = ob_get_contents();
            ob_end_clean();
        } else {
            $MT_banner = "";
        }
    
        return $MT_banner;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_search_topics(): string 
    {
        $MT_search_topics = "<form action=\"search.php\" method=\"post\"><label class=\"col-form-label\">" . translate("Sujets") . " </label>";
        $MT_search_topics .= "<select class=\"form-select\" name=\"topic\"onChange='submit()'>";
        $MT_search_topics .= "<option value=\"\">" . translate("Tous les sujets") . "</option>\n";
    
        $rowQ = cache::Q_select3(
            DB::table('topics')
                ->select('topicid', 'topictext')
                ->orderBy('topictext')
                ->get(), 
            86400, 
            crypt::encrypt('topics(topiccid)')
        );
        
        foreach ($rowQ as $myrow) {
            $MT_search_topics .= "<option value=\"" . $myrow['topicid'] . "\">" . language::aff_langue($myrow['topictext']) . "</option>\n";
        }
    
        $MT_search_topics .= "</select></form>";
    
        return $MT_search_topics;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_search(): string
    {
        return "<form action=\"search.php\" method=\"post\"><label>" . translate("Recherche") . "</label>
        <input class=\"form-control\" type=\"text\" name=\"query\" size=\"10\"></form>";
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_member(): string 
    {
        global $cookie;
    
        $username = $cookie[1];
    
        if ($username == "") {
            $username = Config::get('npds.anonymous');
        }
    
        ob_start();
            mailler::Mess_Check_Mail($username);
            $MT_member = ob_get_contents();
        ob_end_clean();
    
        return $MT_member;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_nb_online(): string
    {
        list($MT_nb_online, $MT_whoim) = online::Who_Online();
    
        return $MT_nb_online;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_whoim(): string
    {
        list($MT_nb_online, $MT_whoim) = online::Who_Online();
    
        return $MT_whoim;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_membre_nom(): string 
    {
        global $cookie;
    
        if (isset($cookie[1])) {
            $rowQ = cache::Q_select3(
                DB::table('users')
                    ->select('name')
                    ->where('uname', metalang::arg_filter($cookie[1]))
                    ->get(), 
                3600, 
                crypt::encrypt('users(name)')
            );
    
            return $rowQ[0]['name'];
        }
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_membre_pseudo(): string
    {
        global $cookie;
    
        return $cookie[1];
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_blocID(string $arg): string
    {
        return @block::oneblock(substr($arg, 1), substr($arg, 0, 1) . "B");
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_block(string $arg): string
    {
        return metalang::meta_lang("blocID($arg)");
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_leftblocs(string $arg): string 
    {
        ob_start();
            block::leftblocks($arg);
            $M_Lblocs = ob_get_contents();
        ob_end_clean();
    
        return $M_Lblocs;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_rightblocs(string $arg): string 
    {
        ob_start();
            block::rightblocks($arg);
            $M_Lblocs = ob_get_contents();
        ob_end_clean();
    
        return $M_Lblocs;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_article(string $arg): string 
    {
        return metalang::meta_lang("articleID($arg)");
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_articleID(string $arg): string 
    {
        $rowQ = cache::Q_select3(
            DB::table('stories')
                ->select('title')
                ->where('sid', metalang::arg_filter($arg))
                ->get(), 
            3600, 
            crypt::encrypt('stories(title)')
        );
    
        return "<a href=\"". site_url('article.php?sid='.$arg)."\">" . $rowQ[0]['title'] . "</a>";
    }
    
    /**
     * 
     *
     * @param   int     $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_article_completID(int  $arg): string 
    {
        if ($arg > 0) {
            $story_limit = 1;
            $news_tab = news::prepa_aff_news("article", $arg, "");
        } else {
            $news_tab = news::prepa_aff_news("index", "", "");
            $story_limit = abs($arg) + 1;
        }
    
        $aid = unserialize($news_tab[$story_limit]['aid']);
        $informant = unserialize($news_tab[$story_limit]['informant']);
        $datetime = unserialize($news_tab[$story_limit]['datetime']);
        $title = unserialize($news_tab[$story_limit]['title']);
        $counter = unserialize($news_tab[$story_limit]['counter']);
        $topic = unserialize($news_tab[$story_limit]['topic']);
        $hometext = unserialize($news_tab[$story_limit]['hometext']);
        $notes = unserialize($news_tab[$story_limit]['notes']);
        $morelink = unserialize($news_tab[$story_limit]['morelink']);
        $topicname = unserialize($news_tab[$story_limit]['topicname']);
        $topicimage = unserialize($news_tab[$story_limit]['topicimage']);
        $topictext = unserialize($news_tab[$story_limit]['topictext']);
        $s_id = unserialize($news_tab[$story_limit]['id']);
        
        if ($aid) {
            ob_start();
                themeindex($aid, $informant, $datetime, $title, $counter, $topic, $hometext, $notes, $morelink, $topicname, $topicimage, $topictext, $s_id);
                $remp = ob_get_contents();
            ob_end_clean();
        } else {
            $remp = "";
        }
    
        return $remp;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_article_complet(string $arg): string 
    {
        return metalang::meta_lang("article_completID($arg)");
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_headlineID(string $arg): string 
    {
        return @boxe::headlines($arg, "");
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_headline(string $arg): string 
    {
        return metalang::meta_lang("headlineID($arg)");
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_list_mns(): string 
    {
        $users = DB::table('users')
                    ->select('uname')
                    ->where('mns', 1)
                    ->get();

        $MT_mns = "<ul class=\"list-group list-group-flush\">";
       
        foreach ($users as $user) {
            $MT_mns .= "<li class=\"list-group-item\"><a href=\"minisite.php?op=". $user['uname'] ."\" target=\"_blank\">". $user['uname'] ."</a></li>";
        }
    
        $MT_mns .= "</ul>";
    
        return $MT_mns;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_LastMember(): string 
    {
        $result = DB::table('users')
                    ->select('uname')
                    ->orderBy('uid', 'desc')
                    ->limit(1)
                    ->offset(0)
                    ->get();

        return $result[0]['uname'];
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_edito(): string 
    {
        list($affich, $M_edito) = edito::fab_edito();
    
        if ((!$affich) or ($M_edito == "")) {
            $M_edito = "";
        }
    
        return $M_edito;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_groupe_text(string $arg): string 
    {
        global $user;
    
        $affich = false;
        $remp = "";
    
        if ($arg != "") {
            if (groupe::groupe_autorisation($arg, groupe::valid_group($user))) {
                $affich = true;
            }
        } else {
            if ($user) {
                $affich = true;
            }
        }
    
        if (!$affich) {
            $remp = "!delete!";
        }
    
        return $remp;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_no_groupe_text(string $arg): string 
    {
        global $user;
    
        $affich = true;
        $remp = "";
    
        if ($arg != "") {
            if (groupe::groupe_autorisation($arg, groupe::valid_group($user))) {
                $affich = false;
            }
            
            if (!$user) {
                $affich = false;
            }
        } else {
            if ($user) {
                $affich = false;
            }
        }
    
        if (!$affich) {
            $remp = "!delete!";
        }
    
        return $remp;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_note(): string 
    {
        return "!delete!";
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_note_admin(): string 
    {
        global $admin;
    
        if (!$admin) {
            return "!delete!";
        } else {
            return "<b>nota</b> : ";
        }
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_debugON(): string
    {
        global $NPDS_debug, $NPDS_debug_str, $NPDS_debug_time, $NPDS_debug_cycle;
    
        $NPDS_debug_cycle = 1;
        $NPDS_debug = true;
        $NPDS_debug_str = "<br />";
        $NPDS_debug_time = microtime(true);
    
        return "";
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_debugOFF(): string 
    {
        global $NPDS_debug, $NPDS_debug_str, $NPDS_debug_time, $NPDS_debug_cycle;
    
        $time_end = microtime(true);
        $NPDS_debug_str .= "=> !DebugOFF!<br /><b>=> exec time for meta-lang : " . round($time_end - $NPDS_debug_time, 4) . " / cycle(s) : $NPDS_debug_cycle</b><br />";
        $NPDS_debug = false;
    
        echo $NPDS_debug_str;
    
        return "";
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_forum_all(): string 
    {
        $rowQ1 = cache::Q_Select3(
            DB::table('catagories')
                ->select('*')
                ->orderBy('cat_id')
                ->get(), 
            3600, 
            crypt::encrypt('categories(*)')
        );
    
        return @forum::forum($rowQ1);
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_forum_categorie(string $arg): string 
    {
        $query = DB::table('catagories')->select('*');

        $countwhere = 0;
        foreach (explode(",", metalang::arg_filter($arg)) as $cat) {

            if($countwhere <= 0)  {
                $query->where('cat_id', '=', $cat);
            } else {
                $query->orWhere('cat_id', '=', $cat);
            }

            $countwhere++;
        }

        $rowQ1 = cache::Q_Select3(
            $query->get(), 
            3600, 
            crypt::encrypt('categories_where_or(*)')
        );
    
        return @forum::forum($rowQ1);
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_forum_message(): string 
    {
        global $user;
    
        $ibid = "";
    
        if (!$user) {
            $ibid = translate("Devenez membre et vous disposerez de fonctions spécifiques : abonnements, forums spéciaux (cachés, membres, ..), statut de lecture, ...");
        }
    
        if ((Config::get('npds.subscribe')) and ($user)) {
            $ibid = translate("Cochez un forum et cliquez sur le bouton pour recevoir un Email lors d'une nouvelle soumission dans celui-ci.");
        }
    
        return $ibid;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_forum_recherche(): string 
    {
        return @forum::searchblock();
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_forum_icones(): string 
    {
        if ($ibid = theme::theme_image("forum/icons/red_folder.gif")) {
            $imgtmpR = $ibid;
        } else {
            $imgtmpR = "images/forum/icons/red_folder.gif";
        }
    
        if ($ibid = theme::theme_image("forum/icons/folder.gif")) {
            $imgtmp = $ibid;
        } else {
            $imgtmp = "images/forum/icons/folder.gif";
        }
    
        $ibid = "<img src=\"$imgtmpR\" border=\"\" alt=\"\" /> = " . translate("Les nouvelles contributions depuis votre dernière visite.") . "<br />";
        $ibid .= "<img src=\"$imgtmp\" border=\"\" alt=\"\" /> = " . translate("Aucune nouvelle contribution depuis votre dernière visite.");
        
        return $ibid;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_forum_subscribeON(): string 
    {
        global $user;
    
        $ibid = "";
        
        if ((Config::get('npds.subscribe')) and ($user)) {
            $userX = base64_decode($user);
            $userR = explode(':', $userX);
            
            if (mailler::isbadmailuser($userR[0]) === false) {
                $ibid = "<form action=\"forum.php\" method=\"post\">
                <input type=\"hidden\" name=\"op\" value=\"maj_subscribe\" />";
            }
        }
    
        return $ibid;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_forum_bouton_subscribe(): string 
    {
        global $user;
    
        if ((Config::get('npds.subscribe')) and ($user)) {
            $userX = base64_decode($user);
            $userR = explode(':', $userX);
            
            if (mailler::isbadmailuser($userR[0]) === false) {
                return '<input class="btn btn-secondary" type="submit" name="Xsub" value="' . translate("OK") . '" />';
            }
        } else {
            return '';
        }
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_forum_subscribeOFF(): string 
    {
        global $user;
    
        $ibid = "";
    
        if ((Config::get('npds.subscribe')) and ($user)) {
            $userX = base64_decode($user);
            $userR = explode(':', $userX);
            
            if (mailler::isbadmailuser($userR[0]) === false) {
                $ibid = "</form>";
            }
        }
    
        return $ibid;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_forum_subfolder(string $arg): string 
    {
        $forum = metalang::arg_filter($arg);

        return forum::sub_forum_folder($forum);
    }
    
    /**
     * 
     *
     * @param   string  $name    [$name description]
     * @param   string  $width   [$width description]
     * @param   string  $height  [$height description]
     * @param   string  $bgcol   [$bgcol description]
     *
     * @return  string
     */
    public function MM_insert_flash(string $name, string $width, string $height, string $bgcol): string
    {
        return ("<object codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflas
        classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\"
        h.cab#version=6,0,0,0\" width=\"" . $width . "\"
        height=\"" . $height . "\"
        id=\"" . $name . "\" align=\"middle\">
      
        <param name=\"allowScriptAccess\"
        value=\"sameDomain\" />
      
        <param name=\"movie\"
        value=\"flash/" . $name . "\" />
      
        <param name=\"quality\" value=\"high\" />
        <param name=\"bgcolor\"
        value=\"" . $bgcol . "\" />
     
        <embed src=\"flash/" . $name . "\"
        quality=\"high\" bgcolor=\"" . $bgcol . "\"
        width=\"" . $width . "\"
        height=\"" . $height . "\"
        name=\"" . $name . "\" align=\"middle\"
        allowScriptAccess=\"sameDomain\"
        type=\"application/x-shockwave-flash\"
        pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
     
        </object>");
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_login(): string 
    {
        global $user;
    
        $boxstuff = '
        <div class="card card-body m-3">
           <h5><a href="user.php?op=only_newuser" role="button" title="' . translate("Nouveau membre") . '"><i class="fa fa-user-plus"></i>&nbsp;' . translate("Nouveau membre") . '</a></h5>
        </div>
        <div class="card card-body m-3">
           <h5 class="mb-3"><i class="fas fa-sign-in-alt fa-lg"></i>&nbsp;' . translate("Connexion") . '</h5>
           <form action="user.php" method="post" name="userlogin_b">
              <div class="row g-2">
                 <div class="col-12">
                    <div class="mb-3 form-floating">
                       <input type="text" class="form-control" name="uname" id="inputuser_b" placeholder="' . translate("Identifiant") . '" required="required" />            
                       <label for="inputuser_b" >' . translate("Identifiant") . '</label>
                   </div>
                </div>
                <div class="col-12">
                   <div class="mb-0 form-floating">
                      <input type="password" class="form-control" name="pass" id="inputPassuser_b" placeholder="' . translate("Mot de passe") . '" required="required" />
                      <label for="inputPassuser_b">' . translate("Mot de passe") . '</label>
                      <span class="help-block small"><a href="user.php?op=forgetpassword" role="button" title="' . translate("Vous avez perdu votre mot de passe ?") . '">' . translate("Vous avez perdu votre mot de passe ?") . '</a></span>
                    </div>
                 </div>
              </div>
              <input type="hidden" name="op" value="login" />
              <div class="mb-3 row">
                 <div class="ms-sm-auto">
                    <button class="btn btn-primary" type="submit" title="' . translate("Valider") . '">' . translate("Valider") . '</button>
                 </div>
              </div>
           </form>
        </div>';
    
        if (isset($user)) {
            $boxstuff = '<h5><a class="text-danger" href="user.php?op=logout"><i class="fas fa-sign-out-alt fa-lg align-middle text-danger me-2"></i>' . translate("Déconnexion") . '</a></h5>';
        }
    
        return $boxstuff;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_administration(): string 
    {
        global $admin;
    
        if ($admin) {
            return "<a href=\"admin.php\">" . translate("Outils administrateur") . "</a>";
        } else {
            return "";
        }
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_admin_infos(string $arg): string 
    {
        $rowQ1 = cache::Q_select3(
            DB::table('authors')
                ->select('url', 'email')
                ->where('aid', metalang::arg_filter($arg))
                ->get(), 
            86400, 
            crypt::encrypt('authors(url_email)')
        );
        
        if ($rowQ1[0]['url'] != '') {
            $auteur = "<a href=\"" . $rowQ1[0]['url'] . "\">$arg</a>";
        } elseif ($rowQ1[0]['email'] != '') {
            $auteur = "<a href=\"mailto:" . $rowQ1[0]['email'] . "\">$arg</a>";
        } else {
            $auteur = $arg;
        }
    
        return $auteur;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_theme_img(string $arg): string 
    {
        return metalang::MM_img($arg);
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_rotate_img(string $arg): string
    {
        mt_srand((int) microtime() * 1000000);
        
        $arg = metalang::arg_filter($arg);
        $tab_img = explode(",", $arg);
    
        if (count($tab_img) > 1) {
            $imgnum = mt_rand(0, count($tab_img) - 1);
        } else if (count($tab_img) == 1) {
            $imgnum = 0;
        } else {
            $imgnum = -1;
        }
    
        if ($imgnum != -1) {
            $Xcontent = "<img src=\"" . $tab_img[$imgnum] . "\" border=\"0\" alt=\"" . $tab_img[$imgnum] . "\" title=\"" . $tab_img[$imgnum] . "\" />";
        }
    
        return $Xcontent;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_sql_nbREQ(): string 
    {
        global $sql_nbREQ;
    
        return "SQL REQ : $sql_nbREQ";
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_top_stories(string $arg): string 
    {
        $content = '';
        $arg = metalang::arg_filter($arg);
    
        $limit = ($arg * 2);

        $xtab = news::news_aff2("libre", 
            DB::table('stories')
                ->select('sid', 'catid', 'ihome', 'time')
                ->orderBy('counter', 'desc')
                ->limit($limit)
                ->offset(0)
                ->get(), 
            0, 
            $limit
        );

        $story_limit = 0;
        while (($story_limit < $arg) and ($story_limit < sizeof($xtab))) {
            list($sid, $catid, $aid, $title, $time, $hometext, $bodytext, $comments, $counter) = $xtab[$story_limit];
            
            if ($counter > 0) {
                $content .= '<li class="ms-4 my-1"><a href="article.php?sid=' . $sid . '" >' . language::aff_langue($title) . '</a>&nbsp;<span class="badge bg-secondary float-end">' . str::wrh($counter) . ' ' . translate("Fois") . '</span></li>';
            }

            $story_limit++;
        }
    
        return $content;
    }
    
    /**
     * 
     *
     * @param   string  $file_name  [$file_name description]
     * @param   int     $topic      [$topic description]
     *
     * @return  string
     */
    public function MM_comment_system(string $file_name, int $topic): string
    {
        ob_start();
            
            if (file_exists("modules/comments/$file_name.conf.php")) {
                include("modules/comments/$file_name.conf.php");
                include("modules/comments/comments.php");
            }
    
            $output = ob_get_contents();
        ob_end_clean();
    
        return $output;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_top_commented_stories(string $arg): string 
    {
        $content = '';
        $arg = metalang::arg_filter($arg);
        
        $limit = ($arg * 2);

        $xtab = news::news_aff2("libre", 
            DB::table('stories')
                ->select('sid', 'catid', 'ihome', 'time')
                ->orderBy('comments', 'desc')
                ->limit($limit)
                ->offset(0)
                ->get(), 
            0, 
            $limit
        );

        $story_limit = 0;
        
        while (($story_limit < $arg) and ($story_limit < sizeof($xtab))) {
            list($sid, $catid, $aid, $title, $time, $hometext, $bodytext, $comments) = $xtab[$story_limit];
            $story_limit++;
            
            if ($comments > 0) {
                $content .= '<li class="ms-4 my-1"><a href="article.php?sid=' . $sid . '" >' . language::aff_langue($title) . '</a>&nbsp;<span class="badge bg-secondary float-end">' . str::wrh($comments) . '</span></li>';
            }
        }
    
        return $content;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_top_categories(string $arg): string 
    {
        $content = '';

        $result = DB::table('stories_cat')
                    ->select('catid', 'title', 'counter')
                    ->orderBy('counter', 'asc')
                    ->limit(metalang::arg_filter($arg))
                    ->offset(0)
                    ->get();
        
        foreach ($result as $storie) {
            if ($storie['counter'] > 0) {
                $content .= '<li class="ms-4 my-1">
                    <a href="index.php?op=newindex&amp;catid=' . $storie['catid'] . '" >
                        ' . language::aff_langue($storie['title']) . '
                    </a>
                    &nbsp;<span class="badge bg-secondary float-end">' . str::wrh($storie['counter']) . '</span>
                </li>';
            }
        }

        return $content;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_top_sections(string $arg): string 
    {
        $content = '';

        $result = DB::table('seccont')
                    ->select('artid', 'title', 'counter')
                    ->orderBy('counter', 'desc')
                    ->limit(metalang::arg_filter($arg))
                    ->offset(0)
                    ->get();
        
        foreach ($result as $seccont) {
            $content .= '<li class="ms-4 my-1">
                <a href="sections.php?op=viewarticle&amp;artid=' . $seccont['artid'] . '" >
                    ' . language::aff_langue($seccont['title']) . '
                </a>
                &nbsp;<span class="badge bg-secondary float-end">' . str::wrh($seccont['counter']) . ' ' . translate("Fois") . '</span></li>';
        }

        return $content;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_top_reviews(string $arg): string 
    {
        $content = '';

        $result = DB::table('reviews')
                    ->select('id', 'title', 'hits')
                    ->orderBy('hits', 'desc')
                    ->limit(metalang::arg_filter($arg))
                    ->offset(0)
                    ->get();

        foreach ($result as $review) {
            if ($review['hits'] > 0) {
                $content .= '<li class="ms-4 my-1">
                    <a href="reviews.php?op=showcontent&amp;id=' . $review['id'] . '" >
                        ' . $review['title'] . '
                    </a>
                    &nbsp;<span class="badge bg-secondary float-end">' . str::wrh($review['hits']) . ' ' . translate("Fois") . '</span></li>';
            }
        }

        return $content;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_top_authors(string $arg): string 
    {
        $content = '';

        $result = DB::table('authors')
                    ->select('aid', 'counter')
                    ->orderBy('counter', 'desc')
                    ->limit(metalang::arg_filter($arg))
                    ->offset(0)
                    ->get();

        foreach ($result as $author) {
            if ($author['counter'] > 0) {
                $content .= '<li class="ms-4 my-1">
                    <a href="search.php?query=&amp;author=' . $author['aid'] . '" >
                        ' . $author['aid'] . '
                    </a>
                    &nbsp;<span class="badge bg-secondary float-end">' . str::wrh($author['counter']) . '</span></li>';
            }
        }
    
        return $content;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_top_polls(string $arg): string 
    {
        $content = '';

        $result = DB::table('poll_desc')
                ->select('pollID', 'pollTitle', 'voters')
                ->orderBy('voters', 'desc')
                ->limit(metalang::arg_filter($arg))
                ->offset(0)
                ->get();

        foreach ($result as $poll) {
            
            if ($poll['voters'] > 0) {
                $content .= '<li class="ms-4 my-1">
                    <a href="pollBooth.php?op=results&amp;pollID=' . $poll['pollID'] . '" >
                        ' . language::aff_langue($poll['pollTitle']) . '
                    </a>
                    &nbsp;<span class="badge bg-secondary float-end">' . str::wrh($poll['voters']) . '</span></li>';
            }
        }
    
        return $content;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_top_storie_authors(string $arg): string 
    {
        $content = '';

        $result = DB::table('users')
                    ->select('uname', 'counter')
                    ->orderBy('counter', 'desc')
                    ->limit(metalang::arg_filter($arg))
                    ->offset(0)
                    ->get();
        
        foreach ($result as $user) {
            
            if ($user['counter'] > 0) {
                $content .= '<li class="ms-4 my-1">
                    <a href="user.php?op=userinfo&amp;uname=' . $user['uname'] . '" >
                        ' . $user['uname'] . '
                    </a>
                    &nbsp;<span class="badge bg-secondary float-end">' . str::wrh($user['counter']) . '</span></li>';
            }
        }
    
        return $content;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_topic_all(): string 
    {
        $aff = '';
        $aff = '<div class="">';
    
        $result = DB::table('topics')
                    ->select('topicid', 'topicname', 'topicimage', 'topictext')
                    ->orderBy('topicname')
                    ->get();
        
        foreach ($result as $topic) {
            
            $total_news = DB::table('stories')
                            ->select(DB::raw('COUNT(*) AS total'))
                            ->where('topic', $topic['topicid'])
                            ->first();

            $aff .= '
               <div class="col-sm-6 col-lg-4 mb-2 griditem px-2">
                  <div class="card my-2">';
            
            if ((($topic['topicimage']) or ($topic['topicimage'] != '')) and (file_exists(Config::get('npds.tipath') . $topic['topicimage']))) {
                $aff .= '<img class="mt-3 ms-3 n-sujetsize" src="' . Config::get('npds.tipath') . $topic['topicimage'] . '" alt="topic_icon" />';
            }
    
            $aff .= '<div class="card-body">';
            
            if ($total_news['total'] != '0') {
                $aff .= '<a href="index.php?op=newtopic&amp;topic=' . $topic['topicid'] . '"><h4 class="card-title">' . language::aff_langue($topic['topicname']) . '</h4></a>';
            } else {
                $aff .= '<h4 class="card-title">' . language::aff_langue($topic['topicname']) . '</h4>';
            }
    
            $aff .= '<p class="card-text">' . language::aff_langue($topic['topictext']) . '</p>
                        <p class="card-text text-end">
                            <span class="small">' . translate("Nb. d'articles") . '</span> <span class="badge bg-secondary">' . $total_news['total'] . '</span>
                        </p>
                     </div>
                  </div>
               </div>';
        }
    
        $aff .= '</div>';
    
        return $aff;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_topic_subscribeOFF(): string 
    {
        $aff = '<div class="mb-3 row"><input type="hidden" name="op" value="maj_subscribe" />';
        $aff .= '<button class="btn btn-primary ms-3" type="submit" name="ok">' . translate("Valider") . '</button>';
        $aff .= '</div></fieldset></form>';
    
        return $aff;
    }
    
    /**
     * 
     *
     * @return  mixed
     */
    public function MM_topic_subscribeON(): mixed
    {
        global $user, $cookie;
        
        if (Config::get('npds.subscribe') and $user) {
            if (mailler::isbadmailuser($cookie[0]) === false) {
                return ('<form action="topics.php" method="post"><fieldset>');
            }
        }
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_topic_subscribe(string $arg): string 
    {
        global $user, $cookie;
    
        $segment = metalang::arg_filter($arg);
        $aff = '';
        
        if (Config::get('npds.subscribe')) {
            if ($user) {
                $aff = '
                  <div class="mb-3 row">';
                
                $result = DB::table('topics')
                            ->select('topicid', 'topictext', 'topicname')
                            ->orderBy('topicname')
                            ->get();

                foreach ($result as $topic) {

                    $resultX = DB::table('subscribe')
                            ->select('topicid')
                            ->where('uid', $cookie[0])
                            ->where('topicid', $topic['topicid'])
                            ->get();

                    if ($resultX) {
                        $checked = 'checked';
                    } else {
                        $checked = '';
                    }
    
                    $aff .= '
                        <div class="' . $segment . '">
                           <div class="form-check">
                              <input type="checkbox" class="form-check-input" name="Subtopicid[' . $topic['topicid'] . ']" id="subtopicid' . $topic['topicid'] . '" ' . $checked . ' />
                              <label class="form-check-label" for="subtopicid' . $topic['topicid'] . '">' . language::aff_langue($topic['topicname']) . '</label>
                           </div>
                        </div>';
                }
    
                $aff .= '</div>';
            }
        }
    
        return $aff;
    }
    
    /**
     * 
     *
     * @param   string  $id_yt_video  [$id_yt_video description]
     *
     * @return  string
     */
    public function MM_yt_video(string $id_yt_video): string 
    {
        $content = '';
        $id_yt_video = metalang::arg_filter($id_yt_video);
        
        if (!defined('CITRON')) {
            $content .= '
               <div class="ratio ratio-16x9">
                  <iframe src="https://www.youtube.com/embed/' . $id_yt_video . '" allowfullscreen="" frameborder="0"></iframe>
               </div>';
        } else {
            $content .= '<div class="youtube_player" videoID="' . $id_yt_video . '"></div>';
        }
    
        return $content;
    }
    
    /**
     * 
     *
     * @param   string  $gr    [$gr description]
     * @param   string  $t_gr  [$t_gr description]
     * @param   string  $i_gr  [$i_gr description]
     *
     * @return  string
     */
    public function MM_espace_groupe(string  $gr, string  $t_gr, string  $i_gr): string
    {
        $gr = metalang::arg_filter($gr);
        $t_gr = metalang::arg_filter($t_gr);
        $i_gr = metalang::arg_filter($i_gr);
    
        return groupe::fab_espace_groupe($gr, $t_gr, $i_gr);
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function MM_blocnote(string $arg): string 
    {
        global $REQUEST_URI;
    
        if (!stristr($REQUEST_URI, "admin.php")) {
            return @block::oneblock($arg, "RB");
        } else {
            return "";
        }
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_forumP(): string 
    {
        global $cookie, $user;
    
        /*Sujet chaud*/
        $hot_threshold = 10;
    
        /*Nbre posts a afficher*/
        $maxcount = "15";
    
        $MM_forumP = '<table cellspacing="3" cellpadding="3" width="top" border="0">'
            . '<tr align="center" class="ligna">'
            . '<th width="5%">' . language::aff_langue('[french]Etat[/french][english]State[/english]') . '</th>'
            . '<th width="20%">' . language::aff_langue('[french]Forum[/french][english]Forum[/english]') . '</th>'
            . '<th width="30%">' . language::aff_langue('[french]Sujet[/french][english]Topic[/english]') . '</th>'
            . '<th width="5%">' . language::aff_langue('[french]RÃ©ponse[/french][english]Replie[/english]') . '</th>'
            . '<th width="20%">' . language::aff_langue('[french]Dernier Auteur[/french][english]Last author[/english]') . '</th>'
            . '<th width="20%">' . language::aff_langue('[french]Date[/french][english]Date[/english]') . '</th>'
            . '</tr>';
    
        /*Requete liste dernier post*/

        $result = DB::table('posts')
                    ->select(DB::raw('MAX(post_id)'))
                    ->where('forum_id', '>', 0)
                    ->groupey('topic_id')
                    ->orderBy(DB::raw('MAX(post_id)'), 'desc')
                    ->limit($maxcount)
                    ->offset(0)
                    ->get();

        foreach ($result as $post) {
    
            /*Requete detail dernier post*/
            $res = DB::table('posts')
                        ->select('posts.topic_id', 'posts.forum_id', 'posts.poster_id', 'posts.post_time', 'forumtopics.topic_title', 'forums.forum_name', 'forums.forum_type', 'forums.forum_pass', 'users.uname', 'forumtopics.topic_status')
                        ->join('forumtopics', 'forumtopics.topic_id', '=', 'posts.topic_id')
                        ->join('forums', 'forumtopics.forum_id', '=', 'forums.forum_id')
                        ->join('users', 'users.uid', '=', 'posts.poster_id')
                        ->where('posts.post_id', $post['post_id'])
                        ->limit(1)
                        ->first();

            if (($res['forum_type'] == "5") or ($res['forum_type'] == "7")) {
    
                $ok_affich = false;
                $tab_groupe = groupe::valid_group($user);
                $ok_affich = groupe::groupe_forum($res['forum_pass'], $tab_groupe);
            } else {
    
                $ok_affich = true;
            }
    
            if ($ok_affich) {
    
                /*Nbre de postes par sujet*/
                $TableRep = DB::table('posts')
                            ->select('*')
                            ->where('forum_id', '>', 0)
                            ->where('topic_id', $res['topic_id'])
                            ->count();


                $replys = ($TableRep - 1);

                /*Gestion lu / non lu*/
                $sqlR = DB::table('forum_read')
                            ->select('rid')
                            ->where('topicid', $res['topic_id'])
                            ->where('uid', $cookie[0])
                            ->where('status', '!=', 0)
                            ->get();

                if ($ibid = theme::theme_image("forum/icons/hot_red_folder.gif")) {
                    $imgtmpHR = $ibid;
                } else {
                    $imgtmpHR = "images/forum/icons/hot_red_folder.gif";
                }
    
                if ($ibid = theme::theme_image("forum/icons/hot_folder.gif")) {
                    $imgtmpH = $ibid;
                } else {
                    $imgtmpH = "images/forum/icons/hot_folder.gif";
                }
    
                if ($ibid = theme::theme_image("forum/icons/red_folder.gif")) {
                    $imgtmpR = $ibid;
                } else {
                    $imgtmpR = "images/forum/icons/red_folder.gif";
                }
    
                if ($ibid = theme::theme_image("forum/icons/folder.gif")) {
                    $imgtmpF = $ibid;
                } else {
                    $imgtmpF = "images/forum/icons/folder.gif";
                }
    
                if ($ibid = theme::theme_image("forum/icons/lock.gif")) {
                    $imgtmpL = $ibid;
                } else {
                    $imgtmpL = "images/forum/icons/lock.gif";
                }
    
                if ($replys >= $hot_threshold) {
    
                    if ($sqlR == 0) {
                        $image = $imgtmpHR;
                    } else {
                        $image = $imgtmpH;}
                } else {
    
                    if ($sqlR == 0) {
                        $image = $imgtmpR;
                    } else {
                        $image = $imgtmpF;}
                }       

                if ($res['topic_status'] != 0) {
                    $image = $imgtmpL;
                }

                $MM_forumP .= '<tr class="lignb">'
                    . '<td align="center"><img src="' . $image . '"></td>'
                    . '<td><a href="viewforum.php?forum=' . $res['forum_id'] . '">' . $res['forum_name'] . '</a></td>'
                    . '<td><a href="viewtopic.php?topic=' . $res['topic_id'] . '&forum=' . $res['forum_id'] . '">' . $res['topic_title'] . '</a></td>'
                    . '<td align="center">' . $replys . '</td>'
                    . '<td><a href="user.php?op=userinfo&uname=' . $res['uname'] . '">' . $res['uname'] . '</a></td>'
                    . '<td align="center">' . $res['post_time'] . '</td>'
                    . '</tr>';
            }
        }
    
        $MM_forumP .= '</table>';
    
        return $MM_forumP;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_forumL(): string 
    {
        global $cookie, $user;
    
        /*Sujet chaud*/
        $hot_threshold = 10;
    
        /*Nbre posts a afficher*/
        $maxcount = "10";
    
        $MM_forumL = '<table cellspacing="3" cellpadding="3" width="top" border="0">'
            . '<tr align="center" class="ligna">'
            . '<td width="8%">' . language::aff_langue('[french]Etat[/french][english]State[/english]') . '</td>'
            . '<td width="35%">' . language::aff_langue('[french]Forum[/french][english]Forum[/english]') . '</td>'
            . '<td width="50%">' . language::aff_langue('[french]Sujet[/french][english]Topic[/english]') . '</td>'
            . '<td width="7%">' . language::aff_langue('[french]RÃ©ponses[/french][english]Replies[/english]') . '</td>'
            . '</tr>';
    
        /*Requete liste dernier post*/

        $result = DB::table('posts')
                    ->select(DB::raw('MAX(post_id)'))
                    ->where('forum_id', '>', 0)
                    ->groupey('topic_id')
                    ->orderBy(DB::raw('MAX(post_id)', 'desc'))
                    ->limit($maxcount)
                    ->offset(0)
                    ->get();
        
        foreach ($result as $post) {
    
            /*Requete detail dernier post*/
            $res = DB::table('posts')
                ->select('posts.topic_id', 'posts.forum_id', 'posts.poster_id', 'forumtopics.topic_title', 'forums.forum_name', 'forums.forum_type', 'forums.forum_pass', 'forumtopics.topic_status' )
                ->join('forumtopics', 'forumtopics.topic_id', '=', 'posts.topic_id')
                ->join('forums', 'forumtopics.forum_id', '=', 'forums.forum_id')
                ->where('posts.post_id', $post['post_id'])
                ->limit(1)
                ->get();

            if (($res['forum_type'] == "5") or ($res['forum_type'] == "7")) {
    
                $ok_affich = false;
                $tab_groupe = groupe::valid_group($user);
                $ok_affich = groupe::groupe_forum($res['forum_pass'], $tab_groupe);
            } else {
    
                $ok_affich = true;
            }
    
            if ($ok_affich) {
    
                /*Nbre de postes par sujet*/
                
                $TableRep = DB::table('posts')
                                ->select('*')
                                ->where('forum_id', '>', 0)
                                ->where('topic_id', $res['topic_id'])
                                ->count();

                $replys = ($TableRep - 1);

                /*Gestion lu / non lu*/
                $sqlR = DB::table('forum_read')
                            ->select('rid')
                            ->where('topicid', $res['topic_id'])
                            ->where('uid', $cookie[0])
                            ->where('status', '!=',  0)
                            ->count();

                if ($ibid = theme::theme_image("forum/icons/hot_red_folder.gif")) {
                    $imgtmpHR = $ibid;
                } else {
                    $imgtmpHR = "images/forum/icons/hot_red_folder.gif";
                }
    
                if ($ibid = theme::theme_image("forum/icons/hot_folder.gif")) {
                    $imgtmpH = $ibid;
                } else {
                    $imgtmpH = "images/forum/icons/hot_folder.gif";
                }
    
                if ($ibid = theme::theme_image("forum/icons/red_folder.gif")) {
                    $imgtmpR = $ibid;
                } else {
                    $imgtmpR = "images/forum/icons/red_folder.gif";
                }
    
                if ($ibid = theme::theme_image("forum/icons/folder.gif")) {
                    $imgtmpF = $ibid;
                } else {
                    $imgtmpF = "images/forum/icons/folder.gif";
                }
    
                if ($ibid = theme::theme_image("forum/icons/lock.gif")) {
                    $imgtmpL = $ibid;
                } else {
                    $imgtmpL = "images/forum/icons/lock.gif";
                }
    
                if ($replys >= $hot_threshold) {
    
                    if ($sqlR == 0) {
                        $image = $imgtmpHR;
                    } else {
                        $image = $imgtmpH;}
                } else {
    
                    if ($sqlR == 0) {
                        $image = $imgtmpR;
                    } else {
                        $image = $imgtmpF;
                    }
                }        

                if ($res['topic_status'] != 0) {
                    $image = $imgtmpL;
                }
    
                $MM_forumL .= '<tr class="lignb">'
                    . '<td align="center"><img src="' . $image . '"></td>'
                    . '<td><a href="viewforum.php?forum=' . $res['forum_id'] . '">' . $res['forum_name'] . '</a></td>'
                    . '<td><a href="viewtopic.php?topic=' . $res['topic_id'] . '&forum=' . $res['forum_id'] . '">' . $res['topic_title'] . '</a></td>'
                    . '<td align="center">' . $replys . '</td>'
                    . '</tr>';
            }
        }
    
        $MM_forumL .= '</table>';
    
        return $MM_forumL;
    }
    
    /**
     * 
     *
     * @param   string  $id_vm_video  [$id_vm_video description]
     *
     * @return  string
     */
    public function MM_vm_video(string $id_vm_video): string 
    {
        $content = '';
        $id_vm_video = metalang::arg_filter($id_vm_video);
    
        if (!defined('CITRON')) {
            $content .= '
               <div class="ratio ratio-16x9">
                  <iframe src="https://player.vimeo.com/video/' . $id_vm_video . '" allowfullscreen="" frameborder="0"></iframe>
               </div>';
        } else {
            $content .= '<div class="vimeo_player" videoID="' . $id_vm_video . '"></div>';
        }
        
        return $content;
    }
    
    /**
     * 
     *
     * @param   string  $id_dm_video  [$id_dm_video description]
     *
     * @return  string
     */
    public function MM_dm_video(string $id_dm_video): string 
    {
        $content = '';
        $id_dm_video = metalang::arg_filter($id_dm_video);
    
        if (!defined('CITRON')) {
            $content .= '
               <div class="ratio ratio-16x9">
                  <iframe src="https://www.dailymotion.com/embed/video/' . $id_dm_video . '" allowfullscreen="" frameborder="0"></iframe>
               </div>';
        } else {
            $content .= '<div class="dailymotion_player" videoID="' . $id_dm_video . '"></div>';
        }
    
        return $content;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function MM_noforbadmail():string 
    {
        global $user, $cookie;
    
        $remp = '';
    
        if (Config::get('npds.subscribe') and $user) {
            if (mailler::isbadmailuser($cookie[0]) === true)
                $remp = '!delete!';
        }
    
        return $remp;
    }

}
