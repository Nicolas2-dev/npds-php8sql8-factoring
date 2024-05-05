<?php

declare(strict_types=1);

namespace Npds\Cache;

use Npds\Cache\StoreInterface;
use Npds\Support\Manager;


class CacheManager extends Manager
{

    /**
     * Create an instance of the array cache driver.
     *
     * @return \Npds\Cache\ArrayStore
     */
    protected function createArrayDriver()
    {
        return $this->repository(new ArrayStore);
    }

    /**
     * Create an instance of the file cache driver.
     *
     * @return \Npds\Cache\FileStore
     */
    protected function createFileDriver()
    {
        $path = $this->app['config']['cache.path'];

        return $this->repository(new FileStore($this->app['files'], $path));
    }

    /**
     * Create an instance of the database cache driver.
     *
     * @return \Npds\Cache\DatabaseStore
     */
    protected function createDatabaseDriver()
    {
        $connection = $this->getDatabaseConnection();

        $encrypter = $this->app['encrypter'];

        //
        $table = $this->app['config']['cache.table'];

        $prefix = $this->getPrefix();

        return $this->repository(new DatabaseStore($connection, $encrypter, $table, $prefix));
    }

    /**
     * Get the database connection for the database driver.
     *
     * @return \Npds\Database\Connection
     */
    protected function getDatabaseConnection()
    {
        $connection = $this->app['config']['cache.connection'];

        return $this->app['db']->connection($connection);
    }

    /**
     * Get the cache "prefix" value.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->app['config']['cache.prefix'];
    }

    /**
     * Set the cache "prefix" value.
     *
     * @param  string  $name
     * @return void
     */
    public function setPrefix($name)
    {
        $this->app['config']['cache.prefix'] = $name;
    }

    /**
     * Create a new cache repository with the given implementation.
     *
     * @param  \Npds\Cache\StoreInterface  $store
     * @return \Npds\Cache\Repository
     */
    protected function repository(StoreInterface $store)
    {
        return new Repository($store);
    }

    /**
     * Get the default cache driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['cache.driver'];
    }

    /**
     * Set the default cache driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['cache.driver'] = $name;
    }

}
