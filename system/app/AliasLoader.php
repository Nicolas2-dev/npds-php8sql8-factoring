<?php

declare(strict_types=1);

namespace npds\system\app;

use RuntimeException;
use npds\system\config\Config;


class AliasLoader
{

    /**
     * Bootstrap the Aliases Loader.
     *
     * @return void
     */
    public static function initialize()
    {
        $classes = Config::get('npds.aliases', array());

        foreach ($classes as $classAlias => $className) {
            // This ensures the alias is created in the global namespace.
            $classAlias = '\\' .ltrim($classAlias, '\\');

            // Check if the Class already exists.
            if (class_exists($classAlias)) {
                // Bail out, a Class already exists with the same name.
                throw new RuntimeException('A class [' .$classAlias .'] already exists with the same name.');
            }

            class_alias($className, $classAlias);
        }
    }
}