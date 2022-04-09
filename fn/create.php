<?php

namespace fluxAggregateRoot;

use FluxEco\AggregateRoot;

/**
 * @throws \JsonException
 */
function create(
    string $correlationId,
    string $actorEmail,
    string $aggregateName,
    string $aggregateId,
    string $payload
) : void {
    AggregateRoot\Api::newFromEnv()->create(
        $correlationId,
        $actorEmail,
        $aggregateName,
        $aggregateId,
        $payload
    );
}