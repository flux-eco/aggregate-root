<?php

namespace FluxEco\AggregateRoot\Adapters;

use FluxEco\AggregateRoot\{Adapters, Core};

use fluxStorage;
use fluxValueObject;
use fluxJsonSchemaInstance;
use fluxGlobalStream;

class Outbounds implements Core\Ports\Outbounds
{
    const GLOBAL_STREAM_SUBJECT = 'AggregateRoot';

    private string $aggregateRootStorageConfigEnvPrefix;
    private string $aggregateRootSchemaDirectory;
    private string $aggregateEventSchemaFilePath;

    private function __construct(
        string $aggregateRootStorageConfigEnvPrefix,
        string $aggregateRootSchemaDirectory,
        string $aggregateEventSchemaFilePath
    ) {
        $this->aggregateRootStorageConfigEnvPrefix = $aggregateRootStorageConfigEnvPrefix;
        $this->aggregateRootSchemaDirectory = $aggregateRootSchemaDirectory;
        $this->aggregateEventSchemaFilePath = $aggregateEventSchemaFilePath;
    }

    public static function new(
        string $aggregateRootStorageConfigEnvPrefix,
        string $aggregateRootSchemaDirectory,
        string $aggregateEventSchemaFilePath
    ) : self {
        return new self(
            $aggregateRootStorageConfigEnvPrefix,
            $aggregateRootSchemaDirectory,
            $aggregateEventSchemaFilePath
        );
    }

    final public function getAppAggregateRootSchemaDirectory() : string
    {
        return $this->aggregateRootSchemaDirectory;
    }

    final public function getAggregateRootSchema(string $aggregateRootName) : array
    {
        $schemaFile = $this->aggregateRootSchemaDirectory . '/' . $aggregateRootName . '.yaml';
        return yaml_parse(file_get_contents($schemaFile));
    }

    public function getAggregateRootStorageConfigEnvPrefix() : string
    {
        return $this->aggregateRootStorageConfigEnvPrefix;
    }

    public function jsonDecodeRootObject(string $rootObjectAsJson, array $rootObjectSchema): Core\Domain\Models\RootObject {
        $schemaInstanceArray = fluxJsonSchemaInstance\getSchemaInstance($rootObjectAsJson, $rootObjectSchema);
        return Core\Domain\Models\RootObject::new($schemaInstanceArray);
    }

    final public function createAggregateRootEventsStorage(string $aggregateName): void
    {
        fluxStorage\createStorage($aggregateName, $this->getAggregateRootEventSchema(), $this->aggregateRootStorageConfigEnvPrefix);
    }

    final public function storeAggregateRootChangedEvent(Core\Domain\Events\AggregateStateChangedEvent $event): void
    {
        fluxStorage\appendData($event->getAggregateName(), $this->getAggregateRootEventSchema(), $event->toArray(), $this->aggregateRootStorageConfigEnvPrefix);
    }

    /** @return Core\Domain\Events\AggregateStateChangedEvent[] */
    final public function queryEvents(
        string $aggregateId,
        string $aggregateName
    ): array
    {
        $filter = ['aggregateId' => $aggregateId];
        $queryResult = fluxStorage\getData($aggregateName,  $this->getAggregateRootEventSchema(), $this->aggregateRootStorageConfigEnvPrefix, $filter, null, null, 'sequence ASC');
        echo "Aggregate ".$aggregateId." events queried: ".count($queryResult).PHP_EOL;
        return Adapters\Storage\AggregateStateChangedEventsAdapter::fromQueryResult($queryResult)->toEvents();
    }

    final public function getAggregateRootEventSchema() : array
    {
        return yaml_parse(file_get_contents($this->aggregateEventSchemaFilePath));
    }

    final public function publishAggregateRootChanged(string $correlationId,  string $eventName, Core\Domain\AggregateRoot $currentState): void {
        $lastChangedBy = $currentState->getLastChangedBy();
        $subject = self::GLOBAL_STREAM_SUBJECT;
        $subjectId = $currentState->getAggregateId();
        $subjectSequence = $currentState->getCurrentSequence();
        $subjectName = $currentState->getAggregateName();
        $rootObjectSchema = json_encode($currentState->getRootObjectSchema());
        $payload = $currentState->getRootObject()->toJson();

        fluxGlobalStream\publishStateChange(
            $correlationId,
            $lastChangedBy,
            $subject,
            $subjectId,
            $subjectSequence,
            $subjectName,
            $rootObjectSchema,
            $eventName,
            $payload
        );
    }

    final public function getNewUuid(): string
    {
        return fluxValueObject\getNewUuid();
    }

    final public function getCurrentTime(): string
    {
        return fluxValueObject\getCurrentTime();
    }
}