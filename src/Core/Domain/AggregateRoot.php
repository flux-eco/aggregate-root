<?php

namespace FluxEco\AggregateRoot\Core\Domain;

use Exception;
use FluxEco\AggregateRoot\Core\{Application, Domain\Models, Ports};
use Symfony\Component\Process\PhpProcess;

class AggregateRoot implements \JsonSerializable
{
    public const AGGREGATE_ROOT_CREATED_EVENT = 'aggregateRootCreated';
    public const AGGREGATE_ROOT_CHANGED_EVENT = 'aggregateRootChanged';
    public const AGGREGATE_ROOT_DELETED_EVENT = 'aggregateRootDeleted';

    public const AGGREGATE_ROOT_STATUS_LIFECYCLE_INCOMPLETE = 'incomplete';
    public const AGGREGATE_ROOT_STATUS_LIFECYCLE_COMPLETED = 'completed';
    public const AGGREGATE_ROOT_STATUS_LIFECYCLE_DELTED = 'deleted';


    /** @var AggregateRoot[] */
    protected static array $instances = [];

    private string $aggregateId;
    private string $aggregateName;
    private int $currentSequence;
    private array $rootObjectSchema;
    private string $createdBy;
    private string $createdDateTime;
    private string $lastChangedDateTime;
    private string $lastChangedBy;
    private string $lifeCycleStatus;

    private ?Models\RootObject $rootObject = null;

    private AggregateRootEventStream $eventStream;
    private Ports\Outbounds $outbounds;

    private function __construct(
        string                   $aggregateId,
        string                   $aggregateName,
        AggregateRootEventStream $eventStream,
        Ports\Outbounds   $outbounds
    )
    {
        $this->aggregateId = $aggregateId;
        $this->aggregateName = $aggregateName;

        $this->eventStream = $eventStream;
        $this->outbounds = $outbounds;

        $this->lifeCycleStatus = self::AGGREGATE_ROOT_STATUS_LIFECYCLE_INCOMPLETE;

        $this->reconstitute();
    }

    public static function new(
        string                 $aggregateId,
        string                 $aggregateName,
        Ports\Outbounds $outbounds

    ): self
    {
        if (!array_key_exists($aggregateId, static::$instances)) {
            $aggregateRootEventStream = AggregateRootEventStream::new(
                $aggregateId,
                $aggregateName,
                $outbounds
            );
            //todo END

            static::$instances[$aggregateId] = new self(
                $aggregateId,
                $aggregateName,
                $aggregateRootEventStream,
                $outbounds
            );
        }
        return static::$instances[$aggregateId];
    }

    /**
     * @throws \JsonException
     */
    public function create(
        string $correlationId,
        string $actorEmail,
        string $commandCreatedDateTime,
        string $aggregateId,
        string $aggregateName,
        array  $rootObjectSchema,
        string $payload
    ): self
    {
        $rootObject = $this->jsonDecodeRootObject($payload, $rootObjectSchema);

        $sequence = $this->eventStream->getNextSequence();

        $rootObjectJsonSchema = json_encode($rootObjectSchema, JSON_THROW_ON_ERROR);

        $eventId = $this->outbounds->getNewUuid();

        echo "Create Aggregate ".PHP_EOL;
        $this->applyRecordAndPublishCurrentState(
            Events\AggregateStateChangedEvent::new(
                $sequence,
                $eventId,
                $correlationId,
                $aggregateId,
                $aggregateName,
                $rootObjectJsonSchema,
                $actorEmail,
                $commandCreatedDateTime,
                self::AGGREGATE_ROOT_CREATED_EVENT,
                json_encode($rootObject, JSON_THROW_ON_ERROR)
            )
        );
        echo "Create Store AggregateEvents ".PHP_EOL;
        $this->eventStream->storeAggregateRootEvents();
        echo "AggregateEvents stored".PHP_EOL;
        return $this;
    }

