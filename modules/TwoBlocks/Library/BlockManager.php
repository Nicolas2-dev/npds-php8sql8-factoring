<?php

declare(strict_types=1);

namespace Modules\TwoBlocks\Library;

use Two\Support\Facades\DB;
use Two\Foundation\Application;
use Modules\TwoUsers\Support\Facades\User;
use Modules\TwoThemes\Library\ThemeManager;
use Modules\TwoCore\Support\Facades\Language;
use Modules\TwoAuthors\Support\Facades\Author;
use Modules\TwoGroupes\Support\Facades\Groupe;


class BlockManager
{
 
    /**
     * The Application Instance.
     *
     * @var \Two\Application\Application
     */
    public $app;

    /**
     * The Theme Instance.
     *
     * @var \Modules\TwoThemes\Library\ThemeManager
     */
    public $theme;


    /**
     * Créez une nouvelle instance de Metas Manager.
     *
     * @return void
     */
    public function __construct(Application $app, ThemeManager $theme)
    {
        //
        $this->app = $app;

        //
        $this->theme = $theme;
    }


    /**
     * Assure la gestion des include# et function# des blocs de NPDS
     * le titre du bloc est exporté (global) )dans $block_title
     *
     * @param   string  $title     [$title description]
     * @param   string  $contentX  [$contentX description]
     *
     * @return  bool
     */
    public function block_fonction(string $title, string $contentX): bool
    {
        global $block_title;

        $block_title = $title;

        //For including PHP functions in block
        if (stristr($contentX, "function#")) {
            $contentX = str_replace('<br />', '', $contentX);
            $contentX = str_replace('<BR />', '', $contentX);
            $contentX = str_replace('<BR>', '', $contentX);
            $contentY = trim(substr($contentX, 9));

            if (stristr($contentY, "params#")) {
                $pos = strpos($contentY, "params#");
                $contentII = trim(substr($contentY, 0, $pos));
                $params = substr($contentY, $pos + 7);
                $prm = explode(',', $params);

                // Remplace le param "False" par la valeur false (idem pour True)
                for ($i = 0; $i <= count($prm) - 1; $i++) {
                    if ($prm[$i] == "false") {
                        $prm[$i] = false;
                    }

                    if ($prm[$i] == "true") {
                        $prm[$i] = true;
                    }
                }

                // En fonction du nombre de params de la fonction : limite actuelle : 8
                if (function_exists($contentII)) {
                    
                    // provisoire le tmp de finaliser le typage correcte de tout les block
                    if ($contentII === 'RecentForumPosts') {
                        $prm_type = array();
                        foreach ($prm as $_prm) {
                            if (is_numeric($_prm)) {
                                $prm_type[] = (int) $_prm;
                            } elseif (is_bool($_prm)) {
                                $prm_type[] = (bool) $_prm;
                            } elseif (is_string($_prm)) {
                                $prm_type[] = (string) $_prm;
                            }
                        }
                        $prm = $prm_type;
                    }

                    switch (count($prm)) {
                        
                        case 1:
                            $contentII($prm[0]);
                            break;
                        case 2:
                            $contentII($prm[0], $prm[1]);
                            break;
                        case 3:
                            $contentII($prm[0], $prm[1], $prm[2]);
                            break;
                        case 4:
                            $contentII($prm[0], $prm[1], $prm[2], $prm[3]);
                            break;
                        case 5:
                            $contentII($prm[0], $prm[1], $prm[2], $prm[3], $prm[4]);
                            break;
                        case 6:
                            $contentII($prm[0], $prm[1], $prm[2], $prm[3], $prm[4], $prm[5]);
                            break;
                        case 7:
                            $contentII($prm[0], $prm[1], $prm[2], $prm[3], $prm[4], $prm[5], $prm[6]);
                            break;
                        case 8:
                            $contentII($prm[0], $prm[1], $prm[2], $prm[3], $prm[4], $prm[5], $prm[6], $prm[7]);
                            break;
                    }
                    return true;
                } else {
                    return false;
                }
            } else {
                if (function_exists($contentY)) {
                    $contentY();
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }
 
    /**
     * Assure la fabrication réelle et le Cache d'un bloc
     *
     * @param   string  $title    [$title description]
     * @param   string  $member   [$member description]
     * @param   string  $content  [$content description]
     * @param   int     $Xcache   [$Xcache description]
     *
     * @return  void
     */
    public function fab_block(string $title, string $member, string $content, int $Xcache): void
    { 
        global $B_class_title, $B_class_content, $REQUEST_URI;
        
        // Multi-Langue
        $title = Language::aff_langue($title);
        
        // Bloc caché
        $hidden = false;
        if (substr($content, 0, 7) == "hidden#") {
            $content = str_replace("hidden#", '', $content);
            $hidden = true;
        }

        // Si on cherche à charger un JS qui a déjà été chargé par routes/pages.php alors on ne le charge pas ...
        global $pages_js;
        if ($pages_js != '') {
            preg_match('#src="([^"]*)#', $content, $jssrc);
            if (is_array($pages_js)) {
                foreach ($pages_js as $jsvalue) {
                    if (array_key_exists('1', $jssrc)) {
                        if ($jsvalue == $jssrc[1]) {
                            $content = '';
                            break;
                        }
                    }
                }
            } else {
                if (array_key_exists('1', $jssrc)) {
                    if ($pages_js == $jssrc[1]) {
                        $content = "";
                    }
                }
            }
        }

        $content = (string) Language::aff_langue($content);
        
        // if ((Config::get('cache.config.SuperCache')) and ($Xcache != 0)) {
        //     $cache_clef = md5($content);
        //     $cache_obj = SuperCache::getInstance();
        //     $cache_obj->setTimingBlock($cache_clef, $Xcache);
        //     $cache_obj->startCachingBlock($cache_clef);
        // } else {
        //     $cache_obj = new SuperCacheEmpty();
        // }

        // if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!Config::get('cache.config.SuperCache')) or ($Xcache == 0)) {
            
            // For including CLASS AND URI in Block
            $B_class_title = '';
            $B_class_content = '';
            $R_uri = '';

            if (stristr($content, 'class-') or stristr($content, 'uri')) {
                $tmp = explode("\n", $content);
                $content = '';
                
                foreach ($tmp as $id => $class) {
                    $temp = explode("#", $class);
                    
                    if ($temp[0] == "class-title") {
                        $B_class_title = str_replace("\r", "", $temp[1]);
                    } else if ($temp[0] == "class-content") {
                        $B_class_content = str_replace("\r", "", $temp[1]);
                    } else if ($temp[0] == "uri") {
                        $R_uri = str_replace("\r", '', $temp[1]);
                    } else {
                        if ($content != '') {
                            $content .= "\n ";
                        }
                        $content .= str_replace("\r", '', $class);
                    }
                }
            }

            // For BLOC URIs
            if ($R_uri) {
          
                $page_ref = basename($REQUEST_URI);
                $tab_uri = explode(" ", $R_uri);
                $R_content = false;
                $tab_pref = parse_url($page_ref);
                $racine_page = $tab_pref['path'];

                if (array_key_exists('query', $tab_pref)) {
                    $tab_pref = explode('&', $tab_pref['query']);
                }

                foreach ($tab_uri as $RR_uri) {
                    $tab_puri = parse_url($RR_uri);
                    $racine_uri = $tab_puri['path'];

                    if ($racine_page == $racine_uri) {
                        if (array_key_exists('query', $tab_puri)) {
                            $tab_puri = explode('&', $tab_puri['query']);
                        }

                        foreach ($tab_puri as $idx => $RRR_uri) {
                            if (substr($RRR_uri, -1) == "*") {
                                // si le token contient *
                                if (substr($RRR_uri, 0, strpos($RRR_uri, "=")) == substr($tab_pref[$idx], 0, strpos($tab_pref[$idx], "="))) {
                                    $R_content = true;
                                }
                            } else {
                                if ($RRR_uri != $tab_pref[$idx]) {
                                    $R_content = false;
                                } else {
                                    $R_content = true;
                                }
                            }
                        }
                    }

                    if ($R_content == true) {
                        break;
                    }
                }

                if (!$R_content) {
                    $content = '';
                }
            }

            // For Javascript in Block
            if (!stristr($content, 'javascript')) {
                $content = nl2br($content);
            }

            // For including externale file in block / the return MUST BE in $content
            if (stristr($content, 'include#')) {
                $Xcontent = false;

                // You can now, include AND cast a fonction with params in the same bloc !
                if (stristr($content, "function#")) {
                    $content = str_replace('<br />', '', $content);
                    $content = str_replace('<BR />', '', $content);
                    $content = str_replace('<BR>', '', $content);
                    $pos = strpos($content, 'function#');
                    $Xcontent = substr(trim($content), $pos);
                    $content = substr(trim($content), 8, $pos - 10);
                } else {
                    $content = substr(trim($content), 8);
                }

                include_once($content);

                if ($Xcontent) {
                    $content = $Xcontent;
                }
            }

            $user = User::getUser();
            $admin = Author::getAdmin();

            if (!empty($content)) {
                if (($member == 1) and (isset($user))) {
                    if (!$this->block_fonction($title, $content)) {
                        if (!$hidden) {
                            $this->theme->themesidebox($title, $content);
                        } else {
                            echo $content;
                        }
                    }
                } elseif ($member == 0) {
                    if (!$this->block_fonction($title, $content)) {
                        if (!$hidden) {
                            $this->theme->themesidebox($title, $content);
                        } else {
                            echo $content;
                        }
                    }
                } elseif (($member > 1) and (isset($user))) {
                    $tab_groupe = Groupe::valid_group($user);
                    if (Groupe::groupe_autorisation($member, $tab_groupe)) {
                        if (!$this->block_fonction($title, $content)) {
                            if (!$hidden)  {
                                $this->theme->themesidebox($title, $content);
                            } else {
                                echo $content;
                            }
                        }
                    }
                } elseif (($member == -1) and (!isset($user))) {
                    if (!$this->block_fonction($title, $content)) {
                        if (!$hidden) {
                            $this->theme->themesidebox($title, $content);
                        } else {
                            echo $content;
                        }
                    }
                } elseif (($member == -127) and (isset($admin)) and ($admin)) {
                    if (!$this->block_fonction($title, $content)) {
                        if (!$hidden) {
                            $this->theme->themesidebox($title, $content);
                        } else {
                            echo $content;
                        }
                    }
                }
            }
        // }
        
        // if ((Config::get('cache.config.SuperCache')) and ($Xcache != 0)) {
        //     $cache_obj->endCachingBlock($cache_clef);
        // }
    }

    /**
     * Meta-Fonction / Blocs de Gauche
     *
     * @param   string  $moreclass  [$moreclass description]
     *
     * @return  void
     */
    public function leftblocks(string $moreclass): void
    {
        $this->Pre_fab_block('', 'LB', $moreclass);
    }
 
    /**
     * Meta-Fonction / Blocs de Droite
     *
     * @param   string  $moreclass  [$moreclass description]
     *
     * @return  void
     */
    public function rightblocks(string $moreclass): void
    {
        $this->Pre_fab_block('', 'RB', $moreclass);
    }
 
    /**
     * Alias de Pre_fab_block pour meta-lang
     *
     * @param   string  $Xid     [$Xid description]
     * @param   string  $Xblock  [$Xblock description]
     *
     * @return  string
     */
    public function oneblock(string $Xid, string $Xblock): string
    {
        $tmp = '';
        ob_start();
            $this->Pre_fab_block($Xid, $Xblock, '');
            $tmp = ob_get_contents();
        ob_end_clean();

        return $tmp;
    }

    /**
     * Assure la fabrication d'un ou de tous les blocs Gauche et Droite
     *
     * @param   string  $Xid        [$Xid description]
     * @param   string  $Xblock     [$Xblock description]
     * @param   string  $moreclass  [$moreclass description]
     *
     * @return  void
     */
    public function Pre_fab_block(string $Xid, string $Xblock, string $moreclass): void
    {
        global $htvar; // modif Jireck

        if ($Xid) {
            $result = (($Xblock == 'RB') 
                ? DB::table('rblocks')->select('title', 'content', 'member', 'cache', 'actif', 'id', 'css')->where('id', $Xid)->get()
                : DB::table('lblocks')->select('title', 'content', 'member', 'cache', 'actif', 'id', 'css')->where('id', $Xid)->get()
            );
        } else {
            $result = (($Xblock == 'RB') 
                ? DB::table('rblocks')->select('title', 'content', 'member', 'cache', 'actif', 'id', 'css')->orderBy('Rindex', 'asc')->get()
                : DB::table('lblocks')->select('title', 'content', 'member', 'cache', 'actif', 'id', 'css')->orderBy('Lindex', 'asc')->get()
            );
        }

        global $bloc_side;

        $bloc_side = $Xblock == 'RB' ? 'RIGHT' : 'LEFT';

        foreach ($result as  $block) {
            if (($block->actif) or ($Xid)) {
                if ($block->css == 1) {
                    $htvar = '<div class="' . $moreclass . '" id="' . $Xblock . '_' . $block->id . '">'; // modif Jireck
                } else {
                    $htvar = '<div class="' . $moreclass . ' ' . strtolower($bloc_side) . 'bloc">'; // modif Jireck
                }

                $this->fab_block($block->title, $block->member, $block->content, $block->cache);
                // echo "</div>"; // modif Jireck
            }
        }
    }
 
    /**
     * Retourne le niveau d'autorisation d'un block (et donc de certaines fonctions)
     * le paramètre (une expression régulière) est le contenu du bloc (function#....)
     *
     * @param   string  $Xcontent  [$Xcontent description]
     *
     * @return  string             [return description]
     */
    public function niv_block(string $Xcontent): string 
    {
        $result = DB::table('rblocks')->select('member', 'actif')->where('content', 'REGEXP', $Xcontent)->first();

        if ($result) {
            return ($result->member . ',' . $result->actif);
        }

        $result = DB::table('lblocks')->select('member', 'actif')->where('content', 'REGEXP', $Xcontent)->first();
        
        if ($result) {
            return ($result->member . ',' . $result->actif);
        }
    }
 
    /**
     * Retourne une chaine??
     * array ou vide contenant la liste des autorisations (-127,-1,0,1,2...126)) SI le bloc est actif SINON ""
     * le paramètre est le contenu du bloc (function#....)
     *
     * @param   string  $Xcontent  [$Xcontent description]
     *
     * @return  string             [return description]
     */
    public function autorisation_block(string $Xcontent): string|array 
    {
        $autoX = array(); //notice .... to follow
        $auto = explode(',', $this->niv_block($Xcontent));

        // le dernier indice indique si le bloc est actif
        $actif = $auto[count($auto) - 1];

        // on dépile le dernier indice
        array_pop($auto);
        foreach ($auto as $autovalue) {
            if (User::autorisation($autovalue)) {
                $autoX[] = $autovalue;
            }
        }
        
        if ($actif) {
            return $autoX;
        } else {
            return '';
        }
    }

}
