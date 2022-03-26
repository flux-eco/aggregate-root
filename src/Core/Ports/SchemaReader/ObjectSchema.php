<?php

namespace FluxEco\AggregateRoot\Core\Ports\SchemaReader;

interface ObjectSchema
{
    public function getProperties(): SchemaProperties;
}