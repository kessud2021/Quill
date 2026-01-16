<?php

namespace Framework\Database;

use DateTime;
use Framework\Support\Collection;

/**
 * Base model class with Active Record pattern
 */
abstract class Model
{
    /**
     * Table name (inferred from class name if not set)
     *
     * @var string|null
     */
    protected ?string $table = null;

    /**
     * Primary key column
     *
     * @var string
     */
    protected string $primaryKey = 'id';

    /**
     * Model attributes
     *
     * @var array
     */
    protected array $attributes = [];

    /**
     * Original attributes
     *
     * @var array
     */
    protected array $original = [];

    /**
     * Relationships
     *
     * @var array
     */
    protected array $relations = [];

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected array $fillable = [];

    /**
     * Soft delete column
     *
     * @var string|null
     */
    protected ?string $softDeleteColumn = 'deleted_at';

    /**
     * Get the table name
     *
     * @return string
     */
    public function getTable(): string
    {
        if ($this->table !== null) {
            return $this->table;
        }

        // Infer from class name: User -> users
        $className = class_basename($this);
        return strtolower($className) . 's';
    }

    /**
     * Get a new query builder
     *
     * @return QueryBuilder
     */
    public static function query(): QueryBuilder
    {
        $instance = new static();
        return app(\Framework\Database\Manager::class)->table($instance->getTable());
    }

    /**
     * Get all records
     *
     * @return Collection
     */
    public static function all(): Collection
    {
        return new Collection(static::query()->get());
    }

    /**
     * Find a record by primary key
     *
     * @param mixed $id
     * @return static|null
     */
    public static function find($id): ?static
    {
        $instance = new static();
        $result = static::query()
            ->where($instance->primaryKey, $id)
            ->first();

        if ($result) {
            return $instance->hydrate($result);
        }

        return null;
    }

    /**
     * Find a record or throw exception
     *
     * @param mixed $id
     * @return static
     */
    public static function findOrFail($id): static
    {
        $record = static::find($id);
        if (!$record) {
            abort(404, 'Record not found');
        }
        return $record;
    }

    /**
     * Find by column value
     *
     * @param string $column
     * @param mixed $value
     * @return static|null
     */
    public static function findBy(string $column, $value): ?static
    {
        $instance = new static();
        $result = static::query()
            ->where($column, $value)
            ->first();

        if ($result) {
            return $instance->hydrate($result);
        }

        return null;
    }

    /**
     * Create a new record
     *
     * @param array $attributes
     * @return static
     */
    public static function create(array $attributes): static
    {
        $instance = new static();
        $instance->fill($attributes);
        $instance->save();
        return $instance;
    }

    /**
     * Update or create a record
     *
     * @param array $attributes
     * @param array $values
     * @return static
     */
    public static function updateOrCreate(array $attributes, array $values = []): static
    {
        $record = static::query();

        foreach ($attributes as $key => $value) {
            $record->where($key, $value);
        }

        $result = $record->first();

        if ($result) {
            $instance = (new static())->hydrate($result);
            $instance->fill($values);
            $instance->save();
            return $instance;
        }

        return static::create(array_merge($attributes, $values));
    }

    /**
     * Fill attributes
     *
     * @param array $attributes
     * @return self
     */
    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if (empty($this->fillable) || in_array($key, $this->fillable)) {
                $this->attributes[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * Hydrate from database result
     *
     * @param array $attributes
     * @return self
     */
    public function hydrate(array $attributes): self
    {
        $this->attributes = $attributes;
        $this->original = $attributes;
        return $this;
    }

    /**
     * Save the model
     *
     * @return bool
     */
    public function save(): bool
    {
        if (isset($this->attributes[$this->primaryKey])) {
            // Update
            $changes = array_diff_assoc($this->attributes, $this->original);
            if (empty($changes)) {
                return true;
            }

            static::query()
                ->where($this->primaryKey, $this->attributes[$this->primaryKey])
                ->update($changes);

            $this->original = $this->attributes;
            return true;
        } else {
            // Insert
            $result = static::query()->insert($this->attributes);

            if ($result) {
                $id = app(\Framework\Database\Manager::class)
                    ->getCurrentConnection()
                    ->lastInsertId();

                $this->attributes[$this->primaryKey] = $id;
                $this->original = $this->attributes;
            }

            return $result;
        }
    }

    /**
     * Delete the model
     *
     * @return bool
     */
    public function delete(): bool
    {
        if ($this->softDeleteColumn && isset($this->attributes[$this->primaryKey])) {
            $this->attributes[$this->softDeleteColumn] = date('Y-m-d H:i:s');
            return $this->save();
        }

        if (isset($this->attributes[$this->primaryKey])) {
            return static::query()
                ->where($this->primaryKey, $this->attributes[$this->primaryKey])
                ->delete() > 0;
        }

        return false;
    }

    /**
     * Restore a soft-deleted model
     *
     * @return bool
     */
    public function restore(): bool
    {
        if ($this->softDeleteColumn && isset($this->attributes[$this->primaryKey])) {
            unset($this->attributes[$this->softDeleteColumn]);
            return $this->save();
        }

        return false;
    }

    /**
     * Force delete (ignore soft deletes)
     *
     * @return bool
     */
    public function forceDelete(): bool
    {
        if (isset($this->attributes[$this->primaryKey])) {
            return static::query()
                ->where($this->primaryKey, $this->attributes[$this->primaryKey])
                ->delete() > 0;
        }

        return false;
    }

    /**
     * Get an attribute
     *
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }

        if (isset($this->relations[$key])) {
            return $this->relations[$key];
        }

        return null;
    }

    /**
     * Set an attribute
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Check if attribute exists
     *
     * @param string $key
     * @return bool
     */
    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]) || isset($this->relations[$key]);
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_merge($this->attributes, $this->relations);
    }

    /**
     * Convert to JSON
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
