<?php

namespace FluxEco\AggregateRoot\Core\Ports;

use FluxEco\AggregateRoot\Core\{Application\Handlers, Application\Processes, Domain};

class AggregateRootService
{
    private Configs\AggregateRootOutbounds $aggregateRootOutbounds;


    private function __construct(
        Configs\AggregateRootOutbounds $aggregateRootOutbounds
    )
    {
        $this->aggregateRootOutbounds = $aggregateRootOutbounds;
    }

    public static function new(Configs\AggregateRootOutbounds $aggregateRootOutbounds): self
    {
        return new self(
            $aggregateRootOutbounds
        );
    }

    final public function createEventStorage(string $aggregateName): void
    {
        $createEventStorageProcess = Processes\CreateAggregateRootEventStorageProcess::new();

        $eventStorage = $this->aggregateRootOutbounds->getAggregateEventStorageClient($aggregateName);
        $eventSchema = $this->aggregateRootOutbounds->getAggregateEventSchema();

        $command = Handlers\CreateAggregateRootEventStorageCommand::new($aggregateName, $eventSchema);
        $handler = Handlers\CreateAggregateRootEventStorageHandler::new($eventStorage);
        $createEventStorageProcess->process($command, $handler);
    }

    public function createAggregateRoot(string $correlationId,
                                        string $actorEmail,
                                        string $commandCreatedDateTime,
                                        string $aggregateId,
                                        string $aggregateName,
                                        string $payload
    ): void
    {
        echo "create AggregateRoot";

        $aggregateRoot = Domain\AggregateRoot::new($aggregateId, $aggregateName, $this->aggregateRootOutbounds);

        echo 'rootObjectSchemaFileLink'.PHP_EOL;
        print_r($this->aggregateRootOutbounds->getAggregateRootSchema($aggregateName));
        echo PHP_EOL.PHP_EOL;

        $aggregateRoot->create(
            $correlationId,
            $actorEmail,
            $commandCreatedDateTime,
            $aggregateId,
            $aggregateName,
            $this->aggregateRootOutbounds->getAggregateRootSchema($aggregateName),
            $payload
        );
    }

    /**
     * @throws \JsonException
     */
    public function changeAggregateRoot(string $correlationId,
                                        string $actorEmail,
                                        string $commandCreatedDateTime,
                                        string $aggregateId,
                                        string $aggregateName,
                                        string $payload): void
    {
        $aggregateRoot = Domain\AggregateRoot::new($aggregateId, $aggregateName, $this->aggregateRootOutbounds);

        $rootObjectSchemaFileLink = $this->aggregateRootOutbounds->getAggregateRootSchema($aggregateName);

        echo 'rootObjectSchemaFileLink'.PHP_EOL;
        print_r($rootObjectSchemaFileLink);
        echo PHP_EOL.PHP_EOL;

        $aggregateRoot->change(
            $correlationId,
            $actorEmail,
            $commandCreatedDateTime,
            $aggregateId,
            $aggregateName,
            $rootObjectSchemaFileLink,
            $payload
        );
    }

    public function deleteAggregateRoot(string $correlationId,
                                        string $actorEmail,
                                        string $commandCreatedDateTime,
                                        string $aggregateId,
                                        string $aggregateName,
    )
    {
        $aggregateRoot = Domain\AggregateRoot::new($aggregateId, $aggregateName, $this->aggregateRootOutbounds);

        $rootObjectSchema = $this->aggregateRootOutbounds->getAggregateRootSchema($aggregateName);
        $aggregateRoot->delete($correlationId, $actorEmail, $commandCreatedDateTime, $aggregateId, $aggregateName, $rootObjectSchema);
    }

    /**
     * @param Domain\AggregateRootEventStream $eventStream
     * @return void
     */
    /*
    private function storeAndPublishAggregateRootEvents(Domain\AggregateRootEventStream $eventStream): void
    {

        if ($eventStream->hasRecordedEvents()) {
            $process = Processes\StoreAggregateRootEventProcess::new();
            $handler = Handlers\StoreAggregateRootChangedEventHandler::new($this->aggregateRootOutbounds->getAggregateEventStorageClient());
            foreach ($eventStream->getRecordedEvents() as $event) {
                $command = Handlers\StoreAggregateRootChangedEventCommand::fromEvent($event);
                $process->process($command, $handler);


                //todo only publish the last change or every change? // also publish the correlationId or not?

                $aggregateId = $event->getAggregateId();
                $aggregateName = $event->getAggregateName();
                $aggregateRootOutbounds = $this->aggregateRootOutbounds;
                $aggregateRoot = Domain\AggregateRoot::new($aggregateId, $aggregateName, $aggregateRootOutbounds);

                $globalStream = $this->aggregateRootOutbounds->getGlobalStreamClient($aggregateName);
                $globalStream->publishAggregateRootChanged($event->getCorrelationId(), $aggregateRoot);
            }
        }
    }*/

}