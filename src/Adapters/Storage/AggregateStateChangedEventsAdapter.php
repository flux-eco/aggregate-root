<?php

namespace FluxEco\AggregateRoot\Adapters\Storage;

use FluxEco\AggregateRoot\Core;

class AggregateStateChangedEventsAdapter
{
    /** @var Core\Domain\Events\AggregateStateChangedEvent[] */
    private array $events;

    private function __construct(array $events)
    {
        $this->events = $events;
    }

    public static function fromQueryResult(array $queryResult): self
    {
        $events = [];
        foreach ($queryResult as $row) {
            $events[$row['sequence']] = Core\Domain\Events\AggregateStateChangedEvent::new(
                $row['sequence'],
                $row['eventId'],
                $row['correlationId'],
                $row['aggregateId'],
                $row['aggregateName'],
                $row['createdBy'],
                $row['createdDateTime'],
                $row['eventName'],
                $row['payload']
            );
        }
        return new self($events);
    }

    /**
     * @return Core\Domain\Events\AggregateStateChangedEvent[]
     */
    final public function toEvents(): array
    {
        return $this->events;
    }
}