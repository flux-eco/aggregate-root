<?php

namespace FluxEco\AggregateRoot\Core\Ports\GlobalStream;
use FluxEco\AggregateRoot\Core\Domain\AggregateRoot;

interface GlobalStreamClient {
    public function publishAggregateRootChanged(string $correlationId,  string $eventName, AggregateRoot $currentState): void;
}