<?php

namespace Framework\Database;

use Framework\Config\Config;

/**
 * Database manager for connection pooling
 */
class Manager
{
    /**
     * Configuration
     *
     * @var Config
     */
    protected Config $config;

    /**
     * Connections
     *
     * @var array
     */
    protected array $connections = [];

    /**
     * Current connection name
     *
     * @var string
     */
    protected string $currentConnection;

    /**
     * Create a new manager
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->currentConnection = $config->get('database.default', 'sqlite');
    }

    /**
     * Get a connection
     *
     * @param string|null $name
     * @return Connection
     */
    public function connection(?string $name = null): Connection
    {
        $name = $name ?? $this->currentConnection;

        if (!isset($this->connections[$name])) {
            $config = $this->config->get("database.connections.{$name}");
            
            if (!$config) {
                throw new \RuntimeException("Database connection [{$name}] not found");
            }

            $this->connections[$name] = new Connection($config);
        }

        return $this->connections[$name];
    }

    /**
     * Get a query builder for a table
     *
     * @param string $table
     * @return QueryBuilder
     */
    public function table(string $table): QueryBuilder
    {
        return new QueryBuilder($this->connection(), $table);
    }

    /**
     * Set the current connection
     *
     * @param string $name
     * @return self
     */
    public function use(string $name): self
    {
        $this->currentConnection = $name;
        return $this;
    }

    /**
     * Get the current connection
     *
     * @return Connection
     */
    public function getCurrentConnection(): Connection
    {
        return $this->connection();
    }
}
