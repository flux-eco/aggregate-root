<?php

namespace FluxEco\AggregateRoot;

class Env
{
    const AGGREGATE_ROOT_SCHEMA_DIRECTORY = 'AGGREGATE_ROOT_SCHEMA_DIRECTORY';
    const AGGREGATE_ROOT_STORAGE_CONFIG_ENV_PREFIX = 'AGGREGATE_ROOT_STORAGE_CONFIG_ENV_PREFIX';
    const AGGREGATE_ROOT_EVENT_SCHEMA_FILE_PATH = 'AGGREGATE_ROOT_EVENT_SCHEMA_FILE_PATH';

    private function __construct()
    {

    }

    public static function new()
    {
        return new self();
    }

    public function getAggregateRootEventSchemaFilePath() : string
    {
        return getenv(self::AGGREGATE_ROOT_EVENT_SCHEMA_FILE_PATH);
    }

    public function getAggregateRootDirectory() : string
    {
        return getenv(self::AGGREGATE_ROOT_SCHEMA_DIRECTORY);
    }

    public function getAggregateRootStorageConfigEnvPrefix() : string
    {
        return getenv(self::AGGREGATE_ROOT_STORAGE_CONFIG_ENV_PREFIX);
    }
}