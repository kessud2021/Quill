<?php

namespace Framework\Support;

class Collection implements \ArrayAccess, \Countable, \Iterator {
    protected $items = [];
    protected $position = 0;

    public function __construct($items = []) {
        $this->items = array_values((array)$items);
    }

    public function all() {
        return $this->items;
    }

    public function count(): int {
        return count($this->items);
    }

    public function push($item) {
        $this->items[] = $item;
        return $this;
    }

    public function pop() {
        return array_pop($this->items);
    }

    public function shift() {
        return array_shift($this->items);
    }

    public function unshift($item) {
        array_unshift($this->items, $item);
        return $this;
    }

    public function map($callback) {
        return new static(array_map($callback, $this->items));
    }

    public function filter($callback) {
        return new static(array_filter($this->items, $callback));
    }

    public function reduce($callback, $initial = null) {
        return array_reduce($this->items, $callback, $initial);
    }

    public function first($default = null) {
        return $this->items[0] ?? $default;
    }

    public function last($default = null) {
        return end($this->items) ?: $default;
    }

    public function pluck($key) {
        return new static(array_column($this->items, $key));
    }

    public function unique($key = null) {
        if ($key === null) {
            return new static(array_unique($this->items));
        }

        $unique = [];
        $values = [];

        foreach ($this->items as $item) {
            $value = is_array($item) ? $item[$key] : $item->$key;

            if (!in_array($value, $values)) {
                $values[] = $value;
                $unique[] = $item;
            }
        }

        return new static($unique);
    }

    public function chunk($size) {
        return new static(array_chunk($this->items, $size));
    }

    public function reverse() {
        return new static(array_reverse($this->items));
    }

    public function sort() {
        sort($this->items);
        return $this;
    }

    public function sortBy($key) {
        usort($this->items, function ($a, $b) use ($key) {
            $aValue = is_array($a) ? $a[$key] : $a->$key;
            $bValue = is_array($b) ? $b[$key] : $b->$key;
            return $aValue <=> $bValue;
        });

        return $this;
    }

    public function offsetExists($offset): bool {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset): mixed {
        return $this->items[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void {
        if ($offset === null) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void {
        unset($this->items[$offset]);
    }

    public function current(): mixed {
        return $this->items[$this->position] ?? null;
    }

    public function key(): mixed {
        return $this->position;
    }

    public function next(): void {
        $this->position++;
    }

    public function rewind(): void {
        $this->position = 0;
    }

    public function valid(): bool {
        return isset($this->items[$this->position]);
    }
}
