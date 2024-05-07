<?php

declare(strict_types=1);

namespace Modules\TwoNews\Library\News;

use Two\Support\Facades\DB;
use Two\Support\Facades\Cache;
use Two\Foundation\Application;
use Two\Support\Facades\Config;
use Modules\TwoCore\Support\Code;
use Modules\TwoCore\Support\Sanitize;
use Modules\TwoNews\Support\SubscribeNew;
use Modules\TwoUsers\Support\Facades\User;
use Modules\TwoEdito\Support\Facades\Edito;
use Modules\TwoThemes\Support\Facades\Theme;
use Modules\TwoCore\Support\Facades\Language;
use Modules\TwoCore\Support\Facades\Metalang;
use Modules\TwoGroupes\Support\Facades\Groupe;


class NewsManager
{
 

    /**
     * The Application Instance.
     *
     * @var \Two\Application\Application
     */
    public $app;

    
    /**
     * Créez une nouvelle instance de Metas Manager.
     *
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Génération des fichiers ultramode.txt et net2zone.txt dans /cache
     *
     * @return  void
     */
    public function ultramode(): void
    {
        $ultra      = module_path('TwoNews', 'storage/news/ultramode.txt');
        $netTOzone  = module_path('TwoNews', 'storage/news/net2zone.txt');

        $file   = fopen("$ultra", "w");
        $file2  = fopen("$netTOzone", "w");

        fwrite($file, "General purpose self-explanatory file with news headlines\n");

        $storyhome = Config::get('two_core::config.storyhome');
        
        $xtab = $this->news_aff('index', 
            DB::table('stories')
                ->select('sid', 'catid', 'ihome')
                ->where('ihome', 0)
                ->where('archive', 0)
                ->orderBy('sid', 'desc')
                ->limit($storyhome)
                ->get(), 
            $storyhome, 
            ''
        );

        $story_limit = 0;
        while (($story_limit < $storyhome) and ($story_limit < sizeof($xtab))) {

            $sid        = $xtab[$story_limit]->sid; 
            $aid        = $xtab[$story_limit]->aid; 
            $title      = $xtab[$story_limit]->title; 
            $time       = $xtab[$story_limit]->time; 
            $hometext   = $xtab[$story_limit]->hometext; 
            $topic      = $xtab[$story_limit]->topic; 

            $res = DB::table('topics')
                        ->select('topictext', 'topicimage')
                        ->where('topicid', $topic)
                        ->fist();

            $hometext = Metalang::meta_lang(strip_tags($hometext));
            
            fwrite($file, "%%\n$title\n". site_url('article.php?sid='.$sid) ."\n$time\n$aid\n". $res->topictext ."\n$hometext\n" .$res->topicimage ."\n");
            fwrite($file2, "<NEWS>\n<NBX>". $res->topictext ."</NBX>\n<TITLE>" . stripslashes($title) . "</TITLE>\n<SUMMARY>$hometext</SUMMARY>\n<URL>". site_url('article.php?sid='. $sid) ."/</URL>\n<AUTHOR>" . $aid . "</AUTHOR>\n</NEWS>\n\n");
        
            $story_limit++;
        }
        
        fclose($file);
        fclose($file2);
    }
 
    /**
     * Gestion + fine des destinataires (-1, 0, 1, 2 -> 127, -127)
     *
     * @param   string|int  $ihome  [$ihome description]
     * @param   string|int     $catid  [$catid description]
     *
     * @return  bool
     */
    public function ctrl_aff(string|int $ihome, string|int $catid = 0): bool
    {
        $user = User::getUser();

        $affich = false;

        if ($ihome == -1 and (!$user)) {
            $affich = true;

        } elseif ($ihome == 0) {
            $affich = true;

        } elseif ($ihome == 1) {
            $affich = $catid > 0 ? false : true;

        } elseif (($ihome > 1) and ($ihome <= 127)) {
            $tab_groupe = Groupe::valid_group($user);
            
            if ($tab_groupe) {
                foreach ($tab_groupe as $groupevalue) {
                    
                    if ($groupevalue == $ihome) {
                        $affich = true;
                        break;
                    }
                }
            }
        } else {
            if ($user) {
                $affich = true;
            }
        }

        return $affich;
    }

