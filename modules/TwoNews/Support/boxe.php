<?php

use Two\Support\Facades\DB;

use Two\Support\Facades\Config;
use Modules\TwoNews\Support\Facades\News;
use Modules\TwoUsers\Support\Facades\User;
use Modules\TwoThemes\Support\Facades\Theme;
use Modules\TwoCore\Support\Facades\Language;



if (! function_exists('ldNews'))
{
    /**
     * Bloc Anciennes News
     * syntaxe : function#oldNews
     * params#$storynum,lecture (affiche le NB de lecture) - facultatif
     *
     * @param   string  $storynum  [$storynum description]
     * @param   string  $typ_aff   [$typ_aff description]
     *
     * @return  void               [return description]
     */
    function oldNews(string $storynum, ?string $typ_aff = ''): void
    {
        global $categories, $cat;

        $boxstuff = '<ul class="list-group">';

        $user   = User::getUser();
        $cookie = User::cookieUser(3);

        //$storynum = isset($cookie) ? $cookie : Config::get('npds.storyhome');
        $storynum = !is_null($cookie) ? $cookie : Config::get('two_core::config.storyhome');

        $query = DB::table('stories')->select('sid', 'catid', 'ihome', 'time');

        if (($categories == 1) and ($cat != '')) {
            if ($user) {
                $query->where('catid', $cat);
            } else {
                $query->where('catid', $cat)->where('ihome', 0);
            }
            
        } else {
            if ($user) {
                $query->where('ihome', 0);
            } 
        }

        $xtab = News::news_aff('old_news', 
            $query->orderBy('time', 'desc')
                    ->limit($storynum)
                    ->get(), 
            $storynum, 
            Config::get('two_core::config.oldnum'));
        
        $vari = 0;        
        $story_limit = 0;
        $time2 = 0;
        $a = 0;

        $locale = Config::get('two_core::config.locale');

        while (($story_limit < Config::get('two_core::config.oldnum')) and ($story_limit < sizeof($xtab))) {
            
            $sid        = $xtab[$story_limit]->sid; 
            $title      = $xtab[$story_limit]->title; 
            $time       = $xtab[$story_limit]->time; 
            $comments   = $xtab[$story_limit]->comments; 
            $counter    = $xtab[$story_limit]->counter;

            $datetime2 = ucfirst(htmlentities(\PHP81_BC\strftime(__d('two_news', 'datestring2'), $time, $locale), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'utf-8'));

            if (Config::get('two_core::config.language') != 'chinese') {
                $datetime2 = ucfirst($datetime2);
            }

            $comments = (($typ_aff == 'lecture') 
                ? '<span class="badge rounded-pill bg-secondary ms-1" title="'. __d('two_news', 'Lu') .'" data-bs-toggle="tooltip">'. $counter .'</span>' 
                : ''
            );

            if ($time2 == $datetime2) {
                $boxstuff .= '<li class="list-group-item list-group-item-action d-inline-flex justify-content-between align-items-center">
                    <a class="n-ellipses" href="'. site_url('article.php?sid='. $sid) .'">
                        '. Language::aff_langue($title) .'
                    </a>'. $comments .'</li>';
            } else {
                if ($a == 0) {
                    $boxstuff .= '<li class="list-group-item fs-6">'. $datetime2 .'</li>
                    <li class="list-group-item list-group-item-action d-inline-flex justify-content-between align-items-center">
                        <a href="'. site_url('article.php?sid='. $sid) .'">
                            '. Language::aff_langue($title) .'
                        </a>'. $comments .'
                    </li>';
                    $time2 = $datetime2;
                    $a = 1;
                } else {
                    $boxstuff .= '<li class="list-group-item fs-6">' . $datetime2 . '</li>
                    <li class="list-group-item list-group-item-action d-inline-flex justify-content-between align-items-center">
                        <a href="'. site_url('article.php?sid='. $sid) .'">
                            '. Language::aff_langue($title) .'
                        </a>'. $comments .'
                    </li>';
                    $time2 = $datetime2;
                }
            }

            $vari++;

            if ($vari == Config::get('two_core::config.oldnum')) {

                $storynum = isset($cookie[3]) ? $cookie[3] : Config::get('two_core::config.storyhome');
                $min = Config::get('two_core::config.oldnum') + $storynum;
                
                $boxstuff .= '<li class="text-center mt-3" >
                    <a href="'. site_url('search.php?min='. $min .'&amp;type=stories&amp;category='. $cat) .'">
                        <strong>'. __d('two_news', 'Articles plus anciens') .'</strong>
                    </a>
                </li>';
            }

            $story_limit++;
        }

        $boxstuff .= '</ul>';

        if ($boxstuff == '<ul></ul>') {
            $boxstuff = '';
        }

        global $block_title;
        $boxTitle = $block_title == '' ? __d('two_news', 'Anciens articles') : $block_title;

        Theme::themesidebox($boxTitle, $boxstuff);
    }
}

