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