    /**
     * Une des fonctions fondamentales de NPDS
     * assure la gestion de la selection des News en fonctions des critères de publication
     *
     * @param   string  $type_req  [$type_req description]
     * @param   string  $sel       [$sel description]
     * @param   string             [ description]
     * @param   int     $storynum  [$storynum description]
     * @param   string             [ description]
     * @param   int     $oldnum    [$oldnum description]
     *
     * @return  array
     */
    public function news_aff(string $type_req, string|array|DB $sel, string|int $storynum, string|int $oldnum): array
    { 
        // pas stabilisé ...!
        // Astuce pour afficher le nb de News correct même si certaines News ne sont pas visibles (membres, groupe de membres)
        // En fait on * le Nb de News par le Nb de groupes

        // $row_Q2 = $this->new_Cache_Select(
        //     DB::table('groupes')
        //         ->select(DB::raw('COUNT(groupe_id) AS total'))
        //         ->first(),
        //         86400,
        //         ''
        // );

        // if ($row_Q2->total < 2) {
        //     $coef = 2;
        // } else {
        //     $coef = $row_Q2->total;
        // }
 
        if ($type_req == 'index') {
            $result = $this->new_Cache_Select($sel, 3600, $type_req);
            $Znum = $storynum;
        }

        if ($type_req == 'old_news') {
            $result = $this->new_Cache_Select($sel, 3600, $type_req);
            $Znum = $oldnum;
        }

        if (($type_req == 'big_story') or ($type_req == 'big_topic')) {
            $result = $this->new_Cache_Select($sel, 3600, $type_req);
            $Znum = $oldnum;
        }

        if ($type_req == 'libre') {
            $result = $this->new_Cache_Select($sel, 3600, $type_req);
            $Znum = $oldnum;
        }

        if ($type_req == 'archive') {
            $result = $this->new_Cache_Select($sel, 3600, $type_req);
            $Znum = $oldnum;
        }

        $ibid = 0;        
        foreach ($result as $myrow) {

            if ($ibid == $Znum) {
                break;
            }

            if ($type_req == "libre") {
                $catid = 0;
            } else {
                $catid = $myrow->catid;                
            }
            
            if ($type_req == "archive") {
                $ihome = 0;
            } else {
                $ihome = $myrow->ihome;
            }

            if ($this->ctrl_aff($ihome, $catid)) {
                if (($type_req == "index") or ($type_req == "libre")) {
                    
                    $res_stories = DB::table('stories')
                        ->select('sid', 'catid', 'aid', 'title', 'time', 'hometext', 'bodytext', 'comments', 'counter', 'topic', 'informant', 'notes')
                        ->where('sid', $myrow->sid)
                        ->where('archive', 0)
                        ->get();
                }
                
                if ($type_req == "archive") {
                    
                    $res_stories = DB::table('stories')
                        ->select('sid', 'catid', 'aid', 'title', 'time', 'hometext', 'bodytext', 'comments', 'counter', 'topic', 'informant', 'notes')
                        ->where('sid', $myrow->sid)
                        ->where('archive', 1)
                        ->get();
                }
                
                if ($type_req == "old_news") {

                    $res_stories = DB::table('stories')
                        ->select('sid', 'title', 'time', 'comments', 'counter')
                        ->where('sid', $myrow->sid)
                        ->where('archive', 0)
                        ->get();
                }

                if (($type_req == "big_story") or ($type_req == "big_topic")) {
                    
                    $res_stories = DB::table('stories')
                        ->select('sid', 'title')
                        ->where('sid', $myrow->sid)
                        ->where('archive', 0)
                        ->get();
                }

                if (!empty($res_stories)) {
                    $tab[$ibid] = (array) $res_stories[0];
                } else {
                    $tab[$ibid] = [];
                }
                
                if (is_array($tab[$ibid])) {
                    $ibid++;
                }
            }
        }

        return $tab;
    }
    
