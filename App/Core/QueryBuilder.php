<?php

namespace App\Core;

use PDO;

class QueryBuilder
{
    protected PDO $pdo;
    protected string $table = '';
    protected array $selects = ['*'];
    protected array $wheres = [];
    protected array $bindings = [];
    protected array $joins = [];
    protected array $orderBys = [];
    protected ?int $limitValue = null;
    protected ?int $offsetValue = null;
    protected array $groupBys = [];
    protected array $havings = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function table(string $table): static
    {
        $this->table = $table;
        return $this;
    }

    protected function addBinding(mixed $value): string
    {
        $this->bindings[] = $value;
        return '?';
    }

    protected function toSql(): string
    {
        $sql = "SELECT " . implode(', ', $this->selects);
        $sql .= " FROM {$this->table}";

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        if (!empty($this->wheres)) {
            $whereClauses = [];
            foreach ($this->wheres as $i => $where) {
                $bool = $i === 0 ? 'WHERE' : strtoupper($where['boolean']);
                $whereClauses[] = "{$bool} {$where['sql']}";
            }
            $sql .= ' ' . implode(' ', $whereClauses);
        }

        if (!empty($this->groupBys)) {
            $sql .= ' GROUP BY ' . implode(', ', $this->groupBys);
        }

        if (!empty($this->havings)) {
            $sql .= ' HAVING ' . implode(', ', $this->havings);
        }

        if (!empty($this->orderBys)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orderBys);
        }

        if ($this->limitValue !== null) {
            $sql .= " LIMIT {$this->limitValue}";
        }
        if ($this->offsetValue !== null) {
            $sql .= " OFFSET {$this->offsetValue}";
        }

        return $sql;
    }

    protected function execute(): \PDOStatement
    {
        $sql = $this->toSql();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt;
    }

    public function select(string|array ...$columns): static
    {
        $this->selects = empty($columns) ? ['*'] : (is_array($columns[0]) ? $columns[0] : $columns);
        return $this;
    }

    public function where(string $column, mixed $operator, mixed $value = null): static
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $placeholder = $this->addBinding($value);

        $this->wheres[] = [
            'sql' => "{$column} {$operator} {$placeholder}",
            'boolean' => 'and'
        ];

        return $this;
    }

    public function orWhere(string $column, mixed $operator, mixed $value = null): static
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $placeholder = $this->addBinding($value);
        $this->wheres[] = [
            'sql' => "{$column} {$operator} {$placeholder}",
            'boolean' => 'or'
        ];

        return $this;
    }

    public function whereIn(string $column, array $values): static
    {
        $placeholders = [];
        foreach ($values as $value) {
            $placeholders[] = $this->addBinding($value);
        }

        $in = implode(', ', $placeholders);
        $this->wheres[] = [
            'sql' => "{$column} IN ({$in})",
            'boolean' => 'and'
        ];

        return $this;
    }

    public function whereNotIn(string $column, array $values): static
    {
        $placeholders = [];
        foreach ($values as $value) {
            $placeholders[] = $this->addBinding($value);
        }

        $in = implode(', ', $placeholders);
        $this->wheres[] = [
            'sql' => "{$column} NOT IN ({$in})",
            'boolean' => 'and'
        ];

        return $this;
    }

    public function whereNull(string $column): static
    {
        $this->wheres[] = [
            'sql' => "{$column} IS NULL",
            'boolean' => 'and'
        ];

        return $this;
    }

    public function whereNotNull(string $column): static
    {
        $this->wheres[] = [
            'sql' => "{$column} IS NOT NULL",
            'boolean' => 'and'
        ];

        return $this;
    }

    public function whereBetween(string $column, array $values): static
    {
        $placeholder1 = $this->addBinding($values[0]);
        $placeholder2 = $this->addBinding($values[1]);

        $this->wheres[] = [
            'sql' => "{$column} BETWEEN {$placeholder1} AND {$placeholder2}",
            'boolean' => 'and'
        ];

        return $this;
    }

    public function whereDate(string $column, string $operator, string $date): static
    {
        $placeholder = $this->addBinding($date);
        $this->wheres[] = [
            'sql' => "DATE({$column}) {$operator} {$placeholder}",
            'boolean' => 'and'
        ];

        return $this;
    }

    public function whereRaw(string $sql, array $bindings = []): static
    {
        foreach ($bindings as $binding) {
            $this->bindings[] = $binding;
        }

        $this->wheres[] = [
            'sql' => $sql,
            'boolean' => 'and'
        ];

        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second): static
    {
        $this->joins[] = "INNER JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): static
    {
        $this->joins[] = "LEFT JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    public function get(): array
    {
        $stmt = $this->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function first(): ?array
    {
        $this->limitValue = 1;
        $stmt = $this->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}