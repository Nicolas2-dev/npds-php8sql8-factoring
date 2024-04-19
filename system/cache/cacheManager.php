<?php

/************************************************************************/
/* DUNE by NPDS / SUPER-CACHE engine                                    */
/*                                                                      */
/* NPDS Copyright (c) 2002-2019 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
/************************************************************************/
/*  Original Autor : Francisco Echarte [patxi@eslomas.com]              */
/*  Revision : 2004-03-15 Version: 1.1 / multi-language support by Dev  */
/*  Revision : 2004-08-10 Version: 1.2 / SQL support by Dev             */
/*  Revision : 2006-01-28 Version: 1.3 / .common support by Dev         */
/*  Revision : 2009-03-12 Version: 1.4 / clean_limit mods by Dev        */
/*  Revision : 2018 Version: 1.5 / support php 7                        */
/************************************************************************/

declare(strict_types=1);

namespace npds\system\cache;

use npds\support\auth\users;
use npds\system\config\Config;


class cacheManager
{

    /**
     * 
     *
     * @var [type]
     */
    protected array $config = array();

    /**
     * 
     *
     * @var [type]
     */
    protected array $timings = array();

    /**
     * 
     *
     * @var [type]
     */
    protected array $blocks = array();

    /**
     * 
     *
     * @var [type]
     */
    protected array $Objets = array();


    /**
     * [$request_uri description]
     *
     * @var [type]
     */
    private string $request_uri;

    /**
     * [$query_string description]
     *
     * @var [type]
     */
    private string $query_string;

    /**
     * [$php_self description]
     *
     * @var [type]
     */
    private string $php_self;

    /**
     * [$genereting_output description]
     *
     * @var [type]
     */
    public int $genereting_output;

    /**
     * [$site_overload description]
     *
     * @var [type]
     */
    private bool $site_overload;

    /**
     * [$npds_sc description]
     *
     * @var bool
     */
    private static bool $npds_sc;

    /**
     * [$instance description]
     *
     * @var cacheManager
     */
    private static ?cacheManager  $instance = null;


    /**
     * [__construct description]
     *
     */
    public function __construct($config, $timings)
    {
        $this->genereting_output = 0;

        $this->config = $config;

        $this->timings = $timings;

        if (!empty($_SERVER) && isset($_SERVER['REQUEST_URI'])) {
            $this->request_uri = $_SERVER['REQUEST_URI'];
        } else {
            $this->request_uri = getenv('REQUEST_URI');
        }

        if (!empty($_SERVER) && isset($_SERVER['QUERY_STRING'])) {
            $this->query_string = $_SERVER['QUERY_STRING'];
        } else {
            $this->query_string = getenv('QUERY_STRING');
        }

        if (!empty($_SERVER) && isset($_SERVER['PHP_SELF'])) {
            $this->php_self = basename($_SERVER['PHP_SELF']);
        } else {
            $this->php_self = basename($GLOBALS['PHP_SELF']);
        }

        $this->site_overload = false;
        
        if (file_exists("storage/storage/cache/site_load.log")) {
            $site_load = file("storage/storage/cache/site_load.log");
            
            if ($site_load[0] >= $this->config['clean_limit']) {
                $this->site_overload = true;
            }
        }

        if (($this->config['run_cleanup'] == 1) and (!$this->site_overload)) {
            $this->cacheCleanup();
        }
    }

    /**
     * instance cacheManager
     *
     * @return cacheManager
     */
    public static function setInstance($config, $timings): cacheManager
    {
        if (static::$instance === null) {
            static::$instance = new self($config, $timings);
        }

        return static::$instance;
    }

    /**
     * Get singleton instance
     *
     * @return cacheManager
     */
    public static function getInstance(): cacheManager
    {
        return static::$instance;
    }

    /**
     * [getNpdsSc description]
     *
     * @return  bool
     */
    public static function getNpdsSc(): bool
    {
        return static::$npds_sc;
    }

