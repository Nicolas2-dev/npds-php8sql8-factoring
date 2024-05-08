<?php

//declare(strict_types=1);

namespace Modules\TwoCore\Library\Metalang;

use Two\Support\Str;
use Two\Http\Request;
use Two\Support\Facades\DB;
use Two\Support\Facades\Cache;
use Two\Foundation\Application;
use Two\Support\Facades\Config;
use Two\Support\Facades\Package;
use Modules\TwoCore\Support\Code;
use Modules\TwoCore\Support\Security;
use Modules\TwoThemes\Support\Facades\Theme;
use Modules\TwoAuthors\Support\Facades\Author;


class MetaLangManager
{
 
    /**
     * The Application Instance.
     *
     * @var \Two\Application\Application
     */
    public $app;

    /**
     * The Application Instance.
     *
     * @var \Two\Http\Request
     */
    public $request;

    /**
     * [$glossaire description]
     *
     * @var [type]
     */
    protected $glossaire = array();

    /**
     * [$metalang_debug_str description]
     *
     * @var [type]
     */
    public $metalang_debug_str;

    /**
     * [$metalang_debug_time description]
     *
     * @var [type]
     */
    public $metalang_debug_time;

    /**
     * [$metalang_debug_cycle description]
     *
     * @var [type]
     */
    public $metalang_debug_cycle;

    /**
     * [$metalang_debug description]
     *
     * @var [type]
     */
    public $metalang_debug;


    /**
     * Créez une nouvelle instance de Metas Manager.
     *
     * @return void
     */
    public function __construct(Application $app, Request $request)
    {
        //
        $this->app = $app;

        //
        $this->request = $request;
    }

    /**
     * Cette fonction doit être utilisée pour filtrer les arguments des requêtes SQL et est
     * automatiquement appelée par META-LANG lors de passage de paramètres
     *
     * @param   string  $argument  [$argument description]
     *
     * @return  string        [return description]
     */
    public function arg_filter(string $argument): string 
    {
        return Security::remove(stripslashes(htmlspecialchars(urldecode($argument), ENT_QUOTES, 'utf-8')));
    }

    /**
     * Cette fonction est utilisée pour intégrer des smilies et comme service pour theme_img()
     *
     * @param   string  $image  [$image description]
     *
     * @return  string
     */
    public function MM_img(string $image): string 
    {
        $image = $this->arg_filter($image);
        $theme_image = Theme::theme_image($image);
        
        if ($theme_image) {
            $ret = '<img src="'. $theme_image .'" border="0" alt="" />';
        } else {
            if (@file_exists('assets/images/'.$image)) {
                $ret = '<img src="assets/images/'. $image .'" border="0" alt="" />';
            } else {
                $ret = false;
            }
        }

        return $ret;
    }

    // public function  MM_asset_url(string $images)
    // {
    //     $package    = Package::where('basename', Theme::getName());
    //     $namespace  = $this->getPackagehint($package['name']);

    //     $package_core    = Package::where('basename', Config::get('two_core::config.packageTwoThemes', 'TwoThemes'));
    //     $namespace_core  = $this->getPackagehint($package_core['name']);

    //     if (app('files')->exists($package['path'] . 'Assets/' . $images)) {
    //         $url = asset_url($images, $namespace);
    //     } elseif (app('files')->exists($package_core['path'] . 'Assets/' . $images)) {
    //         $url = asset_url($images, $namespace_core);
    //     }

    //     return ' src="'.$url.'" ';
    // }


    /**
     * [match_uri description]
     *
     * @param   string  $racine  [$racine description]
     * @param   string  $R_uri   [$R_uri description]
     *
     * @return  bool
     */
    public function match_uri(string $racine, string $R_uri): bool
    {
        foreach (explode(' ', $R_uri) as $RR_uri) {
            if ($racine == $RR_uri) {
                return true;
            }
        }

        return false;
    }

