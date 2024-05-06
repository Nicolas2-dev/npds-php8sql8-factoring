<?php

declare(strict_types=1);

namespace App\Library\Supercache;

use App\Support\Facades\SuperCacheManager;
use App\Library\Supercache\SuperCacheEmpty;

use Npds\Support\Facades\DB;
use Npds\Support\Facades\Config;


class Cache
{

    /**
     * Indique le status de SuperCache
     *
     * @return  string  [return description]
     */
    public static function SC_infos(): string
    {
        $infos = '';
        if (Config::get('cache.config.SuperCache')) {

            $npds_sc = SuperCacheManager::getNpdsSc();

            if ($npds_sc) {
                $infos = '<span class="small">' . translate(".:Page >> Super-Cache:.") . '</span>';
            } else {
                $infos = '<span class="small">' . translate(".:Page >> Super-Cache:.") . '</span>';
            }
        }

        return $infos;
    }

    /**
     * [cacheManagerStart description]
     *
     * @return  
     */
    public static function cacheManagerStart()
    {
        if (Config::get('cache.config.SuperCache')) {
            $cache_obj = SuperCacheManager::getInstance();
            $cache_obj->startCachingPage();
        } else {
            $cache_obj = SuperCacheEmpty::getInstance();
        }

        return $cache_obj;
    }

    /**
     * [cacheManagerStart description]
     *
     * @return  
     */
    public static function cacheManagerStart2(): bool
    {
        if (Config::get('cache.config.SuperCache')) {
            $cache_obj = SuperCacheManager::getInstance();
            $cache_obj->startCachingPage();
        } else {
            $cache_obj = SuperCacheEmpty::getInstance();
        }

        if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!Config::get('cache.config.SuperCache'))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * [cacheManagerEnd description]
     *
     * @return  void
     */
    public static function cacheManagerEnd(): void
    {
        if (Config::get('cache.config.SuperCache')) {
            $cache_obj = SuperCacheManager::getInstance();

            $cache_obj->endCachingPage();
        }
    }

    /**
     * [cacheManagerStartBlock description]
     *
     * @param   string  $cache_clef  [$cache_clef description]
     *
     * @return  bool
     */
    public static function cacheManagerStartBlock(string $cache_clef): bool
    {
        if (Config::get('cache.config.SuperCache')) {
            $cache_obj = SuperCacheManager::getInstance();
            $cache_obj->setTimingBlock($cache_clef, 600);
            $cache_obj->startCachingBlock($cache_clef);            
        } else {
            $cache_obj = SuperCacheEmpty::getInstance();
        }

        if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!Config::get('cache.config.SuperCache'))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * [cacheManagerEndBlock description]
     *
     * @param   string  $cache_clef  [$cache_clef description]
     *
     * @return  void
     */
    public static function cacheManagerEndBlock(string $cache_clef): void
    {
        if (Config::get('cache.config.SuperCache')) {
            $cache_obj = SuperCacheManager::getInstance();

            $cache_obj->endCachingBlock($cache_clef);
        }
    }

    /**
     * [Q_Select description]
     *
     * @param   string              [ description]
     * @param   array               [ description]
     * @param   DB      $Xquery     [$Xquery description]
     * @param   int     $retention  [$retention description]
     * @param   string  $type_req   [$type_req description]
     *
     * @return  array
     */
    public static function Q_Select(string|array|DB $Xquery, int $retention = 3600, string $type_req): array
    {
        $cache_obj = SuperCacheManager::getInstance();
        
        if ((Config::get('cache.config.SuperCache')) and ($cache_obj)) {
            $row = $cache_obj->CachingQuery2($Xquery, $retention, $type_req);
           
            return $row;
        } else {
            $tab_tmp = array();
            
            foreach ($Xquery as $key => $value) {
                $tab_tmp[$key] = $value;
            }

            return $tab_tmp;
        }
    }

    /**
     * [PG_clean description]
     *
     * @param   string  $request  [$request description]
     *
     * @return  void
     */
    public static function PG_clean(string $request): void
    {
        $config = SuperCacheManager::getConfig();

        $page = md5($request);
        $dh = opendir($config['data_dir']);
        
        while (false !== ($filename = readdir($dh))) {
            if ($filename === '.' or $filename === '..' or (strpos($filename, $page) === FALSE)) {
                continue;
            }

            unlink($config['data_dir'] . $filename);
        }
        closedir($dh);
    }

    /**
     * [Q_Clean description]
     *
     * @return  void
     */
    public static function Q_Clean(): void
    {
        $config = SuperCacheManager::getConfig();
        
        $dh = opendir($config['data_dir'] . "sql");
        while (false !== ($filename = readdir($dh))) {
            if ($filename === '.' or $filename === '..') {
                continue;
            }
            
            if (is_file($config['data_dir'] . "sql/" . $filename)) {
                unlink($config['data_dir'] . "sql/" . $filename);
            }
        }
        closedir($dh);

        $fp = fopen($config['data_dir'] . "sql/.htaccess", 'w');
        @fputs($fp, "Deny from All");
        fclose($fp);
    }

    /**
     * [SC_clean description]
     *
     * @return  void
     */
    public static function SC_clean(): void
    {
        $config = SuperCacheManager::getConfig();
        
        $dh = opendir($config['data_dir']);
        
        while (false !== ($filename = readdir($dh))) {
            if ($filename === '.' or $filename === '..' or $filename === 'sql' or $filename === 'index.html') {
                continue;
            }
            
            if (is_file($config['data_dir'] . $filename)) {
                unlink($config['data_dir'] . $filename);
            }
        }
        closedir($dh);

        static::Q_Clean();
    }
    
}
