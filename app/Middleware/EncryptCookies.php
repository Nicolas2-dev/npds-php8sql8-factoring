<?php

namespace App\Middleware;

use Npds\Cookie\Middleware\EncryptCookies as BaseEncrypter;


class EncryptCookies extends BaseEncrypter
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except = array(
        'PHPSESSID',
    );
}
