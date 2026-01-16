<?php

namespace Framework\Database;

use PDO;

class Manager {
    protected $config;
    protected $connections = [];
    protected $defaultConnection;
    protected $sqlLoader;

    public function __construct($config = []) {
        $this->config = $config;
        $this->defaultConnection = $config['default'] ?? 'sqlite';
    }

    public function connection($name = null) {
        $name = $name ?? $this->defaultConnection;

        if (!isset($this->connections[$name])) {
            $this->connections[$name] = $this->createConnection($name);
        }

        return $this->connections[$name];
    }

    protected function createConnection($name) {
        $config = $this->config['connections'][$name] ?? [];

        if (!$config) {
            throw new \Exception("Database connection [$name] not found");
        }

        $driver = $config['driver'] ?? 'sqlite';

        switch ($driver) {
            case 'mysql':
            case 'mariadb':
                return $this->createMysqlConnection($config);
            case 'pgsql':
                return $this->createPgsqlConnection($config);
            case 'sqlite':
            default:
                return $this->createSqliteConnection($config);
        }
    }

    protected function createMysqlConnection($config) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $config['host'] ?? 'localhost',
            $config['port'] ?? 3306,
            $config['database'] ?? ''
        );

        return new Connection(new PDO(
            $dsn,
            $config['username'] ?? 'root',
            $config['password'] ?? '',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            ]
        ));
    }

    protected function createPgsqlConnection($config) {
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            $config['host'] ?? 'localhost',
            $config['port'] ?? 5432,
            $config['database'] ?? ''
        );

        return new Connection(new PDO(
            $dsn,
            $config['username'] ?? 'postgres',
            $config['password'] ?? '',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        ));
    }

    protected function createSqliteConnection($config) {
        $path = $config['database'] ?? ':memory:';

        if ($path !== ':memory:' && !file_exists($path)) {
            touch($path);
        }

        return new Connection(new PDO(
            'sqlite:' . $path,
            null,
            null,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        ));
    }

    public function table($table) {
        return new QueryBuilder($this->connection(), $table);
    }

    public function statement($sql, $bindings = []) {
        return $this->connection()->statement($sql, $bindings);
    }

    public function select($sql, $bindings = []) {
        return $this->connection()->select($sql, $bindings);
    }

    public function query($sql, $bindings = []) {
        return $this->connection()->query($sql, $bindings);
    }

    public function sql($sql, $bindings = []) {
        return $this->connection()->sql($sql, $bindings);
    }

    public function raw($sql) {
        return $this->connection()->raw($sql);
    }

    public function sql() {
        if (!$this->sqlLoader) {
            $this->sqlLoader = new SqlLoader($this->connection(), FRAMEWORK_PATH . '/src/Database/sql');
        }
        return $this->sqlLoader;
    }
}
