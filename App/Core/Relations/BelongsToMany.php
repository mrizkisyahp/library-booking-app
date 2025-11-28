<?php

namespace App\Core\Relations;

class BelongsToMany extends Relation
{
    protected string $pivotTable;
    protected string $foreignPivotKey;
    protected string $relatedPivotKey;

    public function __construct(
        $parent,
        string $related,
        string $pivotTable,
        string $foreignPivotKey,
        string $relatedPivotKey,
        string $localKey
    ) {
        parent::__construct($parent, $foreignPivotKey, $localKey);

        $this->query = $related::Query();
        $this->pivotTable = $pivotTable;
        $this->foreignPivotKey = $foreignPivotKey;
        $this->relatedPivotKey = $relatedPivotKey;
    }

    public function getResults(): array
    {
        $relatedTable = $this->query->getTable();
        $relatedPrimaryKey = (new ($this->query->getModelClass()))->primaryKey();

        return $this->query
            ->join(
                $this->pivotTable,
                "{$relatedTable}.{$relatedPrimaryKey}",
                '=',
                "{$this->pivotTable}.{$this->relatedPivotKey}"
            )
            ->where("{$this->pivotTable}.{$this->foreignPivotKey}", $this->parent->{$this->localKey})
            ->get();
    }
}