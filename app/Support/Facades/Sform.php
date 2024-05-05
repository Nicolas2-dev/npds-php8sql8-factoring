<?php

declare(strict_types=1);

namespace App\Support\Facades;


class Sform extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'npds.sform'; }

}