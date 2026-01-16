<?php

namespace Framework\Database;

use PDO;
use PDOStatement;

/**
 * Database connection wrapper
 */
class Connection
{
    /**
     * PDO instance
     *
     * @var PDO
     */
    protected PDO $pdo;

    /**
     * Connection configuration
     *
     * @var array
     */
    protected array $config;

    /**
     * Create a new connection
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->connect();
    }

    /**
     * Establish the database connection
     *
     * @return void
     */
    protected function connect(): void
    {
        $driver = $this->config['driver'] ?? 'sqlite';

        switch ($driver) {
            case 'sqlite':
                $dsn = 'sqlite:' . $this->config['database'];
                break;
            case 'mysql':
            case 'mariadb':
                $host = $this->config['host'] ?? 'localhost';
                $port = $this->config['port'] ?? 3306;
                $database = $this->config['database'];
                $dsn = "mysql:host={$host};port={$port};dbname={$database}";
                break;
            case 'pgsql':
                $host = $this->config['host'] ?? 'localhost';
                $port = $this->config['port'] ?? 5432;
                $database = $this->config['database'];
                $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
                break;
            default:
                throw new \RuntimeException("Unsupported database driver: {$driver}");
        }

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try {
            $this->pdo = new PDO(
                $dsn,
                $this->config['username'] ?? null,
                $this->config['password'] ?? null,
                $options
            );
        } catch (\PDOException $e) {
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Execute a query
     *
     * @param string $query
     * @param array $bindings
     * @return PDOStatement
     */
    public function execute(string $query, array $bindings = []): PDOStatement
    {
        $statement = $this->pdo->prepare($query);
        $statement->execute($bindings);
        return $statement;
    }

    /**
     * Select data
     *
     * @param string $query
     * @param array $bindings
     * @return array
     */
    public function select(string $query, array $bindings = []): array
    {
        return $this->execute($query, $bindings)->fetchAll();
    }

    /**
     * Insert data
     *
     * @param string $table
     * @param array $values
     * @return bool
     */
    public function insert(string $table, array $values): bool
    {
        $columns = implode(',', array_keys($values));
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $query = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";

        return $this->execute($query, array_values($values))->rowCount() > 0;
    }

    /**
     * Update data
     *
     * @param string $table
     * @param array $values
     * @param string $where
     * @param array $bindings
     * @return int
     */
    public function update(string $table, array $values, string $where, array $bindings = []): int
    {
        $sets = [];
        foreach (array_keys($values) as $column) {
            $sets[] = "{$column} = ?";
        }
        $setClause = implode(',', $sets);
        $query = "UPDATE {$table} SET {$setClause} WHERE {$where}";

        $allBindings = array_merge(array_values($values), $bindings);
        return $this->execute($query, $allBindings)->rowCount();
    }

    /**
     * Delete data
     *
     * @param string $table
     * @param string $where
     * @param array $bindings
     * @return int
     */
    public function delete(string $table, string $where, array $bindings = []): int
    {
        $query = "DELETE FROM {$table} WHERE {$where}";
        return $this->execute($query, $bindings)->rowCount();
    }

    /**
     * Get the last inserted ID
     *
     * @return int
     */
    public function lastInsertId(): int
    {
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Get the PDO instance
     *
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Begin a transaction
     *
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit a transaction
     *
     * @return bool
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * Rollback a transaction
     *
     * @return bool
     */
    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }
}
