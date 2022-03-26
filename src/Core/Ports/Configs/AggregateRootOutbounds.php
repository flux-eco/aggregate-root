<?php

namespace FluxEco\AggregateRoot\Core\Ports\Configs;
use FluxEco\AggregateRoot\Core\Ports;

interface AggregateRootOutbounds
{
    public function getDatabaseName(): string;
    public function getAggregateEventSchema(): array;
    public function getAggregateEventStorageClient(string $aggregateName): Ports\Storage\AggregateEventStorageClient;
    public function getSchemaInstanceProvider(): Ports\SchemaInstanceProvider\SchemaInstanceProvider;
    public function getSchemaFileReader(): Ports\SchemaReader\SchemaFileReader;
    public function getAggregateRootSchema(string $aggregateName): array;
    public function getJsonSchemaAssertersClient(): Ports\Assert\AssertJsonSchemaClient;
    public function getGlobalStreamClient(string $aggregateName): Ports\GlobalStream\GlobalStreamClient;
    public function getValueObjectProviderClient(): Ports\ValueObjectProvider\ValueObjectProviderClient;
}