    /**
     * [new_Cache_Select description]
     *
     * @param   string  $type_req  [$type_req description]
     * @param   string             [ description]
     * @param   array              [ description]
     * @param   DB      $sel       [$sel description]
     * @param   int     $cache     [$cache description]
     *
     * @return  [type]             [return description]
     */
    public function new_Cache_Select(string|array|DB $sel, int $cache, string $type_req)
    {
        return Cache::remember('news'. $type_req, $cache, function () use ($sel) {
            return $sel;
        });
    }

    /**
     * [automatednews description]
     *
     * @return  void
     */
    public function automatednews(): void
    {
        $today = getdate(time() + ((int) Config::get('two_core::config.gmt') * 3600));
        
        $day = $today['mday'];

        if ($day < 10) {
            $day = "0$day";
        }

        $month = $today['mon'];

        if ($month < 10) {
            $month = "0$month";
        }

        $year = $today['year'];
        $hour = $today['hours'];
        $min  = $today['minutes'];

        foreach (DB::table('autonews')
                    ->select('anid', 'date_debval')
                    ->where('date_debval', 'LIKE', ($year-$month).'%')
                    ->get() as $auto_news) 
        {
            preg_match('#^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{1,2}):(\d{1,2})$#', $auto_news->date_debval, $date);
           
            if (($date[1] <= $year) and ($date[2] <= $month) and ($date[3] <= $day)) {
                
                if (($date[4] < $hour) and ($date[5] >= $min) or ($date[4] <= $hour) and ($date[5] <= $min) or (($day - $date[3]) >= 1)) {

                    foreach (DB::table('autonews')
                                    ->select('catid', 'aid', 'title', 'hometext', 'bodytext', 'topic', 'informant', 'notes', 'ihome', 'date_finval', 'auto_epur')
                                    ->where('anid', $auto_news->anid)
                                    ->get() as $autonews) 
                    {

                        DB::table('stories')->insert(array(
                            'catid'         => $autonews->catid,
                            'aid'           => $autonews->aid,
                            'title'         => $subject = stripslashes(Sanitize::FixQuotes($autonews->title)),
                            'time'          => 'now()',
                            'hometext'      => stripslashes(Sanitize::FixQuotes($autonews->hometext)),
                            'bodytext'      => stripslashes(Sanitize::FixQuotes($autonews->bodytext)),
                            'comments'      => 0,
                            'counter'       => 0,
                            'topic'         => $autonews->topic,
                            'informant'     => $autonews->author,
                            'notes'         => stripslashes(Sanitize::FixQuotes($autonews->notes)),
                            'ihome'         => $autonews->ihome,
                            'archive'       => 0,
                            'date_finval'   => $autonews->date_finval,
                            'auto_epur'     => $autonews->epur,
                        ));

                        DB::table('autonews')->where('anid', $auto_news->anid)->delete();

                        if (Config::get('npds.subscribe')) {
                            SubscribeNew::subscribe_mail($autonews->topic, '', $subject, '');
                        }
                        
                        // Note : a revoir
                        // Réseaux sociaux
                        if (file_exists('modules/npds_twi/npds_to_twi.php')) {
                            include('modules/npds_twi/npds_to_twi.php');
                        }

                        if (file_exists('modules/npds_fbk/npds_to_fbk.php')) {
                            include('modules/npds_twi/npds_to_fbk.php');
                        }
                        // Réseaux sociaux
                    }
                }
            }
        }

        // Purge automatique
        foreach (DB::table('stories')
                    ->select('sid', 'date_finval', 'auto_epur')
                    ->where('date_finval', 'LIKE', ($year-$month).'%')
                    ->get() as $storie) 
        {
            preg_match('#^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{1,2}):(\d{1,2})$#', $storie->date_finval, $date);
            
            if (($date[1] <= $year) and ($date[2] <= $month) and ($date[3] <= $day)) {
                if (($date[4] < $hour) and ($date[5] >= $min) or ($date[4] <= $hour) and ($date[5] <= $min)) {
                    
                    if ($storie->epur == 1) {
                        DB::table('stories')->where('sid', $storie->sid)->delete();

                        // Note : A revoir pour include commentaire
                        if (file_exists("modules/comments/config/article.conf.php")) {
                            include("modules/comments/config/article.conf.php");
                            
                            DB::table('posts')
                                ->where('forum_id', $forum)
                                ->where('topic_id', $topic)
                                ->delete();
                        }

                        Ecr_Log('security', "removeStory (". $storie->sid .", epur) by automated epur : system", '');
                    } else {
                        DB::table('stories')->where('sid', $storie->sid)->update(array(
                            'archive'       => 1,
                        ));

                    }
                }
            }
        }
    }

