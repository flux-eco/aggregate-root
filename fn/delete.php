<?php

namespace fluxAggregateRoot;

use FluxEco\AggregateRoot;


function delete(
    string $correlationId,
    string $actorEmail,
    string $aggregateId,
    string $aggregateName
): void {
    AggregateRoot\Api::newFromEnv()->delete(
        $correlationId,
        $actorEmail,
        $aggregateName,
        $aggregateId
    );
}