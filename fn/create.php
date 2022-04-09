<?php

namespace fluxAggregateRoot;

use FluxEco\AggregateRoot;

/**
 * @throws \JsonException
 */
function create(
    string $correlationId,
    string $actorEmail,
    string $aggregateId,
    string $aggregateName,
    string $payload
) : void {
    AggregateRoot\Api::newFromEnv()->create(
        $correlationId,
        $actorEmail,
        $aggregateId,
        $aggregateName,
        $payload
    );
}