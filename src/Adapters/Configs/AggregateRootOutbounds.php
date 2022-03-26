<?php

namespace FluxEco\AggregateRoot\Adapters\Configs;

use FluxEco\AggregateRoot\{Adapters, Core\Ports};
use function Swoole\Coroutine\Http\get;

class AggregateRootOutbounds implements Ports\Configs\AggregateRootOutbounds
{
    private const DATABASE_NAME = 'events';

    private string $databaseName;
    private array $aggregateRootEventSchema;


    private function __construct(
        string $databaseName,
        array  $aggregateRootEventSchema
    )
    {
        $this->databaseName = $databaseName;
        $this->aggregateRootEventSchema = $aggregateRootEventSchema;
    }

    public static function new(): self
    {
        $aggregateRootEventSchemaPath = getenv(AggregateRootEnv::PARAM_AGGREGATE_ROOT_EVENT_SCHEMA);
        $aggregateEventSchema = yaml_parse(file_get_contents($aggregateRootEventSchemaPath));

        return new self(self::DATABASE_NAME, $aggregateEventSchema);
    }


    final public function getAggregateRootSchema(string $aggregateName): array
    {
        $schemaFile = getenv(AggregateRootEnv::PARAM_APP_AGGREGATEROOT_SCHEMA_DIRECTORY).'/'.$aggregateName.'.yaml';
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
            $this->aggregateRootEventSchema
        );
    }

    public function getSchemaInstanceProvider(): Ports\SchemaInstanceProvider\SchemaInstanceProvider
    {
        return Adapters\SchemaInstanceProvider\SchemaInstanceProviderClient::new();
    }


    public function getAggregateEventSchema(): array
    {
        return $this->aggregateRootEventSchema;
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