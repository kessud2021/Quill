<?php

namespace Framework\Database;

class Factory {
    protected $model;
    protected $attributes = [];
    protected $count = 1;

    public function __construct($model) {
        $this->model = $model;
    }

    public function definition() {
        return [];
    }

    public function state($state) {
        // Override attributes for specific state
        return $this;
    }

    public function attributes($attributes = []) {
        $this->attributes = array_merge($this->definition(), $attributes);
        return $this;
    }

    public function count($count) {
        $this->count = $count;
        return $this;
    }

    public function create($attributes = []) {
        $attributes = array_merge($this->definition(), $attributes);

        $models = [];

        for ($i = 0; $i < $this->count; $i++) {
            $models[] = $this->model::create($attributes);
        }

        return $this->count === 1 ? $models[0] : $models;
    }

    public function make($attributes = []) {
        $attributes = array_merge($this->definition(), $attributes);

        $models = [];

        for ($i = 0; $i < $this->count; $i++) {
            $models[] = new $this->model($attributes);
        }

        return $this->count === 1 ? $models[0] : $models;
    }
}
