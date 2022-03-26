<?php

namespace FluxEco\AggregateRoot\Core\Domain\Models;

class RootObject implements \JsonSerializable
{
    private RootObjectProperties $properties;

    private function __construct()
    {
        $this->properties = RootObjectProperties::new();
    }

    public static function new(
        array $properties
    ): self
    {
        $obj = new self();
        foreach ($properties as $key => $value) {
            if (is_null($value)) {
                continue;
            }
            $obj->properties->offsetSet($key, RootObjectProperty::fromArray($value));
        }
        return $obj;
    }

    public function withProperty(string $key, RootObjectProperty $property): RootObject
    {
        $obj = clone($this);
        $obj->properties->offsetSet($key, $property);
        return $obj;
    }

    final public function getProperties(): RootObjectProperties
    {
        return $this->properties;
    }

    public function jsonSerialize(): array
    {
        return $this->properties->toArray();
    }

    /**
     * @throws \JsonException
     */
    public function toJson(): string
    {
        return json_encode($this, JSON_THROW_ON_ERROR);
    }
}