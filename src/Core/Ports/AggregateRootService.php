<?php

namespace FluxEco\AggregateRoot\Core\Ports;

use FluxEco\AggregateRoot\Core\{Application\Handlers, Application\Processes, Domain};

class AggregateRootService
{
    private Outbounds $outbounds;

    private function __construct(
        Outbounds $outbounds
    ) {

        $this->outbounds = $outbounds;
    }

    public static function new(Outbounds $aggregateRootOutbounds) : self
    {
        return new self(
            $aggregateRootOutbounds
        );
    }

    final public function initialize() : void
    {
        foreach ($this->getAggregateRootSchemas() as $schema) {
            $aggregateName = $schema['name'];

            $createEventStorageProcess = Processes\CreateAggregateRootEventStorageProcess::new();

            $eventSchema = $this->outbounds->getAggregateRootEventSchema();

            $command = Handlers\CreateAggregateRootEventStorageCommand::new($aggregateName, $eventSchema);
            $handler = Handlers\CreateAggregateRootEventStorageHandler::new($this->outbounds);
            $createEventStorageProcess->process($command, $handler);
        }
    }

    private function getAggregateRootSchemas() : array
    {
        $aggregateRootSchemas = [];
        $schemDirectory = $this->outbounds->getAppAggregateRootSchemaDirectory();
        $aggregateRootFiles = scandir($schemDirectory);
        foreach ($aggregateRootFiles as $aggregateRootFile) {
            if (pathinfo($aggregateRootFile, PATHINFO_EXTENSION) === "yaml") {
                $aggregateRootSchemas[] = yaml_parse(file_get_contents($schemDirectory . '/' . $aggregateRootFile));
            }
        }
        return $aggregateRootSchemas;
    }

    public function create(
        string $correlationId,
        string $actorEmail,
        string $commandCreatedDateTime,
        string $aggregateId,
        string $aggregateName,
        string $payload
    ) : void {
        echo "create AggregateRoot";

        $aggregateRoot = Domain\AggregateRoot::new($aggregateId, $aggregateName, $this->outbounds);

        $aggregateRoot->create(
            $correlationId,
            $actorEmail,
            $commandCreatedDateTime,
            $aggregateId,
            $aggregateName,
            $this->outbounds->getAggregateRootSchema($aggregateName),
            $payload
        );
    }

    /**
     * @throws \JsonException
     */
    public function change(
        string $correlationId,
        string $actorEmail,
        string $commandCreatedDateTime,
        string $aggregateId,
        string $aggregateName,
        string $payload
    ) : void {
        $aggregateRoot = Domain\AggregateRoot::new($aggregateId, $aggregateName, $this->outbounds);

        $rootObjectSchemaFileLink = $this->outbounds->getAggregateRootSchema($aggregateName);

        $aggregateRoot->change(
            $correlationId,
            $actorEmail,
            $commandCreatedDateTime,
            $aggregateId,
            $aggregateName,
            $rootObjectSchemaFileLink,
            $payload
        );
    }

    public function delete(
        string $correlationId,
        string $actorEmail,
        string $commandCreatedDateTime,
        string $aggregateId,
        string $aggregateName,
    ) {
        $aggregateRoot = Domain\AggregateRoot::new($aggregateId, $aggregateName, $this->outbounds);

        $rootObjectSchema = $this->outbounds->getAggregateRootSchema($aggregateName);
        $aggregateRoot->delete($correlationId, $actorEmail, $commandCreatedDateTime, $aggregateId, $aggregateName,
            $rootObjectSchema);
    }
}