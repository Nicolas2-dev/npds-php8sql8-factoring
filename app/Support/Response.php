<?php

declare(strict_types=1);

namespace App\Support;


class Response
{

    /**
     * Controle de réponse// c'est pas encore assez fin not work with https probably
     *
     * @param   string  $url            [$url description]
     * @param   int     $response_code  [$response_code description]
     *
     * @return  bool
     */
    public static function file_contents_exist(string $url, int $response_code = 200): bool
    {
        $headers = get_headers($url);
        
        if (substr($headers[0], 9, 3) == $response_code) {
            return true;
        } else {
            return false; 
        }
    }
}
