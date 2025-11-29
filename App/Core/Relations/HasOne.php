<?php

namespace App\Core\Relations;

class HasOne extends Relation
{
    public function __construct($parent, string $related, string $foreignKey, string $localKey)
    {
        parent::__construct($parent, $foreignKey, $localKey);
        $this->query = $related::Query();
    }

    public function getResults()
    {
        return $this->query->where($this->foreignKey, $this->parent->{$this->localKey})->first();
    }
}