    /**
     * [charg_metalang description]
     *
     * @return  void
     */
    public function charg_metalang(): void
    {
        //$glossaires = Cache::remember('metalang_glossaire', Config::get('two_core::metalang.cache.glossaire'), function () {
            $glossaires =  DB::table('metalang')
                        ->select('def', 'content', 'type_meta', 'type_uri', 'uri')
                        ->where('type_meta', 'mot')
                        ->orWhere('type_meta', 'meta')
                        ->orWhere('type_meta', 'smil')
                        ->get();
        //});

        foreach ($glossaires as $meta) {
            // la syntaxe est presque la même que pour les blocs (on n'utilise que la racine de l'URI)
            // si type_uri="-" / uri site les URIs où les meta-mot NE seront PAS actifs (tous sauf ...)
            // si type_uri="+" / uri site les URI où les meta-mot seront actifs (seulement ...)
            // Le séparateur entre les URI est l'ESPACE
            // => Exemples : index.php user.php forum.php static.php
            $segment_uri = $this->request->segment(1);

            if ($meta->uri != '') {
                //$match = $this->match_uri($racine['path'], $meta->uri);
                $match = $this->match_uri($segment_uri, $meta->uri);

                if (($match and $meta->type_uri == "+") or (!$match and $meta->type_uri == "-")) {
                    $this->glossaire[$meta->def]['content'] = $meta->content;
                    $this->glossaire[$meta->def]['type'] = $meta->type_meta;
                }
            } else {
                $this->glossaire[$meta->def]['content'] = $meta->content;
                $this->glossaire[$meta->def]['type'] = $meta->type_meta;
            }
        }
    }

    /**
     * [getGlossaire description]
     *
     * @return  array   [return description]
     */
    public function getGlossaire(): array
    {
        return $this->glossaire;
    }

    /**
     * [ana_args description]
     *
     * @param   string  $arg  [$arg description]
     *
     * @return  string|array
     */
    public function ana_args(string $arg): string|array 
    {
        if (substr($arg, -1) == "\"") {
            $arguments[0] = str_replace("\"", '', $arg);
        } else {
            $arguments = explode(',', $arg);
        }

        return $arguments;
    }

