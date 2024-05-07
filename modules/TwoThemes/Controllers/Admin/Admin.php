<?php

namespace Modules\TwoThemes\Controllers\Admin;


use Two\Http\Request;
use Two\Support\Facades\DB;

use Two\Support\Facades\Forge;
use Two\Support\Facades\Config;
use Modules\TwoCore\Support\Paginator;
use Modules\TwoCore\Core\AdminController;
use Modules\TwoThemes\Support\Facades\Theme;
use Modules\TwoCore\Support\Facades\Language;


class Admin extends AdminController
{
    /**
     * 
     */
    protected $f_meta_nom;

    /**
     * 
     */
    protected $f_titre;

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * test
     */
    public function test(Request $request)
    {

        Config::set('two_core::config.short_menu_admin', false);

        // $output = new \Symfony\Component\Console\Output\BufferedOutput;

        // $exitCode = Forge::call('make:package:migration', [
        // '--ansi' => true, '--create' => 'users', 'slug' => 'TwoUsers', 'name' => 'Create_users_table'  
        // ], $output);

        // $exitCode = Forge::call('make:package:migration', [
        // '--create' => 'users_status', 'slug' => 'users', 'name' => 'Create_users_status_table'  
        // ]);

        // $exitCode = Forge::call('make:package:migration', [
        // '--create' => 'users_extend', 'slug' => 'users', 'name' => 'Create_users_extend_table'  
        // ]);        

        //dump($output->fetch());


        // $config = Theme::getConfig();

        // vd($config, Theme::getPath(), Theme::getOptions(), Theme::getThemeOptions()->test_option());
        $radminsuper = true;

        //DB::connection()->setFetchMode(\PDO::FETCH_ASSOC);
        $deja_affiches = $request->query('deja_affiches');

        $admart = Config::get('two_core::config.admart');

        $result = DB::table('stories')->select('sid')->count();
        $nbre_articles = $result;

        settype($deja_affiches, "integer");
        settype($admart, "integer");
        
        $news_stories = DB::table('stories')
                    ->select('sid', 'title', 'hometext', 'topic', 'archive', 'catid', 'ihome')
                    ->orderBy('sid', 'desc')
                    ->limit($admart)
                    ->offset($deja_affiches)
                    ->get();

        $nbPages = ceil($nbre_articles/$admart);
        $current = 1;
        
        if ($deja_affiches >= 1) {
            $current = $deja_affiches/$admart;
        } else if ($deja_affiches < 1) {
            $current = 0;
        } else {
            $current = $nbPages;
        }

        $start = ($current*$admart);

        if ($nbre_articles) {
            $i = 0;
            $stories = array();


// vd($news_stories);

            foreach($news_stories as $storie) {

                $affiche = false;

                $topic = DB::table('topics')
                                ->select('topicadmin', 'topictext', 'topicimage')
                                ->where('topicid', $storie->topic)
                                ->first();

                $storie_cat = DB::table('stories_cat')
                                ->select('title')
                                ->where('catid', $storie->catid)
                                ->first();

                $cat_title = $storie_cat ? $storie_cat->title : '';

                // vd($cat_title);


                if ($radminsuper) {
                    $affiche = true;
                } else {
                    $topicadminX = explode(',', $topic->topicadmin);
                    for ($iX = 0; $iX < count($topicadminX); $iX++) {
                        if (trim($topicadminX[$iX]) == 'Root') {
                            $affiche = true;
                        }
                    }
                }
                
                $hometext = strip_tags($storie->hometext , '<br><br />');
                $lg_max = 200;
             
                if(strlen($hometext)>$lg_max) {
                    $hometext = substr($hometext, 0 , $lg_max).' ...';
                }

                $title = Language::aff_langue($storie->title);




                $stories[$i]['affiche']     = $affiche;
                $stories[$i]['cat_title']   = Language::aff_langue($cat_title);
                $stories[$i]['title']       = $title;
                $stories[$i]['archive']     = $storie->archive;
                $stories[$i]['sid']         = $storie->sid;
                $stories[$i]['hometext']    = htmlentities($hometext, ENT_QUOTES);
                $stories[$i]['ihome']       = $storie->ihome;
                $stories[$i]['catid']       = $storie->catid;
                $stories[$i]['topic']       = $storie->topic;
                $stories[$i]['topicimage']  = $topic->topicimage;
                $stories[$i]['topictext']   = Language::aff_langue($topic->topictext);
                $i++;
            }

// vd($stories);

        }
        
        $data = array(
            'admart'        => $admart,
            'nbre_articles' => $nbre_articles,
            'nbPages'       => $nbPages,
            'paginate'      => Paginator::paginate('admin.php?op=suite_articles&amp;deja_affiches=', '', $nbPages, $current, 1, $admart, $start),
            'stories'       => $stories,
        );

        return $this->createView($data)
            ->shares('title', __d('platform', 'Administration Dashboard'));
    }
}
