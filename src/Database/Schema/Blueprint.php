<?php

namespace Framework\Database\Schema;

class Blueprint {
    protected $table;
    protected $columns = [];
    protected $primaryKeys = [];
    protected $foreignKeys = [];

    public function __construct($table) {
        $this->table = $table;
    }

    public function id() {
        return $this->increments('id')->primary();
    }

    public function increments($column) {
        return $this->addColumn('increments', $column);
    }

    public function string($column, $length = 255) {
        return $this->addColumn('string', $column, ['length' => $length]);
    }

    public function text($column) {
        return $this->addColumn('text', $column);
    }

    public function integer($column) {
        return $this->addColumn('integer', $column);
    }

    public function bigInteger($column) {
        return $this->addColumn('biginteger', $column);
    }

    public function decimal($column, $precision = 8, $scale = 2) {
        return $this->addColumn('decimal', $column, [
            'precision' => $precision,
            'scale' => $scale,
        ]);
    }

    public function boolean($column) {
        return $this->addColumn('boolean', $column);
    }

    public function timestamp($column) {
        return $this->addColumn('timestamp', $column);
    }

    public function timestamps() {
        $this->addColumn('timestamp', 'created_at', ['default' => 'CURRENT_TIMESTAMP']);
        $this->addColumn('timestamp', 'updated_at', ['default' => 'CURRENT_TIMESTAMP']);
    }

    public function softDeletes() {
        return $this->addColumn('timestamp', 'deleted_at', ['nullable' => true]);
    }

    public function date($column) {
        return $this->addColumn('date', $column);
    }

    public function json($column) {
        return $this->addColumn('json', $column);
    }

    public function foreign($column) {
        return new ForeignKeyDefinition($this, $column);
    }

    public function addForeignKey(ForeignKeyDefinition $foreignKey) {
        $this->foreignKeys[] = $foreignKey;
    }

    protected function addColumn($type, $column, $properties = []) {
        $columnDef = new ColumnDefinition($type, $column, $properties);
        $this->columns[] = $columnDef;
        return $columnDef;
    }

    public function primary($columns) {
        if (is_string($columns)) {
            $columns = [$columns];
        }
        $this->primaryKeys = $columns;
    }

    public function getColumns() {
        return $this->columns;
    }

    public function getPrimaryKeys() {
        return $this->primaryKeys;
    }

    public function getForeignKeys() {
        return $this->foreignKeys;
    }

    public function getTable() {
        return $this->table;
    }
}
