<?php

namespace FluxEco\AggregateRoot\Core\Ports;
use FluxEco\AggregateRoot\Core;

interface Outbounds
{
    public function getAppAggregateRootSchemaDirectory(): string;
    public function getAggregateRootStorageConfigEnvPrefix() : string;
    public function getAggregateRootEventSchema(): array;
    public function queryEvents(string $aggregateId,string $aggregateName): array;
    public function createAggregateRootEventsStorage(string $aggregateName): void;
    public function storeAggregateRootChangedEvent(Core\Domain\Events\AggregateStateChangedEvent $event): void;
    public function getAggregateRootSchema(string $aggregateName): array;
    public function publishAggregateRootChanged(string $correlationId,  string $eventName, Core\Domain\AggregateRoot $currentState);
    public function getNewUuid(): string;
    public function getCurrentTime(): string;
}