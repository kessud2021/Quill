<?php

namespace Framework\Database;

/**
 * Fluent SQL query builder
 */
class QueryBuilder
{
    /**
     * Database connection
     *
     * @var Connection
     */
    protected Connection $connection;

    /**
     * Table name
     *
     * @var string
     */
    protected string $table;

    /**
     * SELECT clause columns
     *
     * @var array
     */
    protected array $selects = [];

    /**
     * WHERE conditions
     *
     * @var array
     */
    protected array $wheres = [];

    /**
     * Bindings for query
     *
     * @var array
     */
    protected array $bindings = [];

    /**
     * ORDER BY clauses
     *
     * @var array
     */
    protected array $orders = [];

    /**
     * LIMIT value
     *
     * @var int|null
     */
    protected ?int $limit = null;

    /**
     * OFFSET value
     *
     * @var int|null
     */
    protected ?int $offset = null;

    /**
     * JOIN clauses
     *
     * @var array
     */
    protected array $joins = [];

    /**
     * GROUP BY clauses
     *
     * @var array
     */
    protected array $groups = [];

    /**
     * Create a new query builder
     *
     * @param Connection $connection
     * @param string $table
     */
    public function __construct(Connection $connection, string $table)
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    /**
     * Select columns
     *
     * @param array|string ...$columns
     * @return self
     */
    public function select(...$columns): self
    {
        if (empty($columns)) {
            $columns = ['*'];
        } elseif (count($columns) === 1 && is_array($columns[0])) {
            $columns = $columns[0];
        }

        $this->selects = array_merge($this->selects, $columns);
        return $this;
    }

    /**
     * Add a WHERE condition
     *
     * @param string $column
     * @param string|null $operator
     * @param mixed $value
     * @return self
     */
    public function where(string $column, ?string $operator = null, $value = null): self
    {
        if ($value === null && $operator !== null) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => 'AND',
        ];
        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Add an OR WHERE condition
     *
     * @param string $column
     * @param string|null $operator
     * @param mixed $value
     * @return self
     */
    public function orWhere(string $column, ?string $operator = null, $value = null): self
    {
        if ($value === null && $operator !== null) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => 'OR',
        ];
        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Add a WHERE IN condition
     *
     * @param string $column
     * @param array $values
     * @return self
     */
    public function whereIn(string $column, array $values): self
    {
        if (empty($values)) {
            return $this;
        }

        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->wheres[] = [
            'raw' => "{$column} IN ({$placeholders})",
            'boolean' => 'AND',
        ];
        $this->bindings = array_merge($this->bindings, $values);

        return $this;
    }

    /**
     * Add a WHERE NOT IN condition
     *
     * @param string $column
     * @param array $values
     * @return self
     */
    public function whereNotIn(string $column, array $values): self
    {
        if (empty($values)) {
            return $this;
        }

        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->wheres[] = [
            'raw' => "{$column} NOT IN ({$placeholders})",
            'boolean' => 'AND',
        ];
        $this->bindings = array_merge($this->bindings, $values);

        return $this;
    }

    /**
     * Add a WHERE NULL condition
     *
     * @param string $column
     * @return self
     */
    public function whereNull(string $column): self
    {
        $this->wheres[] = [
            'raw' => "{$column} IS NULL",
            'boolean' => 'AND',
        ];
        return $this;
    }

    /**
     * Add a WHERE NOT NULL condition
     *
     * @param string $column
     * @return self
     */
    public function whereNotNull(string $column): self
    {
        $this->wheres[] = [
            'raw' => "{$column} IS NOT NULL",
            'boolean' => 'AND',
        ];
        return $this;
    }

    /**
     * Order by a column
     *
     * @param string $column
     * @param string $direction
     * @return self
     */
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orders[] = "{$column} {$direction}";
        return $this;
    }

    /**
     * Group by columns
     *
     * @param array|string ...$columns
     * @return self
     */
    public function groupBy(...$columns): self
    {
        if (count($columns) === 1 && is_array($columns[0])) {
            $columns = $columns[0];
        }
        $this->groups = array_merge($this->groups, $columns);
        return $this;
    }

    /**
     * Set the limit
     *
     * @param int $limit
     * @return self
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Set the offset
     *
     * @param int $offset
     * @return self
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Add a join clause
     *
     * @param string $table
     * @param string $on
     * @param string $type
     * @return self
     */
    public function join(string $table, string $on, string $type = 'INNER'): self
    {
        $this->joins[] = "{$type} JOIN {$table} ON {$on}";
        return $this;
    }

    /**
     * Get all records
     *
     * @return array
     */
    public function get(): array
    {
        $query = $this->toSql();
        return $this->connection->select($query, $this->bindings);
    }

    /**
     * Get the first record
     *
     * @return array|null
     */
    public function first(): ?array
    {
        $results = $this->limit(1)->get();
        return $results[0] ?? null;
    }

    /**
     * Count records
     *
     * @return int
     */
    public function count(): int
    {
        $originalSelects = $this->selects;
        $this->selects = ['COUNT(*) as count'];

        $result = $this->first();
        $this->selects = $originalSelects;

        return (int)($result['count'] ?? 0);
    }

    /**
     * Insert a record
     *
     * @param array $values
     * @return bool
     */
    public function insert(array $values): bool
    {
        return $this->connection->insert($this->table, $values);
    }

    /**
     * Update records
     *
     * @param array $values
     * @return int
     */
    public function update(array $values): int
    {
        $where = $this->buildWhereClause();
        return $this->connection->update($this->table, $values, $where, $this->bindings);
    }

    /**
     * Delete records
     *
     * @return int
     */
    public function delete(): int
    {
        $where = $this->buildWhereClause();
        return $this->connection->delete($this->table, $where, $this->bindings);
    }

    /**
     * Build the SQL query
     *
     * @return string
     */
    public function toSql(): string
    {
        $sql = 'SELECT ';

        // Select clause
        if (empty($this->selects)) {
            $sql .= '*';
        } else {
            $sql .= implode(', ', $this->selects);
        }

        $sql .= " FROM {$this->table}";

        // Join clause
        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        // Where clause
        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . $this->buildWhereClause();
        }

        // Group by clause
        if (!empty($this->groups)) {
            $sql .= ' GROUP BY ' . implode(', ', $this->groups);
        }

        // Order by clause
        if (!empty($this->orders)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orders);
        }

        // Limit clause
        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        // Offset clause
        if ($this->offset !== null) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        return $sql;
    }

    /**
     * Build the WHERE clause
     *
     * @return string
     */
    protected function buildWhereClause(): string
    {
        $conditions = [];

        foreach ($this->wheres as $where) {
            if (isset($where['raw'])) {
                $conditions[] = $where['raw'];
            } else {
                $conditions[] = "{$where['column']} {$where['operator']} ?";
            }
        }

        $clause = implode(' AND ', $conditions);

        // Handle OR conditions
        $result = '';
        $currentBoolean = 'AND';
        foreach ($this->wheres as $i => $where) {
            if ($i === 0) {
                if (isset($where['raw'])) {
                    $result .= $where['raw'];
                } else {
                    $result .= "{$where['column']} {$where['operator']} ?";
                }
            } else {
                $boolean = $where['boolean'] ?? 'AND';
                if (isset($where['raw'])) {
                    $result .= " {$boolean} {$where['raw']}";
                } else {
                    $result .= " {$boolean} {$where['column']} {$where['operator']} ?";
                }
            }
        }

        return $result;
    }
}
