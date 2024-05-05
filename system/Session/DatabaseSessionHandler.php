<?php

declare(strict_types=1);

namespace Npds\Session;

use Npds\Database\Connection;
use Npds\Session\ExistenceAwareInterface;


class DatabaseSessionHandler implements \SessionHandlerInterface, ExistenceAwareInterface
{
    /**
     * The database connection instance.
     *
     * @var \Npds\Database\Connection
     */
    protected $connection;

    /**
     * The name of the session table.
     *
     * @var string
     */
    protected $table;

    /**
     * The existence state of the session.
     *
     * @var bool
     */
    protected $exists;

    /**
     * Create a new database session handler instance.
     *
     * @param  \Npds\Database\Connection  $connection
     * @param  string  $table
     * @return void
     */
    public function __construct(Connection $connection, $table)
    {
        $this->table = $table;
        $this->connection = $connection;
    }

    /**
     * {@inheritDoc}
     */
    public function open($savePath, $sessionName): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function read($sessionId): string
    {
        $session = (object) $this->getQuery()->find($sessionId);

        if (isset($session->payload))
        {
            $this->exists = true;

            return base64_decode($session->payload);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function write($sessionId, $data): bool
    {
        if ($this->exists) {
            $this->getQuery()->where('id', $sessionId)->update(array(
                'payload' => base64_encode($data),
                'last_activity' => time(),
            ));
        } else {
            $this->getQuery()->insert(array(
                'id' => $sessionId,
                'payload' => base64_encode($data),
                'last_activity' => time(),
            ));
        }

        $this->exists = true;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function destroy($sessionId): bool
    {
        $this->getQuery()->where('id', $sessionId)->delete();

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function gc($lifetime): int
    {
        $this->getQuery()->where('last_activity', '<=', (time() - $lifetime))->delete();

        return 0;
    }

    /**
     * Get a fresh query builder instance for the table.
     *
     * @return \Npds\Database\Query\Builder
     */
    protected function getQuery()
    {
        return $this->connection->table($this->table);
    }

    /**
     * Set the existence state for the session.
     *
     * @param  bool  $value
     * @return $this
     */
    public function setExists($value)
    {
        $this->exists = $value;

        return $this;
    }

}
