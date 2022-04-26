<?php

namespace fluxAggregateRoot;

use FluxEco\AggregateRoot;

/**
 * @throws \JsonException
 */
function store(
    string $correlationId,
    string $actorEmail,
    string $aggregateId,
    string $aggregateName,
    string $payload
) : void {

    AggregateRoot\Api::newFromEnv()->store(
        $correlationId,
        $actorEmail,
        $aggregateId,
        $aggregateName,
        $payload
    );
}