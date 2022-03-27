<?php

namespace FluxEco\AggregateRoot\Core\Domain;

use Exception;
use FluxEco\AggregateRoot\Core\{Domain\Events\AggregateStateChangedEvent, Ports};

class AggregateRootEventStream implements Ports\Storage\AggregateRootEventStream
{
    protected static array $instances = [];

    private array $recordedEvents = [];
    private array $stream = [];
    private int $lastSequence = 0;
    private Ports\Storage\AggregateEventStorageClient $aggregateEventStorageClient;
    private Ports\GlobalStream\GlobalStreamClient $globalStreamClient;


    private function __construct(
        string                                    $aggregateId,
        string                                    $aggregateName,
        Ports\Storage\AggregateEventStorageClient $aggregateEventStorageClient,
    )
    {
        $this->aggregateId = $aggregateId;
        $this->aggregateName = $aggregateName;

        $this->aggregateEventStorageClient = $aggregateEventStorageClient;

        $this->loadEvents();
    }

    final public function getNextSequence(): int
    {
        return ($this->lastSequence + 1);
    }

    public static function new(string                                    $aggregateId,
                               string                                    $aggregateName,
                               Ports\Storage\AggregateEventStorageClient $aggregateEventStorageClient
    ): self
    {
        if (!array_key_exists($aggregateId, static::$instances)) {
            static::$instances[$aggregateId] = new self($aggregateId, $aggregateName, $aggregateEventStorageClient);
        }
        return static::$instances[$aggregateId];
    }

    private function loadEvents(): void
    {
        $aggregateId = $this->aggregateId;
        $aggregateName = $this->aggregateName;

        $aggregateEvents = $this->aggregateEventStorageClient->queryEvents($aggregateId, $aggregateName);
        if (count($aggregateEvents) > 0) {
            foreach ($aggregateEvents as $event) {
                $this->applyEvent($event);
            }
        }
    }

    final public function applyAndRecordEvent(AggregateStateChangedEvent $event): void {
        $this->applyEvent($event);
        $this->recordEvent($event);
    }

    /**
     * @throws Exception
     */
    private function recordEvent(AggregateStateChangedEvent $event): void {
        /*if (array_key_exists($event->getSequence(), $this->recordedEvents)) {
            throw new \RuntimeException('Sequence already exists');
        }*/
        $this->recordedEvents[$event->getSequence()] = $event;
        //$this->applyEvent($event);
    }

    private function applyEvent(AggregateStateChangedEvent $event): void
    {
        $this->stream[$event->getSequence()] = $event;
        $this->lastSequence = $event->getSequence();
    }


    final public function hasEvents(): bool
    {
        return count($this->stream) > 0;
    }

    final public function hasRecordedEvents(): bool
    {
        return count($this->recordedEvents) > 0;
    }

    /**
     * @return AggregateStateChangedEvent[]
     * @throws Exception
     */
    final public function getEvents(): array
    {
        return $this->stream;
    }

    /** @return AggregateStateChangedEvent[] */
    final public function getRecordedEvents(): array
    {
        return $this->recordedEvents;
    }

    final public function flushRecordedEvents(): void
    {
        $this->recordedEvents = [];
    }

    final public function getAggregateId(): string
    {
        return $this->aggregateId;
    }

    final public function getAggregateName(): string
    {
        return $this->aggregateName;
    }

    public function storeAggregateRootEvents(): void
    {
        if ($this->hasRecordedEvents()) {
            $aggregateEventStorageClient = $this->aggregateEventStorageClient;
            foreach ($this->getRecordedEvents() as $event) {
                $aggregateEventStorageClient->storeAggregateRootChangedEvent($event);
            }
            $this->flushRecordedEvents();
        }
    }
}