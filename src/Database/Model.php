<?php

namespace Framework\Database;

use DateTime;

class Model {
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $attributes = [];
    protected $original = [];
    protected $exists = false;
    protected $softDelete = false;
    protected $timestamps = true;

    public function __construct($attributes = []) {
        $this->fill($attributes);
    }

    public function newInstance($attributes = []) {
        $model = new static($attributes);
        $model->exists = true;
        $model->original = $attributes;
        return $model;
    }

    public function fill($attributes) {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable) || empty($this->fillable)) {
                $this->setAttribute($key, $value);
            }
        }
        return $this;
    }

    public function setAttribute($key, $value) {
        $this->attributes[$key] = $value;
        if ($this->exists) {
            $this->original[$key] = $value;
        }
    }

    public function getAttribute($key) {
        return $this->attributes[$key] ?? null;
    }

    public function __get($key) {
        return $this->getAttribute($key);
    }

    public function __set($key, $value) {
        $this->setAttribute($key, $value);
    }

    public function toArray() {
        return $this->attributes;
    }

    public static function create($attributes = []) {
        $model = new static($attributes);
        $model->save();
        return $model;
    }

    public static function find($id) {
        return static::where((new static())->primaryKey, $id)->first();
    }

    public static function findOrFail($id) {
        $model = static::find($id);
        if (!$model) {
            throw new \Exception("Model not found: " . static::class);
        }
        return $model;
    }

    public static function all() {
        return static::query()->get();
    }

    public static function where($column, $operator = null, $value = null) {
        return static::query()->where($column, $operator, $value);
    }

    public static function query() {
        $model = new static();
        return app('db')->table($model->getTable())->setModel($model);
    }

    public function save() {
        if ($this->exists) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }

    protected function insert() {
        if ($this->timestamps) {
            $this->setAttribute('created_at', (new DateTime())->format('Y-m-d H:i:s'));
            $this->setAttribute('updated_at', (new DateTime())->format('Y-m-d H:i:s'));
        }

        $attributes = $this->attributes;

        $id = app('db')->table($this->getTable())->insert($attributes);

        if ($id) {
            $this->setAttribute($this->primaryKey, $id);
        }

        $this->exists = true;
        $this->original = $this->attributes;

        return $this;
    }

    protected function update() {
        if ($this->timestamps) {
            $this->setAttribute('updated_at', (new DateTime())->format('Y-m-d H:i:s'));
        }

        $changes = array_diff_assoc($this->attributes, $this->original);

        if (empty($changes)) {
            return $this;
        }

        app('db')->table($this->getTable())
            ->where($this->primaryKey, $this->getAttribute($this->primaryKey))
            ->update($changes);

        $this->original = $this->attributes;

        return $this;
    }

    public function delete() {
        if ($this->softDelete) {
            return $this->update(['deleted_at' => (new DateTime())->format('Y-m-d H:i:s')]);
        }

        return app('db')->table($this->getTable())
            ->where($this->primaryKey, $this->getAttribute($this->primaryKey))
            ->delete();
    }

    public function restore() {
        if (!$this->softDelete) {
            return false;
        }

        return $this->update(['deleted_at' => null]);
    }

    public function hasOne($related, $foreignKey = null, $localKey = null) {
        $localKey = $localKey ?: $this->primaryKey;
        $foreignKey = $foreignKey ?: str_singular($this->getTable()) . '_' . $localKey;

        return (new $related())->where($foreignKey, $this->getAttribute($localKey))->first();
    }

    public function hasMany($related, $foreignKey = null, $localKey = null) {
        $localKey = $localKey ?: $this->primaryKey;
        $foreignKey = $foreignKey ?: str_singular($this->getTable()) . '_' . $localKey;

        return (new $related())->where($foreignKey, $this->getAttribute($localKey))->get();
    }

    public function belongsTo($related, $foreignKey = null, $localKey = null) {
        $relatedInstance = new $related();
        $foreignKey = $foreignKey ?: str_singular($relatedInstance->getTable()) . '_' . ($localKey ?: 'id');

        return $relatedInstance->where('id', $this->getAttribute($foreignKey))->first();
    }

    public function belongsToMany($related, $pivot = null, $foreignKey = null, $relatedKey = null) {
        // Implementation would go here
    }

    public function getTable() {
        return $this->table ?? strtolower(class_basename($this)) . 's';
    }

    public function getPrimaryKey() {
        return $this->primaryKey;
    }

    public static function new() {
        return new static();
    }
}

function str_singular($string) {
    $singular = [
        'ches' => 'ch', 'ges' => 'g', 'xes' => 'x', 'zzes' => 'zz',
        'ses' => 's', 'oes' => 'o', 'ies' => 'y', 'us' => 'us',
        's' => ''
    ];

    foreach ($singular as $pattern => $replacement) {
        if (substr($string, -strlen($pattern)) === $pattern) {
            return substr($string, 0, -strlen($pattern)) . $replacement;
        }
    }

    return $string;
}

function class_basename($class) {
    $class = is_object($class) ? get_class($class) : $class;
    return basename(str_replace('\\', '/', $class));
}
