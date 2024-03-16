<?php

declare(strict_types=1);

namespace npds\system\support;

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
        global $httpref, $NPDS_Prefix;
        
        if ($httpref == 1) {
            $referer = htmlentities(strip_tags(hack::removeHack(getenv("HTTP_REFERER"))), ENT_QUOTES, 'utf-8');
            
            if ($referer != '' 
                and !strstr($referer, "unknown") 
                and !stristr($referer, $_SERVER['SERVER_NAME'])) {
                    sql_query("INSERT INTO ".$NPDS_Prefix."referer VALUES (NULL, '$referer')");
            }
        }   
    }

}