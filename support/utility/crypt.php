<?php

declare(strict_types=1);

namespace npds\support\utility;

use npds\system\config\Config;

class crypt
{
 
    /**
     * Composant des fonctions encrypt et decrypt
     *
     * @param   string  $txt          [$txt description]
     * @param   string  $encrypt_key  [$encrypt_key description]
     *
     * @return  string
     */
    public static function keyED(string $txt, string $encrypt_key): string
    {
        $encrypt_key = md5($encrypt_key);
        $ctr = 0;
        $tmp = '';

        for ($i = 0; $i < strlen($txt); $i++) {
            if ($ctr == strlen($encrypt_key)) {
                $ctr = 0;
            }

            $tmp .= substr($txt, $i, 1) ^ substr($encrypt_key, $ctr, 1);
            $ctr++;
        }

        return $tmp;
    }
 
    /**
     * retourne une chaine encryptée en utilisant la valeur de $NPDS_Key
     *
     * @param   string  $txt  [$txt description]
     *
     * @return  string
     */
    public static function encrypt(string $txt): string
    {
        return static::encryptK($txt, Config::get('app.NPDS_Key'));
    }
 
    /**
     * retourne une chaine encryptée en utilisant la clef : $C_key
     *
     * @param   string  $txt    [$txt description]
     * @param   string  $C_key  [$C_key description]
     *
     * @return  string
     */
    public static function encryptK(string $txt, string $C_key): string
    {
        srand( (int) microtime() * 1000000);

        $encrypt_key = md5( (string) rand(0, 32000));
        $ctr = 0;
        $tmp = '';

        for ($i = 0; $i < strlen($txt); $i++) {
            if ($ctr == strlen($encrypt_key)) {
                $ctr = 0;
            }

            $tmp .= substr($encrypt_key, $ctr, 1) . (substr($txt, $i, 1) ^ substr($encrypt_key, $ctr, 1));
            $ctr++;
        }

        return base64_encode(static::keyED($tmp, $C_key));
    }
 
    /**
     * retourne une chaine décryptée en utilisant la valeur de $NPDS_Key
     *
     * @param   string  $txt  [$txt description]
     *
     * @return  string
     */
    public static function decrypt(string $txt): string
    {
        return static::decryptK($txt, Config::get('app.NPDS_Key'));
    }

    /**
     * retourne une décryptée en utilisant la clef de $C_Key
     *
     * @param   string  $txt    [$txt description]
     * @param   string  $C_key  [$C_key description]
     *
     * @return  string
     */
    public static function decryptK(string $txt, string $C_key): string
    {
        $txt = static::keyED(base64_decode($txt), $C_key);
        $tmp = '';

        for ($i = 0; $i < strlen($txt); $i++) {
            $md5 = substr($txt, $i, 1);
            $i++;
            $tmp .= (substr($txt, $i, 1) ^ $md5);
        }

        return $tmp;
    }
}