    /**
     * [aff_news description]
     *
     * @param   string  $op       [$op description]
     * @param   int     $catid    [$catid description]
     * @param   int     $marqeur  [$marqeur description]
     *
     * @return  void
     */
    public function aff_news(string $op, int $catid, int $marqeur): string|bool
    {
        $url = $op;

        if ($marqeur == '') {
            $marqeur = 0;
        }

        if ($op == 'edito-newindex') {
            if ($marqeur == 0) {
                Edito::aff_edito();
            }

            $op = 'news';
        }

        if ($op == "newindex") {
            $op = $catid == '' ? 'news' : 'categories';
        }

        if ($op == 'newtopic') {
            $op = 'topics';
        }

        if ($op == 'newcategory') {
            $op = 'categories';
        }

        $news_tab = $this->prepa_aff_news($op, $catid, $marqeur);

        // si le tableau $news_tab est vide alors return 
        if (is_null($news_tab)) {
            return false;
        }

        $newscount = sizeof($news_tab);

        $list_news = '';

        $story_limit = 0;
        while ($story_limit < $newscount) {            
            $aid            = unserialize($news_tab[$story_limit]['aid']);
            $informant      = unserialize($news_tab[$story_limit]['informant']);
            $datetime       = unserialize($news_tab[$story_limit]['datetime']);
            $title          = unserialize($news_tab[$story_limit]['title']);
            $counter        = unserialize($news_tab[$story_limit]['counter']);
            $topic          = unserialize($news_tab[$story_limit]['topic']);
            $hometext       = unserialize($news_tab[$story_limit]['hometext']);
            $notes          = unserialize($news_tab[$story_limit]['notes']);
            $morelink       = unserialize($news_tab[$story_limit]['morelink']);
            $topicname      = unserialize($news_tab[$story_limit]['topicname']);
            $topicimage     = unserialize($news_tab[$story_limit]['topicimage']);
            $topictext      = unserialize($news_tab[$story_limit]['topictext']);
            $s_id           = unserialize($news_tab[$story_limit]['id']);

            $list_news .= Theme::themeindex($aid, $informant, $datetime, $title, $counter, $topic, $hometext, $notes, $morelink, $topicname, $topicimage, $topictext, $s_id);
        
            $story_limit++;
        }

        $transl1 = translate("Page suivante");
        $transl2 = translate("Home");

        $cookie = User::cookieUser();

        $storynum = ((isset($cookie[3])) 
            ? $cookie[3] 
            : Config::get('two_core::config.storyhome')
        );

        if ($op == 'categories') {
            if (sizeof($news_tab) == $storynum) {
                $marqeur = $marqeur + sizeof($news_tab);
                $list_news .=  '<div class="text-end">
                    <a href="'. site_url('index.php?op='. $url .'&catid='. $catid .'&marqeur='. $marqeur) .'" class="page_suivante" >
                        '. $transl1 .'<i class="fa fa-chevron-right fa-lg ms-2" title="'. $transl1 .'" data-bs-toggle="tooltip"></i>
                    </a>
                </div>';
            } else {
                if ($marqeur >= $storynum) {
                    $list_news .=  '<div class="text-end">
                        <a href="'. site_url('index.php?op='. $url .'&catid='. $catid .'&marqeur=0') .'" class="page_suivante" title="'. $transl2 .'">
                            '. $transl2 .'<i class="fa fa-chevron-right fa-lg ms-2" title="'. $transl2 .'" data-bs-toggle="tooltip"></i>
                        </a>
                    </div>';
                }
            }
        }

        if ($op == 'news') {
            if (sizeof($news_tab) == $storynum) {
                $marqeur = $marqeur + sizeof($news_tab);
                $list_news .=  '<div class="text-end">
                    <a href="'. site_url('index.php?op='. $url .'&catid='. $catid .'&marqeur='. $marqeur) .'" class="page_suivante" >
                        '. $transl1 .'<i class="fa fa-chevron-right fa-lg ms-2" title="'. $transl1 .'" data-bs-toggle="tooltip"></i>
                    </a>
                </div>';
            } else {
                if ($marqeur >= $storynum) {
                    $list_news .=  '<div class="text-end">
                        <a href="'. site_url('index.php?op='. $url .'&catid='. $catid .'&marqeur=0') .'" class="page_suivante" title="'. $transl2 .'">
                            '. $transl2 .'<i class="fa fa-chevron-right fa-lg ms-2" title="'. $transl2 .'" data-bs-toggle="tooltip"></i>
                        </a>
                    </div>';
                }
            }
        }

        if ($op == 'topics') {
            if (sizeof($news_tab) == $storynum) {
                $marqeur = $marqeur + sizeof($news_tab);
                $list_news .=  '<div align="right">
                    <a href="'. site_url('index.php?op=newtopic&topic='. $topic .'&marqeur='. $marqeur) .'" class="page_suivante" >
                        '. $transl1 .'<i class="fa fa-chevron-right fa-lg ms-2" title="'. $transl1 .'" data-bs-toggle="tooltip"></i>
                    </a>
                </div>';
            } else {
                if ($marqeur >= $storynum) {
                    $list_news .=  '<div class="text-end">
                        <a href="'. site_url('index.php?op=newtopic&topic='. $topic .'&marqeur=0') .'" class="page_suivante" title="'. $transl2 .'">
                            '. $transl2 .'<i class="fa fa-chevron-right fa-lg ms-2" title="'. $transl2 .'" data-bs-toggle="tooltip"></i>
                        </a>
                    </div>';
                }
            }
        }

        return $list_news;
    }
 
