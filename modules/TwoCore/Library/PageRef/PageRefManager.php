<?php

declare(strict_types=1);

namespace Modules\TwoCore\Library\PageRef;

use Two\Foundation\Application;
use Modules\TwoThemes\Library\ThemeManager;
use Modules\TwoCore\Support\Facades\Language;


class PageRefManager
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
     * pageref constructor.
     *
     * @param string $theme
     */
    public function __construct(Application $app, ThemeManager $theme)
    {
        $this->app = $app;

        $this->theme = $theme;

        //$this->tiny_mce = $tiny_mce;

        $this->detecteRequestUri();

        //var_dump($this->pages_ref, $this->page_uri); die();
    }    


    /**
     * Get page ref arguments
     *
     * @param string $path
     * @param array $reference
     */
    public function get(string $path, array $reference): void
    {
        $this->pages_ref[$path] = $reference;
    }

    /**
     * Update pages
     *
     * @param array $pages
     */
    public function setPageRef(string $path, array $reference): void
    {
        $this->get($path, $reference);
    }

    /**
     * Get pages
     *
     * @return string
     */
    public function getPageRef(string $path, string $reference = null): string|array
    {
        if (!is_null($reference) && array_key_exists($reference, $this->pages_ref[$path])) {
            $page = $this->pages_ref[$path][$reference];
        } else {
            $page = $this->pages_ref[$path];
        }
        
        return $page;
    }

    public function loadPagedescription()
    {
        if (array_key_exists('meta-description', $this->pages_ref[$this->page_uri]) and ($this->meta_description == '')) {
            $this->meta_description = Language::aff_langue($this->pages_ref[$this->page_uri]['meta-description']);
        }
    }

    public function loadPageKeywords()
    {
        if (array_key_exists('meta-keywords', $this->pages_ref[$this->page_uri]) and ($this->meta_keywords == '')) {
            $this->meta_keywords = Language::aff_langue($this->pages_ref[$this->page_uri]['meta-keywords']);
        }
    }

    public function loadPageEditeur(): array
    {
        if ($this->tiny_mce) {
            if (array_key_exists($this->page_uri, $this->pages_ref)) {
                if (array_key_exists('TinyMce', $this->pages_ref[$this->page_uri])) {
                    $this->tiny_mce_init = true;

                    if (array_key_exists('TinyMce-theme', $this->pages_ref[$this->page_uri]))
                        $this->tiny_mce_theme = $this->pages_ref[$this->page_uri]['TinyMce-theme'];

                    if (array_key_exists('TinyMceRelurl', $this->pages_ref[$this->page_uri]))
                        $this->tiny_mce_relurl = $this->pages_ref[$this->page_uri]['TinyMceRelurl'];
                } else {
                    $this->tiny_mce_init = false;
                    $this->tiny_mce = false;
                }
            } else {
                $this->tiny_mce_init = false;
                $this->tiny_mce = false;
            }
        } else {
            $this->tiny_mce_init = false;
        } 
        
        return ['editeur' => [$this->tiny_mce, $this->tiny_mce_init, $this->tiny_mce_theme, $this->tiny_mce_relurl]];
    }

    /**
     * Load page js
     *
     * @return string
     */
    public function loadPageCss(): array
    {
        if (array_key_exists($this->page_uri, $this->pages_ref)) {
            if (array_key_exists('css', $this->pages_ref[$this->page_uri])) {
                $this->page_css_ref = $this->page_uri;
                $this->page_css = $this->pages_ref[$this->page_uri]['css'];
            } else {
                $this->page_css_ref = '';
                $this->page_css = '';
            }
        } else {
            $this->page_css_ref = '';
            $this->page_css = '';
        }

        return ['page_css' => [$this->page_css_ref, $this->page_css]];
    }   

    /**
     * Load page js
     *
     * @return string
     */
    public function loadPageJs(): string
    {
        if (array_key_exists($this->page_uri, $this->pages_ref)) {
            if (array_key_exists('js', $this->pages_ref[$this->page_uri])) {
                $js = $this->pages_ref[$this->page_uri]['js'];
                if ($js != '') {
                    $this->page_js = $js;
                }
            } else {
                $js = '';
            }
        } else {
            $js = '';
        }
        
        $this->page_js = $js;

        return $js;
    }

    /**
     * Get page request uri
     *
     */
    private function detecteRequestUri(): void
    {
        $this->query_string = preg_split("#(&|\?)#", $_SERVER['REQUEST_URI']);
        
        // on compte le nombre segments
        $this->countUri();
    }

    /**
     * Count segments uri
     *
     */    
    private function countUri(): void
    {
        $this->count_string = count($this->query_string);
        
        // parse uri sur index 0
        $this->pageUri();
    }

    /**
     * Get count query string
     *
     * @return int
     */
    public function getCountUri(): int
    {
        return $this->count_string;
    }

    /**
     * retourne uri
     *
     */
    private function pageUri(): void
    {
        $this->page_uri = basename($this->query_string[0]); 
    }

    /**
     * Get pages
     *
     * @return string
     */
    public function getPages(): array
    {
        return $this->pages_ref;
    }

    /**
     * Update theme
     *
     * @param array $theme
     */
    public function setTheme(string $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * Get theme
     *
     * @return string
     */
    public function getTheme(): ThemeManager
    {
        return $this->theme;
    }

    /**
     * Update page meta description
     *
     * @param array $description
     */
    public function setPageMetaDescription(string $description): void
    {
        $this->meta_description = $description;
    }

    /**
     * Update page meta keywords
     *
     * @param array $keywords
     */
    public function setPageMetaKeywords(string $keywords): void
    {
        $this->meta_keywords = $keywords;
    }

    /**
     * Get page meta description
     *
     * @return string
     */
    public function getPageMetaDescription(): string
    {
        if ($this->meta_description === '') {
            $this->loadPagedescription();
        }

        return $this->meta_description;
    }

    /**
     * Get page meta keywords
     *
     * @return string
     */
    public function getPageMetaKeywords(): string
    {
        if ($this->meta_keywords === '') {
            $this->loadPagekeywords();
        }   

        return $this->meta_keywords;
    }

    /**
     * Get tiny_mce
     *
     * @return string
     */
    public function getTinyMce(): bool
    {   
        if  ($this->tiny_mce === true) {
            $this->loadPageEditeur();
        }

        return $this->tiny_mce;
    }

    /**
     * Get tiny_mce_init
     *
     * @return string
     */
    public function getTinyMceInit(): bool
    {
        return $this->tiny_mce_init;
    }

    /**
     * Get tiny_mce_theme
     *
     * @return string
     */
    public function getTinyMceTheme(): string
    {
        return $this->tiny_mce_theme;
    }

    /**
     * Get $tiny_mce_relurl
     *
     * @return string
     */
    public function getTinyMceRelurl(): string
    {
        return $this->tiny_mce_relurl;
    }

    /**
     * Get page_css
     *
     * @return string
     */
    public function getPageCss(): string
    {
        return $this->page_css;
    }

    /**
     * Get page_css_ref
     *
     * @return string
     */
    public function getPageCssRef(): string
    {
        return $this->page_css_ref;
    } 

    /**
     * Get page_js
     *
     * @return string
     */
    public function getPageJs(): string
    {
        return $this->page_js;
    }

}
