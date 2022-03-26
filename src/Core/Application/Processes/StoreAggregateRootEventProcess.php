<?php

namespace FluxEco\AggregateRoot\Core\Application\Processes;

use FluxEco\AggregateRoot\Core\{Application,
    Application\Handlers,
    Domain,
    Ports\Storage\AggregateRootEventStream};
use FluxEco\AggregateRoot\Adapters\Storage\EventStorageClientClient;

/**
 * Class ProcessHandlerCreateNewImportDefinition
 *
 * @author Martin Studer <martin@fluxlabs.ch>
 */
class StoreAggregateRootEventProcess
{


    private function __construct(

    )
    {

    }

    public static function new(

    ): self
    {
        return new self( );
    }

    public function process(Handlers\StoreAggregateRootChangedEventCommand $command, Handlers\StoreAggregateRootChangedEventHandler $handler): void
    {
       $handler->handle($command);
    }
}