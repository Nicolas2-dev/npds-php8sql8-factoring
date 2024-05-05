<?php

declare(strict_types=1);

namespace Npds\Validation;

use Npds\Database\ConnectionResolverInterface;


class DatabasePresenceVerifier
{
    /**
     * The Database Connection Resolver implementation.
     *
     * @var  \Npds\Database\ConnectionResolverInterface
     */
    protected $db;

    /**
     * The database connection to use.
     *
     * @var string
     */
    protected $connection = null;


    /**
     * Create a new database presence verifier.
     *
     * @param  \Npds\Database\ConnectionResolverInterface  $db
     * @return void
     */
    public function __construct(ConnectionResolverInterface $db)
    {
        $this->db = $db;
    }

    /**
     * Count the number of objects in a collection having the given value.
     *
     * @param  string  $collection
     * @param  string  $column
     * @param  string  $value
     * @param  int     $excludeId
     * @param  string  $idColumn
     * @param  array   $extra
     * @return int
     */
    public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = array())
    {
        $query = $this->table($collection)->where($column, '=', $value);

        if (! is_null($excludeId) && ($excludeId !== 'NULL')) {
            $query->where($idColumn ?: 'id', '<>', $excludeId);
        }

        foreach ($extra as $key => $extraValue) {
            $this->addWhere($query, $key, $extraValue);
        }

        return $query->count();
    }

    /**
     * Count the number of objects in a collection with the given values.
     *
     * @param  string  $collection
     * @param  string  $column
     * @param  array   $values
     * @param  array   $extra
     * @return int
     */
    public function getMultiCount($collection, $column, array $values, array $extra = array())
    {
        $query = $this->table($collection)->whereIn($column, $values);

        foreach ($extra as $key => $extraValue) {
            $this->addWhere($query, $key, $extraValue);
        }

        return $query->count();
    }

    /**
     * Add a "where" clause to the given query.
     *
     * @param  \Npds\Database\Query  $query
     * @param  string  $key
     * @param  string  $value
     * @return void
     */
    protected function addWhere($query, $key, $value)
    {
        if ($value === 'NULL') {
            $query->whereNull($key);
        } else if ($value === 'NOT_NULL') {
            $query->whereNotNull($key);
        } else {
            $query->where($key, $value);
        }
    }

    /**
     * Get a query builder for the given table.
     *
     * @param  string  $table
     * @return \Npds\Database\Query
     */
    protected function table($table)
    {
        $connection = $this->db->connection($this->connection);

        return $connection->table($table);
    }

    /**
     * Set the connection to be used.
     *
     * @param  string  $connection
     * @return void
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

}
