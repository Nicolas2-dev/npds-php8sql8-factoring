<?php

declare(strict_types=1);

namespace Npds\Database;


interface ConnectionResolverInterface
{
    /**
     * Get a Database Connection instance.
     *
     * @param  string  $name
     * @return \Npds\Database\Connection
     */
    public function connection($name = null);

    /**
     * Get the default Connection name.
     *
     * @return string
     */
    public function getDefaultConnection();

    /**
     * Set the default Connection name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultConnection($name);

}
