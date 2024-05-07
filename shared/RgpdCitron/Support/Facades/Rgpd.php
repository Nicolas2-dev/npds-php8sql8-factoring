<?php

declare(strict_types=1);

namespace Shared\RgpdCitron\Support\Facades;

use Two\Support\Facades\Facade;


class Rgpd extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'two-rgpd'; }
}