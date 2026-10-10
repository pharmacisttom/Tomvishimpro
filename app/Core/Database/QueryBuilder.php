<?php

namespace Tomvis\HIMPro\Core\Database;

use PDO;
use Exception;

class QueryBuilder
{
    protected PDO $pdo;
    protected string $table;
    protected array $selects = ['*'];
    protected array $wheres = [];
    protected array $joins = [];
    protected array $orders = [];
    protected ?int $limit = null;
    protected ?int $offset = null;
    protected array $bindings = [];

    public function __construct(PDO $pdo, string $table)
    {
        $this->pdo = $pdo;
        $this->table = $this->sanitizeIdentifier($table);
    }

    protected function sanitizeIdentifier(string $name): string
    {
        // Allow alpha-numeric, underscore, dot (for schema.table)
        if (!preg_match('/^[a-zA-Z0-9_\.]+$/', $name)) {
            throw new Exception("Invalid database identifier: [{$name}]");
        }
        return $name;
    }

    public function select(array|string $columns = ['*']): static
    {
        $cols = is_array($columns) ? $columns : func_get_args();
        $this->selects = array_map([$this, 'sanitizeIdentifier'], $cols);
        return $this;
    }

    public function where(string $column, string $operator, mixed $value = null): static
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $col = $this->sanitizeIdentifier($column);
        $param = ':w_' . count($this->bindings);
        $this->wheres[] = "{$col} {$operator} {$param}";
        $this->bindings[$param] = $value;

        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $col = $this->sanitizeIdentifier($column);
        $dir = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $this->orders[] = "{$col} {$dir}";
        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = max(1, $limit);
        return $this;
    }

    public function offset(int $offset): static
    {
        $this->offset = max(0, $offset);
        return $this;
    }

    public function get(): array
    {
        $sql = $this->toSql();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function first(): ?array
    {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    public function count(): int
    {
        $sql = "SELECT COUNT(*) as aggregate FROM {$this->table}";
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['aggregate'] ?? 0);
    }

    public function paginate(int $perPage = 15, int $page = 1): array
    {
        $page = max(1, $page);
        $total = $this->count();
        $this->limit($perPage)->offset(($page - 1) * $perPage);
        $items = $this->get();

        return [
            'data' => $items,
            'meta' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => (int)ceil($total / $perPage),
            ]
        ];
    }

    public function insert(array $data): bool
    {
        $columns = array_map([$this, 'sanitizeIdentifier'], array_keys($data));
        $params = [];
        $bindings = [];

        foreach ($data as $key => $val) {
            $p = ':' . $key;
            $params[] = $p;
            $bindings[$p] = $val;
        }

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $columns),
            implode(', ', $params)
        );

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($bindings);
    }

    public function insertGetId(array $data): string|int
    {
        $this->insert($data);
        return $this->pdo->lastInsertId();
    }

    public function update(array $data): int
    {
        $sets = [];
        $bindings = $this->bindings;

        foreach ($data as $key => $val) {
            $col = $this->sanitizeIdentifier($key);
            $p = ':u_' . $key;
            $sets[] = "{$col} = {$p}";
            $bindings[$p] = $val;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets);
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->rowCount();
    }

    public function delete(): int
    {
        $sql = "DELETE FROM {$this->table}";
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->rowCount();
    }

    public function toSql(): string
    {
        $sql = "SELECT " . implode(', ', $this->selects) . " FROM {$this->table}";
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        if (!empty($this->orders)) {
            $sql .= " ORDER BY " . implode(', ', $this->orders);
        }
        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }
        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }
        return $sql;
    }
}
