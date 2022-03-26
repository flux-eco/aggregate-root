<?php

namespace FluxEco\AggregateRoot\Core\Ports\Storage;

use  FluxEco\AggregateRoot\Core\Domain;

interface AggregateEventStorageClient
{
    public function storeAggregateRootChangedEvent(Domain\Events\AggregateStateChangedEvent $event);

    public function createAggregateEventsStorage(string $tableName);

    /** @return Domain\Events\AggregateStateChangedEvent[] */
    public function queryEvents(string $aggregateId, string $aggregateName): array;
}