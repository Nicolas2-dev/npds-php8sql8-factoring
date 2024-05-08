<?php

declare(strict_types=1);

namespace Modules\TwoThemes\Library;

use Two\Support\Str;
use Two\Foundation\Application;
use Two\Support\Facades\Config;

use Two\Support\Facades\Package;
use Modules\TwoUsers\Library\User\UserManager;
use Modules\TwoNews\Support\Traits\ThemeIndexTrait;
use Modules\TwoEdito\Support\Traits\ThemeEditoTrait;
use Modules\TwoBlocks\Support\Traits\ThemeBlockTrait;
use Modules\TwoCore\Library\Language\LanguageManager;
use Modules\TwoCore\Library\Metalang\MetaLangManager;
use Modules\TwoNews\Support\Traits\ThemeArticleTrait;
use Modules\TwoThemes\Support\Traits\ThemeAfterTrait;
use Modules\TwoUsers\Support\Traits\UserPopoverTrait;
use Modules\TwoThemes\Support\Traits\ThemeAssetsTrait;
use Modules\TwoThemes\Support\Traits\ThemeBeforeTrait;
use Modules\TwoThemes\Support\Traits\ThemeFooterTrait;
use Modules\TwoThemes\Support\Traits\ThemeHeaderTrait;
use Modules\TwoBlocks\Support\Traits\ThemeSideboxTrait;
use Modules\TwoThemes\Library\Interface\ThemeInterface;
use Modules\TwoThemes\Support\Traits\ThemeBodyOnloadTrait;


/*
 * 
 */
abstract class ThemeManager implements ThemeInterface
{

    use UserPopoverTrait, ThemeBodyOnloadTrait, ThemeBeforeTrait, ThemeAfterTrait, ThemeBlockTrait, ThemeHeaderTrait, 
        ThemeFooterTrait, ThemeSideboxTrait, ThemeArticleTrait, ThemeIndexTrait, ThemeEditoTrait, ThemeAssetsTrait;


    /**
     * The Application Instance.
     *
     * @var \Two\Application\Application
     */
    private Application $app;

    /**
     * The MetaLang Instance.
     *
     * @var \Modules\TwoCore\Library\Metalang\MetaLangManager
     */
    private MetaLangManager $metalang;

    /**
     * The Lanaguage Instance.
     *
     * @var \Modules\TwoCore\Library\Language\LanguageManager
     */
    private LanguageManager $language;

    /**
     * The User Instance.
     *
     * @var \Modules\TwoUsers\Library\User\UserManager
     */
    private UserManager $user;

    /**
     * 
     *
     * @var int
     */
    private string $theme;

    /**
     * [__construct description]
     *
     * @param   Application  $app         [$app description]
     * @param   string       $path        [$path description]
     * @param   string       $theme_name  [$theme_name description]
     * @param   string       $hint        [$hint description]
     *
     * @return  [type]                    [return description]
     */
    public function __construct(Application $app, string $theme, MetaLangManager $metalang, LanguageManager $language, UserManager $user)
    {
        $this->app = $app;

        $this->theme = $theme;

        $this->metalang = $metalang;

        $this->language = $language;

        $this->user = $user;
    }
   
    /**
     * [local_var description]
     *
     * @param   string  $Xcontent  [$Xcontent description]
     *
     * @return  string
     */
    public function local_var(string $Xcontent)
    {
        if (strstr($Xcontent, "!var!")) {
            $deb = strpos($Xcontent, "!var!", 0) + 5;
            $fin = strpos($Xcontent, ' ', $deb);
    
            if ($fin) {
                $H_var = substr($Xcontent, $deb, $fin - $deb);
            } else {
                $H_var = substr($Xcontent, $deb);
            }
    
            return $H_var;
        }        
    }

