<?php

use Carbon\Carbon;
use HMS\Entities\GateKeeper\AccessLog;

$factory->define(AccessLog::class, function (Faker\Generator $faker, array $attributes) {
    return [
        'rfidSerial' => $attributes['tag']->getBestRfidSerial(),
        'user' => $attributes['tag']->getUser(),
        'accessResult' => AccessLog::ACCESS_GRANTED,
        'door' => $attributes['door'],
        'accessTime' => Carbon::now(),
    ];
});
