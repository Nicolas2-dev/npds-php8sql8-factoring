<?php

declare(strict_types=1);

namespace npds\system\routing\__dev;

// global $tiny_mce;
// $tmp_theme = getTheme();
// $page__ref = pageref::configure($tmp_theme, $tiny_mce);



// $page__ref::get('index.php', [
// 'title'     => "[fr]Index[/fr][en]Home[/en][es]Index[/es][de]Index[/de][zh]&#x7D22;&#x5F15;[/zh]+",
// 'blocs'     => "0",
// 'run'       => "yes",
// 'sitemap'   => "0.8",
// //'meta-description' => "alorsd c'est good ou pas good ce system !!!"
// ]);

// $page__ref::get('faq.php', [
// 'title'     => "[fr]faq[/fr][en]Home[/en][es]Index[/es][de]Index[/de][zh]&#x7D22;&#x5F15;[/zh]+",
// 'blocs'     => "0",
// 'run'       => "yes",
// 'sitemap'   => "0.8",
// ]);

// $page__ref::setPageRef('module.php', [
// 'title'     => "[fr]module[/fr][en]Home[/en][es]Index[/es][de]Index[/de][zh]&#x7D22;&#x5F15;[/zh]+",
// 'blocs'     => "0",
// 'run'       => "yes",
// 'sitemap'   => "0.8",
// ]);

// $meta_description = $page__ref::getPageMetaDescription();

// var_dump($meta_description);

// $page__ref::setPageMetaDescription('un petit test voir si sa fonctionne ou pas !!!!');

// $meta_description = $page__ref::getPageMetaDescription();

// // var_dump($meta_description);

// //global $m_description;
// $m_description = $meta_description;

// var_dump($m_description);


class pageref
{
    /**
     * The define pages_ref
     *
     * @var array
     */
    private static array $pages_ref = [];

    /**
     * The define pages_ref
     *
     * @var array
     */
    private static array $query_string = [];

    /**
     * The define pages_ref
     *
     * @var int
     */    
    private static int $count_string;

    /**
     * The define pages_ref
     *
     * @var string
     */    
    private static string $page_uri;

    /**
     * The define page_js
     *
     * @var string
     */    
    private static string $page_js;

    /**
     * The define page_css
     *
     * @var string
     */    
    private static string $page_css;

    /**
     * The define page_css_ref
     *
     * @var string
     */    
    private static string $page_css_ref;    

    /**
     * The define tiny mce editeur
     *
     * @var string
     */
    private static string $tiny_mce_theme;

    /**
     * The define tiny mce editeur
     *
     * @var string
     */
    private static string $tiny_mce_relurl;
    
        /**
     * The define tiny mce editeur
     *
     * @var bool
     */
    private static bool $tiny_mce_init;

    /**
     * The define tiny mce editeur
     *
     * @var bool
     */
    private static bool $tiny_mce;

    /**
     * The define meta description
     *
     * @var string
     */
    private static string $meta_description = '';

    /**
     * The define meta keywords
     *
     * @var string
     */
    private static string $meta_keywords = '';

    /**
     * The define theme
     *
     * @var string
     */
    private static string $theme;

    /**
     * The pageref instance
     *
     * @var class pageref
     */
    private static ?pageref $instance = null;


    /**
     * pageref constructor.
     *
     * @param string $theme
     */
    public function __construct(string $theme, bool $tiny_mce)
    {
        static::$theme = $theme;

        static::$tiny_mce = $tiny_mce;

        static::detecteRequestUri();

        //var_dump(static::$pages_ref, static::$page_uri); die();
    }    

    /**
     * Configure pageref
     *
     * @param string $theme
     *
     * @return pageref
     */
    public static function configure(string $theme, bool $tiny_mce): pageref
    {
        if (static::$instance === null) {
            static::$instance = new self($theme, $tiny_mce);
        }

        return static::$instance;
    }

    /**
     * Get singleton instance
     *
     * @return pageref
     */
    public static function getInstance(): pageref
    {
        return static::$instance;
    }

    /**
     * Get page ref arguments
     *
     * @param string $path
     * @param array $reference
     */
    public static function get(string $path, array $reference): void
    {
        static::$pages_ref[$path] = $reference;
    }

    /**
     * Update pages
     *
     * @param array $pages
     */
    public static function setPageRef(string $path, array $reference): void
    {
        static::get($path, $reference);
    }

    /**
     * Get pages
     *
     * @return string
     */
    public static function getPageRef(string $path, string $reference = null): string|array
    {
        if (!is_null($reference) && array_key_exists($reference, static::$pages_ref[$path])) {
            $page = static::$pages_ref[$path][$reference];
        } else {
            $page = static::$pages_ref[$path];
        }
        
        return $page;
    }

    public static function loadPagedescription()
    {
        if (array_key_exists('meta-description', static::$pages_ref[static::$page_uri]) and (static::$meta_description == '')) {
            static::$meta_description = aff_langue(static::$pages_ref[static::$page_uri]['meta-description']);
        }
    }

    public static function loadPageKeywords()
    {
        if (array_key_exists('meta-keywords', static::$pages_ref[static::$page_uri]) and (static::$meta_keywords == '')) {
            static::$meta_keywords = aff_langue(static::$pages_ref[static::$page_uri]['meta-keywords']);
        }
    }

