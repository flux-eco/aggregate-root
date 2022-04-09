<?php

namespace FluxEco\AggregateRoot\Core\Application\Handlers;

use FluxEco\AggregateRoot\Core\{Ports};

class CreateAggregateRootEventStorageHandler implements Handler
{
    private Ports\Outbounds $outbounds;


    private function __construct(
        Ports\Outbounds $outbounds
    )
    {
        $this->outbounds = $outbounds;
    }

    public static function new(
        Ports\Outbounds $outbounds
    ): self
    {
        return new self(
            $outbounds
        );
    }

    public function handle(Command|CreateAggregateRootEventStorageCommand $createEventStorageCommand)
    {
        $aggregateName = $createEventStorageCommand->getAggregateName();
        $this->outbounds->createAggregateRootEventsStorage($aggregateName);
    }
}