    /**
     * Prépare, serialize et stock dans un tableau les news répondant aux critères
     * $op="" ET $catid="" : les news
     * $op="categories" ET $catid="catid" : les news de la catégorie catid
     * $op="article" ET $catid=ID_X : l'article d'ID X
     * $op="topics" ET $catid="topic" : Les news des sujets
     * 
     * @param   string  $op       [$op description]
     * @param   int     $catid    [$catid description]
     * @param   int     $marqeur  [$marqeur description]
     *
     * @return  array
     */
    public function prepa_aff_news(string $op, int $catid, int $marqeur): array|null
    {
        $cookie = User::cookieUser();

        if (isset($cookie[3])) {
            $storynum = $cookie[3];
        } else {
            $storynum = Config::get('two_core::config.storyhome');
        }

        if ($op == "categories") {
            DB::table('stories_cat')->where('catid', $catid)->update(array(
                'counter'       => DB::raw('counter+1'),
            ));

            if (!isset($marqeur)) {
                $marqeur = 0;
            }

            $xtab = $this->news_aff("libre",
                DB::table('stories')
                    ->select('sid', 'catid', 'ihome', 'time')
                    ->where('catid', $catid)
                    ->where('archive', 0)
                    ->orderBy('sid', 'desc')
                    ->limit($storynum)
                    ->offset($marqeur)
                    ->get(), 
                "", 
                "-1"
            );

            $storynum = sizeof($xtab);
        } elseif ($op == "topics") {
            
            if (!isset($marqeur)) {
                $marqeur = 0;
            }

            $xtab = $this->news_aff("libre",
                DB::table('stories')
                    ->select('sid', 'catid', 'ihome', 'time')
                    ->where('topic', $catid)
                    ->where('archive', 0)
                    ->orderBy('sid', 'desc')
                    ->limit($storynum)
                    ->offset($marqeur)
                    ->get(), 
                "", 
                "-1"
            );

            $storynum = sizeof($xtab);
        } elseif ($op == "news") {
            
            if (!isset($marqeur)) {
                $marqeur = 0;
            }

            $xtab = $this->news_aff("libre", 
                DB::table('stories')
                    ->select('sid', 'catid', 'ihome', 'time')
                    ->where('ihome', '!=', 1)
                    ->where('archive', 0)
                    ->orderBy('sid', 'desc')
                    ->limit($storynum)
                    ->offset($marqeur)
                    ->get(), 
                "", 
                "-1"
            );

            $storynum = sizeof($xtab);
        } elseif ($op == "article") {

            $xtab = $this->news_aff("index", 
                DB::table('stories')
                    ->select('sid', 'catid', 'ihome')
                    ->where('ihome', '!=', 1)
                    ->where('sid', $catid)
                    ->orderBy('sid', 'desc')
                    ->limit($storynum)
                    ->get(),
                1,
                ""
            );
        } else {

            $xtab = $this->news_aff("index", 
                DB::table('stories')
                    ->select('sid', 'catid', 'ihome')
                    ->where('ihome', '!=', 1)
                    ->where('archive', 0)
                    ->orderBy('sid', 'desc')
                    ->limit($storynum)
                    ->get(), 
                $storynum, 
                ""
            );
        }

        $story_limit = 0;
        while (($story_limit < $storynum) and ($story_limit < sizeof($xtab))) {
            
            $sid        = $xtab[$story_limit]['sid'];
            $catid      = $xtab[$story_limit]['catid']; 
            $aid        = $xtab[$story_limit]['aid'];
            $title      = $xtab[$story_limit]['title']; 
            $time       = $xtab[$story_limit]['time'];
            $hometext   = $xtab[$story_limit]['hometext'];
            $bodytext   = $xtab[$story_limit]['bodytext'];
            $comments   = $xtab[$story_limit]['comments'];
            $counter    = $xtab[$story_limit]['counter'];
            $topic      = $xtab[$story_limit]['topic'];
            $informant  = $xtab[$story_limit]['informant'];
            $notes      = $xtab[$story_limit]['notes'];

            $printP = '<a href="'. site_url('print.php?sid='. $sid) .'" class="me-3" title="'. translate("Page spéciale pour impression") .'" data-bs-toggle="tooltip" >
                    <i class="fa fa-lg fa-print"></i>
                </a>&nbsp;';
            
            $sendF = '<a href="'. site_url('friend.php?op=FriendSend&amp;sid='. $sid) .'" class="me-3" title="'. translate("Envoyer cet article à un ami") .'" data-bs-toggle="tooltip" >
                    <i class="fa fa-lg fa-at"></i>
                </a>';
            
            list($topicname, $topicimage, $topictext) = $this->getTopics($sid);
            
            $title      = Language::aff_langue(stripslashes($title));
            $hometext   = Language::aff_langue(stripslashes($hometext));
            $notes      = Language::aff_langue(stripslashes($notes));
            $bodycount  = strlen(strip_tags(Language::aff_langue($bodytext), '<img>'));
            
            if ($bodycount > 0) {
                $bodycount = strlen(strip_tags(Language::aff_langue($bodytext)));
                
                if ($bodycount > 0) {
                    $morelink[0] = Sanitize::wrh($bodycount) .' '. translate("caractères de plus");
                } else {
                    $morelink[0] = ' ';
                }

                $morelink[1] = ' <a href="'. site_url('article.php?sid='. $sid) .'" >'. translate("Lire la suite...") .'</a>';
            } else {
                $morelink[0] = '';
                $morelink[1] = '';
            }

            if ($comments == 0) {
                $morelink[2] = 0;
                $morelink[3] = '<a href="'. site_url('article.php?sid='. $sid) .'" class="me-3">
                        <i class="far fa-comment fa-lg" title="'. translate("Commentaires ?") .'" data-bs-toggle="tooltip"></i>
                    </a>';

            } elseif ($comments == 1) {
                $morelink[2] = $comments;
                $morelink[3] = '<a href="'. site_url('article.php?sid='. $sid) .'" class="me-3">
                        <i class="far fa-comment fa-lg" title="'. translate("Commentaire") .'" data-bs-toggle="tooltip"></i>
                    </a>';

            } else {
                $morelink[2] = $comments;
                $morelink[3] = '<a href="'. site_url('article.php?sid='. $sid) .'" class="me-3" >
                        <i class="far fa-comment fa-lg" title="'. translate("Commentaires") .'" data-bs-toggle="tooltip"></i>
                    </a>';
            }

            $morelink[4] = $printP;
            $morelink[5] = $sendF;
            
            if ($catid != 0) {
                $title1 = DB::table('stories_cat')
                            ->select('title')
                            ->where('catid', $catid)
                            ->first(); 

                // Attention à cela aussi ????? pourquoi pas compris !!!!
                $morelink[6] = '<a href="'. site_url('index.php?op=newcategory&amp;catid='. $catid) .'">&#x200b;'. Language::aff_langue($title1->title) .'</a>';
            } else {
                $morelink[6] = '';
            }

            $news_tab[$story_limit]['aid']          = serialize($aid);
            $news_tab[$story_limit]['informant']    = serialize($informant);
            $news_tab[$story_limit]['datetime']     = serialize($time);
            $news_tab[$story_limit]['title']        = serialize($title);
            $news_tab[$story_limit]['counter']      = serialize($counter);
            $news_tab[$story_limit]['topic']        = serialize($topic);
            $news_tab[$story_limit]['hometext']     = serialize(Metalang::meta_lang(Code::aff_code($hometext)));
            $news_tab[$story_limit]['notes']        = serialize(Metalang::meta_lang(Code::aff_code($notes)));
            $news_tab[$story_limit]['morelink']     = serialize($morelink);
            $news_tab[$story_limit]['topicname']    = serialize($topicname);
            $news_tab[$story_limit]['topicimage']   = serialize($topicimage);
            $news_tab[$story_limit]['topictext']    = serialize($topictext);
            $news_tab[$story_limit]['id']           = serialize($sid);
            
            $story_limit++;
        }

        if (isset($news_tab)) {
            return $news_tab;
        }
    }
 
    /**
     * Retourne le nom, l'image et le texte d'un topic ou False
     *
     * @param   string|int   $sid  [$sid description]
     *
     * @return  bool|array
     */
    public function getTopics(string|int $sid): bool|array
    {
        if ($result = DB::table('stories')
                        ->select('topic')
                        ->where('sid', $sid)
                        ->first()) 
        {
            if ($res_topic = DB::table('topics')
                        ->select('topicname', 'topicimage', 'topictext')
                        ->where('topicid', $result->topic)
                        ->first()) 
            {
                return [
                    $res_topic->topicname, 
                    $res_topic->topicimage, 
                    $res_topic->topictext
                ];
            } else {
                return false;
            }

        } else {
            return false;
        }
    }
}