    public static function loadPageEditeur(): array
    {
        if (static::$tiny_mce) {
            if (array_key_exists(static::$page_uri, static::$pages_ref)) {
                if (array_key_exists('TinyMce', static::$pages_ref[static::$page_uri])) {
                    static::$tiny_mce_init = true;

                    if (array_key_exists('TinyMce-theme', static::$pages_ref[static::$page_uri]))
                        static::$tiny_mce_theme = static::$pages_ref[static::$page_uri]['TinyMce-theme'];

                    if (array_key_exists('TinyMceRelurl', static::$pages_ref[static::$page_uri]))
                        static::$tiny_mce_relurl = static::$pages_ref[static::$page_uri]['TinyMceRelurl'];
                } else {
                    static::$tiny_mce_init = false;
                    static::$tiny_mce = false;
                }
            } else {
                static::$tiny_mce_init = false;
                static::$tiny_mce = false;
            }
        } else {
            static::$tiny_mce_init = false;
        } 
        
        return ['editeur' => [static::$tiny_mce, static::$tiny_mce_init, static::$tiny_mce_theme, static::$tiny_mce_relurl]];
    }

    /**
     * Load page js
     *
     * @return string
     */
    public function loadPageCss(): array
    {
        if (array_key_exists(static::$page_uri, static::$pages_ref)) {
            if (array_key_exists('css', static::$pages_ref[static::$page_uri])) {
                static::$page_css_ref = static::$page_uri;
                static::$page_css = static::$pages_ref[static::$page_uri]['css'];
            } else {
                static::$page_css_ref = '';
                static::$page_css = '';
            }
        } else {
            static::$page_css_ref = '';
            static::$page_css = '';
        }

        return ['page_css' => [static::$page_css_ref, static::$page_css]];
    }   

    /**
     * Load page js
     *
     * @return string
     */
    public function loadPageJs(): string
    {
        if (array_key_exists(static::$page_uri, static::$pages_ref)) {
            if (array_key_exists('js', static::$pages_ref[static::$page_uri])) {
                $js = static::$pages_ref[static::$page_uri]['js'];
                if ($js != '') {
                    static::$page_js = $js;
                }
            } else {
                $js = '';
            }
        } else {
            $js = '';
        }
        
        static::$page_js = $js;

        return $js;
    }

    /**
     * Get page request uri
     *
     */
    private static function detecteRequestUri(): void
    {
        static::$query_string = preg_split("#(&|\?)#", $_SERVER['REQUEST_URI']);
        
        // on compte le nombre segments
        static::countUri();
    }

    /**
     * Count segments uri
     *
     */    
    private static function countUri(): void
    {
        static::$count_string = count(static::$query_string);
        
        // parse uri sur index 0
        static::pageUri();
    }

    /**
     * Get count query string
     *
     * @return int
     */
    public static function getCountUri(): int
    {
        return static::$count_string;
    }

    /**
     * retourne uri
     *
     */
    private static function pageUri(): void
    {
        static::$page_uri = basename(static::$query_string[0]); 
    }

    /**
     * Get pages
     *
     * @return string
     */
    public static function getPages(): array
    {
        return static::$pages_ref;
    }

    /**
     * Update theme
     *
     * @param array $theme
     */
    public static function setTheme(string $theme): void
    {
        static::$theme = $theme;
    }

    /**
     * Get theme
     *
     * @return string
     */
    public static function getTheme(): string
    {
        return static::$theme;
    }

    /**
     * Update page meta description
     *
     * @param array $description
     */
    public static function setPageMetaDescription(string $description): void
    {
        static::$meta_description = $description;
    }

    /**
     * Update page meta keywords
     *
     * @param array $keywords
     */
    public static function setPageMetaKeywords(string $keywords): void
    {
        static::$meta_keywords = $keywords;
    }

    /**
     * Get page meta description
     *
     * @return string
     */
    public static function getPageMetaDescription(): string
    {
        if (static::$meta_description === '') {
            static::loadPagedescription();
        }

        return static::$meta_description;
    }

    /**
     * Get page meta keywords
     *
     * @return string
     */
    public static function getPageMetaKeywords(): string
    {
        if (static::$meta_keywords === '') {
            static::loadPagekeywords();
        }   

        return static::$meta_keywords;
    }

    /**
     * Get tiny_mce
     *
     * @return string
     */
    public static function getTinyMce(): bool
    {   
        if  (static::$tiny_mce === true) {
            static::loadPageEditeur();
        }

        return static::$tiny_mce;
    }

    /**
     * Get tiny_mce_init
     *
     * @return string
     */
    public static function getTinyMceInit(): bool
    {
        return static::$tiny_mce_init;
    }

    /**
     * Get tiny_mce_theme
     *
     * @return string
     */
    public static function getTinyMceTheme(): string
    {
        return static::$tiny_mce_theme;
    }

    /**
     * Get $tiny_mce_relurl
     *
     * @return string
     */
    public static function getTinyMceRelurl(): string
    {
        return static::$tiny_mce_relurl;
    }

    /**
     * Get page_css
     *
     * @return string
     */
    public static function getPageCss(): string
    {
        return static::$page_css;
    }

    /**
     * Get page_css_ref
     *
     * @return string
     */
    public static function getPageCssRef(): string
    {
        return static::$page_css_ref;
    } 

    /**
     * Get page_js
     *
     * @return string
     */
    public static function getPageJs(): string
    {
        return static::$page_js;
    }

}
