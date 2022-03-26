<?php

namespace FluxEco\AggregateRoot\Adapters\Storage;

class LifeCycleStorageClient
{

    private function __construct()
    {

    }

    public static function new(): self
    {
        return new self();
    }

    public function setAggregateRootStatusIncomplete(
        string $aggregateId,
        string $aggregateName
    ) {

    }

    public function setAggregateRootStatusCompleted(
        string $aggregateId,
        string $aggregateName
    ) {

    }

    public function setAggregateRootStatusDeleted(
        string $aggregateId,
        string $aggregateName
    ) {

    }
}