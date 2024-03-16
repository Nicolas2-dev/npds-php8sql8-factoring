<?php

declare(strict_types=1);

namespace npds\system\cache;

use npds\system\cache\cacheManager;
use npds\system\cache\SuperCacheEmpty;

class cache
{

 
    /**
     * Indique le status de SuperCache
     *
     * @return  string  [return description]
     */
    public static function SC_infos(): string
    {
        global $SuperCache, $npds_sc;

        $infos = '';
        if ($SuperCache) {
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
     * @return  SuperCacheEmpty|cacheManager
     */
    public static function cacheManagerStart(): SuperCacheEmpty|cacheManager
    {
        global $SuperCache, $cache_obj;

        if ($SuperCache) {
            $cache_obj = new cacheManager();
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
        global $SuperCache, $cache_obj;

        if ($SuperCache) {
            $cache_obj = new cacheManager();
            $cache_obj->startCachingPage();
        } else {
            $cache_obj = new SuperCacheEmpty();
        }

        if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!$SuperCache)) {
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
        global $SuperCache, $cache_obj;
        
        if ($SuperCache) {
            $cache_obj->endCachingPage();
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
        global $SuperCache, $cache_obj;
        
        if (($SuperCache) and ($cache_obj)) {
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

    /**
     * [PG_clean description]
     *
     * @param   string  $request  [$request description]
     *
     * @return  void
     */
    public static function PG_clean(string $request): void
    {
        global $CACHE_CONFIG;
        
        $page = md5($request);
        $dh = opendir($CACHE_CONFIG['data_dir']);
        
        while (false !== ($filename = readdir($dh))) {
            if ($filename === '.' or $filename === '..' or (strpos($filename, $page) === FALSE)) {
                continue;
            }

            unlink($CACHE_CONFIG['data_dir'] . $filename);
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
        global $CACHE_CONFIG;
        
        $dh = opendir($CACHE_CONFIG['data_dir'] . "sql");
        while (false !== ($filename = readdir($dh))) {
            if ($filename === '.' or $filename === '..') {
                continue;
            }
            
            if (is_file($CACHE_CONFIG['data_dir'] . "sql/" . $filename)) {
                unlink($CACHE_CONFIG['data_dir'] . "sql/" . $filename);
            }
        }
        closedir($dh);

        $fp = fopen($CACHE_CONFIG['data_dir'] . "sql/.htaccess", 'w');
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
        global $CACHE_CONFIG;
        
        $dh = opendir($CACHE_CONFIG['data_dir']);
        
        while (false !== ($filename = readdir($dh))) {
            //if ($filename === '.' or $filename === '..' or $filename === 'ultramode.txt' or $filename === 'net2zone.txt' or $filename === 'sql' or $filename === 'index.html') {
            if ($filename === '.' or $filename === '..' or $filename === 'sql' or $filename === 'index.html') {
                continue;
            }
            
            if (is_file($CACHE_CONFIG['data_dir'] . $filename)) {
                unlink($CACHE_CONFIG['data_dir'] . $filename);
            }
        }
        closedir($dh);

        static::Q_Clean();
    }
}
