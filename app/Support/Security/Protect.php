<?php

declare(strict_types=1);

namespace App\Support\Security;

use Npds\Support\Facades\Config;


class Protect
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
        $bad_uri_content = Config::get('urlProtect');

        $bad_uri_key = static::bad_uri_key();

        $bad_uri_name = static::bad_uri_name();

        $badname_in_uri = static::badname_in_uri($bad_uri_name);

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

    /**
     * [bad_uri_key description]
     *
     * @return  array
     */
    private static function bad_uri_key(): array
    {
        return array_keys($_SERVER);
    }

    /**
     * [bad_uri_name description]
     *
     * @return  array
     */
    private static function bad_uri_name(): array 
    {
        return array('GLOBALS', '_SERVER', '_REQUEST', '_GET', '_POST', '_FILES', '_ENV', '_COOKIE', '_SESSION');
    }

    /**
     * [badname_in_uri description]
     *
     * @param   array  $bad_uri_name  [$bad_uri_name description]
     *
     * @return  array
     */
    private static function badname_in_uri(array $bad_uri_name): array 
    {
        return array_intersect(array_keys($_GET), $bad_uri_name);        
    }

}