    /**
     * @throws \JsonException
     */
    public function change(
        string $correlationId,
        string $actorEmail,
        string $commandCreatedDateTime,
        string $aggregateId,
        string $aggregateName,
        array  $rootObjectSchema,
        string $payload
    ): self
    {
        $rootObject = $this->jsonDecodeRootObject($payload, $rootObjectSchema);
        $reducedRootObject = $this->reduceToChangedProperties(
            $rootObject,
            $rootObjectSchema
        );
        if ($reducedRootObject->getProperties()->count() === 0) {
            return $this;
        }


        $sequence = $this->eventStream->getNextSequence();

        $rootObjectJsonSchema = json_encode($rootObjectSchema, JSON_THROW_ON_ERROR);

        $eventId = $this->outbounds->getNewUuid();

        $this->applyRecordAndPublishCurrentState(
            Events\AggregateStateChangedEvent::new(
                $sequence,
                $eventId,
                $correlationId,
                $aggregateId,
                $aggregateName,
                $rootObjectJsonSchema,
                $actorEmail,
                $commandCreatedDateTime,
                self::AGGREGATE_ROOT_CHANGED_EVENT,
                json_encode($reducedRootObject, JSON_THROW_ON_ERROR)
            )
        );
        $this->eventStream->storeAggregateRootEvents();
        return $this;
    }

    public function delete(
        string $correlationId,
        string $actorEmail,
        string $commandCreatedDateTime,
        string $aggregateId,
        string $aggregateName,
        array  $rootObjectSchema,
    ): void
    {
        $sequence = $this->eventStream->getNextSequence();

        $rootObjectJsonSchema = json_encode($rootObjectSchema, JSON_THROW_ON_ERROR);

        $eventId = $this->outbounds->getNewUuid();

        $this->applyRecordAndPublishCurrentState(
            Events\AggregateStateChangedEvent::new(
                $sequence,
                $eventId,
                $correlationId,
                $aggregateId,
                $aggregateName,
                $rootObjectJsonSchema,
                $actorEmail,
                $commandCreatedDateTime,
                self::AGGREGATE_ROOT_DELETED_EVENT,
                '')
        );
        $this->eventStream->storeAggregateRootEvents();

    }

    private function reduceToChangedProperties(Models\RootObject $transmittedRootObject,  array  $rootObjectSchema): Models\RootObject
    {
        $currentRootObject = $this->rootObject;
        $schemaProperties = $rootObjectSchema['properties']['rootObject']['properties'];

        if ($transmittedRootObject->getProperties()->count() > 0) {

            $currentRootObjectProperties = $currentRootObject->getProperties();
            //echo "schema properties ".print_r($schemaProperties, true).PHP_EOL;

            //echo "currentRootObjectProperties ".print_r($currentRootObjectProperties, true).PHP_EOL;

            $transmittedRootObjectProperties = $transmittedRootObject->getProperties();

            //echo "transmittedRootObjectProperties ".print_r($transmittedRootObjectProperties, true).PHP_EOL;

            //TODO
            foreach ($schemaProperties as $schemaPropertyKey => $schemaProperty) {
                /*if ($rootObjectSchema->offsetExists($transmittedPropertyKey) === false) {
                    throw new Exception('Property ' . $transmittedPropertyKey . ' is not a property of Aggregate ' . $this->aggregateName);
                }*/

                if (
                    $transmittedRootObjectProperties->offsetExists($schemaPropertyKey)
                ) {
                    $transmittedProperty = $transmittedRootObjectProperties->offsetGet($schemaPropertyKey);
                    if ($currentRootObjectProperties->offsetExists($schemaPropertyKey) === true) {
                        $currentProperty = $currentRootObjectProperties->offsetGet($schemaPropertyKey);
                        if ($currentProperty->equals($transmittedProperty) === true) {
                            $transmittedRootObject->getProperties()->offsetUnset($schemaPropertyKey);
                        }
                    }
                }
            }
        }

        //echo  "reduced transmittedRootObjectProperties ".print_r($transmittedRootObject, true).PHP_EOL;

        return $transmittedRootObject;
    }

    /**
     * @throws Exception
     */
    private function reconstitute(): void
    {
        $eventArrayList = $this->eventStream->getEvents();

        if (count($eventArrayList) > 0) {
            foreach ($eventArrayList as $event) {
                $this->applyEvent(
                    $event
                );
            }
        }
    }

    private function applyRecordAndPublishCurrentState(Events\AggregateStateChangedEvent $event)
    {
        //todo decide ordering of these 3 steps
        $this->applyEvent($event);
        $this->recordEvent($event);

        //todo we could introduce try catch an revert all external state (by correlationId) changes if one of them failes
        $correlationId = $event->getCorrelationId();
        $this->publishStateChanged($correlationId, $event->getEventName());
    }