    /**
     * Theme de l'utilisateur connecter ou theme default site
     *
     * @return  string
     */
    public function getTheme()
    {
        $user =$this->user->getUser();

        if (isset($user) and $user != '') {
            $cookie = $this->user->cookieUser(9);

            if ($cookie != '') {
                $ibix = explode('+', urldecode($cookie));
                
                if (array_key_exists(0, $ibix)) {
                    $theme = $ibix[0];
                } else {
                    $theme = Config::get('two_core::config.Default_Theme');
                }

                if (!@opendir('themes/'. $theme)) {
                    $theme = Config::get('two_core::config.Default_Theme');
                }
            } else {
                $theme = Config::get('two_core::config.Default_Theme');
            }
        } else {
            $theme = Config::get('two_core::config.Default_Theme');
        }

        return $theme;
    }

    /**
     * [getSkin description]
     *
     * @return  [type]  [return description]
     */
    public function getSkin()
    {
        $user = $this->user->getUser();

        if (isset($user) and $user != '') {  
            $cookie = $this->user->cookieUser(9);

            if ($cookie != '') {
                $ibix = explode('+', urldecode($cookie));

                if (array_key_exists(1, $ibix)) {
                    $skin = $ibix[1];
                } else {
                    $skin = Config::get('two_core::config.Default_Skin');
                }
            } else{
                $skin = Config::get('two_core::config.Default_Skin');
            }
        } else {
            $skin = Config::get('two_core::config.Default_Skin');
        }

        if ($option = $this->getConfig('skinable')) {
            if ($option['use'] == true && $option['force'] == true) {
                $skin = $option['name'];
            }
        }

        return $skin;
    }

    /**
     * Liste des themes disponible
     *
     * @param   bool    $implode    [$implode description]
     * @param   false               [ description]
     * @param   string  $separator  [$separator description]
     *
     * @return  array|string
     */
    public function themeLists(?bool $implode = false, ?string $separator = ' ')
    {
        $handle = opendir(base_path('themes'));

        while (false !== ($file = readdir($handle))) {
            
            if (($file[0] !== '_') and (!strstr($file, '.'))
                and (!strstr($file, 'TwoBackend'))
            ) {
                $themelist[] = $file;
            }
        }
        natcasesort($themelist);

        if ($implode) {
            $themelist = implode($separator, $themelist);
        }

        closedir($handle);

        return $themelist;
    }
 
    /**
     * Permet de prévisualiser la présentation d'un NEW
     *
     * @param   string  $title     [$title description]
     * @param   string  $hometext  [$hometext description]
     * @param   string  $bodytext  [$bodytext description]
     * @param   string  $notes     [$notes description]
     *
     * @return  void
     */
    public function themepreview(string $title, string $hometext, string $bodytext = '', string $notes = '')
    {
        echo $title .'<br />'. $this->metalang($hometext) .'<br />'. $this->metalang($bodytext) .'<br />'. $this->metalang($notes);
    }

    /**
     * [theme_Image description]
     *
     * @param   [type]  $theme_img  [$theme_img description]
     *
     * @return  [type]              [return description]
     */
    public function theme_Image(string $theme_img)
    {
        if (@$this->getApp('files')->exists($this->getPath() .DS. 'assets' .DS. 'images' .DS. $theme_img)) {

            return asset_url('images/'. $theme_img, 'themes/'. $this->getHint());
        } else {
            return false;
        } 
    }

    /**
     * [theme_Image_Row description]
     *
     * @param   [type]  $theme_img    [$theme_img description]
     * @param   [type]  $default_img  [$default_img description]
     *
     * @return  [type]                [return description]
     */
    public function theme_Image_Row(string $theme_img, string $default_img)
    {
        if (@$this->getApp('files')->exists($this->getPath() .DS. 'assets' .DS. 'images' .DS. $theme_img)) {

            return asset_url('images/'. $theme_img, 'themes/'. $this->getHint());

        } elseif (@$this->getApp('files')->exists(BASEPATH .DS. 'assets' .DS. 'images' .DS. $default_img)) {
            return asset_url($default_img); 

        } else {
            return false;
        }        
    }

