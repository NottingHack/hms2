<?php

use Carbon\Carbon;
use Illuminate\Support\Str;
use Doctrine\Common\Collections\ArrayCollection;

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

$factory->define(HMS\Entities\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'lastname' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'username' => $faker->unique()->userName,
        'rememberToken' => null,
        'roles' => new ArrayCollection(),
        'emailVerifiedAt' => Carbon::now(),
    ];
});
