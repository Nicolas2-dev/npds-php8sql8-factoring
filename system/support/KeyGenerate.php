<?php

declare(strict_types=1);

namespace npds\system\support;

use RuntimeException;


class KeyGenerate
{

    /**
     * Generate a random key for the npds application.
     *
     * @return  string
     */
    public static function getRandomKey(): string
    {
        return static::random(32);
    }

    /**
     * Générer une chaîne alphanumérique plus véritablement "aléatoire".
     *
     * @param  int $length
     * @return string
     *
     * @throws \RuntimeException
     */
    public static function random($length = 16)
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = static::randomBytes($size);

            $string .= substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    /**
     * Générer des octets plus véritablement "aléatoires".
     *
     * @param  int $length
     * @return string
     *
     * @throws \RuntimeException
     */
    public static function randomBytes($length = 16)
    {
        if (PHP_MAJOR_VERSION >= 7) {
            $bytes = random_bytes($length);
        } else if (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = \openssl_random_pseudo_bytes($length, $strong);

            if (($bytes === false) || ($strong === false)) {
                throw new RuntimeException('Unable to generate random string.');
            }
        } else {
            throw new RuntimeException('OpenSSL extension is required for PHP 5.');
        }

        return $bytes;
    }

}
