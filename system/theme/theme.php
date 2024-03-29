<?php

declare(strict_types=1);

namespace npds\system\theme;

class theme
{

    /**
     *  Retourne le chemin complet si l'image est trouvée dans le répertoire image du thème sinon false
     *
     * @param   string  $theme_img  [$theme_img description]
     *
     * @return  string|bool
     */
    public static function theme_image(string $theme_img): string|bool
    {
        global $theme;
        
        if (@file_exists("themes/$theme/assets/images/$theme_img")) {
            return ("themes/$theme/assets/images/$theme_img");
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
        global $Default_Skin, $user;

        if (isset($user) and $user != '') {
            global $cookie;
            
            if ($cookie[9] != '') {
                $ibix = explode('+', urldecode($cookie[9]));
                if (array_key_exists(1, $ibix)) {
                    $skin = $ibix[1];
                } else {
                    $skin = $Default_Skin;
                }
            } else{
                $skin = $Default_Skin;
            }
        } else {
            $skin = $Default_Skin;
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
        global $Default_Theme, $user;

        if (isset($user) and $user != '') {
            global $cookie;

            if ($cookie[9] != '') {
                $ibix = explode('+', urldecode($cookie[9]));
                
                if (array_key_exists(0, $ibix)) {
                    $theme = $ibix[0];
                } else {
                    $theme = $Default_Theme;
                }

                if (!@opendir("themes/$theme")) {
                    $theme = $Default_Theme;
                }
            } else {
                $theme = $Default_Theme;
            }
        } else {
            $theme = $Default_Theme;
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
        echo "$title<br />" . meta_lang($hometext) . "<br />" . meta_lang($bodytext) . "<br />" . meta_lang($notes);
    }
}
