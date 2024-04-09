<?php

declare(strict_types=1);

namespace npds\system\theme;

use npds\system\auth\users;
use npds\system\config\Config;
use npds\system\language\metalang;

class theme
{

    /**
     *  Retourne le chemin complet si l'image est trouvée dans le répertoire image du thème sinon false
     *
     * @param   string  $theme_img    [$theme_img description]
     *
     * @return  string                [return description]
     */
    public static function theme_image(string $theme_img): string|bool
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
    public static function theme_image_row(string $theme_img, string $defautl_img): string|bool
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
    public static function getSkin(): string 
    {
        $user = users::getUser();

        if (isset($user) and $user != '') {  
            $cookie = users::cookieUser(9);

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
    public static function getTheme(): string
    {
        $user = users::getUser();

        if (isset($user) and $user != '') {
            $cookie = users::cookieUser(9);

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
    public static function themeLists(?bool $implode = false, ?string $separator = ' '): array|string
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
    public static function themepreview(string $title, string $hometext, string $bodytext = '', string $notes = ''): void
    {
        echo $title .'<br />'. metalang::meta_lang($hometext) .'<br />'. metalang::meta_lang($bodytext) .'<br />'. metalang::meta_lang($notes);
    }
    
}
