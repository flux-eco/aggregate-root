<?php


namespace FluxEco\AggregateRoot\Adapters\SchemaReader;
use  FluxEco\AggregateRoot\Core\Domain;
use FluxEco\JsonSchemaDocument\Adapters\Api;

class SchemaDocument
{

    private Api\SchemaDocument $schemaDocument;

    private function __construct(Api\SchemaDocument $schemaDocument)
    {
        $this->schemaDocument = $schemaDocument;
    }

    public static function fromApi(Api\SchemaDocument $schemaDocument)
    {
        return new self($schemaDocument);
    }

    public function toDomain():Domain\Models\SchemaDocument {
        return Domain\Models\SchemaDocument::new($this->schemaDocument->getProperties());
    }
}