    /**
     * [getNpdsSc description]
     *
     * @return  bool
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * [startCachingPage description]
     *
     * @return  void
     */
    function startCachingPage(): void
    {
        if ($this->timings[$this->php_self]['timing'] > 0 and ($this->query_string == '' or preg_match("#" . $this->timings[$this->php_self]['query'] . "#", $this->query_string))) {
            $cached_page = $this->checkCache($this->request_uri, $this->timings[$this->php_self]['timing']);
            
            if ($cached_page != '') {
                echo $cached_page;

                static::$npds_sc = true;

                $this->logVisit($this->request_uri, 'HIT');
                
                if ($this->config['exit'] == 1) {
                    exit;
                }
            } else {
                ob_start();
                $this->genereting_output = 1;
                $this->logVisit($this->request_uri, 'MISS');
            }
        } else {
            $this->logVisit($this->request_uri, 'EXCL');
            $this->genereting_output = -1;
        }
    }

    /**
     * [endCachingPage description]
     *
     * @return  void
     */
    function endCachingPage(): void
    {
        if ($this->genereting_output == 1) {
            $output = ob_get_contents();
            // if you want to activate rewrite engine
            //if (file_exists("config/rewrite_engine.php")) {
            //   include ("config/rewrite_engine.php");
            //}
            ob_end_clean();
            $this->insertIntoCache($output, $this->request_uri);
        }
    }

