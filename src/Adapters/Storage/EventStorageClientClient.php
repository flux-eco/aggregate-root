<?php

namespace FluxEco\AggregateRoot\Adapters\Storage;

use FluxEco\AggregateRoot\Core\Application;
use FluxEco\AggregateRoot\Core\Domain;
use FluxEco\AggregateRoot\Core\Ports;
use FluxEco\Storage\Adapters\Api\StorageApi;

class EventStorageClientClient implements Ports\Storage\AggregateEventStorageClient
{
    private const EVENT_STORAGE_PRIMARY_KEY = 'eventId';
    private const SEQUENCE_COLUMN_NAME = 'sequence';
    private StorageApi $storageApi;

    private function __construct(StorageApi $storageApi)
    {
        $this->storageApi = $storageApi;
    }

    public static function new(string $databaseName, string $tableName, array $jsonSchema): self
    {
        if (array_key_exists(self::SEQUENCE_COLUMN_NAME, $jsonSchema['properties']) === false) {
            throw new \Exception('An event storage schema MUST contain a ' . self::SEQUENCE_COLUMN_NAME . ' column!');
        }
        $storageApi = StorageApi::new($databaseName, $tableName, $jsonSchema);
        return new self($storageApi);
    }


    final public function createAggregateEventsStorage(string $tableName)
    {
        $this->storageApi->createStorage(self::EVENT_STORAGE_PRIMARY_KEY);
    }


    final public function storeAggregateRootChangedEvent(Domain\Events\AggregateStateChangedEvent $event): void
    {
        //todo use an explicit adapter?
        $data = $event->toArray();
        $this->storageApi->appendData($data);
    }

    /** @return Domain\Events\AggregateStateChangedEvent[] */
    final public function queryEvents(string $aggregateId,
                                      string $aggregateName): array
    {
        $filter = ['aggregateId' => $aggregateId, 'aggregateName' => $aggregateName];
        $queryResult = $this->storageApi->getData($filter, 0, 'sequence ASC');
        return AggregateStateChangedEventsAdapter::fromQueryResult($queryResult)->toEvents();
    }
}