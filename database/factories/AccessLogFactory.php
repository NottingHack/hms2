<?php

use Carbon\Carbon;
use HMS\Entities\Gatekeeper\AccessLog;
use HMS\Entities\Gatekeeper\AccessLogResult;

$factory->define(AccessLog::class, function (Faker\Generator $faker, array $attributes) {
    return [
        'rfidSerial' => $attributes['tag']->getBestRfidSerial(),
        'user' => $attributes['tag']->getUser(),
        'accessResult' => AccessLogResult::ACCESS_GRANTED,
        'door' => $attributes['door'],
        'accessTime' => Carbon::now(),
    ];
});
