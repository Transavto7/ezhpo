<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\WorkReport::class, function (Faker $faker) {
    return [
        'date' => $faker->dateTimeBetween('-2 years'),
        'user_id' => \App\User::inRandomOrder()->first()->id,
        'pv_id' => \App\Point::inRandomOrder()->first()->id
    ];
});
