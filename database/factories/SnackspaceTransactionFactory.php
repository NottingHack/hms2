<?php

use Carbon\Carbon;
use HMS\Entities\Snackspace\Transaction;

$factory->defineAs(HMS\Entities\Snackspace\Transaction::class, 'vend', function (Faker\Generator $faker, array $attributes) {
    $product = $faker->randomElement($attributes['products']);

    return [
        'user' => $attributes['user'],
        'transactionDatetime' => Carbon::now(),
        'amount' => -$product->getPrice(),
        'type' => Transaction::TYPE_VEND,
        'status' => Transaction::STATE_COMPLETE,
        'description' => $product->getShortDescription(),
        'product' => $product,
    ];
});

$factory->defineAs(HMS\Entities\Snackspace\Transaction::class, 'payment', function (Faker\Generator $faker, array $attributes) {
    $amount = $faker->randomElement([500, 1000, 2000]);

    return [
        'user' => $attributes['user'],
        'transactionDatetime' => Carbon::now(),
        'amount' => $amount,
        'type' => Transaction::TYPE_VEND,
        'status' => Transaction::STATE_COMPLETE,
        'description' => 'Note payment - £'.$amount / 100,
    ];
});
