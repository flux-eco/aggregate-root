<?php

namespace FluxEco\AggregateRoot\Adapters\Storage;

use FluxEco\AggregateRoot\Core\Domain\Events\AggregateStateChangedEvent;

class AggregateStateChangedEventsAdapter
{
    /** @var AggregateStateChangedEvent[] */
    private array $events;

    private function __construct(array $events)
    {
        $this->events = $events;
    }

    public static function fromQueryResult(array $queryResult): self
    {
        $events = [];
        foreach ($queryResult as $row) {
            $events[$row['sequence']] = AggregateStateChangedEvent::new(
                $row['sequence'],
                $row['eventId'],
                $row['correlationId'],
                $row['aggregateId'],
                $row['aggregateName'],
                $row['rootObjectSchema'],
                $row['createdBy'],
                $row['createdDateTime'],
                $row['eventName'],
                $row['payload']
            );
        }
        return new self($events);
    }

    /**
     * @return AggregateStateChangedEvent[]
     */
    final public function toEvents(): array
    {
        return $this->events;
    }
}