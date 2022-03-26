<?php

namespace FluxEco\AggregateRoot\Core\Ports\ValueObjectProvider;

use FluxEco\AggregateRoot\Core\Domain;

interface ValueObjectProviderClient
{
    public function createUuid(): Domain\Models\StringObject;
    public function createCurrentTime(): Domain\Models\StringObject;
}