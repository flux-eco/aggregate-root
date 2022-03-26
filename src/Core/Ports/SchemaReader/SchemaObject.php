<?php

namespace FluxEco\AggregateRoot\Core\Ports\SchemaReader;

interface SchemaObject
{
    public function getType(): string;
}