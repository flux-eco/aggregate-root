<?php

namespace FluxEco\AggregateRoot\Core\Domain\Models;

class RootObjectProperty implements \JsonSerializable
{
    private mixed $value;
    private bool $isEntityId;
    private ?string $describedBy;

    private function __construct(mixed $value, bool $isEntityId = false, ?string $describedBy = null)
    {
        $this->value = $value;
        $this->isEntityId = $isEntityId;
        if ($describedBy !== null) {
            $this->describedBy = $describedBy;
        }
    }

    public static function fromArray(
        array $propertyArray
    ): self
    {
        return new self(
            $propertyArray['value']
        );
    }

    public static function new(
        mixed  $value,
        bool $isEntityId = false,
        ?string $describedBy = null
    ): self
    {
        return new self($value, $isEntityId, $describedBy);
    }

    final public function getDescribedBy(): string
    {
        return $this->describedBy;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    final public function isEntityId(): bool
    {
        return $this->isEntityId;
    }

    public function equals(mixed $other): bool
    {
        echo "currentValue: ";
        echo $this->getValue().PHP_EOL;
        echo "otherValue: ";
        echo $other->getValue().PHP_EOL;
        echo ($this->getValue() === $other->getValue());
        return ($this->getValue() === $other->getValue());
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}