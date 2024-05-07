<?php
/**
 * Two - EncryptCookie
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

namespace App\Middleware;

use Two\Cookie\Middleware\EncryptCookies as BaseEncrypter;


class EncryptCookies extends BaseEncrypter
{
    /**
     * Les noms des cookies qui ne doivent pas être cryptés.
     *
     * @var array
     */
    protected $except = array(
        'PHPSESSID',
    );
}
