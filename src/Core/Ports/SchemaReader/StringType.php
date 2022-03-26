<?php

namespace FluxEco\AggregateRoot\Core\Ports\SchemaReader;

class StringType implements ValueType {

    public function __toString(): string
    {
        return 'string';
    }
}