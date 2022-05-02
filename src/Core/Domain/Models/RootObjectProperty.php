<?php

namespace FluxEco\AggregateRoot\Core\Domain\Models;

class RootObjectProperty implements \JsonSerializable
{
    private mixed $value;
    private ?string $describedBy;

    private function __construct(mixed $value, ?string $describedBy = null)
    {
        $this->value = $value;
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
        ?string $describedBy = null
    ): self
    {
        return new self($value, $describedBy);
    }

    final public function getDescribedBy(): string
    {
        return $this->describedBy;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function equals(mixed $other): bool
    {
        //echo "currentValue: ";
        //echo $this->getValue().PHP_EOL;
        //echo "otherValue: ";
        //echo $other->getValue().PHP_EOL;
        //echo ($this->getValue() === $other->getValue()).PHP_EOL;;
        return ($this->getValue() === $other->getValue());
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}