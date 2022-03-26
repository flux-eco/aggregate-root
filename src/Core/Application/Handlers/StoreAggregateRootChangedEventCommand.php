<?php


namespace FluxEco\AggregateRoot\Core\Application\Handlers;
use FluxEco\AggregateRoot\Core\Domain;

class StoreAggregateRootChangedEventCommand implements Command
{
    private Domain\Events\AggregateStateChangedEvent $event;

    private function __construct(
        Domain\Events\AggregateStateChangedEvent $event
    )
    {
      $this->event = $event;
    }


    public static function fromEvent(
        Domain\Events\AggregateStateChangedEvent $event
    ): self
    {
        return new self(
            $event
        );
    }

    final public function getEvent(): Domain\Events\AggregateStateChangedEvent
    {
        return $this->event;
    }


    final public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}