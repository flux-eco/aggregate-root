<?php

namespace FluxEco\AggregateRoot\Core\Application\Handlers;

class CreateAggregateRootEventStorageCommand implements Command
{
    private string $aggregateName;
    private array $jsonSchema; //todo do we need this?

    private function __construct(string $aggregateName, array $jsonSchema)
    {
        $this->aggregateName = $aggregateName;
        $this->jsonSchema  = $jsonSchema;
    }

    public static function new(string $aggregateName, array $jsonSchema): self
    {
        return new self($aggregateName, $jsonSchema);
    }

    final public function getAggregateName(): string
    {
        return $this->aggregateName;
    }

    final public function getJsonSchema(): array
    {
        return $this->jsonSchema;
    }

    final public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}