    /**
     * [meta_lang description]
     *
     * @param   string  $Xcontent  [$Xcontent description]
     *
     * @return  string
     */
    public function meta_lang(string $Xcontent): string
    {
        // Reduction
        $Xcontent = str_replace("<!--meta", "", $Xcontent);
        $Xcontent = str_replace("meta-->", "", $Xcontent);
        $Xcontent = str_replace("!PHP!", "", $Xcontent);

        // Sauvegarde le contenu original / analyse et transformation
        $Ycontent = $Xcontent;
        $Xcontent = str_replace("\r", " ", $Xcontent);
        $Xcontent = str_replace("\n", " ", $Xcontent);
        $Xcontent = str_replace("\t", " ", $Xcontent);
        $Xcontent = str_replace("<br />", " ", $Xcontent);
        $Xcontent = str_replace("<BR />", " ", $Xcontent);
        $Xcontent = str_replace("<BR>", " ", $Xcontent);
        $Xcontent = str_replace("&nbsp;", " ", $Xcontent);
        $Xcontent = strip_tags($Xcontent);

        if (trim($Xcontent)) {
            $Xcontent .= " ";
            // for compatibility only with old dyna-theme !
            $Xcontent .= "!theme! ";
        } else {
            return $Ycontent;
        }

        $text = array_unique(explode(" ", $Xcontent));
        $Xcontent = $Ycontent;
        // Fin d'analyse / restauration du contenu original

        $tab = array();

        foreach ($text as $word) {
            //   while ($word=each($text)) { // code original + suppression de l'indice de la variable $word !
            // longueur minimale du mot : 2 semble un bon compromis sauf pour les smilies ... (1 est donc le choix par défaut)
            
            
            //vd($word);
            
            if (strlen($word) > 1) {
                $op = 0;
                $arguments = "";
                $cmd = "";
                $car_deb = substr($word, 0, 1);
                $car_fin = substr($word, -1);

                // entité HTML
                if ($car_deb != "&" and $car_fin != ";") {
                    // Mot 'pure'
                    if (($car_fin == "." or $car_fin == "," or $car_fin == ";" or $car_fin == "?" or $car_fin == ":") and ($word != "...")) {
                        $op = 1;
                        $Rword = substr($word, 0, -1);
                    }

                    // peut être une fonction
                    if ($car_fin == ")") {
                        $ibid = strpos($word, "(");
                        
                        if ($ibid) {
                            $op = 2;
                            $Rword = substr($word, 0, $ibid);
                            $arg = substr($word, $ibid + 1, strlen($word) - ($ibid + 2));
                            $arguments = $this->ana_args($arg);
                        } else {
                            $op = 1;
                            $Rword = substr($word, 0, -1);
                        }
                    }

                    // peut être un mot encadré par deux balises
                    if (($car_deb == "[" and $car_fin == "]" and $word != "[code]") or ($car_deb == "{" and $car_fin == "}")) {
                        $op = 5;
                        $Rword = substr($word, 1, -1);
                    }
                } else {
                    $op = 9;
                    $Rword = $word;
                }

                if ($car_deb == "(" and $op != 2) {
                    $op = 3;
                    $Rword = substr($word, 1);
                }

                if ($op == 3 and $car_fin == ")") {
                    $op = 4;
                    $Rword = substr($Rword, 0, -1);
                }

                if ($op == 0) {
                    $Rword = $word;
                }

                // --- REMPLACEMENTS
                $type_meta = "";
                
                $meta_glossaire = $this->getGlossaire();

                if (array_key_exists($Rword, $meta_glossaire)) {
                    $Cword = $meta_glossaire[$Rword]['content'];
                    $type_meta = $meta_glossaire[$Rword]['type'];
                } elseif (array_key_exists($Rword . $car_fin, $meta_glossaire)) {
                    $Cword = $meta_glossaire[$Rword . $car_fin]['content'];
                    $type_meta = $meta_glossaire[$Rword . $car_fin]['type'];
                    $Rword = $Rword . $car_fin;
                    $car_fin = "";
                } else {
                    $Cword = $Rword;
                }

                // Cword est un meta-mot ? (il en reste qui n'ont pas été interprétés par la passe du dessus ... ceux avec params !)
                if (substr($Cword, 0, 1) == "!") {
                    $car_meta = strpos($Cword, "!", 1);
                    
                    if ($car_meta) {
                        $Rword = substr($Cword, 1, $car_meta - 1);
                        $arg = substr($Cword, $car_meta + 1);
                        
                        $arguments = $this->ana_args($arg);
                        
                        if (array_key_exists("!" . $Rword . "!", $meta_glossaire)) {
                            $Cword = $meta_glossaire["!" . $Rword . "!"]['content'];
                            $type_meta = $meta_glossaire["!" . $Rword . "!"]['type'];
                        } else {
                            $Cword = '';
                            $type_meta = '';
                        }
                    }
                }

                // Cword commence par $cmd ?
                if (substr($Cword, 0, 4) == "\$cmd") {
                    @eval($Cword);
                    if ($cmd === false) {
                        $Cword = "<span style=\"color: red; font-weight: bold;\" title=\"Meta-lang : bad return for function\">$Rword</span>";
                    } else {
                        $Cword = $cmd;
                    }
                }

                // Cword commence par function ?
                if ($Cword != '') {
                    // Cword commence par function ?

                    //dump($word, $Rword, $Cword, $arguments);
                    if (substr($Cword, 0, 9) == "function ") 
                    {
                        list($word, $Rword, $Cword) = $this->charg($word, $Rword, $Cword, $arguments);
                    }
                }

                // si le mot se termine par ^ : on supprime ^ | cela permet d'assurer la protection d'un mot (intouchable)
                if ($car_fin == "^") { 
                    $Cword = substr($Cword, 0, -1) . "&nbsp;";
                }

                // si c'est un meta : remplacement identique à str_replace
                if ($type_meta == "meta") {
                    $tab[$Rword] = $Cword;
                } else {
                    if ($car_fin == substr($Rword, -1)) {
                        $car_fin = " ";
                    }

                    $tab[$Rword . $car_fin] = $Cword . $car_fin;
                }

                $admin = Author::getAdmin();

                //if ($this->metalang_debug and $admin and Config::get('two_core::metalang.debug')) {
                if ($this->metalang_debug and Config::get('two_core::metalang.debug')) {
                    $this->metalang_debug_str .= "=> $word<br />";
                } 

            }
        }

        $Xcontent = strtr($Xcontent, $tab);

        // Avons-nous quelque chose à supprimer (balise !delete! .... !/!) ?
        while (strstr($Xcontent, "!delete!")) {
            $deb = strpos($Xcontent, "!delete!", 0);
            $fin = strpos($Xcontent, "!/!", $deb + 8);
            
            if ($fin) {
                $Xcontent = str_replace(substr($Xcontent, $deb, ($fin + 3) - $deb), "", $Xcontent);
            } else {
                $Xcontent = str_replace("!delete!", "", $Xcontent);
            }
        }

        $Xcontent = str_replace("!/!", "", $Xcontent);

        // traitement [code] ... [/code]
        if (strstr($Xcontent, "[code]")) {
            $Xcontent = Code::aff_code($Xcontent);
        }

        $this->metalang_debug_cycle++;

        return $Xcontent;
    }

