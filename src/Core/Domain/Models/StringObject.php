<?php

namespace FluxEco\AggregateRoot\Core\Domain\Models;

class StringObject
{
    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function new(string $value)
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }
}