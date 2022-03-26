<?php

namespace FluxEco\AggregateRoot\Core\Ports\SchemaReader;

use FluxEco\AggregateRoot\Core\Domain;

interface SchemaFileReader
{
    public function readSchemaFile(string $schemaFilePath): Domain\Models\SchemaDocument;
}