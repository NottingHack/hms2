<?php

use Carbon\Carbon;
use HMS\Entities\Snackspace\Transaction;
use HMS\Entities\Snackspace\TransactionState;
use HMS\Entities\Snackspace\TransactionType;

$factory->defineAs(Transaction::class, 'vend', function (Faker\Generator $faker, array $attributes) {
    $product = $faker->randomElement($attributes['products']);

    return [
        'user' => $attributes['user'],
        'transactionDatetime' => Carbon::instance($faker->dateTimeBetween($attributes['user']->getProfile()->getJoinDate())),
        'amount' => -$product->getPrice(),
        'type' => TransactionType::VEND,
        'status' => TransactionState::COMPLETE,
        'description' => $product->getShortDescription(),
        'product' => $product,
    ];
});

$factory->defineAs(Transaction::class, 'payment', function (Faker\Generator $faker, array $attributes) {
    $amount = $faker->randomElement([500, 1000, 2000]);

    return [
        'user' => $attributes['user'],
        'transactionDatetime' => $attributes['user']->getProfile()->getJoinDate(),
        'amount' => $amount,
        'type' => TransactionType::CASH_PAYMENT,
        'status' => TransactionState::COMPLETE,
        'description' => 'Note payment - £' . $amount / 100,
    ];
});
