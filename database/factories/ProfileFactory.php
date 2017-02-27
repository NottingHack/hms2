<?php

use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(HMS\Entities\Profile::class, function (Faker\Generator $faker, array $attributes) {
    return [
        'user' => isset($attributes['user']) ? $attributes['user'] : null,
        'joinDate' => Carbon::instance($faker->dateTimeBetween($startDate = '-10 years')),
        'unlockText' => 'Welcome ' . $attributes['user']->getFirstName(),
        'creditLimit' => \Meta::get('member_credit_limit'),
        'address1' => $faker->streetAddress,
        'addressCity' => $faker->city,
        'addressCounty' => $faker->county,
        'addressPostcode' => $faker->postcode,
        'contactNumber' => $faker->phoneNumber,
        'dateOfBirth' => Carbon::instance($faker->dateTimeBetween($startDate = '-100 years', $endDate = '-18 years')),
    ];
});

$factory->defineAs(HMS\Entities\Profile::class, 'youngHacker', function (Faker\Generator $faker, array $attributes) {
    return [
        'user' => isset($attributes['user']) ? $attributes['user'] : null,
        'joinDate' => Carbon::instance($faker->dateTimeBetween($startDate = '-2 years')),
        'unlockText' => 'Welcome ' . $attributes['user']->getFirstName(),
        'creditLimit' => \Meta::get('member_credit_limit'),
        'address1' => $faker->streetAddress,
        'addressCity' => $faker->city,
        'addressCounty' => $faker->county,
        'addressPostcode' => $faker->postcode,
        'contactNumber' => $faker->phoneNumber,
        'dateOfBirth' => Carbon::instance($faker->dateTimeBetween($startDate = '-18 years', $endDate = '-16 years')),
    ];
});

$factory->defineAs(HMS\Entities\Profile::class, 'approval', function (Faker\Generator $faker, array $attributes) {
    return [
        'user' => isset($attributes['user']) ? $attributes['user'] : null,
        'unlockText' => 'Welcome ' . $attributes['user']->getFirstName(),
        'creditLimit' => \Meta::get('member_credit_limit'),
        'address1' => $faker->streetAddress,
        'addressCity' => $faker->city,
        'addressCounty' => $faker->county,
        'addressPostcode' => $faker->postcode,
        'contactNumber' => $faker->phoneNumber,
        'dateOfBirth' => Carbon::instance($faker->dateTimeBetween($startDate = '-18 years', $endDate = '-16 years')),
    ];
});
