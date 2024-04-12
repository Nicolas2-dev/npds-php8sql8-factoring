<?php

declare(strict_types=1);

namespace npds\system\news;

use npds\system\logs\logs;
use npds\system\auth\users;
use npds\system\auth\groupe;
use npds\system\cache\cache;
use npds\system\support\str;
use npds\system\utility\code;
use npds\system\config\Config;
use npds\system\support\edito;
use npds\system\language\language;
use npds\system\language\metalang;
use npds\system\support\facades\DB;
use npds\system\subscribe\subscribe;

class news
{
 
    /**
     * Génération des fichiers ultramode.txt et net2zone.txt dans /cache
     *
     * @return  void
     */
    public static function ultramode(): void
    {
        $ultra = "storage/news/ultramode.txt";
        $netTOzone = "storage/news/net2zone.txt";

        $file = fopen("$ultra", "w");
        $file2 = fopen("$netTOzone", "w");

        fwrite($file, "General purpose self-explanatory file with news headlines\n");

        $storyhome = Config::get('npds..storyhome');
        $storynum = $storyhome;

        $xtab = static::news_aff('index', 
            DB::table('stories')
                ->select('sid', 'catid', 'ihome')
                ->where('ihome', 0)
                ->where('archive', 0)
                ->orderBy('sid', 'desc')
                ->limit($storynum)
                ->get(), 
            $storyhome, 
            ''
        );

        $story_limit = 0;
        while (($story_limit < $storynum) and ($story_limit < sizeof($xtab))) {

            $sid        = $xtab[$story_limit]['sid']; 
            $aid        = $xtab[$story_limit]['aid']; 
            $title      = $xtab[$story_limit]['title']; 
            $time       = $xtab[$story_limit]['time']; 
            $hometext   = $xtab[$story_limit]['hometext']; 
            $topic      = $xtab[$story_limit]['topic']; 

            $res = DB::table('topics')
                        ->select('topictext', 'topicimage')
                        ->where('topicid', $topic)
                        ->fist();

            $hometext = metalang::meta_lang(strip_tags($hometext));
            
            fwrite($file, "%%\n$title\n". site_url('article.php?sid='.$sid) ."\n$time\n$aid\n". $res['topictext'] ."\n$hometext\n" .$res['topicimage'] ."\n");
            fwrite($file2, "<NEWS>\n<NBX>". $res['topictext'] ."</NBX>\n<TITLE>" . stripslashes($title) . "</TITLE>\n<SUMMARY>$hometext</SUMMARY>\n<URL>". site_url('article.php?sid='. $sid) ."/</URL>\n<AUTHOR>" . $aid . "</AUTHOR>\n</NEWS>\n\n");
        
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
    public static function ctrl_aff(string|int $ihome, string|int $catid = 0): bool
    {
        $user = users::getUser();

        $affich = false;

        if ($ihome == -1 and (!$user)) {
            $affich = true;
        } elseif ($ihome == 0) {
            $affich = true;
        } elseif ($ihome == 1) {
            $affich = $catid > 0 ? false : true;
        } elseif (($ihome > 1) and ($ihome <= 127)) {
            $tab_groupe = groupe::valid_group($user);
            
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
 
    // note va disparaitre par la suite function deprecated !!!!! sert encore pour les fichier du front end !!!
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
    public static function news_aff(string $type_req, string $sel, string|int $storynum, string|int $oldnum): array
    { // pas stabilisé ...!
        global $NPDS_Prefix;
        
        // Astuce pour afficher le nb de News correct même si certaines News ne sont pas visibles (membres, groupe de membres)
        // En fait on * le Nb de News par le Nb de groupes
        $row_Q2 = cache::Q_select("SELECT COUNT(groupe_id) AS total FROM " . $NPDS_Prefix . "groupes", 86400);
        $NumG = $row_Q2[0];

        if ($NumG['total'] < 2) {
            $coef = 2;
        } else {
            $coef = $NumG['total'];
        }
        
        settype($storynum, "integer");
        
        if ($type_req == 'index') {
            $Xstorynum = $storynum * $coef;
            $result = cache::Q_select("SELECT sid, catid, ihome FROM " . $NPDS_Prefix . "stories $sel ORDER BY sid DESC LIMIT $Xstorynum", 3600);
            $Znum = $storynum;
        }

        if ($type_req == 'old_news') {
            //      $Xstorynum=$oldnum*$coef;
            $result = cache::Q_select("SELECT sid, catid, ihome, time FROM " . $NPDS_Prefix . "stories $sel ORDER BY time DESC LIMIT $storynum", 3600);
            $Znum = $oldnum;
        }

        if (($type_req == 'big_story') or ($type_req == 'big_topic')) {
            //      $Xstorynum=$oldnum*$coef;
            $result = cache::Q_select("SELECT sid, catid, ihome, counter FROM " . $NPDS_Prefix . "stories $sel ORDER BY counter DESC LIMIT $storynum", 0);
            $Znum = $oldnum;
        }

        if ($type_req == 'libre') {
            $Xstorynum = (int) $oldnum * $coef; //need for what ?
            $result = cache::Q_select("SELECT sid, catid, ihome, time FROM " . $NPDS_Prefix . "stories $sel", 3600);
            $Znum = $oldnum;
        }

        if ($type_req == 'archive') {
            $Xstorynum = $oldnum * $coef; //need for what ?
            $result = cache::Q_select("SELECT sid, catid, ihome FROM " . $NPDS_Prefix . "stories $sel", 3600);
            $Znum = $oldnum;
        }

        $ibid = 0;
        settype($tab, 'array');

        foreach ($result as $myrow) {

            $s_sid = $myrow['sid'];
            $catid = $myrow['catid'];
            $ihome = $myrow['ihome'];
           
            // not used !!!
            // if (array_key_exists('time', $myrow)) {
            //     $time = $myrow['time'];
            // }

            if ($ibid == $Znum) {
                break;
            }

            if ($type_req == "libre") {
                $catid = 0;
            }

            if ($type_req == "archive") {
                $ihome = 0;
            }
            
            if (static::ctrl_aff($ihome, $catid)) {
                if (($type_req == "index") or ($type_req == "libre")) {
                    $result2 = sql_query("SELECT sid, catid, aid, title, time, hometext, bodytext, comments, counter, topic, informant, notes FROM " . $NPDS_Prefix . "stories WHERE sid='$s_sid' AND archive='0'");
                }
                
                if ($type_req == "archive") {
                    $result2 = sql_query("SELECT sid, catid, aid, title, time, hometext, bodytext, comments, counter, topic, informant, notes FROM " . $NPDS_Prefix . "stories WHERE sid='$s_sid' AND archive='1'");
                }
                
                if ($type_req == "old_news") {
                    $result2 = sql_query("SELECT sid, title, time, comments, counter FROM " . $NPDS_Prefix . "stories WHERE sid='$s_sid' AND archive='0'");
                }
                
                if (($type_req == "big_story") or ($type_req == "big_topic")) {
                    $result2 = sql_query("SELECT sid, title FROM " . $NPDS_Prefix . "stories WHERE sid='$s_sid' AND archive='0'");
                }

                $tab[$ibid] = sql_fetch_row($result2);
                
                if (is_array($tab[$ibid])) {
                    $ibid++;
                }
                
                sql_free_result($result2);
            }
        }

        @sql_free_result($result);

        return $tab;
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
    public static function news_aff2(string $type_req, string|array|DB $sel, string|int $storynum, string|int $oldnum): array
    { 
        // pas stabilisé ...!
        // Astuce pour afficher le nb de News correct même si certaines News ne sont pas visibles (membres, groupe de membres)
        // En fait on * le Nb de News par le Nb de groupes

        // $row_Q2 = cache::Q_select3(
        //     DB::table('groupes')
        //         ->select(DB::raw('COUNT(groupe_id) AS total'))
        //         ->get(),
        //         86400,
        //         ''
        // );

        // if ($row_Q2[0]['total'] < 2) {
        //     $coef = 2;
        // } else {
        //     $coef = $row_Q2[0]['total'];
        // }

        //settype($storynum, "integer");
        
        if ($type_req == 'index') {
            $result = cache::Q_select3($sel, 3600, $type_req);
            $Znum = $storynum;
        }

        if ($type_req == 'old_news') {
            $result = cache::Q_select3($sel, 3600, $type_req);
            $Znum = $oldnum;
        }

        if (($type_req == 'big_story') or ($type_req == 'big_topic')) {
            $result = cache::Q_select3($sel, 3600, $type_req);
            $Znum = $oldnum;
        }

        if ($type_req == 'libre') {
            $result = cache::Q_select3($sel, 3600, $type_req);
            $Znum = $oldnum;
        }

        if ($type_req == 'archive') {
            $result = cache::Q_select3($sel, 3600, $type_req);
            $Znum = $oldnum;
        }

        settype($tab, 'array');

        $ibid = 0;        
        foreach ($result as $myrow) {

            if ($ibid == $Znum) {
                break;
            }

            if ($type_req == "libre") {
                $catid = 0;
            } else {
                $catid = $myrow['catid'];                
            }
            
            if ($type_req == "archive") {
                $ihome = 0;
            } else {
                $ihome = $myrow['ihome'];
            }
            
            if (static::ctrl_aff($ihome, $catid)) {
                if (($type_req == "index") or ($type_req == "libre")) {
                    
                    $result2 = DB::table('stories')
                        ->select('sid', 'catid', 'aid', 'title', 'time', 'hometext', 'bodytext', 'comments', 'counter', 'topic', 'informant', 'notes')
                        ->where('sid', $myrow['sid'])
                        ->where('archive', 0)
                        ->get();
                }
                
                if ($type_req == "archive") {
                    
                    $result2 = DB::table('stories')
                        ->select('sid', 'catid', 'aid', 'title', 'time', 'hometext', 'bodytext', 'comments', 'counter', 'topic', 'informant', 'notes')
                        ->where('sid', $myrow['sid'])
                        ->where('archive', 1)
                        ->get();
                }
                
                if ($type_req == "old_news") {

                    $result2 = DB::table('stories')
                        ->select('sid', 'title', 'time', 'comments', 'counter')
                        ->where('sid', $myrow['sid'])
                        ->where('archive', 0)
                        ->get();
                }
                
                if (($type_req == "big_story") or ($type_req == "big_topic")) {
                    
                    $result2 = DB::table('stories')
                        ->select('sid', 'title')
                        ->where('sid', $myrow['sid'])
                        ->where('archive', 0)
                        ->get();
                }

                if (!empty($result2[0])) {
                    $tab[$ibid] = $result2[0];
                } else {
                    return [];
                }
                
                if (is_array($tab[$ibid])) {
                    $ibid++;
                }
            }
        }

        return $tab;
    }
    
    /**
     * [automatednews description]
     *
     * @return  void
     */
    public static function automatednews(): void
    {
        $today = getdate(time() + ((int) Config::get('npds.gmt') * 3600));
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
        $min = $today['minutes'];

        $result = DB::table('autonews')
                    ->select('anid', 'date_debval')
                    ->where('date_debval', 'LIKE', ($year-$month).'%')
                    ->get();

        foreach ($result as $auto_news) {
            preg_match('#^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{1,2}):(\d{1,2})$#', $auto_news['date_debval'], $date);
           
            if (($date[1] <= $year) and ($date[2] <= $month) and ($date[3] <= $day)) {
                
                if (($date[4] < $hour) and ($date[5] >= $min) or ($date[4] <= $hour) and ($date[5] <= $min) or (($day - $date[3]) >= 1)) {
                    
                    $result2 = DB::table('autonews')
                                    ->select('catid', 'aid', 'title', 'hometext', 'bodytext', 'topic', 'informant', 'notes', 'ihome', 'date_finval', 'auto_epur')
                                    ->where('anid', $auto_news['anid'])
                                    ->get();

                    foreach ($result2 as $autonews) {

                        DB::table('stories')->insert(array(
                            'catid'         => $autonews['catid'],
                            'aid'           => $autonews['aid'],
                            'title'         => $subject = stripslashes(str::FixQuotes($autonews['title'])),
                            'time'          => 'now()',
                            'hometext'      => stripslashes(str::FixQuotes($autonews['hometext'])),
                            'bodytext'      => stripslashes(str::FixQuotes($autonews['bodytext'])),
                            'comments'      => 0,
                            'counter'       => 0,
                            'topic'         => $autonews['topic'],
                            'informant'     => $autonews['author'],
                            'notes'         => stripslashes(str::FixQuotes($autonews['notes'])),
                            'ihome'         => $autonews['ihome'],
                            'archive'       => 0,
                            'date_finval'   => $autonews['date_finval'],
                            'auto_epur'     => $autonews['epur'],
                        ));

                        DB::table('autonews')->where('anid', $auto_news['anid'])->delete();

                        if (Config::get('npds.subscribe')) {
                            subscribe::subscribe_mail('topic', $autonews['topic'], '', $subject, '');
                        }
                        
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
        $result = DB::table('stories')
                    ->select('sid', 'date_finval', 'auto_epur')
                    ->where('date_finval', 'LIKE', ($year-$month).'%')
                    ->get(); 

        foreach ($result as $storie) {
            preg_match('#^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{1,2}):(\d{1,2})$#', $storie['date_finval'], $date);
            
            if (($date[1] <= $year) and ($date[2] <= $month) and ($date[3] <= $day)) {
                if (($date[4] < $hour) and ($date[5] >= $min) or ($date[4] <= $hour) and ($date[5] <= $min)) {
                    
                    if ($storie['epur'] == 1) {
                        DB::table('stories')->where('sid', $storie['sid'])->delete();

                        if (file_exists("modules/comments/config/article.conf.php")) {
                            include("modules/comments/config/article.conf.php");
                            DB::table('posts')->where('forum_id', $forum)->where('topic_id', $topic)->delete();
                        }

                        logs::Ecr_Log('security', "removeStory (". $storie['sid'] .", epur) by automated epur : system", '');
                    } else {
                        DB::table('stories')->where('sid', $storie['sid'])->update(array(
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
     * @param   int|string     $catid    [$catid description]
     * @param   int|string     $marqeur  [$marqeur description]
     *
     * @return  void
     */
    public static function aff_news(string $op, int|string $catid, int|string $marqeur): void
    {
        $url = $op;

        if ($marqeur == '') {
            $marqeur = 0;
        }

        if ($op == 'edito-newindex') {
            if ($marqeur == 0) {
                edito::aff_edito();
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

        $news_tab = static::prepa_aff_news($op, $catid, $marqeur);

        // si le tableau $news_tab est vide alors return 
        if (is_null($news_tab)) {
            return;
        }

        $newscount = sizeof($news_tab);

        $story_limit = 0;
        while ($story_limit < $newscount) {            
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

            $story_limit++;

            themeindex($aid, $informant, $datetime, $title, $counter, $topic, $hometext, $notes, $morelink, $topicname, $topicimage, $topictext, $s_id);
        }

        $transl1 = translate("Page suivante");
        $transl2 = translate("Home");

        $cookie = users::cookieUser();

        $storynum = ((isset($cookie[3])) 
            ? $cookie[3] 
            : Config::get('npds.storyhome')
        );

        if ($op == 'categories') {
            if (sizeof($news_tab) == $storynum) {
                $marqeur = $marqeur + sizeof($news_tab);
                echo '<div class="text-end">
                    <a href="'. site_url('index.php?op='. $url .'&catid='. $catid .'&marqeur='. $marqeur) .'" class="page_suivante" >
                        '. $transl1 .'<i class="fa fa-chevron-right fa-lg ms-2" title="'. $transl1 .'" data-bs-toggle="tooltip"></i>
                    </a>
                </div>';
            } else {
                if ($marqeur >= $storynum) {
                    echo '<div class="text-end">
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
                echo '<div class="text-end">
                    <a href="'. site_url('index.php?op='. $url .'&catid='. $catid .'&marqeur='. $marqeur) .'" class="page_suivante" >
                        '. $transl1 .'<i class="fa fa-chevron-right fa-lg ms-2" title="'. $transl1 .'" data-bs-toggle="tooltip"></i>
                    </a>
                </div>';
            } else {
                if ($marqeur >= $storynum) {
                    echo '<div class="text-end">
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
                echo '<div align="right">
                    <a href="'. site_url('index.php?op=newtopic&topic='. $topic .'&marqeur='. $marqeur) .'" class="page_suivante" >
                        '. $transl1 .'<i class="fa fa-chevron-right fa-lg ms-2" title="'. $transl1 .'" data-bs-toggle="tooltip"></i>
                    </a>
                </div>';
            } else {
                if ($marqeur >= $storynum) {
                    echo '<div class="text-end">
                        <a href="'. site_url('index.php?op=newtopic&topic='. $topic .'&marqeur=0') .'" class="page_suivante" title="'. $transl2 .'">
                            '. $transl2 .'<i class="fa fa-chevron-right fa-lg ms-2" title="'. $transl2 .'" data-bs-toggle="tooltip"></i>
                        </a>
                    </div>';
                }
            }
        }
    }
 
    /**
     * Prépare, serialize et stock dans un tableau les news répondant aux critères
     * $op="" ET $catid="" : les news
     * $op="categories" ET $catid="catid" : les news de la catégorie catid
     * $op="article" ET $catid=ID_X : l'article d'ID X
     * $op="topics" ET $catid="topic" : Les news des sujets
     * 
     * @param   string  $op       [$op description]
     * @param   int|string     $catid    [$catid description]
     * @param   string|int     $marqeur  [$marqeur description]
     *
     * @return  array
     */
    public static function prepa_aff_news(string $op, int|string $catid, string|int $marqeur): array|null
    {
        $cookie = users::cookieUser();

        if (isset($cookie[3])) {
            $storynum = $cookie[3];
        } else {
            $storynum = Config::get('npds.storyhome');
        }

        if ($op == "categories") {
            DB::table('stories_cat')->where('catid', $catid)->update(array(
                'counter'       => DB::raw('counter+1'),
            ));

            settype($marqeur, "integer");
            
            if (!isset($marqeur)) {
                $marqeur = 0;
            }

            $xtab = static::news_aff2("libre",
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
            settype($marqeur, "integer");
            
            if (!isset($marqeur)) {
                $marqeur = 0;
            }

            $xtab = static::news_aff2("libre",
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
            settype($marqeur, "integer");
            
            if (!isset($marqeur)) {
                $marqeur = 0;
            }

            $xtab = static::news_aff2("libre", 
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

            $xtab = static::news_aff2("index", 
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

            $xtab = static::news_aff2("index", 
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

            $printP = '<a href="'. site_url('print.php?sid='. $sid) .'" class="me-3" title="'. translate("Page spéciale pour impression") .'" data-bs-toggle="tooltip" ><i class="fa fa-lg fa-print"></i></a>&nbsp;';
            $sendF = '<a href="'. site_url('friend.php?op=FriendSend&amp;sid='. $sid) .'" class="me-3" title="'. translate("Envoyer cet article à un ami") .'" data-bs-toggle="tooltip" ><i class="fa fa-lg fa-at"></i></a>';
            
            list($topicname, $topicimage, $topictext) = static::getTopics($sid);
            
            $title = language::aff_langue(stripslashes($title));
            $hometext = language::aff_langue(stripslashes($hometext));
            $notes = language::aff_langue(stripslashes($notes));
            $bodycount = strlen(strip_tags(language::aff_langue($bodytext), '<img>'));
            
            if ($bodycount > 0) {
                $bodycount = strlen(strip_tags(language::aff_langue($bodytext)));
                
                if ($bodycount > 0) {
                    $morelink[0] = str::wrh($bodycount) .' '. translate("caractères de plus");
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
                $morelink[3] = '<a href="'. site_url('article.php?sid='. $sid) .'" class="me-3"><i class="far fa-comment fa-lg" title="'. translate("Commentaires ?") .'" data-bs-toggle="tooltip"></i></a>';
            } elseif ($comments == 1) {
                $morelink[2] = $comments;
                $morelink[3] = '<a href="'. site_url('article.php?sid='. $sid) .'" class="me-3"><i class="far fa-comment fa-lg" title="'. translate("Commentaire") .'" data-bs-toggle="tooltip"></i></a>';
            } else {
                $morelink[2] = $comments;
                $morelink[3] = '<a href="'. site_url('article.php?sid='. $sid) .'" class="me-3" ><i class="far fa-comment fa-lg" title="'. translate("Commentaires") .'" data-bs-toggle="tooltip"></i></a>';
            }

            $morelink[4] = $printP;
            $morelink[5] = $sendF;
            
            if ($catid != 0) {
                $title1 = DB::table('stories_cat')
                            ->select('title')
                            ->where('catid', $catid)
                            ->first(); 

                // Attention à cela aussi ????? pourquoi pas compris !!!!
                $morelink[6] = '<a href="'. site_url('index.php?op=newcategory&amp;catid='. $catid) .'">&#x200b;'. language::aff_langue($title1['title']) .'</a>';
            } else {
                $morelink[6] = '';
            }

            $news_tab[$story_limit]['aid'] = serialize($aid);
            $news_tab[$story_limit]['informant'] = serialize($informant);
            $news_tab[$story_limit]['datetime'] = serialize($time);
            $news_tab[$story_limit]['title'] = serialize($title);
            $news_tab[$story_limit]['counter'] = serialize($counter);
            $news_tab[$story_limit]['topic'] = serialize($topic);
            $news_tab[$story_limit]['hometext'] = serialize(metalang::meta_lang(code::aff_code($hometext)));
            $news_tab[$story_limit]['notes'] = serialize(metalang::meta_lang(code::aff_code($notes)));
            $news_tab[$story_limit]['morelink'] = serialize($morelink);
            $news_tab[$story_limit]['topicname'] = serialize($topicname);
            $news_tab[$story_limit]['topicimage'] = serialize($topicimage);
            $news_tab[$story_limit]['topictext'] = serialize($topictext);
            $news_tab[$story_limit]['id'] = serialize($sid);
            
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
    public static function getTopics(string|int $sid): bool|array
    {
        if ($result = DB::table('stories')
                        ->select('topic')
                        ->where('sid', $sid)
                        ->first()) 
        {
            $res_topic = DB::table('topics')
                        ->select('topicname', 'topicimage', 'topictext')
                        ->where('topicid', $result['topic'])
                        ->first(); 

            if ($res_topic) {
                return [
                    $res_topic['topicname'], 
                    $res_topic['topicimage'], 
                    $res_topic['topictext']
                ];
            } else {
                return false;
            }

        } else {
            return false;
        }
    }
}
