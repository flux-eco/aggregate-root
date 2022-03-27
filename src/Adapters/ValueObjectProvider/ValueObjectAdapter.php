<?php

namespace FluxEco\AggregateRoot\Adapters\ValueObjectProvider;

use FluxEco\AggregateRoot\Core\Domain;
use FluxEco\ValueObject\Adapters\Api;

class ValueObjectAdapter
{
    private Api\StringObject $providedStringObject;

    private function __construct(Api\StringObject $stringObject)
    {
        $this->providedStringObject = $stringObject;
    }

    public static function fromApi(Api\StringObject $stringObject): self
    {
        return new self($stringObject);
    }

    public function toDomain(): Domain\Models\StringObject
    {
        return Domain\Models\StringObject::new($this->providedStringObject->getValue());
    }
}