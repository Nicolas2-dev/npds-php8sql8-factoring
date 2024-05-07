<?php
/**
 * Two - Database
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

return array(
    // Le style d'extraction du PDO.
    'fetch' => PDO::FETCH_CLASS,

    // Le nom de connexion à la base de données par défaut.
    'default' => 'mysql',

    // Les connexions à la base de données.
    'connections' => array(
        'sqlite' => array(
            'driver'    => 'sqlite',
            'database'  => BASEPATH .'storage' .DS .'database.sqlite',
            'prefix'    => '',
        ),
        'mysql' => array(
            'driver'    => 'mysql',
            'hostname'  => 'localhost',
            'database'  => 'Two',
            'username'  => 'Two',
            'password'  => 'Password',
            'prefix'    => PREFIX,
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ),
        'pgsql' => array(
            'driver'   => 'pgsql',
            'host'     => 'localhost',
            'database' => 'Two',
            'username' => 'Two',
            'password' => 'Password',
            'charset'  => 'utf8',
            'prefix'   => PREFIX,
            'schema'   => 'public',
        ),
    ),

    // Table de référentiel de migration
    'migrations' => 'migrations',
);
