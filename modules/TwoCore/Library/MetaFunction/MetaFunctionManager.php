<?php

declare(strict_types=1);

namespace Modules\TwoCore\Library\MetaFunction;

use Two\Support\Str;
use Two\Support\Facades\DB;
use Two\Foundation\Application;
use Two\Support\Facades\Config;
use Two\Support\Facades\Mailer;
use Two\Support\Facades\Package;
use Modules\TwoCore\Support\Spam;
use Modules\TwoUsers\Support\Facades\User;
use Modules\TwoEdito\Support\Facades\Edito;
use Modules\TwoThemes\Library\ThemeManager;
use Modules\TwoBlocks\Support\Facades\Block;
use Modules\TwoThemes\Support\Facades\Theme;
use Modules\TwoUsers\Support\Facades\Online;
use Modules\TwoCore\Support\Facades\Language;
use Modules\TwoCore\Support\Facades\Metalang;
use Modules\TwoAuthors\Support\Facades\Author;
use Modules\TwoGroupes\Support\Facades\Groupe;
use Modules\TwoCore\Library\Metalang\MetaLangManager;


class MetaFunctionManager
{

    /**
     * The Application Instance.
     *
     * @var \Two\Application\Application
     */
    public $app;

    /**
     * [$metalang description]
     *
     * @var [type]
     */
    protected MetaLangManager $metalang;

    /**
     * [$theme description]
     *
     * @var [type]
     */
    protected ThemeManager $theme;


    /**
     * Créez une nouvelle instance de Metas Manager.
     *
     * @return void
     */
    public function __construct(Application $app, MetaLangManager $metalang, ThemeManager $theme)
    {
        //
        $this->app = $app;

        //
        $this->metalang = $metalang;

        //
        $this->theme = $theme;
    }