    /**
     * @throws Exception
     */
    private function applyEvent(Events\AggregateStateChangedEvent $event): void
    {
        $applyMethodName = 'apply' . ucfirst($event->getEventName());
        $this->{$applyMethodName}($event);
    }

    private function applyAggregateRootCreated(Events\AggregateStateChangedEvent $event): void
    {
        $this->rootObjectSchema = json_decode($event->getRootObjectSchema(), true);
        $this->currentSequence = $event->getSequence();
        $this->createdBy = $event->getCreatedBy();
        $this->createdDateTime = $event->getCreatedDateTime();
        $this->lastChangedBy = $event->getCreatedBy();
        $this->lastChangedDateTime = $event->getCreatedDateTime();
        $this->aggregateName = $event->getAggregateName();
        $this->aggregateId = $event->getAggregateId();

        $rootObjectAsArray = json_decode($event->getPayload(), true);
        $this->rootObject = Models\RootObject::new($rootObjectAsArray);

        //todo check if all schema requirements ar fullfield
        $this->lifeCycleStatus = self::AGGREGATE_ROOT_STATUS_LIFECYCLE_COMPLETED;
    }

    /**
     * @throws \JsonException
     */
    private function applyAggregateRootChanged(Events\AggregateStateChangedEvent $event): void
    {
        $this->rootObjectSchema = json_decode($event->getRootObjectSchema(), true);
        $this->currentSequence = $event->getSequence();
        $this->lastChangedBy = $event->getCreatedBy();
        $this->lastChangedDateTime = $event->getCreatedDateTime();
        $changedProperties = json_decode($event->getPayload(), true, 512, JSON_THROW_ON_ERROR);

        $currentRootObjectState = $this->rootObject;
        //TODO
        foreach ($changedProperties as $key => $propertyArray) {
            $rootObjectProperty = Models\RootObjectProperty::fromArray($propertyArray);
            $currentRootObjectState = $currentRootObjectState->withProperty($key, $rootObjectProperty);
        }
        $this->rootObject = $currentRootObjectState;

        //todo check if all schema requirements ar fullfield
        $this->lifeCycleStatus = self::AGGREGATE_ROOT_STATUS_LIFECYCLE_COMPLETED;
    }

    /**
     * @throws \JsonException
     */
    private function applyAggregateRootDeleted(Events\AggregateStateChangedEvent $event): void
    {
        $this->rootObjectSchema = json_decode($event->getRootObjectSchema(), true);
        $this->currentSequence = $event->getSequence();
        $this->lastChangedBy = $event->getCreatedBy();
        $this->lastChangedDateTime = $event->getCreatedDateTime();

        $this->lifeCycleStatus = self::AGGREGATE_ROOT_STATUS_LIFECYCLE_DELTED;
    }


    /**
     * @return  AggregateRootEventStream
     */
    final public function getEventStream(): AggregateRootEventStream
    {
        return $this->eventStream;
    }


    private function recordEvent(Events\AggregateStateChangedEvent $event): void
    {
        $this->eventStream->applyAndRecordEvent($event);
    }

    private function publishStateChanged(string $corrleationId, string $eventName): void
    {
        $this->outbounds->publishAggregateRootChanged($corrleationId, $eventName, $this);
    }


    final public function getAggregateId(): string
    {
        return $this->aggregateId;
    }


    final public function getAggregateName(): string
    {
        return $this->aggregateName;
    }


    final public function getCurrentSequence(): int
    {
        return $this->currentSequence;
    }


    final public function getRootObjectSchema(): array
    {
        return $this->rootObjectSchema;
    }

    final public function getCreatedBy(): string
    {
        return $this->createdBy;
    }


    final public function getCreatedDateTime(): string
    {
        return $this->createdDateTime;
    }


    final public function getLastChangedDateTime(): string
    {
        return $this->lastChangedDateTime;
    }


    final public function getLastChangedBy(): string
    {
        return $this->lastChangedBy;
    }


    final public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    final public function getRootObject(): Models\RootObject
    {
        return $this->rootObject;
    }


    /**
     * @throws \JsonException
     */
    private function jsonDecodeRootObject(string $rootObjectAsJson, array $rootObjectSchema): Models\RootObject
    {
        return $this->outbounds->jsonDecodeRootObject($rootObjectAsJson, $rootObjectSchema);
    }
}