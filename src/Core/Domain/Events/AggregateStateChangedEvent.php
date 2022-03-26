<?php

namespace FluxEco\AggregateRoot\Core\Domain\Events;

class AggregateStateChangedEvent implements \JsonSerializable
{
    private array $headers;
    private string $payload;

    /**
     * @throws \JsonException
     */
    private function __construct(
        array  $headers,
        string $payload
    )
    {
        $this->headers = $headers;
        $this->payload = $payload;
    }

    public static function new(
        int    $sequence,
        string $eventId,
        string $correlationId,
        string $aggregateId,
        string $aggregateName,
        string $rootObjectSchema,
        string $createdBy,
        string $createdDateTime,
        string $eventName,
        string $payload
    )
    {
        $headers = [];
        $headers['sequence'] = $sequence;
        $headers['eventId'] = $eventId;
        $headers['correlationId'] = $correlationId;
        $headers['aggregateId'] = $aggregateId;
        $headers['aggregateName'] = $aggregateName;
        $headers['rootObjectSchema'] = $rootObjectSchema;
        $headers['createdBy'] = $createdBy;
        $headers['createdDateTime'] = $createdDateTime;
        $headers['eventName'] = $eventName;

        return new self(
            $headers,
            $payload
        );
    }

    /**
     * @throws \JsonException
     */
    public static function fromArray(array $eventData): self
    {

        return self::new(
            $eventData['sequence'],
            $eventData['eventId'],
            $eventData['correlationId'],
            $eventData['aggregateId'],
            $eventData['aggregateName'],
            $eventData['rootObjectSchema'],
            $eventData['createdBy'],
            $eventData['createdDateTime'],
            $eventData['eventName'],
            $eventData['payload']
        );
    }

    final public function getSequence(): int
    {
        return $this->headers['sequence'];
    }

    final public function getEventId(): string
    {
        return $this->headers['eventId'];
    }

    final public function getCorrelationId(): string
    {
        return $this->headers['correlationId'];
    }

    final public function getAggregateId(): string
    {
        return $this->headers['aggregateId'];
    }

    final public function getAggregateName(): string
    {
        return $this->headers['aggregateName'];
    }


    final public function getRootObjectSchema(): string
    {
        return $this->headers['rootObjectSchema'];
    }

    final public function getCreatedBy(): string
    {
        return $this->headers['createdBy'];
    }


    final  public function getCreatedDateTime(): string
    {
        return $this->headers['createdDateTime'];
    }


    final public function getEventName(): string
    {
        return $this->headers['eventName'];
    }

    final public function getPayload(): string
    {
        return $this->payload;
    }

    final public function toArray(): array
    {
        $arrayData = $this->headers;
        $arrayData['payload'] = $this->payload;
        return $arrayData;
    }


    final public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}