    /**
     * Cette fonction est utilisée pour intégrer des smilies et comme service pour theme_img()
     *
     * @param   string  $ibid  [$ibid description]
     *
     * @return  string
     */
    public function  MM_img(string $ibid)
    {
        $ibid = $this->metalang->arg_filter($ibid);
        $ibidX = $this->theme->theme_image($ibid);
        
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

    public function  MM_asset_url(string $class, string $style, string $image, string $alt)
    {
        $package    = Package::where('basename', Theme::getName());
        $namespace  = $this->getPackagehint($package['name']);

        $package_core    = Package::where('basename', Config::get('two_core::config.packageTwoThemes', 'TwoThemes'));
        $namespace_core  = $this->getPackagehint($package_core['name']);

        if (app('files')->exists($package['path'] . 'Assets/' . $image)) {
            $url = asset_url($image, $namespace);
        } elseif (app('files')->exists($package_core['path'] . 'Assets/' . $image)) {
            $url = asset_url($image, $namespace_core);
        }

        return '<img class="'.$class.'" style="'.$style.'" src="'.$url.'" alt="'.$alt.'">';
        return $url;
    }

    /**
     * 
     *
     * @return  string
     */
    public function  MM_sc_infos()
    {
        return sc_infos();
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
    public function  MM_Scalcul(string $opex, int $premier, int $deuxieme)
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
    public function  MM_anti_spam(string $arg)
    {
        return ("<a href=\"mailto:" . Spam::anti_spam($arg, 1) . "\" target=\"_blank\">" . Spam::anti_spam($arg, 0) . "</a>");
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_msg_foot()
    {
        // Note a modifier pour prendre en compte le theme 
        if ($foot1 = Config::get('two_core::config.foot1')) {
            $MT_foot = stripslashes($foot1) . "<br />";
        }
    
        if ($foot2 = Config::get('two_core::config.foot2')) {
            $MT_foot .= stripslashes($foot2) . "<br />";
        }
    
        if ($foot3 = Config::get('two_core::config.foot3')) {
            $MT_foot .= stripslashes($foot3) . "<br />";
        }
    
        if ($foot4 = Config::get('two_core::config.foot4')) {
            $MT_foot .= stripslashes($foot4);
        }
    
        return Language::aff_langue($MT_foot);
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_date() 
    {
        return ucfirst(
            htmlentities(\PHP81_BC\strftime(__d('two_core', 'daydate'), 
            time(), Config::get('two_core::config.locale')), 
            ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 
            'utf-8')
        );
    }
    
    /**
     * 
     *
     * @return  string|bool
     */
    public function  MM_banner()
    {
        global $hlpfile;
    
        if ((Config::get('npds.banners')) and (!$hlpfile)) {
            ob_start();
                //include("banners.php");
                $banner = ob_get_contents();
            ob_end_clean();

            return $banner;
        } else {
            return false;
        }
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_search_topics() 
    {
        // foreach (Cache::Q_select3(
        //     DB::table('topics')
        //         ->select('topicid', 'topictext')
        //         ->orderBy('topictext')
        //         ->get(), 
        //     86400, 
        //     Crypt::encrypt('topics(topiccid)')
        // ) as $res_topic) 
        // {   
        //     $options_topics = '';
        //     $options_topics .= '<option value="' . $res_topic['topicid'] . '">' . Language::aff_langue($res_topic['topictext']) . '</option>';
        // }
    
        // return '<form action="'. site_url('search.php') .'" method="post">
        //         <label class="col-form-label">' . __d('two_core', 'Sujets') . ' </label>
        //         <select class="form-select" name="topic"onChange="submit()">
        //             <option value="">' . __d('two_core', 'Tous les sujets') . '</option>
        //                 '. $options_topics .'
        //             </select>
        //         </form>';
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_search()
    {
        return '<form action="'. site_url('search.php') .'" method="post"><label>' . __d('two_core', 'Recherche') . '</label>
        <input class="form-control" type="text" name="query" size="10"></form>';
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_member()
    {
        if (!$username = User::cookieUser(1)) {
            $username = Config::get('two_core::config.anonymous');
        }
    
        ob_start();
            Mailer::Mess_Check_Mail($username);
            $MT_member = ob_get_contents();
        ob_end_clean();
    
        return $MT_member;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_nb_online()
    {
        list($MT_nb_online, $MT_whoim) = Online::Who_Online();
    
        return $MT_nb_online;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_whoim()
    {
        list($MT_nb_online, $MT_whoim) = Online::Who_Online();
    
        return $MT_whoim;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_membre_nom()
    {
        // $cookie = Users::cookieUser(1);

        // if (isset($cookie)) {
        //     $rowQ = Cache::Q_select3(
        //         DB::table('users')
        //             ->select('name')
        //             ->where('uname', Metalang::arg_filter($cookie))
        //             ->get(), 
        //         3600, 
        //         Crypt::encrypt('users(name)')
        //     );
    
        //     return $rowQ[0]['name'];
        // }
    }

    /**
     * 
     *
     * @return  string
     */
    public function  MM_membre_pseudo()
    {
        return User::cookieUser(1);
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function  MM_blocID(string $arg)
    {
        return @Block::oneblock(substr($arg, 1), substr($arg, 0, 1) . "B");
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function  MM_block(string $arg)
    {
        return Metalang::meta_lang("blocID($arg)");
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function  MM_leftblocs(string $arg)
    {
        ob_start();
            Block::leftblocks($arg);
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
    public function  MM_rightblocs(string $arg)
    {
        ob_start();
            Block::rightblocks($arg);
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
    public function  MM_article(string $arg)
    {
        return Metalang::meta_lang("articleID($arg)");
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function  MM_articleID(string $arg) 
    {
        // $rowQ = Cache::Q_select3(
        //     DB::table('stories')
        //         ->select('title')
        //         ->where('sid', Metalang::arg_filter($arg))
        //         ->get(), 
        //     3600, 
        //     Crypt::encrypt('stories(title)')
        // );
    
        // return '<a href="'. site_url('article.php?sid='.$arg).'">' . $rowQ[0]['title'] . '</a>';
    }
    
    /**
     * 
     *
     * @param   int     $arg  [$arg description]
     *
     * @return  string
     */
    public function  MM_article_completID(int  $arg)
    {
        // if ($arg > 0) {
        //     $story_limit = 1;
        //     $news_tab = News::prepa_aff_news("article", $arg, "");
        // } else {
        //     $news_tab = News::prepa_aff_news("index", "", "");
        //     $story_limit = abs($arg) + 1;
        // }
    
        // $aid = unserialize($news_tab[$story_limit]['aid']);
        // $informant = unserialize($news_tab[$story_limit]['informant']);
        // $datetime = unserialize($news_tab[$story_limit]['datetime']);
        // $title = unserialize($news_tab[$story_limit]['title']);
        // $counter = unserialize($news_tab[$story_limit]['counter']);
        // $topic = unserialize($news_tab[$story_limit]['topic']);
        // $hometext = unserialize($news_tab[$story_limit]['hometext']);
        // $notes = unserialize($news_tab[$story_limit]['notes']);
        // $morelink = unserialize($news_tab[$story_limit]['morelink']);
        // $topicname = unserialize($news_tab[$story_limit]['topicname']);
        // $topicimage = unserialize($news_tab[$story_limit]['topicimage']);
        // $topictext = unserialize($news_tab[$story_limit]['topictext']);
        // $s_id = unserialize($news_tab[$story_limit]['id']);
        
        // if ($aid) {
        //     ob_start();
        //         themeindex($aid, $informant, $datetime, $title, $counter, $topic, $hometext, $notes, $morelink, $topicname, $topicimage, $topictext, $s_id);
        //         $remp = ob_get_contents();
        //     ob_end_clean();
        // } else {
        //     $remp = "";
        // }
    
        // return $remp;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function  MM_article_complet(string $arg)
    {
        return Metalang::meta_lang("article_completID($arg)");
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function  MM_headlineID(string $arg) 
    {
        return @Boxe::headlines($arg, "");
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function  MM_headline(string $arg) 
    {
        return Metalang::meta_lang("headlineID($arg)");
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_list_mns()
    {
        // $MT_mns = '<ul class="list-group list-group-flush">';
       
        // foreach (DB::table('users')
        //             ->select('uname')
        //             ->where('mns', 1)
        //             ->get()
        //      as $user) 
        // {
        //     $MT_mns .= '<li class="list-group-item">
        //         <a href="'. site_url('minisite.php?op='. $user['uname']) .'" target="_blank">
        //             '. $user['uname'] .'
        //         </a>
        //     </li>';
        // }
    
        // $MT_mns .= "</ul>";
    
        // return $MT_mns;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_LastMember()
    {
        $result = DB::table('users')
                    ->select('uname')
                    ->orderBy('uid', 'desc')
                    ->limit(1)
                    ->offset(0)
                    ->first();

        return $result->uname;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_edito()
    {
        list($affich, $M_edito) = Edito::fab_edito();
    
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
    public function  MM_groupe_text(string $arg) 
    {
        $user = User::getUser();
    
        $affich = false;
        $remp = "";
    
        if ($arg != "") {
            if (Groupe::groupe_autorisation($arg, Groupe::valid_group($user))) {
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
    public function  MM_no_groupe_text(string $arg) 
    {
        $user = User::getUser();
    
        $affich = true;
        $remp = "";
    
        if ($arg != "") {
            if (Groupe::groupe_autorisation($arg, Groupe::valid_group($user))) {
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
    public function  MM_note()
    {
        return "!delete!";
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_note_admin()
    {
        $admin = Author::getAdmin();
    
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
    public function  MM_debugON()
    {
        $admin = true; //Author::getAdmin();

        if ($admin and Config::get('two_core::metalang.debug')) {
            $metalang = app('two_metalang');
            $metalang->set_metalang_debug_cycle(1);
            $metalang->set_metalang_debug(true);
            $metalang->set_metalang_debug_str("<br />");
            $metalang->set_metalang_debug_time(microtime(true));

            return;
        }
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_debugOFF()
    {
        $admin = true; //Author::getAdmin();

        if ($admin and Config::get('two_core::metalang.debug')) {
            $metalang = app('two_metalang');
            $metalang->metalang_debug_str .= "=> !DebugOFF!<br /><b>=> exec time for meta-lang : " . round(microtime(true) - $metalang->get_metalang_debug_time(), 4) . " / cycle(s) : ". $metalang->get_metalang_debug_cycle() ."</b><br />";
            $metalang->set_metalang_debug(false);
        
            echo $metalang->get_metalang_debug_str();

            return;
        }
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_forum_all()
    {
        // return @Forum::forum(Cache::Q_Select3(
        //     DB::table('catagories')
        //         ->select('*')
        //         ->orderBy('cat_id')
        //         ->get(), 
        //     3600, 
        //     Crypt::encrypt('categories(*)')
        // ));
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function  MM_forum_categorie(string $arg) 
    {
        // $query = DB::table('catagories')->select('*');

        // $countwhere = 0;
        // foreach (explode(",", Metalang::arg_filter($arg)) as $cat) {

        //     if($countwhere <= 0)  {
        //         $query->where('cat_id', '=', $cat);
        //     } else {
        //         $query->orWhere('cat_id', '=', $cat);
        //     }

        //     $countwhere++;
        // }

        // return @Forum::forum(Cache::Q_Select3(
        //     $query->get(), 
        //     3600, 
        //     Crypt::encrypt('categories_where_or(*)')
        // ));
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_forum_message()
    {
        $user = User::getUser();
    
        $ibid = "";
    
        if (!$user) {
            $ibid = __d('two_core', 'Devenez membre et vous disposerez de fonctions spécifiques : abonnements, forums spéciaux (cachés, membres, ..), statut de lecture, ...');
        }
    
        if ((Config::get('two_core::config.subscribe')) and ($user)) {
            $ibid = __d('two_core', 'Cochez un forum et cliquez sur le bouton pour recevoir un Email lors d\'une nouvelle soumission dans celui-ci.');
        }
    
        return $ibid;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_forum_recherche()
    {
        // return @Forum::searchblock();
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_forum_icones()
    {
        return '<img src="'. $this->theme->theme_image_row('forum/icons/red_folder.gif', 'assets/images/forum/icons/red_folder.gif') .'" border="" alt="" /> = ' . __d('two_core', 'Les nouvelles contributions depuis votre dernière visite.') . '
                <br />
                <img src="'. $this->theme->theme_image_row('forum/icons/folder.gif', 'assets/images/forum/icons/folder.gif') .'" border="" alt="" /> = ' . __d('two_core', 'Aucune nouvelle contribution depuis votre dernière visite.');
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_forum_subscribeON()
    {
        $ibid = "";
        
        if ((Config::get('two_core::config.subscribe')) and (User::getUser())) {
            if (Mailer::isbadmailuser(User::cookieUser(0)) === false) {
                $ibid = '<form action="'. site_url('forum.php') .'" method="post">
                <input type="hidden" name="op" value="maj_subscribe" />';
            }
        }
    
        return $ibid;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_forum_bouton_subscribe()
    {
        if ((Config::get('two_core::config.subscribe')) and (User::getUser())) {
            
            if (Mailer::isbadmailuser(User::cookieUser(0)) === false) {
                return '<input class="btn btn-secondary" type="submit" name="Xsub" value="' . __d('two_core', 'OK') . '" />';
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
    public function  MM_forum_subscribeOFF()
    {
        $ibid = "";
    
        if ((Config::get('two_core::config.subscribe')) and (User::getUser())) {   
            if (Mailer::isbadmailuser(User::cookieUser(0)) === false) {
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
    public function  MM_forum_subfolder(string $arg) 
    {
        // return Forum::sub_forum_folder(Metalang::arg_filter($arg));
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
    public function  MM_insert_flash(string $name, string $width, string $height, string $bgcol)
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
    public function  MM_login() 
    {
        $boxstuff = '
        <div class="card card-body m-3">
           <h5><a href="'. site_url('user.php?op=only_newuser') .'" role="button" title="' . __d('two_core', 'Nouveau membre') . '"><i class="fa fa-user-plus"></i>&nbsp;' . __d('two_core', 'Nouveau membre') . '</a></h5>
        </div>
        <div class="card card-body m-3">
           <h5 class="mb-3"><i class="fas fa-sign-in-alt fa-lg"></i>&nbsp;' . __d('two_core', 'Connexion') . '</h5>
           <form action="'. site_url('user.php') .'" method="post" name="userlogin_b">
              <div class="row g-2">
                 <div class="col-12">
                    <div class="mb-3 form-floating">
                       <input type="text" class="form-control" name="uname" id="inputuser_b" placeholder="' . __d('two_core', 'Identifiant') . '" required="required" />            
                       <label for="inputuser_b" >' . __d('two_core', 'Identifiant') . '</label>
                   </div>
                </div>
                <div class="col-12">
                   <div class="mb-0 form-floating">
                      <input type="password" class="form-control" name="pass" id="inputPassuser_b" placeholder="' . __d('two_core', 'Mot de passe') . '" required="required" />
                      <label for="inputPassuser_b">' . __d('two_core', 'Mot de passe') . '</label>
                      <span class="help-block small"><a href="'. site_url('user.php?op=forgetpassword') .'" role="button" title="' . __d('two_core', 'Vous avez perdu votre mot de passe ?') . '">' . __d('two_core', 'Vous avez perdu votre mot de passe ?') . '</a></span>
                    </div>
                 </div>
              </div>
              <input type="hidden" name="op" value="login" />
              <div class="mb-3 row">
                 <div class="ms-sm-auto">
                    <button class="btn btn-primary" type="submit" title="' . __d('two_core', 'Valider') . '">' . __d('two_core', 'Valider') . '</button>
                 </div>
              </div>
           </form>
        </div>';
    
        $user   = User::getUser();

        if (isset($user)) {
            $boxstuff = '<h5><a class="text-danger" href="'. site_url('user.php?op=logout') .'"><i class="fas fa-sign-out-alt fa-lg align-middle text-danger me-2"></i>' . __d('two_core', 'Déconnexion') . '</a></h5>';
        }
    
        return $boxstuff;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_administration() 
    {
        if (Author::getAdmin()) {
            return '<a href="'. site_url('admin.php').'">' . __d('two_core', 'Outils administrateur') . '</a>';
        } else {
            return '';
        }
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function  MM_admin_infos(string $arg) 
    {
        // $rowQ1 = Cache::Q_select3(
        //     DB::table('authors')
        //         ->select('url', 'email')
        //         ->where('aid', Metalang::arg_filter($arg))
        //         ->get(), 
        //     86400, 
        //     Crypt::encrypt('authors(url_email)')
        // );
        
        // if ($rowQ1[0]['url'] != '') {
        //     $auteur = '<a href="' . $rowQ1[0]['url'] . '">'. $arg .'</a>';
        // } elseif ($rowQ1[0]['email'] != '') {
        //     $auteur = '<a href="mailto:' . $rowQ1[0]['email'] . '">'. $arg .'</a>';
        // } else {
        //     $auteur = $arg;
        // }
    
        // return $auteur;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function  MM_theme_img(string $arg)
    {
        return Metalang::MM_img($arg);
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function  MM_rotate_img(string $arg)
    {
        mt_srand((int) microtime() * 1000000);
        
        $arg = Metalang::arg_filter($arg);
        $tab_img = explode(",", $arg);
    
        if (count($tab_img) > 1) {
            $imgnum = mt_rand(0, count($tab_img) - 1);
        } else if (count($tab_img) == 1) {
            $imgnum = 0;
        } else {
            $imgnum = -1;
        }
    
        if ($imgnum != -1) {
            $Xcontent = '<img src="'. $tab_img[$imgnum] .'" border="0" alt="'. $tab_img[$imgnum] .'" title="'. $tab_img[$imgnum] .'" />';
        }
    
        return $Xcontent;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_sql_nbREQ()
    {
        // global $sql_nbREQ;
    
        // return "SQL REQ : $sql_nbREQ";
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function  MM_top_stories(string $arg)
    {
        // $content = '';
        // $arg = Metalang::arg_filter($arg);
    
        // $limit = ($arg * 2);

        // $xtab = News::news_aff2("libre", 
        //     DB::table('stories')
        //         ->select('sid', 'catid', 'ihome', 'time')
        //         ->orderBy('counter', 'desc')
        //         ->limit($limit)
        //         ->offset(0)
        //         ->get(), 
        //     0, 
        //     $limit
        // );

        // $story_limit = 0;
        // while (($story_limit < $arg) and ($story_limit < sizeof($xtab))) {
            
        //     $sid        = $xtab[$story_limit]['sid'];
        //     $title      = $xtab[$story_limit]['title']; 
        //     $counter    = $xtab[$story_limit]['counter'];

        //     if ($counter > 0) {
        //         $content .= '<li class="ms-4 my-1">
        //             <a href="'. site_url('article.php?sid=' . $sid) .'" >
        //                 '. Language::aff_langue($title) .'
        //             </a>
        //             &nbsp;<span class="badge bg-secondary float-end">'. str::wrh($counter) .' '. __d('two_core', 'Fois') .'</span>
        //         </li>';
        //     }

        //     $story_limit++;
        // }
    
        // return $content;
    }
    
    /**
     * 
     *
     * @param   string  $file_name  [$file_name description]
     * @param   int     $topic      [$topic description]
     *
     * @return  string
     */
    public function  MM_comment_system(string $file_name, int $topic)
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
    public function  MM_top_commented_stories(string $arg)
    {
        // $content = '';
        // $arg = Metalang::arg_filter($arg);
        
        // $limit = ($arg * 2);

        // $xtab = News::news_aff2("libre", 
        //     DB::table('stories')
        //         ->select('sid', 'catid', 'ihome', 'time')
        //         ->orderBy('comments', 'desc')
        //         ->limit($limit)
        //         ->offset(0)
        //         ->get(), 
        //     0, 
        //     $limit
        // );

        // $story_limit = 0;
        // while (($story_limit < $arg) and ($story_limit < sizeof($xtab))) {
            
        //     $sid        = $xtab[$story_limit]['sid'];
        //     $title      = $xtab[$story_limit]['title'];
        //     $comments   = $xtab[$story_limit]['comments'];

        //     if ($comments > 0) {
        //         $content .= '<li class="ms-4 my-1">
        //             <a href="'. site_url('article.php?sid='. $sid) .'" >
        //                 '. Language::aff_langue($title) .'
        //             </a>
        //             &nbsp;<span class="badge bg-secondary float-end">'. Str::wrh($comments) .'</span>
        //         </li>';
        //     }

        //     $story_limit++;
        // }
    
        // return $content;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function  MM_top_categories(string $arg)
    {
        // $content = '';

        // foreach (DB::table('stories_cat')
        //             ->select('catid', 'title', 'counter')
        //             ->orderBy('counter', 'asc')
        //             ->limit(Metalang::arg_filter($arg))
        //             ->offset(0)
        //             ->get() as $storie) 
        // {
        //     if ($storie['counter'] > 0) {
        //         $content .= '<li class="ms-4 my-1">
        //             <a href="'. site_url('index.php?op=newindex&amp;catid='. $storie['catid']) .'" >
        //                 '. Language::aff_langue($storie['title']) .'
        //             </a>
        //             &nbsp;<span class="badge bg-secondary float-end">'. Str::wrh($storie['counter']) .'</span>
        //         </li>';
        //     }
        // }

        // return $content;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function  MM_top_sections(string $arg) 
    {
        // $content = '';

        // foreach (DB::table('seccont')
        //             ->select('artid', 'title', 'counter')
        //             ->orderBy('counter', 'desc')
        //             ->limit(Metalang::arg_filter($arg))
        //             ->offset(0)
        //             ->get()  as $seccont) 
        // {
        //     $content .= '<li class="ms-4 my-1">
        //         <a href="'. site_url('sections.php?op=viewarticle&amp;artid='. $seccont['artid']) .'" >
        //             '. Language::aff_langue($seccont['title']) .'
        //         </a>
        //         &nbsp;<span class="badge bg-secondary float-end">'. Str::wrh($seccont['counter']) .' '. __d('two_core', 'Fois') .'</span></li>';
        // }

        // return $content;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function  MM_top_reviews(string $arg)
    {
        // $content = '';

        // foreach (DB::table('reviews')
        //             ->select('id', 'title', 'hits')
        //             ->orderBy('hits', 'desc')
        //             ->limit(Metalang::arg_filter($arg))
        //             ->offset(0)
        //             ->get()as $review) 
        // {
        //     if ($review['hits'] > 0) {
        //         $content .= '<li class="ms-4 my-1">
        //             <a href="'. site_url('reviews.php?op=showcontent&amp;id=' . $review['id']) .'" >
        //                 '. $review['title'] .'
        //             </a>
        //             &nbsp;<span class="badge bg-secondary float-end">'. Str::wrh($review['hits']) .' '. __d('two_core', 'Fois') .'</span></li>';
        //     }
        // }

        // return $content;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function  MM_top_authors(string $arg) 
    {
        // $content = '';

        // foreach (DB::table('authors')
        //             ->select('aid', 'counter')
        //             ->orderBy('counter', 'desc')
        //             ->limit(Metalang::arg_filter($arg))
        //             ->offset(0)
        //             ->get() as $author)
        // {
        //     if ($author['counter'] > 0) {
        //         $content .= '<li class="ms-4 my-1">
        //             <a href="'. site_url('search.php?query=&amp;author='. $author['aid']) .'" >
        //                 '. $author['aid'] .'
        //             </a>
        //             &nbsp;<span class="badge bg-secondary float-end">'. Str::wrh($author['counter']) .'</span></li>';
        //     }
        // }
    
        // return $content;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function  MM_top_polls(string $arg)
    {
        // $content = '';

        // foreach (DB::table('poll_desc')
        //         ->select('pollID', 'pollTitle', 'voters')
        //         ->orderBy('voters', 'desc')
        //         ->limit(Metalang::arg_filter($arg))
        //         ->offset(0)
        //         ->get() as $poll) 
        // {
        //     if ($poll['voters'] > 0) {
        //         $content .= '<li class="ms-4 my-1">
        //             <a href="'. site_url('pollBooth.php?op=results&amp;pollID='. $poll['pollID']) .'" >
        //                 '. Language::aff_langue($poll['pollTitle']) .'
        //             </a>
        //             &nbsp;<span class="badge bg-secondary float-end">'. Str::wrh($poll['voters']) .'</span></li>';
        //     }
        // }
    
        // return $content;
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function  MM_top_storie_authors(string $arg) 
    {
        // $content = '';

        // foreach (DB::table('users')
        //             ->select('uname', 'counter')
        //             ->orderBy('counter', 'desc')
        //             ->limit(Metalang::arg_filter($arg))
        //             ->offset(0)
        //             ->get() as $user) 
        // {
        //     if ($user['counter'] > 0) {
        //         $content .= '<li class="ms-4 my-1">
        //             <a href="'. site_url('user.php?op=userinfo&amp;uname='. $user['uname']) .'" >
        //                 '. $user['uname'] .'
        //             </a>
        //             &nbsp;<span class="badge bg-secondary float-end">'. Str::wrh($user['counter']) .'</span></li>';
        //     }
        // }
    
        // return $content;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_topic_all()
    {
        // $aff = '';
        // $aff = '<div class="">';
    
        // foreach (DB::table('topics')
        //             ->select('topicid', 'topicname', 'topicimage', 'topictext')
        //             ->orderBy('topicname')
        //             ->get() as $topic) 
        // {
        //     $total_news = DB::table('stories')
        //                     ->select(DB::raw('COUNT(*) AS total'))
        //                     ->where('topic', $topic['topicid'])
        //                     ->first();

        //     $aff .= '
        //        <div class="col-sm-6 col-lg-4 mb-2 griditem px-2">
        //           <div class="card my-2">';
            
        //     if ((($topic['topicimage']) or ($topic['topicimage'] != '')) and (file_exists(Config::get('npds.tipath') . $topic['topicimage']))) {
        //         $aff .= '<img class="mt-3 ms-3 n-sujetsize" src="'. Config::get('npds.tipath') . $topic['topicimage'] .'" alt="topic_icon" />';
        //     }
    
        //     $aff .= '<div class="card-body">';
            
        //     if ($total_news['total'] != '0') {
        //         $aff .= '<a href="'. site_url('index.php?op=newtopic&amp;topic=' . $topic['topicid']) .'"><h4 class="card-title">'. language::aff_langue($topic['topicname']) .'</h4></a>';
        //     } else {
        //         $aff .= '<h4 class="card-title">'. Language::aff_langue($topic['topicname']) .'</h4>';
        //     }
    
        //     $aff .= '<p class="card-text">'. Language::aff_langue($topic['topictext']) .'</p>
        //                 <p class="card-text text-end">
        //                     <span class="small">' . __d('two_core', 'Nb. d'articles') . '</span> <span class="badge bg-secondary">'. $total_news['total'] .'</span>
        //                 </p>
        //              </div>
        //           </div>
        //        </div>';
        // }
    
        // $aff .= '</div>';
    
        // return $aff;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_topic_subscribeOFF()
    {
        return '<div class="mb-3 row">
                    <input type="hidden" name="op" value="maj_subscribe" />
                    <button class="btn btn-primary ms-3" type="submit" name="ok">' . __d('two_core', 'Valider') . '</button>
                </div>
            </fieldset>
            </form>';
    }
    
    /**
     * 
     *
     * @return  mixed
     */
    public function  MM_topic_subscribeON()
    {
        if (Config::get('two_core::config.subscribe') and User::getUser()) {
            if (Mailer::isbadmailuser(User::cookieUser(0)) === false) {
                return '<form action="'. site_url('topics.php') .'" method="post"><fieldset>';
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
    public function  MM_topic_subscribe(string $arg)
    {
        // $segment = Metalang::arg_filter($arg);
        // $aff = '';
        
        // if (Config::get('npds.subscribe')) {
        //     if (Users::getUser()) {
        //         $aff = '
        //           <div class="mb-3 row">';
                
        //         foreach (DB::table('topics')
        //                     ->select('topicid', 'topictext', 'topicname')
        //                     ->orderBy('topicname')
        //                     ->get() as $topic) 
        //         {

        //             $resultX = DB::table('subscribe')
        //                     ->select('topicid')
        //                     ->where('uid', Users::cookieUser(0))
        //                     ->where('topicid', $topic['topicid'])
        //                     ->get();

        //             if ($resultX) {
        //                 $checked = 'checked';
        //             } else {
        //                 $checked = '';
        //             }
    
        //             $aff .= '
        //                 <div class="'. $segment .'">
        //                    <div class="form-check">
        //                       <input type="checkbox" class="form-check-input" name="Subtopicid['. $topic['topicid'] .']" id="subtopicid'. $topic['topicid'] .'" '. $checked .' />
        //                       <label class="form-check-label" for="subtopicid'. $topic['topicid'] .'">'. Language::aff_langue($topic['topicname']) .'</label>
        //                    </div>
        //                 </div>';
        //         }
    
        //         $aff .= '</div>';
        //     }
        // }
    
        // return $aff;
    }
    
    /**
     * 
     *
     * @param   string  $id_yt_video  [$id_yt_video description]
     *
     * @return  string
     */
    public function  MM_yt_video(string $id_yt_video)
    {
        $content = '';
        $id_yt_video = Metalang::arg_filter($id_yt_video);
        
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
    public function  MM_espace_groupe(string  $gr, string  $t_gr, string  $i_gr)
    {
        $gr = Metalang::arg_filter($gr);
        $t_gr = Metalang::arg_filter($t_gr);
        $i_gr = Metalang::arg_filter($i_gr);
    
        return Groupe::fab_espace_groupe($gr, $t_gr, $i_gr);
    }
    
    /**
     * 
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string
     */
    public function  MM_blocnote(string $arg) 
    {
        // global $REQUEST_URI;
    
        // if (!stristr($REQUEST_URI, "admin.php")) {
        //     return @Block::oneblock($arg, "RB");
        // } else {
        //     return "";
        // }
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_forumP() 
    {
        // /*Sujet chaud*/
        // $hot_threshold = 10;
    
        // /*Nbre posts a afficher*/
        // $maxcount = "15";
    
        // $MM_forumP = '<table cellspacing="3" cellpadding="3" width="top" border="0">'
        //     . '<tr align="center" class="ligna">'
        //     . '<th width="5%">'. Language::aff_langue('[french]Etat[/french][english]State[/english]') .'</th>'
        //     . '<th width="20%">'. Language::aff_langue('[french]Forum[/french][english]Forum[/english]') .'</th>'
        //     . '<th width="30%">'. Language::aff_langue('[french]Sujet[/french][english]Topic[/english]') .'</th>'
        //     . '<th width="5%">'. Language::aff_langue('[french]RÃ©ponse[/french][english]Replie[/english]') .'</th>'
        //     . '<th width="20%">'. Language::aff_langue('[french]Dernier Auteur[/french][english]Last author[/english]') .'</th>'
        //     . '<th width="20%">'. Language::aff_langue('[french]Date[/french][english]Date[/english]') .'</th>'
        //     . '</tr>';
    
        // /*Requete liste dernier post*/
        // foreach (DB::table('posts')
        //             ->select(DB::raw('MAX(post_id)'))
        //             ->where('forum_id', '>', 0)
        //             ->groupey('topic_id')
        //             ->orderBy(DB::raw('MAX(post_id)'), 'desc')
        //             ->limit($maxcount)
        //             ->offset(0)
        //             ->get() as $post) 
        // {
        //     /*Requete detail dernier post*/
        //     $res = DB::table('posts')
        //                 ->select('posts.topic_id', 'posts.forum_id', 'posts.poster_id', 'posts.post_time', 'forumtopics.topic_title', 'forums.forum_name', 'forums.forum_type', 'forums.forum_pass', 'users.uname', 'forumtopics.topic_status')
        //                 ->join('forumtopics', 'forumtopics.topic_id', '=', 'posts.topic_id')
        //                 ->join('forums', 'forumtopics.forum_id', '=', 'forums.forum_id')
        //                 ->join('users', 'users.uid', '=', 'posts.poster_id')
        //                 ->where('posts.post_id', $post['post_id'])
        //                 ->limit(1)
        //                 ->first();

        //     if (($res['forum_type'] == "5") or ($res['forum_type'] == "7")) {
        //         $ok_affich = false;

        //         $tab_groupe = Groupe::valid_group(Users::getUser());
        //         $ok_affich = Groupe::groupe_forum($res['forum_pass'], $tab_groupe);
        //     } else {
        //         $ok_affich = true;
        //     }
    
        //     if ($ok_affich) {
    
        //         /*Nbre de postes par sujet*/
        //         $TableRep = DB::table('posts')
        //                     ->select('*')
        //                     ->where('forum_id', '>', 0)
        //                     ->where('topic_id', $res['topic_id'])
        //                     ->count();

        //         $replys = ($TableRep - 1);

        //         /*Gestion lu / non lu*/
        //         $sqlR = DB::table('forum_read')
        //                     ->select('rid')
        //                     ->where('topicid', $res['topic_id'])
        //                     ->where('uid', Users::cookieUser(0))
        //                     ->where('status', '!=', 0)
        //                     ->get();
    
        //         if ($replys >= $hot_threshold) {
        //             if ($sqlR == 0) {
        //                 $image = $this->theme->theme_image_row('forum/icons/hot_red_folder.gif', 'assets/images/forum/icons/hot_red_folder.gif');
        //             } else {
        //                 $image = $this->theme->theme_image_row('forum/icons/hot_folder.gif', 'assets/images/forum/icons/hot_folder.gif');
        //             }
        //         } else {
        //             if ($sqlR == 0) {
        //                 $image = $this->theme->theme_image_row('forum/icons/red_folder.gif', 'assets/images/forum/icons/red_folder.gif');
        //             } else {
        //                 $image = $this->theme->theme_image_row('forum/icons/folder.gif', 'assets/images/forum/icons/folder.gif');}
        //         }       

        //         if ($res['topic_status'] != 0) {
        //             $image = $this->theme->theme_image_row('forum/icons/lock.gif', 'assets/images/forum/icons/lock.gif');
        //         }

        //         $MM_forumP .= '<tr class="lignb">
        //             <td align="center">
        //                 <img src="'. $image .'">
        //             </td>
        //             <td>
        //                 <a href="'. site_url('viewforum.php?forum='. $res['forum_id']) .'">
        //                     '. $res['forum_name'] .'
        //                 </a>
        //             </td>
        //             <td>
        //                 <a href="'. site_url('viewtopic.php?topic='. $res['topic_id'] .'&forum='. $res['forum_id']) .'">
        //                     '. $res['topic_title'] .'
        //                 </a>
        //             </td>
        //             <td align="center">
        //                 '. $replys . '
        //             </td>
        //             <td>
        //                 <a href="'. site_url('user.php?op=userinfo&uname='. $res['uname']) .'">
        //                     '. $res['uname'] .'
        //                 </a>
        //             </td>
        //             <td align="center">
        //                 '. $res['post_time'] .'
        //             </td>
        //             </tr>';
        //     }
        // }
    
        // $MM_forumP .= '</table>';
    
        // return $MM_forumP;
    }
    
    /**
     * 
     *
     * @return  string
     */
    public function  MM_forumL() 
    {
        // /*Sujet chaud*/
        // $hot_threshold = 10;
    
        // /*Nbre posts a afficher*/
        // $maxcount = "10";
    
        // $MM_forumL = '<table cellspacing="3" cellpadding="3" width="top" border="0">'
        //     . '<tr align="center" class="ligna">'
        //     . '<td width="8%">' . Language::aff_langue('[french]Etat[/french][english]State[/english]') . '</td>'
        //     . '<td width="35%">' . Language::aff_langue('[french]Forum[/french][english]Forum[/english]') . '</td>'
        //     . '<td width="50%">' . Language::aff_langue('[french]Sujet[/french][english]Topic[/english]') . '</td>'
        //     . '<td width="7%">' . Language::aff_langue('[french]RÃ©ponses[/french][english]Replies[/english]') . '</td>'
        //     . '</tr>';
    
        // /*Requete liste dernier post*/
        // foreach (DB::table('posts')
        //             ->select(DB::raw('MAX(post_id)'))
        //             ->where('forum_id', '>', 0)
        //             ->groupey('topic_id')
        //             ->orderBy(DB::raw('MAX(post_id)', 'desc'))
        //             ->limit($maxcount)
        //             ->offset(0)
        //             ->get() as $post) 
        // {
        //     /*Requete detail dernier post*/
        //     $res = DB::table('posts')
        //         ->select('posts.topic_id', 'posts.forum_id', 'posts.poster_id', 'forumtopics.topic_title', 'forums.forum_name', 'forums.forum_type', 'forums.forum_pass', 'forumtopics.topic_status' )
        //         ->join('forumtopics', 'forumtopics.topic_id', '=', 'posts.topic_id')
        //         ->join('forums', 'forumtopics.forum_id', '=', 'forums.forum_id')
        //         ->where('posts.post_id', $post['post_id'])
        //         ->limit(1)
        //         ->get();

        //     if (($res['forum_type'] == "5") or ($res['forum_type'] == "7")) {
        //         $ok_affich = false;

        //         $tab_groupe = Groupe::valid_group(Users::getUser());
        //         $ok_affich = Groupe::groupe_forum($res['forum_pass'], $tab_groupe);
        //     } else {
        //         $ok_affich = true;
        //     }
    
        //     if ($ok_affich) {
    
        //         /*Nbre de postes par sujet*/
        //         $TableRep = DB::table('posts')
        //                         ->select('*')
        //                         ->where('forum_id', '>', 0)
        //                         ->where('topic_id', $res['topic_id'])
        //                         ->count();

        //         $replys = ($TableRep - 1);

        //         /*Gestion lu / non lu*/
        //         $sqlR = DB::table('forum_read')
        //                     ->select('rid')
        //                     ->where('topicid', $res['topic_id'])
        //                     ->where('uid', Users::cookieUser(0))
        //                     ->where('status', '!=',  0)
        //                     ->count();

        //         if ($replys >= $hot_threshold) {
        //             if ($sqlR == 0) {
        //                 $image = $this->theme->theme_image_row('forum/icons/hot_red_folder.gif', 'images/forum/icons/hot_red_folder.gif');
        //             } else {
        //                 $image = $this->theme->theme_image_row('forum/icons/hot_folder.gif', 'images/forum/icons/hot_folder.gif');
        //             }
        //         } else {
        //             if ($sqlR == 0) {
        //                 $image = $this->theme->theme_image_row('forum/icons/red_folder.gif', 'images/forum/icons/red_folder.gif');
        //             } else {
        //                 $image = $this->theme->theme_image_row('forum/icons/folder.gif', 'images/forum/icons/folder.gif');
        //             }
        //         }        

        //         if ($res['topic_status'] != 0) {
        //             $image = $this->theme->theme_image_row('forum/icons/lock.gif', 'images/forum/icons/lock.gif');
        //         }
    
        //         $MM_forumL .= '<tr class="lignb">
        //             <td align="center">
        //                 <img src="'. $image .'">
        //             </td>
        //             <td>
        //                 <a href="'. site_url('viewforum.php?forum='. $res['forum_id']) .'">
        //                 '. $res['forum_name'] .'
        //                 </a>
        //                 </td>
        //             <td>
        //                 <a href="'. site_url('viewtopic.php?topic='. $res['topic_id'] .'&forum='. $res['forum_id']) .'">
        //                 '. $res['topic_title'] .'
        //                 </a>
        //                 </td>
        //             <td align="center">
        //                 '. $replys .'
        //             </td>
        //             </tr>';
        //     }
        // }
    
        // $MM_forumL .= '</table>';
    
        // return $MM_forumL;
    }
    
    /**
     * 
     *
     * @param   string  $id_vm_video  [$id_vm_video description]
     *
     * @return  string
     */
    public function  MM_vm_video(string $id_vm_video) 
    {
        $content = '';
        $id_vm_video = Metalang::arg_filter($id_vm_video);
    
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
    public function  MM_dm_video(string $id_dm_video)
    {
        $content = '';
        $id_dm_video = Metalang::arg_filter($id_dm_video);
    
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
    public function  MM_noforbadmail()
    {
        $remp = '';
    
        if (Config::get('two_core::config.subscribe') and User::getUser()) {
            if (Mailer::isbadmailuser(User::cookieUser(0)) === true)
                $remp = '!delete!';
        }
    
        return $remp;
    }

    /**
     * [getPackageHint description]
     *
     * @param   [type]  $package  [$package description]
     *
     * @return  [type]            [return description]
     */
    protected function getPackageHint($package)
    {
        if (strpos($package, '/') === false) {
            return $package;
        }
    
        list ($vendor, $namespace) = explode('/', $package);
    
        $slug = (Str::length($namespace) <= 3) ? Str::lower($namespace) : Str::snake($namespace);
    
        return Str::lower($vendor) . '/' . $slug;
    }

}
