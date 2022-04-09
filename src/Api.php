<?php


namespace FluxEco\AggregateRoot;

use FluxEco\AggregateRoot\{Adapters, Core\Ports};

class Api
{
    private Adapters\ValueObjectProvider\ValueObjectProviderClient $valueObjectCreatorClient;
    private Ports\AggregateRootService $aggregateRootService;
    private Adapters\Outbounds $outbounds;

    private function __construct(
        string $databaseName,
        string $aggregateRootSchemaDirectory,
        string $aggregateEventSchemaFilePath
    )
    {
        $outbounds =  Adapters\Outbounds::new(
            $databaseName,
            $aggregateRootSchemaDirectory,
            $aggregateEventSchemaFilePath
        );
        $this->aggregateRootService = Ports\AggregateRootService::new(
            $outbounds
        );
        $this->outbounds = $outbounds;
    }

    public static function newFromEnv(): self
    {
        $env = Env::new();

        return self::new($env->getAggregateRootStorageConfigEnvPrefix(), $env->getAggregateRootDirectory(), $env->getAggregateRootEventSchemaFilePath());
    }


    public static function new(
        string $databaseName,
        string $aggregateRootSchemaDirectory,
        string $aggregateEventSchemaFilePath
    ): self
    {
        return new self($databaseName, $aggregateRootSchemaDirectory, $aggregateEventSchemaFilePath);
    }

    final public function initialize(): void
    {
        $this->aggregateRootService->initialize();
    }

    /**
     * @throws \JsonException
     */
    final public function create(
        string $correlationId,
        string $actorEmail,
        string $aggregateId,
        string $aggregateName,
        string $payload
    ): void
    {
        //todo move
        $payloadArray = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        $payloadArray['id'] = $aggregateId;
        $payloadWithId = json_encode($payloadArray, JSON_THROW_ON_ERROR);

        $commandCreatedDateTime = $this->outbounds->getCurrentTime();


        $this->aggregateRootService->create(
            $correlationId,
            $actorEmail,
            $commandCreatedDateTime,
            $aggregateId,
            $aggregateName,
            $payloadWithId
        );
    }

    final public function change(
        string $correlationId,
        string $actorEmail,
        string $aggregateId,
        string $aggregateName,
        string $payload
    ): void
    {

        $commandCreatedDateTime = $this->outbounds->getCurrentTime();;

        $this->aggregateRootService->change(
            $correlationId,
            $actorEmail,
            $commandCreatedDateTime,
            $aggregateId,
            $aggregateName,
            $payload);
    }


    final public function delete(
        string $correlationId,
        string $actorEmail,
        string $aggregateId,
        string $aggregateName
    ): void
    {
        if ($correlationId === null) {
            $correlationId = $this->outbounds->getNewUuid();
        }

        $commandCreatedDateTime = $this->outbounds->getCurrentTime();

        $this->aggregateRootService->delete(
            $correlationId,
            $actorEmail,
            $commandCreatedDateTime,
            $aggregateId,
            $aggregateName
        );
    }

}