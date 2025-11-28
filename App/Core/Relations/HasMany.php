<?php

namespace App\Core\Relations;

use App\Core\QueryBuilder;

class HasMany extends Relation
{
    public function __construct($parent, string $related, string $foreignKey, string $localKey)
    {
        parent::__construct($parent, $foreignKey, $localKey);
        $this->query = $related::Query();
    }

    public function getResults(): array
    {
        return $this->query->where($this->foreignKey, $this->parent->{$this->localKey})->get();
    }
}