<?php

declare(strict_types=1);

namespace npds\support;

use npds\system\config\Config;
use npds\support\security\hack;
use npds\system\support\facades\DB;

class referer
{

    /**
     * [refererUpdate description]
     *
     * @return  void
     */
    public static function refererUpdate(): void
    {
        if (Config::get('npds.httpref') == 1) {
            
            $http_referer = getenv("HTTP_REFERER");

            if($http_referer) {
                $referer = htmlentities(strip_tags(hack::removeHack($http_referer)), ENT_QUOTES, 'utf-8');
                
                if ($referer != '' 
                and !strstr($referer, "unknown") 
                and !stristr($referer, $_SERVER['SERVER_NAME'])) {
                    DB::table('referer')->insert(array(
                        'url'       => $referer,
                    ));
                }
            }
        }   
    }

}