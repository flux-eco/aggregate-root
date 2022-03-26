<?php

namespace FluxEco\AggregateRoot\Core\Application\Processes;

use FluxEco\AggregateRoot\Core\{
    Application,
    Ports
};

/**
 * Class ProcessHandlerCreateNewImportDefinition
 *
 * @author Martin Studer <martin@fluxlabs.ch>
 */
class QueryAggregateEventsProcess
{
    private Application\Handlers\GetAggregateRootEventStreamHandler $getAggregateEventsHandler;

    private function __construct(Application\Queries\GetAggregateRootEventStreamHandler $getAggregateEventsHandler)
    {
        $this->getAggregateEventsHandler = $getAggregateEventsHandler;
    }

    public static function new(Ports\Storage\GlobalStreamEventStorage $storageClient): self
    {
        $queryHandler = Application\Queries\GetAggregateRootEventStreamHandler::new($storageClient);
        return new self($queryHandler);
    }

    public function process(Application\Queries\GetAggregateRootEventStreamQuery $query): \JsonSerializable
    {
        return $this->getAggregateEventsHandler->handle($query);
    }
}