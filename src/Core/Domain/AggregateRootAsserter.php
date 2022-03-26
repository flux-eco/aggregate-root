<?php

namespace FluxEco\AggregateRoot\Core\Domain;

use Exception;
use FluxEco\AggregateRoot\Core\Ports\AggregateRootEventWriter;

use FluxEco\AggregateRoot\Core\Domain\Exceptions\AggregateRootPropertyIsNotEqual;
use Flux\Eco\JsonSchemaAsserters\Core\Ports\Exceptions\PropertyIsNotEqualException;


class AggregateRootAsserter
{

    private string $aggregateName;
    private string $aggregateRootSchemaFilePath;
    private AggregateRootEventWriter\JsonSchemaAsserters $jsonSchemaAsserters;

    private function __construct(string $aggregateName, string $aggregateRootSchemaFilePath, AggregateRootEventWriter\JsonSchemaAsserters $jsonSchemaAsserters)
    {
        $this->aggregateName = $aggregateName;
        $this->aggregateRootSchemaFilePath = $aggregateRootSchemaFilePath;
        $this->jsonSchemaAsserters = $jsonSchemaAsserters;
    }

    public static function new(string $aggregateName, string $aggregateRootSchemaFilePath, AggregateRootEventWriter\JsonSchemaAsserters $jsonSchemaAsserters): self
    {
        return new self($aggregateName, $aggregateRootSchemaFilePath, $jsonSchemaAsserters);
    }

    /**
     * @param Models\ObjectProperties[] $aggregateRootProperties
     * @throws Exception
     */
    final public function assertAggregatePropertiesCorrespondsCurrentSchema(array $aggregateRootProperties): void
    {
        foreach ($aggregateRootProperties as $propertyKey => $propertyValue) {
            $this->jsonSchemaAsserters->assertPropertyExistsInSchema($propertyKey, $propertyValue, $this->aggregateRootSchemaFilePath);
        }
    }

    public function assertAggregateIdCorrespondsCurrentId(string $aggregateId, string $currentId): void
    {
        if ($aggregateId !== $currentId) {
            throw new Exception('The transmitted aggregateId ' . $aggregateId . ' is not equal current aggregateId ' . $currentId);
        }
    }

    public function assertAggregateNameCorrespondsCurrentName(string $aggregateName, string $currentName): void
    {
        if ($aggregateName !== $this->aggregateName) {
            throw new Exception('The transmitted aggregateName ' . $aggregateName . ' is not equal current aggregateName ' . $currentName);
        }
    }


    final public function AssertAggregatePropertyIsEqual(string $propertyKey, string|array|object|int|null $property, string|array|object|int|null $currentState): void
    {
        try {
            $this->jsonSchemaAsserters->assertPropertyIsEqual($propertyKey, $property, $currentState, $this->aggregateRootSchemaFilePath);
        } catch (PropertyIsNotEqualException $exception) {
            throw new AggregateRootPropertyIsNotEqual($exception->getMessage());
        }
    }

}