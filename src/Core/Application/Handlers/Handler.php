<?php

namespace FluxEco\AggregateRoot\Core\Application\Handlers;

interface Handler
{
    public function handle(Command $command);
}