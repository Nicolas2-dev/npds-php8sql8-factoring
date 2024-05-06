<?php
declare(strict_types=1);

namespace App\Support\Facades;

use Npds\Support\Facades\Facade;


class SuperCacheEmpty extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'npds.super.cahe.empty'; }
}