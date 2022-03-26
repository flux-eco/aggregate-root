<?php


namespace FluxEco\AggregateRoot\Adapters\SchemaInstanceProvider;
use FluxEco\AggregateRoot\Core\{Domain, Domain\Models, Ports};
use Flux\Eco\ObjectProvider\SchemaInstance\Adapters\Api\{SchemaInstanceApi};

class SchemaInstanceProviderClient implements Ports\SchemaInstanceProvider\SchemaInstanceProvider
{

    private SchemaInstanceApi $objectProviderApi;

    private function __construct(SchemaInstanceApi $objectProviderApi)
    {
        $this->objectProviderApi = $objectProviderApi;
    }

    public static function new(): self
    {
        $objectProviderApi = SchemaInstanceApi::new();
        return new self($objectProviderApi);
    }


    public function provideRootObject(mixed $value, array $schema): Models\RootObject
    {
        $schemaInstanceArray = $this->objectProviderApi->provideSchemaInstance($value, $schema);
        return Models\RootObject::new($schemaInstanceArray);
    }
}