<?php

namespace Modules\TwoGroupes\Support\Facades;

use Two\Support\Facades\Facade;


class Groupe extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'two_groupe'; }
}
