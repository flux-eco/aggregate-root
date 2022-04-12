<?php

namespace fluxAggregateRoot;

use FluxEco\AggregateRoot;

function change(
    string $correlationId,
    string $actorEmail,
    string $aggregateId,
    string $aggregateName,
    string $payload
) : void {
    AggregateRoot\Api::newFromEnv()->change(
        $correlationId,
        $actorEmail,
        $aggregateId,
        $aggregateName,
        $payload
    );
}