<?php

declare(strict_types=1);

namespace npds\system\language;


//$lang = language::configure($language, $user_language);

// var_dump($lang, 
// language::isLocale('de'), 
// language::getConfig(), 
// language::getInfo(), 
// language::getInfo('de'), 
// language::getName(), 
// language::getName('de'), 
// language::getIso(), 
// language::getIso('de'), 
// language::getDir(), 
// language::getDir('de'), 
// language::getIso('es', '-'),
// language::getUserLocale()
// );

// var_dump(
//    language::localeList(),
//    language::localeList(true, ' | '),
//    language::getLoclaleList(', ')
// );


class language
{

    /**
     * The define language
     *
     * @var string
     */
    private static string $language;

    /**
     * The define user language
     *
     * @var string
     */
    private static string $user_language;

    /**
     * The define config languages
     *
     * @var array
     */
    private static array $config;

    /**
     * The define config languages
     *
     * @var string
     */
    private static array $list = [];

    /**
     * The language instance
     *
     * @var language
     */
    private static ?language $instance = null;


    /**
     * Language constructor.
     *
     * @param string $language
     * @param string $user_language
     */
    public function __construct(string $language, string $user_language)
    {
        static::$language = $language;

        static::$user_language = $user_language;

        static::$config = static::languagesConfig();
    }    

    /**
     * Configure language
     *
     * @param string $language
     * @param string $user_language
     *
     * @return language
     */
    public static function configure(string $language, string $user_language): language
    {
        if (static::$instance === null) {
            static::$instance = new self($language, $user_language);
        }

        return static::$instance;
    }

    /**
     * Get singleton instance
     *
     * @return language
     */
    public static function getInstance(): language
    {
        return static::$instance;
    }

    /**
     * Retourne le tableau des languages disponible dans le fichier config languages
     *
     * @return  array
     */
    private static function languagesConfig(): array
    {
       return require ('config/languages.php');
    }

    /**
     * Check the locale
     *
     * @param string $locale
     *
     * @return bool
     */
    public static function isLocale(string $locale): bool
    {
        return static::$language == $locale;
    }

    /**
     * La liste des languages
     *
     * @param   bool    $cache      [$cache description]
     * @param   string  $separator  [$separator description]
     *
     * @return  array
     */
    public static function localeList(bool $cache = false, string $separator = ' '): array
    {
        $list = [];
    
        foreach(static::getConfig() as $code => $lang) {
            $list[] .= "$code";
        }
    
        if ($cache) {
            static::languageWhiteToCache(implode($separator, (array) static::$list));
        }
        
        return static::$list = $list;
    }

    /**
     * Get locale list
     *
     * @param   string  $separator  [$separator description]
     *
     * @return  string              [return description]
     */
    public static function getLoclaleList(string $separator = ' '): string
    {
        return implode($separator, (array) static::$list);
    }

    /**
     * Ont génère le fichier de cache
     *
     * @param   [type]  $list  [$list description]
     *
     * @return  void
     */
    private static function languageWhiteToCache($list): void
    {
       $file = fopen('storage/language/lang_code.php', 'w');
       fwrite($file, "<?php \$languageslist=\"".trim($list)."\"; ?>");
       fclose($file);
    }

    /**
     * Update locale
     *
     * @param   string  $locale  [$locale description]
     *
     * @return  void
     */
    public static function setLocale(string $locale): void
    {
        static::$language = $locale;
    }

    /**
     * Get locale
     *
     * @return  string
     */
    public static function getLocale(): string
    {
        return static::$language;
    }

    /**
     * Update user locale
     *
     * @param   string  $locale  [$locale description]
     *
     * @return  void
     */
    public static function setUserLocale(string $locale): void
    {
        static::$user_language = $locale;
    }

    /**
     * Get user locale
     *
     * @return  string
     */
    public static function getUserLocale(): string
    {
        return static::$user_language;
    }

    /**
     * Get locale
     *
     * @return  array
     */
    public static function getConfig(): array
    {
        return static::$config;
    }

    /**
     * Get locale info
     *
     * @param   string  $locale  [$locale description]
     *
     * @return  string
     */
    public static function getInfo(string $locale = null): string
    {
        if (!is_null($locale)) {
            $info = static::$config[$locale]['info'];
        } else {
            $info = static::$config[static::$language]['info'];
        }

        return $info;
    }

    /**
     * Get locale name
     *
     * @param   string  $locale  [$locale description]
     *
     * @return  string
     */
    public static function getName(string $locale = null): string
    {
        if (!is_null($locale)) {
            $name = static::$config[$locale]['name'];
        } else {
            $name = static::$config[static::$language]['name'];
        }

        return $name;
    }

    /**
     * Get locale name
     *
     * @param   string  $locale  [$locale description]
     *
     * @return  string
     */
    public static function getCode(string $locale = null): string
    {
        if (!is_null($locale)) {
            $name = static::$config[$locale]['locale'];
        } else {
            $name = static::$config[static::$language]['locale'];
        }

        return $name;
    }

    /**
     *  Get locale iso
     *
     * @param   string  $locale     [$locale description]
     * @param   string  $separator  [$separator description]
     *
     * @return  string
     */
    public static function getIso(string $locale = null, string $separator = null): string
    {
        if (!is_null($locale)) {
            $iso = static::$config[$locale]['iso'];
        } else {
            $iso = static::$config[static::$language]['iso'];
        }

        if (!is_null($separator)) {
            $iso = static::isoSeparator($iso, $separator);
        }

        return $iso;
    }

    /**
     * Get locale dir
     *
     * @param   string  $locale  [$locale description]
     *
     * @return  string
     */
    public static function getDir(string $locale = null): string
    {
        if (!is_null($locale)) {
            $dir = static::$config[$locale]['dir'];
        } else {
            $dir = static::$config[static::$language]['dir'];
        }

        return $dir;
    }

    /**
     * Replace Separator Iso
     *
     * @param   [type]  $iso        [$iso description]
     * @param   [type]  $separator  [$separator description]
     *
     * @return  string
     */
    private static function isoSeparator($iso, $separator): string
    {
        $iso_temp = explode('_', $iso);

        return $iso_temp[0] . $separator . $iso_temp[1];
    }

    /**
     * __call
     *
     * @param  string $name
     * @param  array $arguments
     * 
     * @return string
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists(static::$instance, $name)) {
            return call_user_func_array([static::$instance, $name], $arguments);
        }

        throw new \BadMethodCallException('Undefined method ' . $name);
    }

}
