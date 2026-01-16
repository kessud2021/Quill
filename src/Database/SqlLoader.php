<?php

namespace Framework\Database;

class SqlLoader {
    protected $sqlPath;
    protected $connection;

    public function __construct(Connection $connection, $sqlPath = null) {
        $this->connection = $connection;
        $this->sqlPath = $sqlPath ?? __DIR__ . '/sql';
    }

    public function execute($file, $bindings = []) {
        $sql = $this->load($file);
        return $this->connection->query($sql, $bindings);
    }

    public function query($file, $bindings = []) {
        $sql = $this->load($file);
        return $this->connection->sql($sql, $bindings);
    }

    public function select($file, $bindings = []) {
        return $this->query($file, $bindings);
    }

    public function insert($file, $bindings = []) {
        return $this->execute($file, $bindings);
    }

    public function update($file, $bindings = []) {
        return $this->execute($file, $bindings);
    }

    public function delete($file, $bindings = []) {
        return $this->execute($file, $bindings);
    }

    public function load($file) {
        $path = $this->getPath($file);

        if (!file_exists($path)) {
            throw new \Exception("SQL file not found: $file at $path");
        }

        $sql = file_get_contents($path);

        if (!$sql) {
            throw new \Exception("SQL file is empty: $file");
        }

        return trim($sql);
    }

    protected function getPath($file) {
        $file = ltrim($file, '/');

        if (!str_ends_with($file, '.sql')) {
            $file .= '.sql';
        }

        return $this->sqlPath . '/' . $file;
    }

    public function exists($file) {
        return file_exists($this->getPath($file));
    }

    public function all() {
        if (!is_dir($this->sqlPath)) {
            return [];
        }

        $files = glob($this->sqlPath . '/*.sql');
        return array_map('basename', $files);
    }
}
