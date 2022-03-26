<?php

namespace FluxEco\AggregateRoot\Core\Ports\Assert;


interface AssertJsonSchemaClient
{
    public function assert(\JsonSerializable $value, array $jsonSchema): void;
}