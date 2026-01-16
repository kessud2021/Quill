<?php

namespace Framework\Database\Schema;

class ForeignKeyDefinition {
    protected $blueprint;
    protected $column;
    protected $references;
    protected $on;
    protected $onDelete = 'NO ACTION';
    protected $onUpdate = 'NO ACTION';

    public function __construct(Blueprint $blueprint, $column) {
        $this->blueprint = $blueprint;
        $this->column = $column;
    }

    public function references($column) {
        $this->references = $column;
        return $this;
    }

    public function on($table) {
        $this->on = $table;
        $this->blueprint->addForeignKey($this);
        return $this;
    }

    public function onDelete($action) {
        $this->onDelete = $action;
        return $this;
    }

    public function onUpdate($action) {
        $this->onUpdate = $action;
        return $this;
    }

    public function getColumn() {
        return $this->column;
    }

    public function getReferences() {
        return $this->references;
    }

    public function getOn() {
        return $this->on;
    }

    public function getOnDelete() {
        return $this->onDelete;
    }

    public function getOnUpdate() {
        return $this->onUpdate;
    }
}
