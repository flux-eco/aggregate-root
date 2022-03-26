<?php

namespace FluxEco\AggregateRoot\Core\Application\Handlers;

use FluxEco\AggregateRoot\Core\{Ports};

class CreateAggregateRootEventStorageHandler implements Handler
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

    public function handle(Command|CreateAggregateRootEventStorageCommand $createEventStorageCommand)
    {
        $tableName = $createEventStorageCommand->getAggregateName();
        $this->eventStorageClient->createAggregateEventsStorage($tableName);
    }
}