    /**
     * [checkCache description]
     *
     * @param   string  $request  [$request description]
     * @param   int     $refresh  [$refresh description]
     *
     * @return  string
     */
    function checkCache(string $request, int $refresh): string
    {
        $user = users::getUser();

        if (!$this->config['non_differentiate']) {
            if (isset($user) and $user != '') {
                $cookie = explode(':', base64_decode($user));
                $cookie = $cookie[1];
            } else {
                $cookie = '';
            }
        }

        // the .common is used for non differentiate cache page (same page for user and anonymous)
        if (substr($request, -7) == '.common') {
            $cookie = '';
        }

        $filename = $this->config['data_dir'] . $cookie . md5($request) . '.' . Config::get('npds.language');
        
        // Overload
        if ($this->site_overload) {
            $refresh = $refresh * 2;
        }

        if (file_exists($filename)) {
            if (filemtime($filename) > time() - $refresh) {
                if (filesize($filename) > 0) {
                    $data = fread($fp = fopen($filename, 'r'), filesize($filename));
                    fclose($fp);
                    return $data;
                } else {
                    return '';
                }
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    /**
     * [insertIntoCache description]
     *
     * @param   string  $content  [$content description]
     * @param   string  $request  [$request description]
     *
     * @return  void
     */
    function insertIntoCache(string $content, string $request): void
    {
        $user = users::getUser();

        if (!$this->config['non_differentiate']) {
            if (isset($user) and $user != '') {
                $cookie = explode(":", base64_decode($user));
                $cookie = $cookie[1];
            } else {
                $cookie = '';
            }
        }

        // the .common is used for non differentiate cache page (same page for user and anonymous)
        if (substr($request, -7) == '.common') {
            $cookie = '';
        }

        if (substr($request, 0, 5) == 'objet') {
            $request = substr($request, 5);
            $affich = false;
        } else {
            $affich = true;
        }

        $nombre = $this->config['data_dir'] . $cookie . md5($request) . '.' . Config::get('npds.language');

        if ($fp = fopen($nombre, 'w')) {
            flock($fp, LOCK_EX);
            fwrite($fp, $content);
            flock($fp, LOCK_UN);
            fclose($fp);
        }

        if ($affich) {
            echo $content;
        }

        static::$npds_sc = false;
    }

    /**
     * [logVisit description]
     *
     * @param   string  $request  [$request description]
     * @param   string  $type     [$type description]
     *
     * @return  void
     */
    function logVisit(string|array $request, string $type): void
    {
        if (!$this->config['save_stats']) {
            return;
        }

        $logfile = $this->config['data_dir'] . 'stats.log';
        $fp = fopen($logfile, 'a');
        flock($fp, LOCK_EX);
        fseek($fp, filesize($logfile));
        $salida = sprintf("%-10s %-74s %-4s\r\n", time(), $request, $type);
        fwrite($fp, $salida);
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    /**
     * [cacheCleanup description]
     *
     * @return  void
     */
    function cacheCleanup(): void
    {
        // Cette fonction n'est plus adaptée au nombre de fichiers manipulé par SuperCache
        srand( (int) microtime() * 1000000);
        $num = rand(1, 100);

        if ($num <= $this->config['cleanup_freq']) {
            $dh = opendir($this->config['data_dir']);
            $clean = false;
            
            // Clean SC directory
            $objet = "SC";
            while (false !== ($filename = readdir($dh))) {
                if ($filename === '.' or $filename === '..' or $filename === 'sql' or $filename === 'index.html') {
                    continue;
                }

                if (filemtime($this->config['data_dir'] . $filename) < time() - $this->config['max_age']) {
                    @unlink($this->config['data_dir'] . $filename);
                    $clean = true;
                }
            }
            closedir($dh);

            // Clean SC/SQL directory
            $dh = opendir($this->config['data_dir'] . "sql/");
            $objet .= "+SQL";
            while (false !== ($filename = readdir($dh))) {
                if ($filename === '.' or $filename === '..') {
                    continue;
                }

                if (filemtime($this->config['data_dir'] . "sql/" . $filename) < time() - $this->config['max_age']) {
                    @unlink($this->config['data_dir'] . "sql/" . $filename);
                    $clean = true;
                }
            }
            closedir($dh);

            $fp = fopen($this->config['data_dir'] . "sql/.htaccess", 'w');
            @fputs($fp, "Deny from All");
            fclose($fp);

            if ($clean) {
                $this->logVisit($this->request_uri, 'CLEAN ' . $objet);
            }
        }
    }

    /**
     * [UsercacheCleanup description]
     *
     * @return  void
     */
    function UsercacheCleanup(): void
    {
        $user = users::getUser();

        if (isset($user)) {
            $cookie = explode(":", base64_decode($user));
        }

        $dh = opendir($this->config['data_dir']);
        while (false !== ($filename = readdir($dh))) {
            if ($filename === '.' or $filename === '..') {
                continue;
            }
            
            // Le fichier appartient-il à l'utilisateur connecté ?
            if (substr($filename, 0, strlen($cookie[1])) == $cookie[1]) {
                // Le calcul md5 fournit une chaine de 32 chars donc si ce n'est pas 32 c'est que c'est un homonyme ...
                $filename_final = explode(".", $filename);
                
                if (strlen(substr($filename_final[0], strlen($cookie[1]))) == 32) {
                    unlink($this->config['data_dir'] . $filename);
                }
            }
        }
        closedir($dh);
    }

    /**
     * [startCachingBlock description]
     *
     * @param   string  $Xblock  [$Xblock description]
     *
     * @return  void
     */
    function startCachingBlock(string $Xblock): void
    {
        if ($this->blocks[$Xblock]['timing'] > 0) {
            $cached_page = $this->checkCache($Xblock, (int) $this->blocks[$Xblock]['key']);
            
            if ($cached_page != '') {
                echo $cached_page;
                $this->logVisit($Xblock, 'HIT');
                
                static::$npds_sc = true;

                if ($this->config['exit'] == 1) {
                    exit;
                }
            } else {
                ob_start();
                $this->genereting_output = 1;
                $this->logVisit($Xblock, 'MISS');
            }
        } else {
            $this->genereting_output = -1;
            $this->logVisit($Xblock, 'NO-CACHE');
        }
    }

    /**
     * [endCachingBlock description]
     *
     * @param   string  $Xblock  [$Xblock description]
     *
     * @return  void
     */
    function endCachingBlock(string $Xblock): void
    {
        if ($this->genereting_output == 1) {
            $output = ob_get_contents();
            ob_end_clean();
            $this->insertIntoCache($output, $Xblock);
        }
    }

    /**
     * 
     *
     * @param   [type]  $cache_clef  [$cache_clef description]
     * @param   [type]  $cache       [$cache description]
     *
     * @return  [type]
     */
    public function setTimingBlock($cache_clef, $cache) {
        $this->blocks[$cache_clef]['key'] = $cache_clef;
        $this->blocks[$cache_clef]['timing'] = $cache;
    }

    /**
     * [CachingQuery description]
     *
     * @param   string|array  $Xquery     [$Xquery description]
     * @param   int     $retention  [$retention description]
     *
     * @return  array
     */
    function CachingQuery(string|array $Xquery, int $retention): array
    {
        $filename = $this->config['data_dir'] . "sql/" . md5($Xquery);

        if (file_exists($filename)) {
            if (filemtime($filename) > time() - $retention) {
                
                if (filesize($filename) > 0) {
                    $data = fread($fp = fopen($filename, 'r'), filesize($filename));
                    fclose($fp);
                } else {
                    return array();
                }

                $no_cache = false;
                $this->logVisit($Xquery, 'HIT');

                return unserialize($data);
            } else {
                $no_cache = true;
            }
        } else {
            $no_cache = true;
        }

        if ($no_cache) {
            $result = @sql_query($Xquery);
            $tab_tmp = array();
            
            while ($row = sql_fetch_assoc($result)) {
                $tab_tmp[] = $row;
            }

            if ($fp = fopen($filename, 'w')) {
                flock($fp, LOCK_EX);
                fwrite($fp, serialize($tab_tmp));
                flock($fp, LOCK_UN);
                fclose($fp);
            }

            $this->logVisit($Xquery, 'MISS');

            return $tab_tmp;
        }
    }

    function CachingQuery2(array $Xquery, int $retention, string $type_req): array
    {   
        $filename = $this->config['data_dir'] . "sql/" . md5($type_req);

        if (file_exists($filename)) {
            if (filemtime($filename) > time() - $retention) {
                
                if (filesize($filename) > 0) {
                    $data = fread($fp = fopen($filename, 'r'), filesize($filename));
                    fclose($fp);
                } else {
                    return array();
                }

                $no_cache = false;
                $this->logVisit($Xquery, 'HIT');

                return unserialize($data);
            } else {
                $no_cache = true;
            }
        } else {
            $no_cache = true;
        }

        if ($no_cache) {
            if ($fp = fopen($filename, 'w')) {
                flock($fp, LOCK_EX);
                fwrite($fp, serialize($Xquery));
                flock($fp, LOCK_UN);
                fclose($fp);
            }

            $this->logVisit($Xquery, 'MISS');

            return $Xquery;
        }
    }

    /**
     * [startCachingObjet description]
     *
     * @param   string  $Xobjet  [$Xobjet description]
     *
     * @return  string|array
     */
    function startCachingObjet(string $Xobjet): string|array 
    {
        if ($this->Objets[$Xobjet]['timing'] > 0) {
            $cached_page = $this->checkCache($Xobjet, $this->Objets[$Xobjet]['key']);
            
            if ($cached_page != '') {
                $this->logVisit($Xobjet, 'HIT');
                
                if ($this->config['exit'] == 1) {
                    exit;
                }

                return unserialize($cached_page);
            } else {
                $this->genereting_output = 1;
                $this->logVisit($Xobjet, 'MISS');

                return "";
            }
        } else {
            $this->genereting_output = -1;
            $this->logVisit($Xobjet, 'NO-CACHE');

            return "";
        }
    }

    /**
     * 
     *
     * @param   [type]  $cache_clef  [$cache_clef description]
     * @param   [type]  $cache       [$cache description]
     *
     * @return  [type]
     */
    public function setTimingObjet($cache_clef, $cache) {
        $this->Objets[$cache_clef]['key'] = $cache_clef;
        $this->Objets[$cache_clef]['timing'] = $cache;
    }

    /**
     * [endCachingObjet description]
     *
     * @param   string  $Xobjet  [$Xobjet description]
     * @param   array  $Xtab    [$Xtab description]
     *
     * @return  void
     */
    function endCachingObjet(string $Xobjet, array $Xtab): void
    {
        if ($this->genereting_output == 1) {
            $this->insertIntoCache(serialize($Xtab), "objet" . $Xobjet);
        }
    }
}
