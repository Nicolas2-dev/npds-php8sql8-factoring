<?php

declare(strict_types=1);

namespace App\Support\Language;


use Npds\Foundation\Application;
use Npds\Support\Facades\Config;


class LanguageManager
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
     * [getLocale description]
     *
     * @return  string
     */
    public function getLocale()
    {
        global $user_language;

        if (isset($user_language)) {
            $locale = $user_language;
        } else {
            $locale = Config::get('npds.language');
        }

        return $locale;
    }

    /**
     * [languages description]
     *
     * @return  array
     */
    public function languages()
    {
        return Config::get('languages');
    }

    /**
     * [getLocaleIso description]
     *
     * @return  string
     */
    public function getLocaleIso() 
    {
        $locale = $this->getLocale();

        $languages = $this->languages();
        
        return $languages[$locale]['iso'];
    }
 
    /**
     * Analyse le contenu d'une chaine et converti la section correspondante ([langue] OU [!langue] ...[/langue])
     * à la langue / [transl] ... [/transl] permet de simuler un appel translate("xxxx")
     *
     * @param   string  $ibid  [$ibid description]
     *
     * @return  string         [return description]
     */
    public function aff_langue(string $ibid)
    {
        // copie du tableau + rajout de transl pour gestion de l'appel à translate(...); - Theme Dynamic
        $tab_llangue = $this->make_tab_langue();
        $tab_llangue[] = 'transl';

        reset($tab_llangue);

        $ok_language = false;
        $trouve_language = false;
       
        foreach ($tab_llangue as $key => $lang) {
            $pasfin = true;
            $pos_deb = false;
            $abs_pos_deb = false;
            $pos_fin = false;

            while ($pasfin) {
                // tags [langue] et [/langue]
                $pos_deb = strpos(  $ibid, "[$lang]", 0); 
                $pos_fin = strpos(  $ibid, "[/$lang]", 0); 

                if ($pos_deb === false) {
                    $pos_deb = -1;
                }

                if ($pos_fin === false) {
                    $pos_fin = -1;
                }

                // tags [!langue]
                $abs_pos_deb = strpos(  $ibid, "[!$lang]", 0); 
                if ($abs_pos_deb !== false) {
                    $ibid = str_replace("[!$lang]", "[$lang]", $ibid);
                    $pos_deb = $abs_pos_deb;

                    if ($lang != Config::get('npds.language')) {
                        $trouve_language = true;
                    }
                }

                $decal = strlen($lang) + 2;

                if (($pos_deb >= 0) and ($pos_fin >= 0)) {
                    $fragment = substr($ibid, $pos_deb + $decal, ($pos_fin - $pos_deb - $decal));
                    
                    if ($trouve_language == false) {
                        if ($lang != 'transl') {
                            $ibid = str_replace("[$lang]" . $fragment . "[/$lang]", $fragment, $ibid);
                        } else {
                            $ibid = str_replace("[$lang]" . $fragment . "[/$lang]", translate($fragment), $ibid);
                        }
                        $ok_language = true;
                    } else {
                        if ($lang != 'transl') {
                            $ibid = str_replace("[$lang]" . $fragment . "[/$lang]", "", $ibid);
                        } else {
                            $ibid = str_replace("[$lang]" . $fragment . "[/$lang]", translate($fragment), $ibid);
                        }
                    }
                } else {
                    $pasfin = false;
                }
            }

            if ($ok_language)
                $trouve_language = true;
        }

        return $ibid;
    }
 
    /**
     * Charge le tableau TAB_LANGUE qui est utilisé par les fonctions multi-langue
     *
     * @return  array
     */
    public function make_tab_langue()
    {
        $languageslist = $this->languageList();

        $languageslocal = Config::get('npds.language') . ' ' . str_replace(Config::get('npds.language'), '', $languageslist);
        $languageslocal = trim(str_replace('  ', ' ', $languageslocal));
        $tab_langue = explode(' ', $languageslocal);

        return $tab_langue;
    }
 
    /**
     * Charge une zone de formulaire de selection de la langue
     *
     * @param   string  $ibid  [$ibid description]
     *
     * @return  string
     */
    public function aff_localzone_langue(string $ibid)
    {
        $flag = array('fr' => '🇫🇷', 'es' => '🇪🇸', 'de' => '🇩🇪', 'en' => '🇺🇸', 'zh' => '🇨🇳');

        $M_langue = '
        <div class="mb-3">
        <select name="' . $ibid . '" class="form-select" onchange="this.form.submit()">
            <option value="">' . translate("Choisir une langue") . '</option>';
        
        foreach ($this->make_tab_langue() as $bidon => $langue) {
            $M_langue .= '
                <option value="' . $langue . '">' . $flag[$langue] . ' ' . translate("$langue") . '</option>';
        }

        $M_langue .= '
                <option value="">- ' . translate("Aucune langue") . '</option>
            </select>
        </div>
        <noscript>
            <input class="btn btn-primary" type="submit" name="local_sub" value="' . translate("Valider") . '" />
        </noscript>';

        return $M_langue;
    }
 
    /**
     * Charge une FORM de selection de langue $ibid_index = URL de la Form, $ibid = nom du champ
     *
     * @param   string  $ibid_index  [$ibid_index description]
     * @param   string  $ibid        [$ibid description]
     * @param   string  $mess        [$mess description]
     *
     * @return  string
     */    
    public function aff_local_langue(string $ibid_index, string $ibid, ?string $mess = '')
    {
        if ($ibid_index == '') {
            global $REQUEST_URI;
            $ibid_index = $REQUEST_URI;
        }

        $M_langue = '<form action="' . $ibid_index . '" name="local_user_language" method="post">';
        $M_langue .= $mess . $this->aff_localzone_langue($ibid);
        $M_langue .= '</form>';

        return $M_langue;
    }
 
    /**
     * appel la fonction aff_langue en modifiant temporairement la valeur de la langue
     *
     * @param   string  $local_user_language  [$local_user_language description]
     * @param   string  $ibid                 [$ibid description]
     *
     * @return  string
     */
    public function preview_local_langue(?string $local_user_language, string $ibid)
    {
        if ($local_user_language) {
            $old_langue = Config::get('npds.language');

            Config::set('npds.language', $local_user_language);

            $tab_langue = $this->make_tab_langue(); // ???
            
            $ibid = $this->aff_langue($ibid);

            Config::set('npds.language.old_langue', $old_langue);
        }

        return $ibid;
    }
 
    /**
     * renvoi le code language iso 639-1 et code pays ISO 3166-2 
     * $l=> 0 ou 1(requis), 
     * $s (séparateur - | _) , 
     * $c=> 0 ou 1 (requis)
     *
     * @param   string|int  $l  [$l description]
     * @param   string|int  $s  [$s description]
     * @param   string|int  $c  [$c description]
     *
     * @return  string
     */
    public function language_iso(string|int $l, string|int $s, string|int $c)
    {
        global $user_language;

        $iso_lang = '';
        $iso_country = '';
        $ietf = '';
        $select_lang = '';
        $select_lang = !empty($user_language) ? $user_language : Config::get('npds.language');
        
        switch ($select_lang) {
            case "fr":
                $iso_lang = 'fr';
                $iso_country = 'FR';
                break;

            case "en":
                $iso_lang = 'en';
                $iso_country = 'US';
                break;

            case "es":
                $iso_lang = 'es';
                $iso_country = 'ES';
                break;

            case "de":
                $iso_lang = 'de';
                $iso_country = 'DE';
                break;

            case "zh":
                $iso_lang = 'zh';
                $iso_country = 'CN';
                break;

            default:
                break;
        }

        if ($c !== 1) {
            $ietf = $iso_lang;
        }

        if (($l == 1) and ($c == 1)) {
            $ietf = $iso_lang . $s . $iso_country;
        }

        if (($l !== 1) and ($c == 1)) {
            $ietf = $iso_country;
        }

        if (($l !== 1) and ($c !== 1)) {
            $ietf = '';
        }

        if (($l == 1) and ($c !== 1)) {
            $ietf = $iso_lang;
        }

        return $ietf;
    }

    /**
     * [languageList description]
     *
     * @return  string
     */
    public function languageList()
    {
        $languageslist = '';
        $handle = opendir(APPPATH .'language');
        while (false !== ($file = readdir($handle))) {
            if (!strstr($file, '.')) {
                $languageslist .= "$file ";
            }
        }
        closedir($handle);

        $this->languageWhiteToCache($languageslist);

        return $languageslist;
    }

    /**
     * [languageWhiteToCache description]
     *
     * @param   [type]  $languageslist  [$languageslist description]
     *
     * @return  void
     */
    public function languageWhiteToCache($languageslist)
    {
        $file = fopen('storage/language/lang_code.php', 'w');
        fwrite($file, "<?php \$languageslist=\"" . trim($languageslist) . "\"; ?>");
        fclose($file);
    }
}
