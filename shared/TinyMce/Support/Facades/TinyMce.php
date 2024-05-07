<?php
/**
 * Two - TinyMce
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

declare(strict_types=1);

namespace Shared\TinyMce\Support\Facades;

use Two\Support\Facades\Facade;


class TinyMce extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'two-editeur-tinymce'; }
}