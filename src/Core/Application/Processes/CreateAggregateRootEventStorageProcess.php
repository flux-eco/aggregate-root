<?php

namespace FluxEco\AggregateRoot\Core\Application\Processes;

use FluxEco\AggregateRoot\Core\{Application\Handlers, Ports};

/**
 * Class ProcessHandlerCreateNewImportDefinition
 *
 * @author Martin Studer <martin@fluxlabs.ch>
 */
class CreateAggregateRootEventStorageProcess
{



    private function __construct()
    {

    }

    public static function new(): self
    {
        return new self();
    }

    public function process(Handlers\Command $createEventStorageCommand, Handlers\Handler $handler): void
    {
        $handler->handle($createEventStorageCommand);
    }
}