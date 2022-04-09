<?php

namespace FluxEco\AggregateRoot\Core\Domain\Models;

use FluxEco\JsonSchemaDocument;

class SchemaDocument
{
    /** @var JsonSchemaDocument\SchemaObject[] */
    private array $properties;

    private function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    /** @param JsonSchemaDocument\SchemaObject[] $properties */
    public static function new(array $properties) : self
    {
        return new self($properties);
    }

    public function offsetExists(string $transmittedPropertyKey) : bool
    {
        return array_key_exists($transmittedPropertyKey, $this->properties);
    }

    public function offsetGet(string $transmittedPropertyKey)
    {
        return $this->properties[$transmittedPropertyKey];
    }

    /** @return JsonSchemaDocument\SchemaObject[] */
    public function getProperties() : array
    {
        return $this->properties;
    }
}