<?php

declare(strict_types=1);

namespace App\Library\Theme;

use App\Support\Facades\User;
use App\Support\Metalang\Metalang;

use Npds\Foundation\Application;
use Npds\Support\Facades\Config;


class ThemeManager
{
    /**
     * The Application Instance.
     *
     * @var Application
     */
    public $app;

    
    /**
     * Mailer constructor.
     *
     * @param string $theme
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     *  Retourne le chemin complet si l'image est trouvée dans le répertoire image du thème sinon false
     *
     * @param   string  $theme_img    [$theme_img description]
     *
     * @return  string                [return description]
     */
    public function theme_image(string $theme_img): string|bool
    {
        $theme = static::getTheme();
        
        if (@file_exists('themes/'. $theme .'/assets/images/'. $theme_img)) {
            return 'themes/'. $theme .'/assets/images/'. $theme_img;
        } else {
            return false;
        }
    }

    /**
     *  Retourne le chemin complet si l'image est trouvée dans le répertoire image du thème sinon false
     *
     * @param   string  $theme_img    [$theme_img description]
     * @param   string  $defautl_img  [$defautl_img description]
     *
     * @return  string                [return description]
     */
    public function theme_image_row(string $theme_img, string $defautl_img): string|bool
    {
        $theme = static::getTheme();
        
        if (@file_exists('themes/'. $theme .'/assets/images/'. $theme_img)) {
            return ('themes/'. $theme .'/assets/images/'. $theme_img);
        } elseif (@file_exists($defautl_img)) {
            return $defautl_img;  
        } else {
            return false;
        }
    }

    /**
     * Skin de l'utilisateur connecter ou Skin default site
     *
     * @return  string
     */
    public function getSkin(): string 
    {
        $user = Users::getUser();

        if (isset($user) and $user != '') {  
            $cookie = Users::cookieUser(9);

            if ($cookie != '') {
                $ibix = explode('+', urldecode($cookie));
                if (array_key_exists(1, $ibix)) {
                    $skin = $ibix[1];
                } else {
                    $skin = Config::get('npds.Default_Skin');
                }
            } else{
                $skin = Config::get('npds.Default_Skin');
            }
        } else {
            $skin = Config::get('npds.Default_Skin');
        }

        return $skin;
    }

    /**
     * Theme de l'utilisateur connecter ou theme default site
     *
     * @return  string
     */
    public function getTheme(): string
    {
        $user = Users::getUser();

        if (isset($user) and $user != '') {
            $cookie = Users::cookieUser(9);

            if ($cookie != '') {
                $ibix = explode('+', urldecode($cookie));
                
                if (array_key_exists(0, $ibix)) {
                    $theme = $ibix[0];
                } else {
                    $theme = Config::get('npds.Default_Theme');
                }

                if (!@opendir("themes/$theme")) {
                    $theme = Config::get('npds.Default_Theme');
                }
            } else {
                $theme = Config::get('npds.Default_Theme');
            }
        } else {
            $theme = Config::get('npds.Default_Theme');
        }

        return $theme;
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
    public function themeLists(?bool $implode = false, ?string $separator = ' '): array|string
    {
        $handle = opendir('themes');

        while (false !== ($file = readdir($handle))) {
            
            if (($file[0] !== '_') and (!strstr($file, '.'))
                and (!strstr($file, 'themes-dynamic'))
                and (!strstr($file, 'default'))
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
    public function themepreview(string $title, string $hometext, string $bodytext = '', string $notes = ''): void
    {
        echo $title .'<br />'. Metalang::meta_lang($hometext) .'<br />'. Metalang::meta_lang($bodytext) .'<br />'. Metalang::meta_lang($notes);
    }
    
}
