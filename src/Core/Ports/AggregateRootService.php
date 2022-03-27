<?php

namespace FluxEco\AggregateRoot\Core\Ports;

use FluxEco\AggregateRoot\Core\{Application\Handlers, Application\Processes, Domain};

class AggregateRootService
{
    private Configs\Outbounds $outbounds;

    private function __construct(
        Configs\Outbounds $outbounds
    ) {
        $this->outbounds = $outbounds;
    }

    public static function new(Configs\Outbounds $aggregateRootOutbounds) : self
    {
        return new self(
            $aggregateRootOutbounds
        );
    }

    final public function initialiceAggregateRoots() : void
    {
        foreach ($this->getAggregateRootSchemas() as $schema) {
            $aggregateName = $schema['name'];

            $createEventStorageProcess = Processes\CreateAggregateRootEventStorageProcess::new();

            $eventStorage = $this->outbounds->getAggregateEventStorageClient($aggregateName);
            $eventSchema = $this->outbounds->getAggregateRootEventSchema();

            $command = Handlers\CreateAggregateRootEventStorageCommand::new($aggregateName, $eventSchema);
            $handler = Handlers\CreateAggregateRootEventStorageHandler::new($eventStorage);
            $createEventStorageProcess->process($command, $handler);
        }
    }

    private function getAggregateRootSchemas() : array
    {
        $aggregateRootSchemas = [];
        $schemDirectory = $this->outbounds->getAppAggregateRootSchemaDirectory();
        $aggregateRootFiles = scandir($schemDirectory);
        foreach ($aggregateRootFiles as $aggregateRootFile) {
            if (pathinfo($aggregateRootFile, PATHINFO_EXTENSION) === "yaml") {
                $aggregateRootSchemas[] = yaml_parse(file_get_contents($schemDirectory . '/' . $aggregateRootFile));
            }
        }
        return $aggregateRootSchemas;
    }

    public function createAggregateRoot(
        string $correlationId,
        string $actorEmail,
        string $commandCreatedDateTime,
        string $aggregateId,
        string $aggregateName,
        string $payload
    ) : void {
        echo "create AggregateRoot";

        $aggregateRoot = Domain\AggregateRoot::new($aggregateId, $aggregateName, $this->outbounds);

        echo 'rootObjectSchemaFileLink' . PHP_EOL;
        print_r($this->outbounds->getAggregateRootSchema($aggregateName));
        echo PHP_EOL . PHP_EOL;

        $aggregateRoot->create(
            $correlationId,
            $actorEmail,
            $commandCreatedDateTime,
            $aggregateId,
            $aggregateName,
            $this->outbounds->getAggregateRootSchema($aggregateName),
            $payload
        );
    }

    /**
     * @throws \JsonException
     */
    public function changeAggregateRoot(
        string $correlationId,
        string $actorEmail,
        string $commandCreatedDateTime,
        string $aggregateId,
        string $aggregateName,
        string $payload
    ) : void {
        $aggregateRoot = Domain\AggregateRoot::new($aggregateId, $aggregateName, $this->outbounds);

        $rootObjectSchemaFileLink = $this->outbounds->getAggregateRootSchema($aggregateName);

        echo 'rootObjectSchemaFileLink' . PHP_EOL;
        print_r($rootObjectSchemaFileLink);
        echo PHP_EOL . PHP_EOL;

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

    public function deleteAggregateRoot(
        string $correlationId,
        string $actorEmail,
        string $commandCreatedDateTime,
        string $aggregateId,
        string $aggregateName,
    ) {
        $aggregateRoot = Domain\AggregateRoot::new($aggregateId, $aggregateName, $this->outbounds);

        $rootObjectSchema = $this->outbounds->getAggregateRootSchema($aggregateName);
        $aggregateRoot->delete($correlationId, $actorEmail, $commandCreatedDateTime, $aggregateId, $aggregateName,
            $rootObjectSchema);
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