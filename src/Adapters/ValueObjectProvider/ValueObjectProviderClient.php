<?php

namespace FluxEco\AggregateRoot\Adapters\ValueObjectProvider;

use  FluxEco\AggregateRoot\Core;
use FluxEco\ValueObjectAdapters\Api;

class ValueObjectProviderClient implements Core\Ports\ValueObjectProvider\ValueObjectProviderClient
{
    private Api\ValueObjectApi $objectProvider;

    private function __construct(Api\ValueObjectApi $objectProvider)
    {
        $this->objectProvider = $objectProvider;
    }

    public static function new(): self
    {
        $objectProvider = Api\ValueObjectApi::new();
        return new self($objectProvider);
    }

    public function createUuid(): Core\Domain\Models\StringObject
    {
        $valueObject = $this->objectProvider->createUuid();
        return ValueObjectAdapter::fromApi($valueObject)->toDomain();
    }

    public function createCurrentTime(): Core\Domain\Models\StringObject
    {
        $valueObject = $this->objectProvider->createCurrentTime();
        return ValueObjectAdapter::fromApi($valueObject)->toDomain();
    }
}