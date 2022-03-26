<?php

namespace FluxEco\AggregateRoot\Core\Ports\Api;


interface AggregateRootEvent
{
    public function getSequence(): int;

    public function getCorrelationId(): string;

    public function getAggregateId(): string;

    public function getAggregateName(): string;

    public function getAggregateRootSchemaVersion(): string;

    public function getEventCreatedBy(): string;

    public function getEventCreatedDateTime(): string;

    public function getEventName(): string;

    public function getPayload(): string;
}