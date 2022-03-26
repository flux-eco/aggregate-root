<?php

namespace FluxEco\AggregateRoot\Core\Ports\Storage;

interface AggregateRootEventStream
{
    public function getNextSequence(): int;
    public function hasRecordedEvents(): bool;
    public function getRecordedEvents(): array;
    public function storeAggregateRootEvents(): void;
}