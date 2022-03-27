<?php

namespace FluxEco\AggregateRoot\Adapters\Configs;

use FluxEco\AggregateRoot\{Adapters, Core\Ports};
use function Swoole\Coroutine\Http\get;

class Outbounds implements Ports\Configs\Outbounds
{
    private const DATABASE_NAME = 'events';

    private string $databaseName;


    private function __construct(
        string $databaseName,
    )
    {
        $this->databaseName = $databaseName;
    }

    public static function new(): self
    {
        return new self(self::DATABASE_NAME);
    }

    final public function getAppAggregateRootSchemaDirectory(): string
    {
        return getenv(AggregateRootEnv::APP_AGGREGATEROOT_SCHEMA_DIRECTORY);
    }


    final public function getAggregateRootSchema(string $aggregateName): array
    {
        $schemaFile = getenv(AggregateRootEnv::APP_AGGREGATEROOT_SCHEMA_DIRECTORY).'/'.$aggregateName.'.yaml';
        return yaml_parse(file_get_contents($schemaFile));
    }

    public function getDatabaseName(): string
    {
        return $this->databaseName;
    }

    public function getJsonSchemaAssertersClient(): Ports\Assert\AssertJsonSchemaClient
    {
        return Adapters\Assert\AsserterClientClientAssert::new();
    }


    public function getAggregateEventStorageClient(
        string $aggregateName
    ): Ports\Storage\AggregateEventStorageClient
    {
        return Adapters\Storage\EventStorageClientClient::new(
            $this->databaseName,
            $aggregateName,
            $this->getAggregateRootEventSchema()
        );
    }

    public function getSchemaInstanceProvider(): Ports\SchemaInstanceProvider\SchemaInstanceProvider
    {
        return Adapters\SchemaInstanceProvider\SchemaInstanceProviderClient::new();
    }


    public function getAggregateRootEventSchema(): array
    {
        return yaml_parse(file_get_contents(getenv(AggregateRootEnv::FLUXECO_AGGREGATEROOT_DIRECTORY).'/schemas/AggregateRootEvent.yaml'));
    }

    public function getSchemaFileReader(): Ports\SchemaReader\SchemaFileReader
    {
        return Adapters\SchemaReader\SchemaFileReaderClient::new();
    }

    public function getGlobalStreamClient(string $aggregateName): Ports\GlobalStream\GlobalStreamClient
    {
        return Adapters\GlobalStream\GlobalStreamClient::newFromAggregate($aggregateName);
    }

    public function getValueObjectProviderClient(): Ports\ValueObjectProvider\ValueObjectProviderClient
    {
        return Adapters\ValueObjectProvider\ValueObjectProviderClient::new();
    }
}