if (! function_exists('bigstory'))
{
    /**
     * Bloc BigStory
     * syntaxe : function#bigstory
     *
     * @return  void    [return description]
     */
    function bigstory(): void
    {
        $content = '';
        $today = getdate();
        $day = $today['mday'];

        if ($day < 10) {
            $day = "0$day";
        }

        $month = $today['mon'];

        if ($month < 10) {
            $month = "0$month";
        }

        $year = $today['year'];
        $tdate = "$year-$month-$day";

        $cookie = User::cookieUser(3);

        if (isset($cookie)) {
            $storynum = $cookie;
        } else {
            $storynum = Config::get('two_core::config.storyhome');
        }

        $xtab = News::news_aff("big_story", 
            DB::table('stories')
                ->select('sid', 'catid', 'ihome', 'counter')
                ->where('time', 'LIKE', '%'.$tdate.'%')
                ->orderBy('counter', 'desc')
                ->limit($storynum)
                ->get(),
            1,
            1
        );

        if (sizeof($xtab)) {
            $fsid   = $xtab[0]['sid']; 
            $ftitle = $xtab[0]['title'];
        } else {
            $fsid = '';
            $ftitle = '';
        }

        $content .= ($fsid == '' and $ftitle == '') ?
            '<span class="fw-semibold">'. __d('two_news', 'Il n\'y a pas encore d\'article du jour.') .'</span>' :
            '<span class="fw-semibold">'. __d('two_news', 'L\'article le plus consulté aujourd\'hui est :') .'</span><br /><br /><a href="'. site_url('article.php?sid='. $fsid) .'">'. Language::aff_langue($ftitle) .'</a>';

        global $block_title;
        $boxtitle = $block_title == '' ? __d('two_news', 'Article du Jour') : $block_title;

        theme::themesidebox($boxtitle, $content);
    }
}

if (! function_exists('category'))
{
    /**
     * Bloc de gestion des catégories
     * syntaxe : function#category
     *
     * @return  void
     */
    function category(): void
    {
        global $cat; // ????? A rechercher !!!

        $result = DB::table('stories_cat')
                    ->select('catid', 'title')
                    ->orderBy('title')
                    ->get();

        $numrows = count($result);

        if ($numrows == 0) {
            return;
        } else {
            $boxstuff = '<ul>';

            foreach ($result as $storie) {
                $numrows = DB::table('stories')
                                ->select('sid')
                                ->where('catid', $storie->catid)
                                ->limit(1)
                                ->offset(0)
                                ->count();

                if ($numrows > 0) {
                    $res = DB::table('stories')
                            ->select('time')
                            ->where('catid', $storie->catid)
                            ->orderBy('sid', 'desc')
                            ->limit(1)
                            ->offset(0)
                            ->first();

                    $boxstuff .= (($cat == $storie->catid)
                        ? '<li><strong>'. Language::aff_langue($storie->title) .'</strong></li>'
                        : '<li class="list-group-item list-group-item-action hyphenate">
                                <a href="'. site_url('index.php?op=newcategory&amp;catid='. $storie->catid) .'" data-bs-html="true" data-bs-toggle="tooltip" data-bs-placement="right" title="'. __d('two_news', 'Dernière contribution') .' <br />'. formatTimestamp($res->time) .' ">
                                    '. Language::aff_langue($storie->title) .'
                                </a>
                            </li>'
                    );
                }
            }

            $boxstuff .= '</ul>';

            global $block_title;
            $title = $block_title == '' ? __d('two_news', 'Catégories') : $block_title;

            Theme::themesidebox($title, $boxstuff);
        }
    }
}

if (! function_exists('bloc_rubrique'))
{
    /**
     * Bloc des Rubriques
     * syntaxe : function#bloc_rubrique
     *
     * @return  void
     */
    function bloc_rubrique(): void
    {
        $boxstuff = '<ul>';

        foreach (DB::table('rubriques')
                    ->select('rubid', 'rubname', 'ordre')
                    ->where('enligne', 1)
                    ->where('rubname', '<>', 'divers')
                    ->orderBy('ordre')
                    ->get() as $rubriques) 
        { 
            $title = Language::aff_langue($rubriques->rubname);

            $boxstuff .= '<li><strong>' . $title . '</strong></li>';

            foreach (DB::table('sections')
                    ->select('secid', 'secname', 'userlevel', 'ordre')
                    ->where('rubid', $rubriques->rubid)
                    ->orderBy('ordre')
                    ->get() as $section) 
            {

                $nb_article = DB::table('seccont')
                                    ->select('artid')
                                    ->where('secid', $section->secid)
                                    ->count();

                if ($nb_article > 0) {
                    $boxstuff .= '<ul>';
                    $tmp_auto = explode(',', $section->userlevel);

                    foreach ($tmp_auto as $userlevel) {
                        $okprintLV1 = User::autorisation($userlevel);
                        if ($okprintLV1) {
                            break;
                        }
                    }

                    if ($okprintLV1) {
                        $sec = Language::aff_langue($section->secname);
                        $boxstuff .= '<li><a href="'. site_url('sections.php?op=listarticles&amp;secid='. $section->secid) .'">'. $sec .'</a></li>';
                    }

                    $boxstuff .= '</ul>';
                }
            }
        }

        $boxstuff .= '</ul>';

        global $block_title;
        $title = $block_title == '' ? __d('two_news', 'Rubriques') : $block_title;

        Theme::themesidebox($title, $boxstuff);
    }
}
