<?php

namespace Framework\Database;

use PDO;

class Connection {
    protected $pdo;
    protected $transactions = 0;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getPdo() {
        return $this->pdo;
    }

    public function statement($sql, $bindings = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->prepareBindings($bindings));
        return $stmt;
    }

    public function select($sql, $bindings = []) {
        return $this->statement($sql, $bindings)->fetchAll();
    }

    public function selectOne($sql, $bindings = []) {
        $results = $this->select($sql, $bindings);
        return $results[0] ?? null;
    }

    public function insert($sql, $bindings = []) {
        $this->statement($sql, $bindings);
        return $this->pdo->lastInsertId();
    }

    public function update($sql, $bindings = []) {
        return $this->statement($sql, $bindings)->rowCount();
    }

    public function delete($sql, $bindings = []) {
        return $this->statement($sql, $bindings)->rowCount();
    }

    protected function prepareBindings($bindings) {
        foreach ($bindings as &$binding) {
            if ($binding instanceof \DateTime) {
                $binding = $binding->format('Y-m-d H:i:s');
            }
        }
        return $bindings;
    }

    public function beginTransaction() {
        if ($this->transactions === 0) {
            $this->pdo->beginTransaction();
        }
        $this->transactions++;
    }

    public function commit() {
        if ($this->transactions === 1) {
            $this->pdo->commit();
        }
        $this->transactions--;
    }

    public function rollback() {
        if ($this->transactions === 1) {
            $this->pdo->rollBack();
        }
        $this->transactions--;
    }

    public function transaction($callback) {
        $this->beginTransaction();

        try {
            $result = call_user_func($callback, $this);
            $this->commit();
            return $result;
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    public function query($sql, $bindings = []) {
        return $this->statement($sql, $bindings);
    }

    public function sql($sql, $bindings = []) {
        return $this->statement($sql, $bindings)->fetchAll();
    }

    public function raw($sql) {
        return $this->pdo->query($sql)->fetchAll();
    }
}