    /**
     * [charg description]
     *
     * @param   [type]  $word       [$word description]
     * @param   [type]  $Rword      [$Rword description]
     * @param   [type]  $Cword      [$Cword description]
     * @param   [type]  $arguments  [$arguments description]
     *
     * @return  array               [return description]
     */
    function charg($word, $Rword, $Cword, $arguments): array
    {
        $Rword = "MM_".str_replace("!", "", $Rword);

        $meta_function = app('two_metafunction');

        if ((!method_exists($meta_function, $Rword)) or (!function_exists($Rword))) {
            @eval($Cword);
        }

        if (is_array($arguments)) {
            array_walk($arguments, [$this, 'arg_filter']);

            // on cherche dans la class metafunction avec arguments, si la methode exist si oui on la retourne
            if (method_exists($meta_function, $Rword)) {              
                $Cword = call_user_func_array([$meta_function, $Rword], $arguments);
            }
            // la method na pas ete trouve dans la classe metacunction alors on retourn la function avec argument de la db 
            else {
                $Cword = call_user_func_array($Rword, $arguments);
            }
        } 
        else 
        {
            // on regarde dans la class metafunction, si la methode exist si oui on la retourne 
            if (method_exists($meta_function, $Rword)) {
                $Cword = call_user_func([$meta_function, $Rword]);
            }
            // la method na pas ete trouve dans la classe metacunction alors on retourn la function de la db 
            else {
                $Cword = call_user_func($Rword);
            }
        }

        $Rword = $word;
        
        return array($word, $Rword, $Cword);
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

    /**
     * [set_metalang_debug_str description]
     *
     * @param   [type]  $metalang_debug_str  [$metalang_debug_str description]
     *
     * @return  void                         [return description]
     */
    public function set_metalang_debug_str($metalang_debug_str): void 
    {
        $this->metalang_debug_str = $metalang_debug_str;
    }

    /**
     * [get_metalang_debug_str description]
     *
     * @return  string  [return description]
     */
    public function get_metalang_debug_str(): string 
    {
        return $this->metalang_debug_str;
    }

    /**
     * [set_metalang_debug_time description]
     *
     * @param   [type]  $metalang_debug_time  [$metalang_debug_time description]
     *
     * @return  void                          [return description]
     */
    public function set_metalang_debug_time($metalang_debug_time): void 
    {
        $this->metalang_debug_time = $metalang_debug_time;
    }

    /**
     * [get_metalang_debug_time description]
     *
     * @return  string  [return description]
     */
    public function get_metalang_debug_time(): string 
    {
        return $this->metalang_debug_time; 
    }

    /**
     * [set_metalang_debug_cycle description]
     *
     * @param   [type]  $metalang_debug_cycle  [$metalang_debug_cycle description]
     *
     * @return  void                           [return description]
     */
    public function set_metalang_debug_cycle($metalang_debug_cycle): void 
    {
        $this->metalang_debug_cycle = $metalang_debug_cycle;
    }

    /**
     * [get_metalang_debug_cycle description]
     *
     * @return  string  [return description]
     */
    public function get_metalang_debug_cycle(): string
    {
        return $this->metalang_debug_cycle;
    }

    /**
     * [set_metalang_debug description]
     *
     * @param   [type]  $metalang_debug  [$metalang_debug description]
     *
     * @return  void                     [return description]
     */
    public function set_metalang_debug($metalang_debug): void 
    {
        $this->metalang_debug = $metalang_debug;
    }

    /**
     * [get_metalang_debug description]
     *
     * @return  string  [return description]
     */
    public function get_metalang_debug(): string
    {
        return $this->metalang_debug;
    }

}
