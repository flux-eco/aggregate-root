# flux-eco/aggregate-root

Manage aggregate roots described as json schema. Evaluates and stores events in a storage. Up to now this component is binded to a mysql storage. 
The storage can be replaced by the implementation of a further adapter.

The usage is demonstrated by the following example application:
https://github.com/flux-caps/todo-app

## Usage
.env
```
AGGREGATE_ROOT_SCHEMA_DIRECTORY=schemas/domain
AGGREGATE_ROOT_STORAGE_CONFIG_ENV_PREFIX=EVENTS_
AGGREGATE_ROOT_EVENT_SCHEMA_FILE_PATH=../schemas/AggregateRootEvent.yaml
EVENTS_STORAGE_HOST=localhost
EVENTS_STORAGE_DRIVER=Pdo_Mysql
EVENTS_STORAGE_NAME=events
EVENTS_STORAGE_USER=user
EVENTS_STORAGE_PASSWORD=password
PROJECTION_APP_SCHEMA_DIRECTORY=../vendor/flux-eco/projection/schemas
PROJECTION_ECO_SCHEMA_DIRECTORY=schemas/projections
PROJECTION_STORAGE_CONFIG_ENV_PREFIX=PROJECTION_
PROJECTION_STORAGE_NAME=projection
PROJECTION_STORAGE_HOST=localhost
PROJECTION_STORAGE_DRIVER=Pdo_Mysql
PROJECTION_STORAGE_USER=user
PROJECTION_STORAGE_PASSWORD=password
STREAM_STORAGE_CONFIG_ENV_PREFIX=STREAM_
STREAM_STORAGE_NAME=stream
STREAM_STORAGE_HOST=localhost
STREAM_STORAGE_DRIVER=Pdo_Mysql
STREAM_STORAGE_USER=user
STREAM_STORAGE_PASSWORD=password
STREAM_TABLE_NAME=stream
STREAM_STATE_SCHEMA_FILE=../vendor/flux-eco/global-stream/schemas/State.yaml
```

schemas\domain\account.yaml
```
name: account
type: object
properties:
    aggregateId:
        type: string
        readOnly: true
    correlationId:
        type: string
        readOnly: true
    aggregateName:
        type: string
        const: todo
        readOnly: true
    sequence:
        type: integer
        readOnly: true
    createdDateTime:
        type: string
        format: date-time
        readOnly: true
    createdBy:
        type: string
        format: email
        readOnly: true
    changedDateTime:
        type: string
        format: date-time
        readOnly: true
    changedBy:
        type: string
        format: email
        readOnly: true
    rootObjectSchema:
        type: string
        const: v1
    rootObject:
        type: object
        properties:
            firstname:
                type: string
            lastname:
                type: string
```

schemas\projections\account.yaml
```
title: account
type: object
aggregateRootNames:
  - account
properties:
  projectionId:
    type: string
  firstname:
    type: string
    index: account.rootObject.firstname
  lastname:
    type: string
    index: account.rootObject.lastname
```

example.php
```
<?php

require_once __DIR__ . '/../vendor/autoload.php';

FluxEco\DotEnv\Api::new()->load(__DIR__);

//initialize
fluxAggregateRoot\initialize();

//create
$correlationId = fluxValueObject\getNewUuid();
$actorEmail = 'example@fluxlabs.ch';
$aggregateName = 'account';
$aggregateId = fluxValueObject\getNewUuid();
$payload = json_encode([
   "firstname" => "Emmett",
   "lastname" => "Brown"
]);
fluxAggregateRoot\create($correlationId, $actorEmail, $aggregateName, $aggregateId, $payload);


//change
$correlationId = fluxValueObject\getNewUuid();
$payload = json_encode([
    "firstname" => "Dr. Emmett",
    "lastname" => "Brown"
]);
fluxAggregateRoot\change($correlationId, $actorEmail, $aggregateName, $aggregateId, $payload);

//delete
fluxAggregateRoot\delete($correlationId, $actorEmail, $aggregateName, $aggregateId);
```

## Contributing :purple_heart:
Please ...
1. ... register an account at https://git.fluxlabs.ch
2. ... create pull requests :fire:


## Adjustment suggestions / bug reporting :feet:
Please ...
1. ... register an account at https://git.fluxlabs.ch
2. ... ask us for a Service Level Agreement: support@fluxlabs.ch :kissing_heart:
3. ... read and create issues