<?php

namespace Framework\Database\Schema;

use Framework\Database\Connection;

class Builder {
    protected $connection;
    protected $blueprint;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    public function create($table, $callback) {
        $blueprint = new Blueprint($table);
        $callback($blueprint);

        $sql = $this->buildCreateTableSql($blueprint);
        $this->connection->statement($sql);
    }

    public function table($table, $callback) {
        $blueprint = new Blueprint($table);
        $callback($blueprint);

        foreach ($blueprint->getColumns() as $column) {
            $sql = $this->buildAlterTableSql($table, $column);
            $this->connection->statement($sql);
        }
    }

    public function drop($table) {
        $this->connection->statement("DROP TABLE IF EXISTS $table");
    }

    public function dropIfExists($table) {
        $this->drop($table);
    }

    protected function buildCreateTableSql(Blueprint $blueprint) {
        $table = $blueprint->getTable();
        $columnDefinitions = [];

        foreach ($blueprint->getColumns() as $column) {
            $columnDefinitions[] = $this->buildColumnDefinition($column);
        }

        $sql = "CREATE TABLE IF NOT EXISTS $table (" . implode(',', $columnDefinitions);

        if ($primaryKeys = $blueprint->getPrimaryKeys()) {
            $sql .= ", PRIMARY KEY (" . implode(',', $primaryKeys) . ")";
        }

        foreach ($blueprint->getForeignKeys() as $fk) {
            $sql .= ", CONSTRAINT fk_" . $fk->getColumn() . " FOREIGN KEY (" . $fk->getColumn() . ") 
                   REFERENCES " . $fk->getOn() . "(" . $fk->getReferences() . ") 
                   ON DELETE " . $fk->getOnDelete() . " 
                   ON UPDATE " . $fk->getOnUpdate();
        }

        $sql .= ")";

        return $sql;
    }

    protected function buildAlterTableSql($table, $column) {
        return "ALTER TABLE $table ADD " . $this->buildColumnDefinition($column);
    }

    protected function buildColumnDefinition($column) {
        $definition = $column->getName() . ' ' . $this->getColumnType($column);

        if (!$column->isNullable()) {
            $definition .= ' NOT NULL';
        } else {
            $definition .= ' NULL';
        }

        if ($column->isAutoIncrement()) {
            $definition .= ' AUTO_INCREMENT';
        }

        if ($column->getDefault() !== null) {
            if (is_string($column->getDefault())) {
                $definition .= " DEFAULT '" . $column->getDefault() . "'";
            } else {
                $definition .= " DEFAULT " . $column->getDefault();
            }
        }

        if ($column->isUnique()) {
            $definition .= ' UNIQUE';
        }

        return $definition;
    }

    protected function getColumnType($column) {
        $type = $column->getType();
        $properties = $column->getProperties();

        switch ($type) {
            case 'string':
                $length = $properties['length'] ?? 255;
                return "VARCHAR($length)";
            case 'text':
                return 'TEXT';
            case 'integer':
                return 'INTEGER';
            case 'biginteger':
                return 'BIGINT';
            case 'decimal':
                $precision = $properties['precision'] ?? 8;
                $scale = $properties['scale'] ?? 2;
                return "DECIMAL($precision,$scale)";
            case 'boolean':
                return 'BOOLEAN';
            case 'timestamp':
                return 'TIMESTAMP';
            case 'date':
                return 'DATE';
            case 'json':
                return 'JSON';
            case 'increments':
                return 'INTEGER AUTO_INCREMENT';
            default:
                return 'VARCHAR(255)';
        }
    }
}
