<?php

namespace FluxEco\AggregateRoot\Core\Domain\Models;

class RootObjectProperties implements \IteratorAggregate
{
    /** @var RootObjectProperty[] */
    private array $properties = [];

    private function __construct()
    {

    }

    public static function new(): self
    {
        return new self();
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->properties);
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->properties);
    }

    public function offsetGet(mixed $offset): RootObjectProperty
    {
        return $this->properties[$offset];
    }

    public function offsetSet(mixed $offset, RootObjectProperty $value)
    {
        $this->properties[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->properties[$offset]);
    }

    public function count(): int
    {
        return count($this->properties);
    }

    public function toArray(): array
    {
        return $this->properties;
    }
}