<?php

namespace fluxAggregateRoot;

use FluxEco\AggregateRoot;

function initialize() : void
{
    AggregateRoot\Api::newFromEnv()->initialize();
}