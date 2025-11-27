<?php

namespace App\Core;

use App\Core\Relations\BelongsTo;
use App\Core\Relations\HasMany;
use App\Core\Relations\HasOne;
use App\Core\Relations\BelongsToMany;
use App\Core\Event;
use App\Core\Relations\Relation;

abstract class DbModel extends Model
{
    public ?int $id = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;
    public ?string $deleted_at = null;
    abstract public static function tableName(): string;
    abstract public function attributes(): array;
    abstract public static function primaryKey(): string;
    protected bool $timestamps = true;
    protected bool $softDeletes = false;
    protected array $relations = [];
    public static function Query(): QueryBuilder
    {
        $qb = new QueryBuilder(App::$app->db->pdo);
        $query = $qb->table(static::tableName())->setModel(static::class);

        $instance = new static();
        if ($instance->softDeletes) {
            $query->whereNull('deleted_at');
        }

        return $query;
    }

    public function save(): bool
    {
        $primaryKey = static::primaryKey();
        $now = date('Y-m-d H:i:s');
        $isNew = empty($this->{$primaryKey});

        // update if found primary key
        if (!$isNew) {
            if ($this->timestamps) {
                $this->updated_at = $now;
            }

            $fields = [];
            foreach ($this->attributes() as $attribute) {
                $fields[$attribute] = $this->{$attribute};
            }
            $result = static::Query()->where($primaryKey, $this->{$primaryKey})->update($fields) > 0;

            if ($result) {
                Event::dispatch(static::tableName() . '.updated', $this);
            }

            return $result;
        }

        // insert if not
        if ($this->timestamps) {
            $this->created_at = $now;
            $this->updated_at = $now;
        }

        $data = [];
        foreach ($this->attributes() as $attribute) {
            $data[$attribute] = $this->{$attribute};
        }

        $inserted = static::Query()->insert($data);

        if ($inserted) {
            $this->{$primaryKey} = (int) App::$app->db->pdo->lastInsertId();
            Event::dispatch(static::tableName() . '.created', $this);
        }

        return $inserted;
    }

    public function delete(): bool
    {
        $primaryKey = static::primaryKey();

        if ($this->softDeletes) {
            $this->deleted_at = date('Y-m-d H:i:s');
            $result = $this->save();

            if ($result) {
                Event::dispatch(static::tableName() . '.deleted', $this);
            }

            return $result;
        }

        $result = static::Query()->where($primaryKey, $this->{$primaryKey})->delete() > 0;

        if ($result) {
            Event::dispatch(static::tableName() . '.deleted', $this);
        }

        return $result;
    }

    public static function withTrashed(): QueryBuilder
    {
        $qb = new QueryBuilder(App::$app->db->pdo);
        return $qb->table(static::tableName())->setModel(static::class);
    }

    public static function onlyTrashed(): QueryBuilder
    {
        return static::withTrashed()->whereNotNull('deleted_at');
    }

    public function restore(): bool
    {
        if (!$this->softDeletes) {
            return false;
        }

        $this->deleted_at = null;
        $result = $this->save();

        if ($result) {
            Event::dispatch(static::tableName() . '.restored', $this);
        }

        return $result;
    }

    public function forceDelete(): bool
    {
        $primaryKey = static::primaryKey();

        return static::withTrashed()->where($primaryKey, $this->{$primaryKey})->delete() > 0;
    }

    protected function hasMany(string $related, ?string $foreignKey = null, ?string $localKey = null): HasMany
    {
        $foreignKey = $foreignKey ?? strtolower(static::class) . '_id';
        $localKey = $localKey ?? static::primaryKey();

        return new HasMany($this, $related, $foreignKey, $localKey);
    }

    protected function belongsTo(string $related, ?string $foreignKey = null, ?string $ownerKey = null): BelongsTo
    {
        $foreignKey = $foreignKey ?? strtolower($related) . '_id';
        $ownerKey = $ownerKey ?? (new $related())->primaryKey();

        return new BelongsTo($this, $related, $foreignKey, $ownerKey);
    }

    protected function hasOne(string $related, ?string $foreignKey = null, ?string $localKey = null): HasOne
    {
        $foreignKey = $foreignKey ?? strtolower(static::class) . '_id';
        $localKey = $localKey ?? static::primaryKey();

        return new HasOne($this, $related, $foreignKey, $localKey);
    }

    protected function belongsToMany(
        string $related,
        string $pivotTable,
        ?string $foreignPivotKey = null,
        ?string $relatedPivotKey = null,
        ?string $localKey = null,
    ): BelongsToMany {
        $foreignPivotKey = $foreignPivotKey ?? strtolower(static::class) . '_id';
        $relatedPivotKey = $relatedPivotKey ?? strtolower($related) . '_id';
        $localKey = $localKey ?? static::primaryKey();

        return new BelongsToMany($this, $related, $pivotTable, $foreignPivotKey, $relatedPivotKey, $localKey);
    }

    public function __get(string $name)
    {
        if (isset($this->relations[$name])) {
            return $this->relations[$name];
        }

        if (method_exists($this, $name)) {
            $relation = $this->{$name}();
            if ($relation instanceof Relation) {
                $this->relations[$name] = $relation->getResults();
                return $this->relations[$name];
            }
        }

        if (property_exists($this, $name)) {
            return $this->$name;
        }

        return null;
    }

    public function load(string ...$relations): static
    {
        foreach ($relations as $relation) {
            if (method_exists($this, $relation)) {
                $this->relations[$relation] = $this->$relation()->getResults();
            }
        }

        return $this;
    }
}
