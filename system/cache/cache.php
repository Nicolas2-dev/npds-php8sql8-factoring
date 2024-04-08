<?php

declare(strict_types=1);

namespace npds\system\cache;

use npds\system\config\Config;
use npds\system\support\facades\DB;
use npds\system\cache\SuperCacheEmpty;
use npds\system\support\facades\Cache as SuperCache;


class cache
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

            $npds_sc = SuperCache::getInstance()::getNpdsSc();

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
     * @return  SuperCacheEmpty|SuperCache
     */
    public static function cacheManagerStart(): SuperCacheEmpty|SuperCache
    {
        if (Config::get('cache.config.SuperCache')) {
            $cache_obj = SuperCache::getInstance();
            $cache_obj->startCachingPage();
        } else {
            $cache_obj = new SuperCacheEmpty();
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
            $cache_obj = SuperCache::getInstance();
            $cache_obj->startCachingPage();
        } else {
            $cache_obj = new SuperCacheEmpty();
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
            $cache_obj = SuperCache::getInstance();

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
            $cache_obj = SuperCache::getInstance();
            $cache_obj->setTimingBlock($cache_clef, 600);
            $cache_obj->startCachingBlock($cache_clef);            
        } else {
            $cache_obj = new SuperCacheEmpty();
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
            $cache_obj = SuperCache::getInstance();

            $cache_obj->endCachingBlock($cache_clef);
        }
    }

    /**
     * Ces fonctions sont en dehors de la Classe pour permettre un appel sans instanciation d'objet
     *
     * @param   string  $Xquery     [$Xquery description]
     * @param   int     $retention  [$retention description]
     *
     * @return  array               [return description]
     */
    public static function Q_Select(string $Xquery, int $retention = 3600): array
    {
        $cache_obj = SuperCache::getInstance();

        if ((Config::get('cache.config.SuperCache')) and ($cache_obj)) {
            $row = $cache_obj->CachingQuery($Xquery, $retention);
           
            return $row;
        } else {
            $result = @sql_query($Xquery);
            $tab_tmp = array();
            
            while ($row = sql_fetch_assoc($result)) {
                $tab_tmp[] = $row;
            }
            
            return $tab_tmp;
        }
    }


    public static function Q_Select3(string|array|DB $Xquery, int $retention = 3600, string $type_req): array
    {
        $cache_obj = SuperCache::getInstance();
        
        if ((Config::get('cache.config.SuperCache')) and ($cache_obj)) {
            $row = $cache_obj->CachingQuery2($Xquery, $retention, $type_req);
           
            return $row;
        } else {
            $tab_tmp = array();
            
            foreach ($Xquery as $row) {
                $tab_tmp[] = $row;
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
        $config = SuperCache::getConfig();

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
        $config = SuperCache::getConfig();
        
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
        $config = SuperCache::getConfig();
        
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
