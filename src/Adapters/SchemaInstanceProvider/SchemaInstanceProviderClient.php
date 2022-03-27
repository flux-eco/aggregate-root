<?php


namespace FluxEco\AggregateRoot\Adapters\SchemaInstanceProvider;
use FluxEco\AggregateRoot\Core\{Domain, Domain\Models, Ports};
use FluxEco\JsonSchemaInstance\Adapters\Api\{JsonSchemaInstanceApi};

class SchemaInstanceProviderClient implements Ports\SchemaInstanceProvider\SchemaInstanceProvider
{

    private JsonSchemaInstanceApi $objectProviderApi;

    private function __construct(JsonSchemaInstanceApi $objectProviderApi)
    {
        $this->objectProviderApi = $objectProviderApi;
    }

    public static function new(): self
    {
        $objectProviderApi = JsonSchemaInstanceApi::new();
        return new self($objectProviderApi);
    }


    public function provideRootObject(mixed $value, array $schema): Models\RootObject
    {
        $schemaInstanceArray = $this->objectProviderApi->provideSchemaInstance($value, $schema);
        return Models\RootObject::new($schemaInstanceArray);
    }
}