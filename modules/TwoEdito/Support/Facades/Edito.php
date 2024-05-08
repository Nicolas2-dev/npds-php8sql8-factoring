<?php

namespace Modules\TwoEdito\Support\Facades;

use Two\Support\Facades\Facade;


class Edito extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'two_edito'; }
}
