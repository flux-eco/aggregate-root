<?php

namespace FluxEco\AggregateRoot\Core\Domain\Exceptions;

use Exception;

class AggregateRootPropertyIsNotEqual extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}