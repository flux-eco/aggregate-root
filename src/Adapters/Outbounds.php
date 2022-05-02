<?php

namespace FluxEco\AggregateRoot\Adapters;

use FluxEco\AggregateRoot\{Adapters, Core};

use fluxStorage;
use fluxValueObject;
use fluxJsonSchemaInstance;
use fluxGlobalStream;
use fluxChannel;

class Outbounds implements Core\Ports\Outbounds
{
    const GLOBAL_STREAM_SUBJECT = 'aggregateRoot';

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

    final public function publishDomainEvents(string $correlationId, Core\Domain\Events\AggregateStateChangedEvent $event,  Core\Domain\AggregateRoot $currentState) {

        //Standard Event
        $channel = self::GLOBAL_STREAM_SUBJECT.'/'.$currentState->getAggregateName();
        $this->publishEvent($channel, $correlationId, $event->getEventName(),$currentState, $currentState->getRootObject()->toJson());


        //Additional Events
        $payload = json_decode($event->getPayload(), true);
        $aggregateRootSchema = $this->getAggregateRootSchema($event->getAggregateName());

        //todo make adapter objects
        if(key_exists('events', $aggregateRootSchema)) {
            foreach($aggregateRootSchema['events'] as $eventSchema) {
                $eventName = $eventSchema['title'];
                $propertyName = $eventSchema['changedPropertyRule']['propertyName'];
                $condition = $eventSchema['changedPropertyRule']['condition'];
                $value = $eventSchema['changedPropertyRule']['value'];
                $channelName = $eventSchema['channelName'];
                if(key_exists($propertyName, $payload)) {
                    switch ($condition) {
                        case 'isEqual': {
                            if($value === $payload[$propertyName]['value']) {
                                $payload = [];
                                foreach($eventSchema['propertyNamesEventPayload'] as $propertyName) {
                                    if($currentState->getRootObject()->getProperties()->offsetExists($propertyName)) {
                                        $payload[$propertyName] = $currentState->getRootObject()->getProperties()->offsetGet($propertyName);
                                    } else {
                                        echo $propertyName. " does not exists in current aggregate ".$currentState->getAggregateName(). " ".$currentState->getAggregateId().PHP_EOL;
                                        $payload[$propertyName] = null;
                                    }
                                }
                                echo "publish event";
                                $this->publishEvent(
                                    $channelName, $event->getCorrelationId(),$eventName, $currentState, json_encode($payload)
                                );
                            }
                        }
                    }
                }
            }
        }
    }


    private function publishEvent(string $channel, string $correlationId,  string $eventName, Core\Domain\AggregateRoot $currentState, string $payload): void {
        $lastChangedBy = $currentState->getLastChangedBy();
        $subject = self::GLOBAL_STREAM_SUBJECT;
        $subjectId = $currentState->getAggregateId();
        $subjectSequence = $currentState->getCurrentSequence();
        $subjectName = $currentState->getAggregateName();

        $additionalHeaders = [
            'aggregateId' => $currentState->getAggregateId(),
            'aggregateName' => $currentState->getAggregateName()
        ];

        $message = fluxValueObject\getNewMessage($correlationId, $eventName, $payload, $additionalHeaders);

        fluxGlobalStream\publishStateChange(
            $correlationId,
            $lastChangedBy,
            $channel,
            $subject,
            $subjectId,
            $subjectSequence,
            $subjectName,
            $eventName,
            $message->toJson()
        );
        fluxGlobalStream\notify($channel);
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