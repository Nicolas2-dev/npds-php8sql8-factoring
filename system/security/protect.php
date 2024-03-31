<?php

declare(strict_types=1);

namespace npds\system\security;


class protect
{

    /**
     * [url description]
     *
     * @param   string  $arr  [$arr description]
     * @param   string  $key  [$key description]
     *
     * @return  void
     */
    public static function url(string $arr, string $key): void
    {
        global $bad_uri_content, $bad_uri_key, $badname_in_uri;
    
        // include url_protect Bad Words and create the filter function
        include("config/url_protect.php");
    
        // mieux faire face aux techniques d'évasion de code : base64_decode(utf8_decode(bin2hex($arr))));
        $arr = rawurldecode($arr);
        $RQ_tmp = strtolower($arr);
        $RQ_tmp_large = strtolower($key) . "=" . $RQ_tmp;
    
        if (
            in_array($RQ_tmp, $bad_uri_content)
            or
            in_array($RQ_tmp_large, $bad_uri_content)
            or
            in_array($key, $bad_uri_key, true)
            or
            count($badname_in_uri) > 0
        ) {
            access_denied();
        }

        unset($bad_uri_content);
        unset($bad_uri_key);
        unset($badname_in_uri);
    }

}