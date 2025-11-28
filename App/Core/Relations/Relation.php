<?php

namespace App\Core\Relations;

use App\Core\QueryBuilder;

abstract class Relation
{
    protected QueryBuilder $query;
    protected $parent;
    protected string $foreignKey;
    protected string $localKey;

    public function __construct($parent, string $foreignKey, string $localKey)
    {
        $this->parent = $parent;
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
    }

    abstract public function getResults();

    public function getQuery(): QueryBuilder
    {
        return $this->query;
    }
}