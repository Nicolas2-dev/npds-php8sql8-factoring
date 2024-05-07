<?php

namespace Modules\TwoCore\Support\Facades;

use Two\Support\Facades\Facade;


class Metatag extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'two_metatag'; }
}
