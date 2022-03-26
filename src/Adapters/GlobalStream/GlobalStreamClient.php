<?php


namespace FluxEco\AggregateRoot\Adapters\GlobalStream;

use FluxEco\AggregateRoot\Core\{Domain\AggregateRoot, Ports};
use FluxEco\GlobalStream\Adapters\Api;

class GlobalStreamClient implements Ports\GlobalStream\GlobalStreamClient
{
    private string $subject;
    private Api\GlobalStreamApi $globalStreamApi;

    private function __construct(Api\GlobalStreamApi $globalStreamApi)
    {
        $this->subject = Api\GlobalStreamSubjects::AGGREGATE_ROOT;
        $this->globalStreamApi = $globalStreamApi;
    }

    public static function newFromAggregate(string $aggregateName): self
    {
        $globalStreamApi = Api\GlobalStreamApi::new([$aggregateName]);
        return new self($globalStreamApi);
    }

    public function publishAggregateRootChanged(string $correlationId, string $eventName, AggregateRoot $currentState): void
    {
        $lastChangedBy = $currentState->getLastChangedBy();
        $subject = $this->subject;
        $subjectId = $currentState->getAggregateId();
        $subjectSequence = $currentState->getCurrentSequence();
        $subjectName = $currentState->getAggregateName();
        $rootObjectSchema = json_encode($currentState->getRootObjectSchema());
        $payload = $currentState->getRootObject()->toJson();

        $this->globalStreamApi->publishStateChange(
            $correlationId,
            $lastChangedBy,
            $subject,
            $subjectId,
            $subjectSequence,
            $subjectName,
            $rootObjectSchema,
            $eventName,
            $payload
        );
    }


}