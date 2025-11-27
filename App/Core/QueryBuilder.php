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
    protected ?string $modelClass = null;

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

    protected function hydrate(array $data): mixed
    {
        if ($this->modelClass === null) {
            return $data;
        }

        $instance = new $this->modelClass();
        foreach ($data as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->{$key} = $value;
            }
        }

        return $instance;
    }

    public function setModel(string $modelClass): static
    {
        $this->modelClass = $modelClass;
        return $this;
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

    public function orderBy(string $column, string $direction = 'asc'): static
    {
        $direction = strtoupper($direction);
        if (!in_array($direction, ['ASC', 'DESC'])) {
            $direction = 'ASC';
        }

        $this->orderBys[] = "{$column} {$direction}";
        return $this;
    }

    public function groupBy(string ...$columns): static
    {
        $this->groupBys = array_merge($this->groupBys, $columns);
        return $this;
    }

    public function having(string $column, string $operator, mixed $value): static
    {
        $placeholder = $this->addBinding($value);
        $this->havings[] = "{$column} {$operator} {$placeholder}";
        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limitValue = $limit;
        return $this;
    }

    public function offset(int $offset): static
    {
        $this->offsetValue = $offset;
        return $this;
    }

    public function insert(array $data): bool
    {
        $columns = array_keys($data);
        $placeholders = [];

        foreach ($data as $value) {
            $placeholders[] = $this->addBinding($value);
        }

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($this->bindings);
    }

    public function update(array $data): int
    {
        $sets = [];
        $updateBindings = [];

        foreach ($data as $column => $value) {
            $updateBindings[] = $value;
            $sets[] = "{$column} = ?";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets);

        // Build WHERE clause
        if (!empty($this->wheres)) {
            $whereClauses = [];
            foreach ($this->wheres as $i => $where) {
                $bool = $i === 0 ? 'WHERE' : strtoupper($where['boolean']);
                $whereClauses[] = "{$bool} {$where['sql']}";
            }
            $sql .= ' ' . implode(' ', $whereClauses);
        }

        $allBindings = array_merge($updateBindings, $this->bindings);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($allBindings);

        return $stmt->rowCount();
    }

    public function delete(): int
    {
        $sql = "DELETE FROM {$this->table}";

        if (!empty($this->wheres)) {
            $whereClauses = [];
            foreach ($this->wheres as $i => $where) {
                $bool = $i === 0 ? 'WHERE' : strtoupper($where['boolean']);
                $whereClauses[] = "{$bool} {$where['sql']}";
            }
            $sql .= ' ' . implode(' ', $whereClauses);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);

        return $stmt->rowCount();
    }

    public function find(int|string $id, string $primaryKey = 'id'): ?array
    {
        return $this->where($primaryKey, $id)->first();
    }

    public function findOrFail(int|string $id, string $primaryKey = 'id'): array
    {
        $result = $this->find($id, $primaryKey);

        if ($result === null) {
            throw new \Exception("Record not found with {$primaryKey} = {$id}");
        }

        return $result;
    }

    public function exists(): bool
    {
        $stmt = $this->execute();
        return $stmt->rowCount() > 0;
    }

    public function count(string $column = '*'): int
    {
        $originalSelects = $this->selects;
        $originalBindings = $this->bindings;

        $this->selects = ["COUNT({$column}) as count"];

        $stmt = $this->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->selects = $originalSelects;
        $this->bindings = $originalBindings;

        return (int) ($result['count'] ?? 0);
    }

    public function raw(string $sql, array $bindings = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function paginate(int $perPage = 15, int $page = 1): Paginator
    {
        $total = $this->count();

        $lastPage = (int) ceil($total / $perPage);
        $page = max(1, min($page, $lastPage ?: 1));
        $offset = ($page - 1) * $perPage;

        $this->limit($perPage)->offset($offset);
        $items = $this->get();

        return new Paginator(
            items: $items,
            total: $total,
            perPage: $perPage,
            currentPage: $page,
            lastPage: $lastPage
        );
    }

    public function chunk(int $size, callable $callback): bool
    {
        $page = 1;

        do {
            $result = $this->limit($size)->offset(($page - 1) * $size)->get();

            if (empty($result)) {
                break;
            }

            if ($callback($result, $page) === false) {
                return false;
            }

            $page++;
        } while (count($result) === $size);

        return true;
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }

    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();

        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    public function get(): array
    {
        $stmt = $this->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($this->modelClass !== null) {
            return array_map(fn($row) => $this->hydrate($row), $results);
        }

        return $results;
    }

    public function first(): mixed
    {
        $this->limitValue = 1;
        $stmt = $this->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        return $this->hydrate($result);
    }
}