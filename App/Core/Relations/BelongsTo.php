<?php

namespace App\Core\Relations;

class BelongsTo extends Relation
{
    public function __construct($parent, string $related, string $foreignKey, string $ownerKey)
    {
        parent::__construct($parent, $foreignKey, $ownerKey);
        $this->query = $related::Query();
    }

    public function getResults()
    {
        return $this->query->where($this->localKey, $this->parent->{$this->foreignKey})->first();
    }
}