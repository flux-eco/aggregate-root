<?php

namespace FluxEco\AggregateRoot\Core\Application\Handlers;

use FluxEco\AggregateRoot\Core\{Application\Handlers, Ports};
use Flux\Eco\ObjectProvider;

class StoreAggregateRootChangedEventHandler implements Handler
{
    private Ports\Storage\AggregateEventStorageClient $eventStorageClient;

    private function __construct(
        Ports\Storage\AggregateEventStorageClient $eventStorageClient)
    {
        $this->eventStorageClient = $eventStorageClient;
    }

    public static function new(
        Ports\Storage\AggregateEventStorageClient $eventStorageClient
    ): self
    {
        return new self(
            $eventStorageClient
        );
    }
    
    public function handle(Command|Handlers\StoreAggregateRootChangedEventCommand $command): void
    {
        $event = $command->getEvent();
        $this->eventStorageClient->storeAggregateRootChangedEvent($event);
    }

}