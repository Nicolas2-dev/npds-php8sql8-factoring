<?php

declare(strict_types=1);

namespace npds\system\support;

use npds\system\config\Config;
use npds\system\security\hack;

class referer
{

    /**
     * [refererUpdate description]
     *
     * @return  void
     */
    public static function refererUpdate(): void
    {
        global $NPDS_Prefix;
        
        if (Config::get('app.httpref') == 1) {
            
            $http_referer = getenv("HTTP_REFERER");

            if($http_referer) {
                $referer = htmlentities(strip_tags(hack::removeHack($http_referer)), ENT_QUOTES, 'utf-8');
                
                if ($referer != '' 
                and !strstr($referer, "unknown") 
                and !stristr($referer, $_SERVER['SERVER_NAME'])) {
                    sql_query("INSERT INTO ".$NPDS_Prefix."referer VALUES (NULL, '$referer')");
                }
            }
        }   
    }

}