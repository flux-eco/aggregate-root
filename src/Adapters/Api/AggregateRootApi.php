<?php


namespace FluxEco\AggregateRoot\Adapters\Api;

use FluxEco\AggregateRoot\{Adapters, Core\Ports};

class AggregateRootApi
{
    private const CURRENT_SCHEMA_VERSION = 'latest';

    //todo
    const AGGREGATEROOT_SCHEMA_DIRECTORY = '/app/flux-app/schemas/Domain';

    private Adapters\ValueObjectProvider\ValueObjectProviderClient $valueObjectCreatorClient;
    private Ports\AggregateRootService $aggregateRootService;

    private function __construct(
        Ports\AggregateRootService                             $aggregateRootService,
        Adapters\ValueObjectProvider\ValueObjectProviderClient $valueObjectCreatorClient)
    {
        $this->aggregateRootService = $aggregateRootService;
        $this->valueObjectCreatorClient = $valueObjectCreatorClient;
    }

    public static function fromAggregateRootName(string $aggregateRootName): self
    {
        $aggregateRootSchemaPath = self::AGGREGATEROOT_SCHEMA_DIRECTORY . '/' . $aggregateRootName . '.yaml';
        return self::new($aggregateRootSchemaPath);
    }


    public static function new(): self
    {
        $aggregateRootService = Ports\AggregateRootService::new(
            Adapters\Configs\AggregateRootOutbounds::new()
        );
        $valueObjectCreatorClient = Adapters\ValueObjectProvider\ValueObjectProviderClient::new();
        return new self($aggregateRootService, $valueObjectCreatorClient);
    }

    final public function initializeAggregateRoot(string $aggregateName): void
    {
        $this->aggregateRootService->createEventStorage($aggregateName);
    }

    /**
     * @throws \JsonException
     */
    final public function createAggregateRoot(
        string $correlationId,
        string $actorEmail,
        string $aggregateName,
        string $aggregateId,
        string $payload
    ): void
    {
        //todo move
        $payloadArray = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        $payloadArray['id'] = $aggregateId;
        $payloadWithId = json_encode($payloadArray, JSON_THROW_ON_ERROR);

        $commandCreatedDateTime = $this->valueObjectCreatorClient->createCurrentTime()->getValue();


        $this->aggregateRootService->createAggregateRoot(
            $correlationId,
            $actorEmail,
            $commandCreatedDateTime,
            $aggregateId,
            $aggregateName,
            $payloadWithId
        );
    }

    final public function changeAggregateRoot(
        string $correlationId,
        string $actorEmail,
        string $aggregateId,
        string $aggregateName,
        string $payload
    ): void
    {

        $commandCreatedDateTime = $this->valueObjectCreatorClient->createCurrentTime()->getValue();

        $this->aggregateRootService->changeAggregateRoot(
            $correlationId,
            $actorEmail,
            $commandCreatedDateTime,
            $aggregateId,
            $aggregateName,
            $payload);
    }


    final public function deleteItem(
        string $correlationId,
        string $actorEmail,
        string $aggregateId,
        string $aggregateName
    ): void
    {
        if ($correlationId === null) {
            $correlationId = $this->valueObjectCreatorClient->createUuid()->getValue();
        }

        $commandCreatedDateTime = $this->valueObjectCreatorClient->createCurrentTime()->getValue();

        $this->aggregateRootService->deleteAggregateRoot(
            $correlationId,
            $actorEmail,
            $commandCreatedDateTime,
            $aggregateId,
            $aggregateName
        );
    }

}