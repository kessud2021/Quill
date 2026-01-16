<?php

namespace Framework\Database;

class QueryBuilder {
    protected $connection;
    protected $table;
    protected $model;
    protected $columns = ['*'];
    protected $wheres = [];
    protected $bindings = [];
    protected $joins = [];
    protected $orderBys = [];
    protected $limit;
    protected $offset;
    protected $updates = [];

    public function __construct(Connection $connection, $table) {
        $this->connection = $connection;
        $this->table = $table;
    }

    public function setModel($model) {
        $this->model = $model;
        return $this;
    }

    public function select($columns = ['*']) {
        if (is_string($columns)) {
            $columns = [$columns];
        }
        $this->columns = $columns;
        return $this;
    }

    public function where($column, $operator = null, $value = null) {
        if ($operator === null) {
            $operator = '=';
            $value = null;
        }

        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'type' => 'basic',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
        ];

        $this->bindings[] = $value;

        return $this;
    }

    public function whereIn($column, $values) {
        $placeholders = implode(',', array_fill(0, count($values), '?'));

        $this->wheres[] = [
            'type' => 'in',
            'column' => $column,
            'placeholders' => $placeholders,
        ];

        $this->bindings = array_merge($this->bindings, $values);

        return $this;
    }

    public function whereNull($column) {
        $this->wheres[] = [
            'type' => 'null',
            'column' => $column,
        ];
        return $this;
    }

    public function whereNotNull($column) {
        $this->wheres[] = [
            'type' => 'notnull',
            'column' => $column,
        ];
        return $this;
    }

    public function join($table, $first, $operator, $second) {
        $this->joins[] = [
            'type' => 'inner',
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second,
        ];
        return $this;
    }

    public function leftJoin($table, $first, $operator, $second) {
        $this->joins[] = [
            'type' => 'left',
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second,
        ];
        return $this;
    }

    public function orderBy($column, $direction = 'asc') {
        $this->orderBys[] = [
            'column' => $column,
            'direction' => strtoupper($direction),
        ];
        return $this;
    }

    public function limit($limit) {
        $this->limit = $limit;
        return $this;
    }

    public function offset($offset) {
        $this->offset = $offset;
        return $this;
    }

    public function take($limit) {
        return $this->limit($limit);
    }

    public function skip($offset) {
        return $this->offset($offset);
    }

    public function paginate($page = 1, $perPage = 15) {
        $offset = ($page - 1) * $perPage;
        return $this->limit($perPage)->offset($offset)->get();
    }

    public function get() {
        $results = $this->connection->select($this->toSql(), $this->bindings);
        
        if ($this->model) {
            $models = [];
            foreach ($results as $result) {
                $model = $this->model->newInstance($result);
                $model->exists = true;
                $models[] = $model;
            }
            return $models;
        }

        return $results;
    }

    public function first() {
        $result = $this->limit(1)->get();
        return $result[0] ?? null;
    }

    public function count() {
        $sql = 'SELECT COUNT(*) as count FROM ' . $this->table . $this->buildWheres();
        $result = $this->connection->selectOne($sql, $this->wheres ? $this->bindings : []);
        return $result['count'] ?? 0;
    }

    public function exists() {
        return $this->first() !== null;
    }

    public function insert($data) {
        if (empty($data)) {
            return 0;
        }

        if (!is_array($data[0] ?? null)) {
            $data = [$data];
        }

        $columns = array_keys($data[0]);
        $placeholders = '(' . implode(',', array_fill(0, count($columns), '?')) . ')';
        $allPlaceholders = implode(',', array_fill(0, count($data), $placeholders));

        $sql = 'INSERT INTO ' . $this->table . ' (' . implode(',', $columns) . ') VALUES ' . $allPlaceholders;

        $bindings = [];
        foreach ($data as $row) {
            $bindings = array_merge($bindings, array_values($row));
        }

        return $this->connection->insert($sql, $bindings);
    }

    public function update($data) {
        $updates = [];
        $bindings = [];

        foreach ($data as $column => $value) {
            $updates[] = $column . ' = ?';
            $bindings[] = $value;
        }

        $sql = 'UPDATE ' . $this->table . ' SET ' . implode(',', $updates) . $this->buildWheres();
        $bindings = array_merge($bindings, $this->bindings);

        return $this->connection->update($sql, $bindings);
    }

    public function delete() {
        $sql = 'DELETE FROM ' . $this->table . $this->buildWheres();
        return $this->connection->delete($sql, $this->bindings);
    }

    public function toSql() {
        $sql = 'SELECT ' . implode(',', $this->columns) . ' FROM ' . $this->table;

        foreach ($this->joins as $join) {
            $sql .= ' ' . strtoupper($join['type']) . ' JOIN ' . $join['table'] . 
                    ' ON ' . $join['first'] . ' ' . $join['operator'] . ' ' . $join['second'];
        }

        $sql .= $this->buildWheres();

        if (!empty($this->orderBys)) {
            $orderBys = [];
            foreach ($this->orderBys as $order) {
                $orderBys[] = $order['column'] . ' ' . $order['direction'];
            }
            $sql .= ' ORDER BY ' . implode(',', $orderBys);
        }

        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        if ($this->offset !== null) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        return $sql;
    }

    protected function buildWheres() {
        if (empty($this->wheres)) {
            return '';
        }

        $whereStrings = [];

        foreach ($this->wheres as $where) {
            if ($where['type'] === 'basic') {
                $whereStrings[] = $where['column'] . ' ' . $where['operator'] . ' ?';
            } elseif ($where['type'] === 'in') {
                $whereStrings[] = $where['column'] . ' IN (' . $where['placeholders'] . ')';
            } elseif ($where['type'] === 'null') {
                $whereStrings[] = $where['column'] . ' IS NULL';
            } elseif ($where['type'] === 'notnull') {
                $whereStrings[] = $where['column'] . ' IS NOT NULL';
            }
        }

        return ' WHERE ' . implode(' AND ', $whereStrings);
    }
}
