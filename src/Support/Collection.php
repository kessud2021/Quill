<?php

namespace Framework\Support;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;

/**
 * Collection class for working with arrays
 */
class Collection implements Countable, IteratorAggregate, JsonSerializable
{
    /**
     * Items in the collection
     *
     * @var array
     */
    protected array $items;

    /**
     * Create a new collection
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = array_values($items);
    }

    /**
     * Get an item by key
     *
     * @param int|string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->items[$key] ?? $default;
    }

    /**
     * Add an item to the collection
     *
     * @param mixed $item
     * @return self
     */
    public function add($item): self
    {
        $this->items[] = $item;
        return $this;
    }

    /**
     * Remove an item by key
     *
     * @param int|string $key
     * @return mixed
     */
    public function forget($key)
    {
        $item = $this->items[$key] ?? null;
        unset($this->items[$key]);
        return $item;
    }

    /**
     * Filter items
     *
     * @param callable $callback
     * @return self
     */
    public function filter(callable $callback): self
    {
        $items = array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH);
        return new static($items);
    }

    /**
     * Map over items
     *
     * @param callable $callback
     * @return self
     */
    public function map(callable $callback): self
    {
        $items = array_map($callback, $this->items);
        return new static($items);
    }

    /**
     * Reduce items
     *
     * @param callable $callback
     * @param mixed $initial
     * @return mixed
     */
    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->items, $callback, $initial);
    }

    /**
     * Get all items
     *
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Count items
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Get iterator
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Convert to JSON
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->items;
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * Check if empty
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return count($this->items) === 0;
    }

    /**
     * Get first item
     *
     * @return mixed
     */
    public function first()
    {
        return $this->items[0] ?? null;
    }

    /**
     * Get last item
     *
     * @return mixed
     */
    public function last()
    {
        return end($this->items) ?: null;
    }

    /**
     * Pluck values from items
     *
     * @param string $key
     * @return self
     */
    public function pluck(string $key): self
    {
        $items = array_map(function ($item) use ($key) {
            return is_array($item) ? $item[$key] ?? null : $item->$key ?? null;
        }, $this->items);
        return new static($items);
    }

    /**
     * Group items
     *
     * @param callable $callback
     * @return array
     */
    public function groupBy(callable $callback): array
    {
        $groups = [];
        foreach ($this->items as $item) {
            $key = $callback($item);
            $groups[$key][] = $item;
        }
        return $groups;
    }
}