    /**
     * [asset_theme_url description]
     *
     * @param   [type]  $image  [$image description]
     *
     * @return  [type]          [return description]
     */
    public function asset_theme_url($image, $hint)
    {
        return asset_url($image, 'themes/'.$this->metalang($hint));
    }

    /**
     * [asset_theme description]
     *
     * @param   [type]  $image  [$image description]
     *
     * @return  [type]          [return description]
     */
    public function asset_theme($image)
    {
        $package    = Package::where('basename', $this->getName());
        $namespace  = $this->getPackagehint($package['name']);

        $package_core    = Package::where('basename', Config::get('two_core::config.packageTwoThemes', 'TwoThemes'));
        $namespace_core  = $this->getPackagehint($package_core['name']);

        if (app('files')->exists($package['path'] . 'Assets/' . $image)) {
            $url = asset_url($image, $namespace);
        } elseif (app('files')->exists($package_core['path'] . 'Assets/' . $image)) {
            $url = asset_url($image, $namespace_core);
        }
        
        return $url;
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
     * [colsyst description]
     *
     * @param   [type]  $coltarget  [$coltarget description]
     *
     * @return  [type]              [return description]
     */
    public function colsyst(string $coltarget)
    {
        $coltoggle = '
        <div class="col d-lg-none me-2 my-2">
            <hr />
            <a class=" small float-end" href="#" data-bs-toggle="collapse" data-bs-target="' . $coltarget . '">
                <span class="plusdecontenu trn">
                    Plus de contenu
                </span>
            </a>
        </div>';

    echo $coltoggle;        
    }

    /**
     * [getApp description]
     *
     * @return  [type]  [return description]
     */
    public function getApp(string $classname = null)
    {
        if (is_null($classname)) {
            return $this->app;
        } else {
            return $this->app[$classname];
        }
    }

    /**
     * [getName description]
     *
     * @return  [type]  [return description]
     */
    public function getName()
    {
        return $this->theme;
    }

    /**
     * [getConfig description]
     *
     * @return  [type]  [return description]
     */
    public function getConfig(string $config = 'config')
    {
        return Config::get($this->getHint() .'::'. $config);
    }

    /**
     * [getOptions description]
     *
     * @return  [type]  [return description]
     */
    public function getOptions()
    {
        if (class_exists($this->getClassOption())) {
            return $this->getConfig('options');
        } else {
            return null;
        }
    }

    /**
     * [getClassOption description]
     *
     * @return  [type]  [return description]
     */
    public function getClassOption()
    {
        return sprintf('\%s\%s\Library\ThemeOptions', $this->getNamespace(), $this->getName());
    }

    /**
     * [getClassOption description]
     *
     * @return  [type]  [return description]
     */
    public function classOptionInstance()
    {
        return app('two_theme_options');
    }


    /**
     * [getName description]
     *
     * @return  [type]  [return description]
     */
    public function getHint()
    {
        return Str::snake($this->getName());
    }

    /**
     * [Metalang description]
     *
     * @param   [type]  $content  [$content description]
     *
     * @return  [type]            [return description]
     */
    public function metalang($content)
    {
        return $this->metalang->meta_lang($content);
    }

    /**
     * [Language description]
     *
     * @param   [type]  $content  [$content description]
     *
     * @return  [type]            [return description]
     */
    public function language($content)
    {
        return $this->language->aff_langue($content);
    }

    /**
     * [getPath description]
     *
     * @return  [type]  [return description]
     */
    public function getPath()
    {
        return $this->app->config->get('packages.themes.path') .DS. $this->getName();
    }

    /**
     * Get theme namespace.
     *
     * @return string
     */
    public function getNamespace()
    {
        $namespace = $this->app->config->get('packages.themes.namespace');

        return rtrim($namespace, '/\\');
    }

}