<?php

namespace Framework\Database\Schema;

class ColumnDefinition {
    protected $type;
    protected $name;
    protected $properties = [];
    protected $isNullable = false;
    protected $isPrimary = false;
    protected $isUnique = false;
    protected $defaultValue;
    protected $autoIncrement = false;

    public function __construct($type, $name, $properties = []) {
        $this->type = $type;
        $this->name = $name;
        $this->properties = $properties;
    }

    public function nullable() {
        $this->isNullable = true;
        return $this;
    }

    public function primary() {
        $this->isPrimary = true;
        return $this;
    }

    public function unique() {
        $this->isUnique = true;
        return $this;
    }

    public function default($value) {
        $this->defaultValue = $value;
        return $this;
    }

    public function autoIncrement() {
        $this->autoIncrement = true;
        return $this;
    }

    public function getType() {
        return $this->type;
    }

    public function getName() {
        return $this->name;
    }

    public function getProperties() {
        return $this->properties;
    }

    public function isNullable() {
        return $this->isNullable;
    }

    public function isPrimary() {
        return $this->isPrimary;
    }

    public function isUnique() {
        return $this->isUnique;
    }

    public function getDefault() {
        return $this->defaultValue;
    }

    public function isAutoIncrement() {
        return $this->autoIncrement;
    }
}
